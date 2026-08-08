<?php
/**
 * One-time — create Studio News post: Joy of Europe 2026 kids submissions.
 *
 * Pulls images from Cloudinary folder: Kid exhibition news/1
 * Categories: Studio News + Kid Exhibition
 * Does NOT modify the theme.
 *
 * Usage:
 * 1. Upload this file to the WordPress root (same folder as wp-load.php).
 * 2. Log in as admin, visit: https://arttutorhanoi.com/create-joy-of-europe-post-once.php
 * 3. Confirm Create Post.
 * 4. Delete this file from the server root after success.
 *
 * @package Art_Tutor_Hanoi
 */

require __DIR__ . '/wp-load.php';

if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
	wp_die( 'Admin login required.' );
}

header( 'Content-Type: text/html; charset=utf-8' );

const ATH_JOY_POST_OPTION     = 'ath_joy_of_europe_2026_post_id';
const ATH_JOY_CLOUD_NAME      = 'dwy4wtjtc';
const ATH_JOY_ASSET_FOLDER    = 'Kid exhibition news/1';
const ATH_JOY_POST_SLUG       = 'joy-of-europe-2026-kids-submissions';
const ATH_JOY_STUDIO_NEWS     = 'studio-news';
const ATH_JOY_KID_EXHIBITION  = 'kid-exhibition';

/**
 * Parse cloudinary://key:secret@cloud URL.
 *
 * @return array{api_key:string,api_secret:string,cloud_name:string}|null
 */
function ath_joy_parse_cloudinary_url( $url ) {
	$url = trim( (string) $url );
	if ( $url === '' ) {
		return null;
	}
	$url = preg_replace( '/^CLOUDINARY_URL=/', '', $url );
	if ( ! preg_match( '#^cloudinary://([^:]+):([^@]+)@([^/\s]+)#', $url, $matches ) ) {
		return null;
	}
	return array(
		'api_key'    => $matches[1],
		'api_secret' => $matches[2],
		'cloud_name' => $matches[3],
	);
}

/**
 * Cloudinary credentials from WP Cloudinary plugin / constants.
 *
 * @return array{api_key:string,api_secret:string,cloud_name:string}|null
 */
function ath_joy_cloudinary_credentials() {
	$candidates = array();

	if ( defined( 'CLOUDINARY_CONNECTION_STRING' ) ) {
		$candidates[] = (string) CLOUDINARY_CONNECTION_STRING;
	}
	if ( defined( 'CLOUDINARY_URL' ) ) {
		$candidates[] = (string) CLOUDINARY_URL;
	}

	$connect = get_option( 'cloudinary_connect', array() );
	if ( is_array( $connect ) && ! empty( $connect['cloudinary_url'] ) ) {
		$candidates[] = (string) $connect['cloudinary_url'];
	}

	$direct = get_option( 'cloudinary_url', '' );
	if ( is_string( $direct ) && $direct !== '' ) {
		$candidates[] = $direct;
	}

	foreach ( $candidates as $url ) {
		$parsed = ath_joy_parse_cloudinary_url( $url );
		if ( $parsed && strcasecmp( $parsed['cloud_name'], ATH_JOY_CLOUD_NAME ) === 0 ) {
			return $parsed;
		}
	}

	return null;
}

/**
 * Authenticated Cloudinary request.
 *
 * @param array{api_key:string,api_secret:string,cloud_name:string} $creds Credentials.
 * @param string                                                     $method GET|POST.
 * @param string                                                     $path   Path after /v1_1/{cloud}/.
 * @param array<string,mixed>                                        $params Query or body params.
 * @return array{ok:bool,data?:array<string,mixed>,error?:string}
 */
function ath_joy_cloudinary_request( array $creds, $method, $path, array $params = array() ) {
	$cloud = rawurlencode( $creds['cloud_name'] );
	$url   = "https://api.cloudinary.com/v1_1/{$cloud}/{$path}";
	$args  = array(
		'timeout' => 45,
		'headers' => array(
			'Authorization' => 'Basic ' . base64_encode( $creds['api_key'] . ':' . $creds['api_secret'] ),
		),
	);

	if ( strtoupper( $method ) === 'POST' ) {
		$args['headers']['Content-Type'] = 'application/json; charset=utf-8';
		$args['body']                    = wp_json_encode( $params );
		$response                        = wp_remote_post( $url, $args );
	} else {
		$response = wp_remote_get( add_query_arg( $params, $url ), $args );
	}

	if ( is_wp_error( $response ) ) {
		return array(
			'ok'    => false,
			'error' => $response->get_error_message(),
		);
	}

	$code = (int) wp_remote_retrieve_response_code( $response );
	$body = json_decode( (string) wp_remote_retrieve_body( $response ), true );

	if ( $code < 200 || $code >= 300 || ! is_array( $body ) ) {
		$msg = is_array( $body ) && isset( $body['error']['message'] )
			? (string) $body['error']['message']
			: 'Cloudinary API HTTP ' . $code;
		return array(
			'ok'    => false,
			'error' => $msg,
		);
	}

	return array(
		'ok'   => true,
		'data' => $body,
	);
}

/**
 * Normalize Cloudinary image resources.
 *
 * @param array<int,array<string,mixed>> $resources Raw resources.
 * @return array<int,array<string,mixed>>
 */
function ath_joy_normalize_resources( array $resources ) {
	$out = array();
	foreach ( $resources as $resource ) {
		if ( ( $resource['resource_type'] ?? 'image' ) !== 'image' ) {
			continue;
		}
		if ( empty( $resource['secure_url'] ) && empty( $resource['public_id'] ) ) {
			continue;
		}
		$out[] = $resource;
	}

	usort(
		$out,
		static function ( $a, $b ) {
			$af = (string) ( $a['filename'] ?? $a['public_id'] ?? '' );
			$bf = (string) ( $b['filename'] ?? $b['public_id'] ?? '' );
			return strnatcasecmp( $af, $bf );
		}
	);

	return $out;
}

/**
 * List image resources in a Cloudinary asset folder.
 *
 * @param array{api_key:string,api_secret:string,cloud_name:string} $creds Credentials.
 * @return array{ok:bool,resources?:array<int,array<string,mixed>>,error?:string}
 */
function ath_joy_list_cloudinary_folder( array $creds, $folder ) {
	$resources   = array();
	$next_cursor = null;

	do {
		$params = array(
			'asset_folder'  => $folder,
			'max_results'   => 100,
			'resource_type' => 'image',
		);
		if ( $next_cursor ) {
			$params['next_cursor'] = $next_cursor;
		}

		$response = ath_joy_cloudinary_request( $creds, 'GET', 'resources/by_asset_folder', $params );
		if ( ! $response['ok'] ) {
			// Fallback: Search API (works when by_asset_folder is unavailable).
			return ath_joy_search_cloudinary_folder( $creds, $folder );
		}

		foreach ( (array) ( $response['data']['resources'] ?? array() ) as $resource ) {
			$resources[] = $resource;
		}
		$next_cursor = $response['data']['next_cursor'] ?? null;
	} while ( $next_cursor );

	return array(
		'ok'        => true,
		'resources' => ath_joy_normalize_resources( $resources ),
	);
}

/**
 * Search API fallback for asset folder.
 *
 * @param array{api_key:string,api_secret:string,cloud_name:string} $creds Credentials.
 * @return array{ok:bool,resources?:array<int,array<string,mixed>>,error?:string}
 */
function ath_joy_search_cloudinary_folder( array $creds, $folder ) {
	$resources   = array();
	$next_cursor = null;
	$expression  = 'asset_folder="' . str_replace( '"', '\\"', $folder ) . '" AND resource_type:image';

	do {
		$params = array(
			'expression'  => $expression,
			'max_results' => 100,
		);
		if ( $next_cursor ) {
			$params['next_cursor'] = $next_cursor;
		}

		$response = ath_joy_cloudinary_request( $creds, 'POST', 'resources/search', $params );
		if ( ! $response['ok'] ) {
			return array(
				'ok'    => false,
				'error' => $response['error'] ?? 'Cloudinary search failed.',
			);
		}

		foreach ( (array) ( $response['data']['resources'] ?? array() ) as $resource ) {
			$resources[] = $resource;
		}
		$next_cursor = $response['data']['next_cursor'] ?? null;
	} while ( $next_cursor );

	return array(
		'ok'        => true,
		'resources' => ath_joy_normalize_resources( $resources ),
	);
}

/**
 * Delivery URL with friendly transforms.
 *
 * @param array<string,mixed> $resource Cloudinary resource.
 */
function ath_joy_delivery_url( array $resource, $width = 1600 ) {
	$public_id = (string) ( $resource['public_id'] ?? '' );
	$version   = ! empty( $resource['version'] ) ? 'v' . $resource['version'] . '/' : '';
	$format    = ! empty( $resource['format'] ) ? '.' . $resource['format'] : '';

	if ( $public_id === '' ) {
		return (string) ( $resource['secure_url'] ?? '' );
	}

	return sprintf(
		'https://res.cloudinary.com/%s/image/upload/c_limit,w_%d,f_auto,q_auto/%s%s%s',
		ATH_JOY_CLOUD_NAME,
		(int) $width,
		$version,
		$public_id,
		$format
	);
}

/**
 * Ensure category exists; return term_id.
 *
 * @param string $slug Category slug.
 * @param string $name Category name.
 */
function ath_joy_ensure_category( $slug, $name ) {
	$term = get_category_by_slug( $slug );
	if ( $term ) {
		return (int) $term->term_id;
	}

	$blog = get_category_by_slug( 'blog' );
	$parent = $blog ? (int) $blog->term_id : 0;

	$result = wp_insert_term(
		$name,
		'category',
		array(
			'slug'   => $slug,
			'parent' => $parent,
		)
	);

	if ( is_wp_error( $result ) ) {
		return 0;
	}

	return (int) $result['term_id'];
}

/**
 * Build Gutenberg post content.
 *
 * @param array<int,array<string,mixed>> $resources Cloudinary images.
 */
function ath_joy_build_post_content( array $resources ) {
	$blocks   = array();
	$blocks[] = '<!-- wp:paragraph -->';
	$blocks[] = '<p>We have successfully submitted <strong>12 artworks</strong> by <strong>11 of our students</strong> to the international art competition <strong>Joy of Europe</strong> (<em>Radost Evrope</em>) in Serbia.</p>';
	$blocks[] = '<!-- /wp:paragraph -->';
	$blocks[] = '';
	$blocks[] = '<!-- wp:paragraph -->';
	$blocks[] = '<p>Results will be announced on <strong>31 August</strong>.</p>';
	$blocks[] = '<!-- /wp:paragraph -->';
	$blocks[] = '';
	$blocks[] = '<!-- wp:paragraph -->';
	$blocks[] = '<p>A selection of the submitted works:</p>';
	$blocks[] = '<!-- /wp:paragraph -->';

	$index = 0;
	foreach ( $resources as $resource ) {
		$index++;
		$url    = ath_joy_delivery_url( $resource, 1600 );
		$width  = (int) ( $resource['width'] ?? 0 );
		$height = (int) ( $resource['height'] ?? 0 );
		$alt    = sprintf( 'Student artwork submitted to Joy of Europe 2026 (%d)', $index );

		if ( $url === '' ) {
			continue;
		}

		$attrs = array(
			'sizeSlug' => 'large',
			'linkDestination' => 'none',
		);
		$attrs_json = wp_json_encode( $attrs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
		$size_attr  = '';
		if ( $width > 0 && $height > 0 ) {
			$size_attr = sprintf( ' width="%d" height="%d"', $width, $height );
		}

		$blocks[] = '<!-- wp:image ' . $attrs_json . ' -->';
		$blocks[] = '<figure class="wp-block-image size-large"><img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '"' . $size_attr . ' loading="lazy" decoding="async"/></figure>';
		$blocks[] = '<!-- /wp:image -->';
		$blocks[] = '';
	}

	return implode( "\n", $blocks );
}

/**
 * Create or update the post.
 *
 * @param array<int,array<string,mixed>> $resources Images.
 * @return array{ok:bool,post_id?:int,edit_link?:string,view_link?:string,error?:string,updated?:bool}
 */
function ath_joy_create_post( array $resources ) {
	if ( empty( $resources ) ) {
		return array(
			'ok'    => false,
			'error' => 'No images found in Cloudinary folder "' . ATH_JOY_ASSET_FOLDER . '".',
		);
	}

	$studio_id = ath_joy_ensure_category( ATH_JOY_STUDIO_NEWS, 'Studio News' );
	$kid_id    = ath_joy_ensure_category( ATH_JOY_KID_EXHIBITION, 'Kid Exhibition' );
	$cat_ids   = array_values( array_filter( array( $studio_id, $kid_id ) ) );

	if ( count( $cat_ids ) < 2 ) {
		return array(
			'ok'    => false,
			'error' => 'Could not resolve categories studio-news / kid-exhibition.',
		);
	}

	$title   = '12 Artworks Submitted to Joy of Europe 2026 in Serbia';
	$content = ath_joy_build_post_content( $resources );
	$excerpt = 'We submitted 12 artworks by 11 students to the Joy of Europe international art competition in Serbia. Results will be announced on 31 August.';

	$existing_id = (int) get_option( ATH_JOY_POST_OPTION, 0 );
	$by_slug     = get_page_by_path( ATH_JOY_POST_SLUG, OBJECT, 'post' );
	if ( ! $existing_id && $by_slug ) {
		$existing_id = (int) $by_slug->ID;
	}

	$postarr = array(
		'post_title'   => $title,
		'post_name'    => ATH_JOY_POST_SLUG,
		'post_content' => $content,
		'post_excerpt' => $excerpt,
		'post_status'  => 'publish',
		'post_type'    => 'post',
		'post_author'  => get_current_user_id(),
		'post_category'=> $cat_ids,
	);

	if ( $existing_id && get_post( $existing_id ) ) {
		$postarr['ID'] = $existing_id;
		$post_id       = wp_update_post( $postarr, true );
		$updated       = true;
	} else {
		$post_id = wp_insert_post( $postarr, true );
		$updated = false;
	}

	if ( is_wp_error( $post_id ) ) {
		return array(
			'ok'    => false,
			'error' => $post_id->get_error_message(),
		);
	}

	$post_id = (int) $post_id;
	update_option( ATH_JOY_POST_OPTION, $post_id, false );
	wp_set_post_categories( $post_id, $cat_ids, false );

	if ( function_exists( 'ath_flush_studio_news_cache' ) ) {
		ath_flush_studio_news_cache();
	}

	return array(
		'ok'        => true,
		'post_id'   => $post_id,
		'updated'   => $updated,
		'edit_link' => get_edit_post_link( $post_id, 'raw' ),
		'view_link' => get_permalink( $post_id ),
		'image_count' => count( $resources ),
	);
}

$creds   = ath_joy_cloudinary_credentials();
$preview = null;
$result  = null;
$error   = null;

if ( ! $creds ) {
	$error = 'Cloudinary credentials for cloud "' . ATH_JOY_CLOUD_NAME . '" not found in WordPress options (cloudinary_connect / cloudinary_url).';
} else {
	$listed = ath_joy_list_cloudinary_folder( $creds, ATH_JOY_ASSET_FOLDER );
	if ( ! $listed['ok'] ) {
		$error = 'Cloudinary list failed: ' . $listed['error'];
	} else {
		$preview = $listed['resources'];
		if ( isset( $_POST['ath_joy_create'] ) && check_admin_referer( 'ath_joy_create_post' ) ) {
			$result = ath_joy_create_post( $preview );
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Create Joy of Europe 2026 post</title>
	<style>
		body { font-family: system-ui, sans-serif; max-width: 880px; margin: 2rem auto; padding: 0 1rem; line-height: 1.5; }
		.ok { color: #0a7; }
		.err { color: #b00020; }
		.meta { color: #555; }
		code { background: #f4f4f4; padding: 0.1em 0.35em; border-radius: 3px; }
		.grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 0.75rem; margin: 1rem 0; }
		.grid img { width: 100%; height: 120px; object-fit: cover; border-radius: 6px; background: #eee; }
		button { background: #1a1a1a; color: #fff; border: 0; padding: 0.7rem 1.2rem; border-radius: 6px; cursor: pointer; font-size: 1rem; }
		button:hover { opacity: 0.9; }
		.warn { background: #fff8e6; border: 1px solid #e6c200; padding: 0.75rem 1rem; border-radius: 6px; }
	</style>
</head>
<body>
	<h1>Joy of Europe 2026 — Studio News post</h1>
	<p class="meta">Folder: <code><?php echo esc_html( ATH_JOY_ASSET_FOLDER ); ?></code> · Cloud: <code><?php echo esc_html( ATH_JOY_CLOUD_NAME ); ?></code></p>
	<p class="meta">Categories: <code>studio-news</code> + <code>kid-exhibition</code></p>

	<?php if ( $error ) : ?>
		<p class="err"><?php echo esc_html( $error ); ?></p>
	<?php endif; ?>

	<?php if ( $result && ! empty( $result['ok'] ) ) : ?>
		<p class="ok">
			<?php echo ! empty( $result['updated'] ) ? 'Updated' : 'Created'; ?>
			post #<?php echo (int) $result['post_id']; ?>
			with <?php echo (int) $result['image_count']; ?> images.
		</p>
		<p>
			<a href="<?php echo esc_url( $result['view_link'] ); ?>">View post</a>
			·
			<a href="<?php echo esc_url( $result['edit_link'] ); ?>">Edit in admin</a>
		</p>
		<p class="warn"><strong>Delete</strong> <code>create-joy-of-europe-post-once.php</code> from the WordPress root after you are done.</p>
	<?php elseif ( $result && empty( $result['ok'] ) ) : ?>
		<p class="err"><?php echo esc_html( $result['error'] ?? 'Create failed.' ); ?></p>
	<?php endif; ?>

	<?php if ( is_array( $preview ) ) : ?>
		<p>Found <strong><?php echo count( $preview ); ?></strong> image(s) in Cloudinary.</p>
		<div class="grid">
			<?php foreach ( $preview as $resource ) : ?>
				<img src="<?php echo esc_url( ath_joy_delivery_url( $resource, 400 ) ); ?>" alt="">
			<?php endforeach; ?>
		</div>

		<?php if ( empty( $result['ok'] ) ) : ?>
			<form method="post">
				<?php wp_nonce_field( 'ath_joy_create_post' ); ?>
				<button type="submit" name="ath_joy_create" value="1">Create / update post</button>
			</form>
			<p class="meta">Title: <em>12 Artworks Submitted to Joy of Europe 2026 in Serbia</em></p>
		<?php endif; ?>
	<?php endif; ?>
</body>
</html>
