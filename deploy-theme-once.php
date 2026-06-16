<?php
/**
 * One-time theme deploy over HTTPS (when FTP/SFTP ports are blocked on your network).
 *
 * Usage:
 * 1. Run locally:  scripts/build-theme-deploy.ps1
 * 2. Upload deploy-theme-once.php + art-tutor-deploy.zip to WordPress root (cPanel File Manager).
 * 3. Log in as admin, visit: https://yoursite.com/deploy-theme-once.php
 * 4. Click Deploy (or upload zip on the form).
 * 5. Delete both files from server root after success.
 *
 * @package Art_Tutor_Hanoi
 */

require __DIR__ . '/wp-load.php';

if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
	wp_die( 'Admin login required.' );
}

$theme_dir = get_stylesheet_directory();
$zip_name  = 'art-tutor-deploy.zip';
$zip_path  = __DIR__ . '/' . $zip_name;
$max_bytes = 5 * 1024 * 1024;

$allowed_prefixes = array(
	'inc/',
	'config/',
	'partials/',
	'assets/css/',
	'assets/js/',
	'data/',
	'templates/',
);

/**
 * @param string $path Path inside zip (forward slashes).
 */
function ath_deploy_path_allowed( $path, $allowed_prefixes ) {
	$path = str_replace( '\\', '/', $path );
	if ( '' === $path || str_starts_with( $path, '/' ) || str_contains( $path, '..' ) ) {
		return false;
	}
	foreach ( $allowed_prefixes as $prefix ) {
		if ( str_starts_with( $path, $prefix ) ) {
			return true;
		}
	}
	return false;
}

/**
 * @return array{ok:bool, messages:string[], files:string[]}
 */
function ath_deploy_extract_zip( $zip_file, $theme_dir, $allowed_prefixes, $max_bytes ) {
	$messages = array();
	$files    = array();

	if ( ! class_exists( 'ZipArchive' ) ) {
		return array(
			'ok'       => false,
			'messages' => array( 'ZipArchive PHP extension is not available on this server.' ),
			'files'    => array(),
		);
	}

	$size = filesize( $zip_file );
	if ( false === $size || $size > $max_bytes ) {
		return array(
			'ok'       => false,
			'messages' => array( 'Zip file missing or larger than 5 MB.' ),
			'files'    => array(),
		);
	}

	$zip = new ZipArchive();
	if ( true !== $zip->open( $zip_file ) ) {
		return array(
			'ok'       => false,
			'messages' => array( 'Could not open zip archive.' ),
			'files'    => array(),
		);
	}

	for ( $i = 0; $i < $zip->numFiles; $i++ ) {
		$name = $zip->getNameIndex( $i );
		if ( false === $name ) {
			continue;
		}

		$name = str_replace( '\\', '/', $name );
		if ( str_ends_with( $name, '/' ) ) {
			continue;
		}

		if ( ! ath_deploy_path_allowed( $name, $allowed_prefixes ) ) {
			$messages[] = 'Skipped (not allowed): ' . esc_html( $name );
			continue;
		}

		$dest = $theme_dir . '/' . $name;
		$dir  = dirname( $dest );
		if ( ! is_dir( $dir ) && ! wp_mkdir_p( $dir ) ) {
			$messages[] = 'Failed to create folder for: ' . esc_html( $name );
			continue;
		}

		$contents = $zip->getFromIndex( $i );
		if ( false === $contents ) {
			$messages[] = 'Failed to read: ' . esc_html( $name );
			continue;
		}

		if ( false === file_put_contents( $dest, $contents ) ) {
			$messages[] = 'Failed to write: ' . esc_html( $name );
			continue;
		}

		$files[]    = $name;
		$messages[] = 'Updated: ' . esc_html( $name );
	}

	$zip->close();

	return array(
		'ok'       => ! empty( $files ),
		'messages' => $messages,
		'files'    => $files,
	);
}

$result = null;

if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
	check_admin_referer( 'ath_deploy_theme' );

	$upload_path = $zip_path;

	if ( ! empty( $_FILES['deploy_zip']['tmp_name'] ) && is_uploaded_file( $_FILES['deploy_zip']['tmp_name'] ) ) {
		$upload_path = $_FILES['deploy_zip']['tmp_name'];
	} elseif ( ! is_readable( $zip_path ) ) {
		wp_die( 'No zip uploaded and ' . esc_html( $zip_name ) . ' not found in WordPress root.' );
	}

	$result = ath_deploy_extract_zip( $upload_path, $theme_dir, $allowed_prefixes, $max_bytes );
}

header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Deploy Art Tutor Theme</title>
	<style>
		body { font-family: system-ui, sans-serif; max-width: 720px; margin: 2rem auto; padding: 0 1rem; line-height: 1.5; }
		.ok { color: #0a7; }
		.err { color: #c00; }
		ul { padding-left: 1.25rem; }
		code { background: #f4f4f4; padding: 0.1em 0.35em; border-radius: 3px; }
	</style>
</head>
<body>
	<h1>Deploy Art Tutor Theme (HTTPS)</h1>
	<p>Target: <code><?php echo esc_html( $theme_dir ); ?></code></p>

<?php if ( is_array( $result ) ) : ?>
	<h2 class="<?php echo $result['ok'] ? 'ok' : 'err'; ?>">
		<?php echo $result['ok'] ? 'Deploy complete' : 'Deploy failed or no files updated'; ?>
	</h2>
	<ul>
		<?php foreach ( $result['messages'] as $line ) : ?>
			<li><?php echo $line; ?></li>
		<?php endforeach; ?>
	</ul>
	<?php if ( $result['ok'] ) : ?>
		<p><strong>Next:</strong> Purge cache (Autoptimize + Bluehost), then test
			<code>/wp-json/ath/v1/thank-you-qr?entry=ID&amp;from=adult</code></p>
		<p><strong>SEO playbook:</strong> upload <code>sync-seo-once.php</code> to WordPress root, visit it once (admin), then delete — or open <code>/seo/</code> while logged in as admin.</p>
		<p class="err"><strong>Delete</strong> <code>deploy-theme-once.php</code> and <code><?php echo esc_html( $zip_name ); ?></code> from the server root now.</p>
	<?php endif; ?>
<?php else : ?>
	<p>Use when FTP/SFTP times out (ports 21/22 blocked). Upload <code><?php echo esc_html( $zip_name ); ?></code> to this folder, or choose the zip below.</p>
	<form method="post" enctype="multipart/form-data">
		<?php wp_nonce_field( 'ath_deploy_theme' ); ?>
		<p>
			<label>Zip file (optional if <?php echo esc_html( $zip_name ); ?> is already in root):<br>
				<input type="file" name="deploy_zip" accept=".zip,application/zip">
			</label>
		</p>
		<p><button type="submit">Deploy to theme</button></p>
	</form>
	<?php if ( is_readable( $zip_path ) ) : ?>
		<p class="ok">Found <code><?php echo esc_html( $zip_name ); ?></code> in WordPress root — click Deploy.</p>
	<?php endif; ?>
<?php endif; ?>
</body>
</html>
