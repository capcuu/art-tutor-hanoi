<?php
/**
 * Thank you page — v2 layout, page setup, Fluent Forms redirects.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Fluent Forms IDs that should redirect to the thank you page.
 *
 * @return int[]
 */
function ath_booking_form_ids() {
	$ids = array();

	foreach ( ath_book_tabs() as $tab ) {
		if ( ! empty( $tab['form_id'] ) ) {
			$ids[] = (int) $tab['form_id'];
		}
	}

	$ids = array_values( array_unique( array_filter( array_map( 'intval', $ids ) ) ) );

	return apply_filters( 'ath_booking_form_ids', $ids );
}

/**
 * Whether a Fluent Form ID is a book-a-class form (handles server/local ID mismatch).
 *
 * @param int $form_id Form ID.
 */
function ath_is_booking_form_id( $form_id ) {
	$form_id = (int) $form_id;

	if ( in_array( $form_id, ath_booking_form_ids(), true ) ) {
		return true;
	}

	if ( function_exists( 'ath_fluent_qr_form_config' ) && ath_fluent_qr_form_config( $form_id ) ) {
		return true;
	}

	return (bool) apply_filters( 'ath_is_booking_form_id', false, $form_id );
}

/**
 * Load a Fluent Forms submission row (model or DB fallback).
 *
 * @param int $entry_id Submission ID.
 */
function ath_get_fluent_submission( $entry_id ) {
	$entry_id = (int) $entry_id;

	if ( $entry_id <= 0 ) {
		return null;
	}

	if ( class_exists( '\FluentForm\App\Models\Submission' ) ) {
		$row = \FluentForm\App\Models\Submission::find( $entry_id );
		if ( $row ) {
			return $row;
		}
	}

	global $wpdb;

	return $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}fluentform_submissions WHERE id = %d",
			$entry_id
		)
	);
}

/**
 * VietQR block data for thank-you page (self-contained fallback).
 *
 * @param int    $entry_id Submission ID.
 * @param string $source   Book tab slug (?from=).
 */
function ath_thank_you_qr_data( $entry_id, $source = '' ) {
	if ( function_exists( 'ath_fluent_qr_thank_you_data' ) ) {
		$data = ath_fluent_qr_thank_you_data( $entry_id, $source );
		if ( $data ) {
			return $data;
		}
	}

	$entry_id = (int) $entry_id;
	$source   = sanitize_key( $source );

	if ( $entry_id <= 0 ) {
		return null;
	}

	$submission = ath_get_fluent_submission( $entry_id );

	if ( ! $submission || empty( $submission->form_id ) ) {
		return null;
	}

	$response = json_decode( (string) $submission->response, true );

	if ( ! is_array( $response ) ) {
		$response = array();
	}

	$method = strtolower(
		(string) ( $submission->payment_method ?? ( $response['payment_method'] ?? '' ) )
	);
	$online = array( 'stripe', 'paypal', 'mollie', 'razorpay' );

	if ( in_array( $method, $online, true ) ) {
		return null;
	}

	if ( 'paid' === (string) ( $submission->payment_status ?? '' ) && in_array( $method, $online, true ) ) {
		return null;
	}

	$is_kids = ( 'kids' === $source ) || isset( $response['names'] );
	$base    = $is_kids ? 'KidsClass' : 'AdultBooking';
	$fields  = $is_kids
		? array( 'names[first_name]', 'subject', 'input_text' )
		: array( 'first_name', 'input_text' );

	$name = '';
	foreach ( $fields as $field_name ) {
		if ( function_exists( 'ath_fluent_qr_response_value' ) ) {
			$value = trim( ath_fluent_qr_response_value( $response, $field_name ) );
		} elseif ( preg_match( '/^(.+)\[(.+)\]$/', $field_name, $m ) ) {
			$value = trim( (string) ( $response[ $m[1] ][ $m[2] ] ?? '' ) );
		} else {
			$value = trim( (string) ( $response[ $field_name ] ?? '' ) );
		}
		if ( $value !== '' ) {
			$name = $value;
			break;
		}
	}

	$usd = 0.0;
	if ( function_exists( 'ath_fluent_qr_usd_from_submission' ) ) {
		$usd = ath_fluent_qr_usd_from_submission( $submission, $response );
	} else {
		if ( ! empty( $submission->payment_total ) ) {
			$currency = strtoupper( (string) ( $submission->currency ?? 'USD' ) );
			$total    = (float) $submission->payment_total;
			$usd      = 'USD' === $currency ? ( $total >= 100 ? $total / 100 : $total ) : $total;
		}
		if ( $usd <= 0 && ! empty( $response['payment_input'] ) ) {
			$usd = (float) $response['payment_input'];
		}
	}

	$config   = function_exists( 'ath_fluent_qr_config' ) ? ath_fluent_qr_config() : array();
	$usd2vnd  = (int) ( $config['usd2vnd'] ?? 26500 );
	$amount   = $usd > 0 ? (int) round( $usd * $usd2vnd ) : 0;

	if ( $amount <= 0 ) {
		return null;
	}

	$add_info = $name ? $base . ' - ' . $name : $base;
	if ( function_exists( 'ath_fluent_qr_sanitize_add_info' ) ) {
		$add_info = ath_fluent_qr_sanitize_add_info( $add_info );
	}

	$bank_image = $config['bankImage'] ?? 'https://img.vietqr.io/image/VCB-9971263202-compact.png';
	$qr_url     = function_exists( 'ath_fluent_qr_build_image_url' )
		? ath_fluent_qr_build_image_url( $amount, $add_info )
		: add_query_arg(
			array(
				'amount'      => $amount,
				'addInfo'     => $add_info,
				'accountName' => $config['accountName'] ?? 'Hoang Minh Anh',
			),
			$bank_image
		);

	return array(
		'entry_id'       => $entry_id,
		'amount_label'   => number_format_i18n( $amount ) . ' VND',
		'add_info'       => $add_info,
		'qr_image_url'   => $qr_url,
		'bank_name'      => $config['bankName'] ?? 'VIETCOMBANK',
		'account_number' => $config['accountNumber'] ?? '9971263202',
		'account_name'   => $config['accountName'] ?? 'Hoang Minh Anh',
	);
}

/**
 * Render VietQR HTML for thank-you page.
 *
 * @param array<string, mixed> $qr_data QR data from ath_thank_you_qr_data().
 */
function ath_thank_you_qr_html( array $qr_data ) {
	if ( empty( $qr_data['qr_image_url'] ) ) {
		return '';
	}

	ob_start();
	?>
<div class="thank-you-page__payment ath-vietqr" aria-labelledby="thank-you-payment-heading">
  <h2 id="thank-you-payment-heading" class="thank-you-page__payment-title">Pay by bank transfer</h2>
  <p class="thank-you-page__payment-lead">Scan the QR code with your banking app and pay the exact amount shown.</p>
  <dl class="thank-you-page__bank-details">
    <div class="thank-you-page__bank-row">
      <dt>Bank</dt>
      <dd><?php echo esc_html( $qr_data['bank_name'] ); ?></dd>
    </div>
    <div class="thank-you-page__bank-row">
      <dt>Account number</dt>
      <dd><?php echo esc_html( $qr_data['account_number'] ); ?></dd>
    </div>
    <div class="thank-you-page__bank-row">
      <dt>Account name</dt>
      <dd><?php echo esc_html( $qr_data['account_name'] ); ?></dd>
    </div>
    <div class="thank-you-page__bank-row">
      <dt>Amount</dt>
      <dd><strong><?php echo esc_html( $qr_data['amount_label'] ); ?></strong></dd>
    </div>
    <?php if ( ! empty( $qr_data['add_info'] ) ) : ?>
    <div class="thank-you-page__bank-row">
      <dt>Transfer note</dt>
      <dd><?php echo esc_html( $qr_data['add_info'] ); ?></dd>
    </div>
    <?php endif; ?>
  </dl>
  <p class="thank-you-page__qr-wrap">
    <img class="thank-you-page__qr-image" src="<?php echo esc_url( $qr_data['qr_image_url'] ); ?>" alt="<?php esc_attr_e( 'VietQR payment code', 'art-tutor-hanoi' ); ?>" width="280" height="280" loading="eager" decoding="async" />
  </p>
</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * Diagnose why thank-you QR data is empty (for REST debug / support).
 *
 * @param int    $entry_id Submission ID.
 * @param string $source   Book tab slug.
 * @return array<string, mixed>
 */
function ath_thank_you_qr_diagnose( $entry_id, $source = '' ) {
	$entry_id = (int) $entry_id;
	$source   = sanitize_key( $source );
	$out      = array(
		'v'       => defined( 'ATH_QR_LOGIC_VERSION' ) ? ATH_QR_LOGIC_VERSION : 'legacy',
		'entry'   => $entry_id,
		'from'    => $source,
		'adult_form_id' => defined( 'ATH_ADULT_BOOKING_FORM_ID' ) ? (int) ATH_ADULT_BOOKING_FORM_ID : null,
	);

	if ( $entry_id <= 0 ) {
		$out['why'] = 'missing_entry_id';
		return $out;
	}

	$submission = function_exists( 'ath_get_fluent_submission' )
		? ath_get_fluent_submission( $entry_id )
		: ( function_exists( 'ath_fluent_get_submission' ) ? ath_fluent_get_submission( $entry_id ) : null );

	if ( ! $submission ) {
		global $wpdb;
		$table = $wpdb->prefix . 'fluentform_submissions';
		$out['why']              = 'submission_not_found';
		$out['submissions_table'] = $table;
		$out['latest_entry_id']   = (int) $wpdb->get_var( "SELECT MAX(id) FROM {$table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $out;
	}

	$response = json_decode( (string) $submission->response, true );
	if ( ! is_array( $response ) ) {
		$response = array();
	}

	$method = strtolower( (string) ( $submission->payment_method ?? ( $response['payment_method'] ?? '' ) ) );
	$online = array( 'stripe', 'paypal', 'mollie', 'razorpay' );

	$out['form_id']         = (int) $submission->form_id;
	$out['payment_method']  = $method;
	$out['payment_status']  = (string) ( $submission->payment_status ?? '' );
	$out['payment_input']   = $response['payment_input'] ?? null;
	$out['dropdown']        = $response['dropdown'] ?? null;
	$out['payment_total']   = $submission->payment_total ?? null;

	if ( in_array( $method, $online, true ) ) {
		$out['why'] = 'online_payment_method';
		return $out;
	}

	if ( function_exists( 'ath_fluent_qr_usd_from_submission' ) ) {
		$out['computed_usd'] = ath_fluent_qr_usd_from_submission( $submission, $response );
	} else {
		$out['computed_usd'] = null;
		$out['why']          = 'old_fluent_qr_php_on_server';
		return $out;
	}

	if ( empty( $out['computed_usd'] ) || (float) $out['computed_usd'] <= 0 ) {
		$out['why'] = 'amount_zero';
		return $out;
	}

	$out['why'] = 'ok';
	return $out;
}

/**
 * Map booking form ID to book tab slug (for ?from= on thank you page).
 *
 * @param int $form_id Fluent Forms ID.
 */
function ath_booking_form_source( $form_id ) {
	$form_id = (int) $form_id;

	foreach ( ath_book_tabs() as $slug => $tab ) {
		if ( ! empty( $tab['form_id'] ) && (int) $tab['form_id'] === $form_id ) {
			return $slug;
		}
	}

	return '';
}

/**
 * Thank you page base URL.
 */
function ath_thank_you_base_url() {
	return user_trailingslashit( home_url( '/thank-you' ) );
}

/**
 * Thank you page URL with optional source tab.
 *
 * @param string $from Book tab slug or life-drawing.
 */
function ath_thank_you_url( $from = '' ) {
	$url = ath_thank_you_base_url();

	if ( $from !== '' ) {
		$url = add_query_arg( 'from', sanitize_key( $from ), $url );
	}

	return $url;
}

/**
 * Is this the thank you page?
 *
 * @param WP_Post|int|null $post Post object or ID.
 */
function ath_is_thank_you_page( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || $post->post_type !== 'page' ) {
		return false;
	}

	if ( in_array( $post->post_name, array( 'thank-you', 'thank-you-for-booking' ), true ) ) {
		return true;
	}

	return false;
}

/**
 * Create or update the Thank You page.
 */
function ath_ensure_thank_you_page() {
	$page   = get_page_by_path( 'thank-you' );
	$legacy = get_page_by_path( 'thank-you-for-booking' );

	if ( ! $page && $legacy && $legacy->post_status === 'publish' ) {
		wp_update_post(
			array(
				'ID'         => $legacy->ID,
				'post_name'  => 'thank-you',
				'post_title' => 'Thank You',
			)
		);
		delete_post_meta( $legacy->ID, '_wp_page_template' );
		$page = get_post( $legacy->ID );
	}

	if ( ! $page ) {
		$page_id = wp_insert_post(
			array(
				'post_title'   => 'Thank You',
				'post_name'    => 'thank-you',
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '',
			),
			true
		);

		if ( ! is_wp_error( $page_id ) && $page_id ) {
			$page = get_post( $page_id );
		}
	}

	if ( $page ) {
		delete_post_meta( $page->ID, '_wp_page_template' );
		if ( $page->post_title !== 'Thank You' ) {
			wp_update_post(
				array(
					'ID'         => $page->ID,
					'post_title' => 'Thank You',
				)
			);
		}
	}

	if ( $page && $legacy && (int) $legacy->ID !== (int) $page->ID && $legacy->post_status === 'publish' ) {
		wp_trash_post( (int) $legacy->ID );
	}
}

add_action( 'after_switch_theme', 'ath_ensure_thank_you_page' );

add_action(
	'init',
	function () {
		if ( get_option( 'ath_thank_you_page_setup' ) === '2' ) {
			return;
		}
		ath_ensure_thank_you_page();
		update_option( 'ath_thank_you_page_setup', '2' );
	},
	5
);

add_filter(
	'page_link',
	function ( $link, $post_id ) {
		if ( ath_is_thank_you_page( $post_id ) ) {
			return ath_thank_you_base_url();
		}
		return $link;
	},
	10,
	2
);

add_action(
	'template_redirect',
	function () {
		if ( ! is_page( 'thank-you-for-booking' ) ) {
			return;
		}

		$target = ath_thank_you_base_url();
		if ( isset( $_GET['from'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$from = sanitize_key( wp_unslash( $_GET['from'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( $from !== '' ) {
				$target = add_query_arg( 'from', $from, $target );
			}
		}

		wp_safe_redirect( $target, 301 );
		exit;
	},
	1
);

/**
 * Redirect booking forms to the thank you page after submit.
 */
add_filter(
	'fluentform/form_submission_confirmation',
	function ( $confirmation, $form_data, $form ) {
		$form_id = isset( $form->id ) ? (int) $form->id : 0;

		if ( ! ath_is_booking_form_id( $form_id ) ) {
			return $confirmation;
		}

		$source = ath_booking_form_source( $form_id );
		if ( '' === $source ) {
			$source = 'adult';
		}

		$confirmation['redirectTo'] = 'customUrl';
		$confirmation['customUrl']  = ath_thank_you_url( $source );

		return $confirmation;
	},
	10,
	3
);

/**
 * Append submission ID to thank-you URL when customer pays by bank QR.
 */
add_filter(
	'fluentform/redirect_url_value',
	function ( $redirect_url, $insert_id, $form, $form_data ) {
		$form_id = isset( $form->id ) ? (int) $form->id : 0;

		if ( strpos( $redirect_url, '/thank-you' ) === false && strpos( $redirect_url, 'thank-you' ) === false ) {
			return $redirect_url;
		}

		$method = is_array( $form_data ) && isset( $form_data['payment_method'] )
			? (string) $form_data['payment_method']
			: '';

		$offline = function_exists( 'ath_fluent_qr_offline_payment_methods' )
			? ath_fluent_qr_offline_payment_methods()
			: array( 'test' );

		if ( ! in_array( $method, $offline, true ) ) {
			return $redirect_url;
		}

		return add_query_arg( 'entry', (int) $insert_id, $redirect_url );
	},
	10,
	4
);

/**
 * Persist redirect URLs in Fluent Forms settings (runs once).
 */
function ath_sync_booking_form_redirects() {
	if ( ! class_exists( '\FluentForm\App\Models\FormMeta' ) ) {
		return;
	}

	foreach ( ath_booking_form_ids() as $form_id ) {
		$settings = \FluentForm\App\Models\FormMeta::retrieve( 'formSettings', $form_id );

		if ( ! is_array( $settings ) ) {
			continue;
		}

		$source = ath_booking_form_source( $form_id );
		$url    = ath_thank_you_url( $source );

		if (
			isset( $settings['confirmation']['redirectTo'], $settings['confirmation']['customUrl'] )
			&& 'customUrl' === $settings['confirmation']['redirectTo']
			&& $settings['confirmation']['customUrl'] === $url
		) {
			continue;
		}

		if ( ! isset( $settings['confirmation'] ) || ! is_array( $settings['confirmation'] ) ) {
			$settings['confirmation'] = array();
		}

		$settings['confirmation']['redirectTo']           = 'customUrl';
		$settings['confirmation']['customUrl']            = $url;
		$settings['confirmation']['samePageFormBehavior'] = 'hide_form';

		\FluentForm\App\Models\FormMeta::persist( $form_id, 'formSettings', $settings );
	}
}

add_action(
	'init',
	function () {
		if ( get_option( 'ath_booking_form_redirects_synced' ) === '2' ) {
			return;
		}
		ath_sync_booking_form_redirects();
		update_option( 'ath_booking_form_redirects_synced', '2' );
	},
	20
);

/**
 * Bypass full-page cache for thank-you URLs with ?entry= (must run before cache serves HTML).
 */
add_action(
	'init',
	function () {
		$entry_id = isset( $_GET['entry'] ) ? (int) $_GET['entry'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( $entry_id <= 0 ) {
			return;
		}

		$path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ) : '';
		if ( ! is_string( $path ) || strpos( $path, 'thank-you' ) === false ) {
			return;
		}

		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}
		if ( ! defined( 'DONOTCACHEDB' ) ) {
			define( 'DONOTCACHEDB', true );
		}
	},
	0
);

add_filter(
	'rocket_cache_reject_uri',
	function ( $uris ) {
		$uris[] = '/thank-you(.*)';
		return $uris;
	}
);

add_filter(
	'rocket_cache_query_strings',
	function ( $qs ) {
		$qs[] = 'entry';
		$qs[] = 'from';
		return $qs;
	}
);

/**
 * Disable full-page cache for thank-you URLs with a submission entry.
 */
add_action(
	'template_redirect',
	function () {
		if ( ! is_page( 'thank-you' ) ) {
			return;
		}

		$entry_id = isset( $_GET['entry'] ) ? (int) $_GET['entry'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( $entry_id <= 0 ) {
			return;
		}

		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}
		if ( ! defined( 'DONOTCACHEDB' ) ) {
			define( 'DONOTCACHEDB', true );
		}

		nocache_headers();
	},
	0
);

/**
 * REST endpoint — load VietQR HTML (bypasses page cache via client fetch).
 */
add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'ath/v1',
			'/thank-you-qr',
			array(
				'methods'             => 'GET',
				'permission_callback' => '__return_true',
				'callback'            => function ( $request ) {
					nocache_headers();
					$entry_id = (int) $request->get_param( 'entry' );
					$from     = sanitize_key( (string) $request->get_param( 'from' ) );
					$qr_data  = ath_thank_you_qr_data( $entry_id, $from );

					if ( ! $qr_data ) {
						$body = array( 'html' => '' );
						if ( function_exists( 'ath_thank_you_qr_diagnose' ) ) {
							$body['debug'] = ath_thank_you_qr_diagnose( $entry_id, $from );
						}
						return new WP_REST_Response( $body, 404 );
					}

					return new WP_REST_Response(
						array(
							'html'   => ath_thank_you_qr_html( $qr_data ),
							'amount' => $qr_data['amount_label'],
							'v'      => defined( 'ATH_QR_LOGIC_VERSION' ) ? ATH_QR_LOGIC_VERSION : 'legacy',
						),
						200
					);
				},
			)
		);
	}
);

/**
 * Server-side VietQR inject (when page is not cached).
 */
add_action(
	'template_redirect',
	function () {
		if ( ! is_page( 'thank-you' ) ) {
			return;
		}

		$entry_id = isset( $_GET['entry'] ) ? (int) $_GET['entry'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( $entry_id <= 0 ) {
			return;
		}

		ob_start(
			static function ( $html ) {
				if ( strpos( $html, 'thank-you-page__payment' ) !== false ) {
					return $html;
				}

				$from = isset( $_GET['from'] ) ? sanitize_key( wp_unslash( $_GET['from'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if ( function_exists( 'ath_book_tab_legacy_map' ) ) {
					$legacy = ath_book_tab_legacy_map();
					if ( isset( $legacy[ $from ] ) ) {
						$from = $legacy[ $from ];
					}
				}

				$entry_id = isset( $_GET['entry'] ) ? (int) $_GET['entry'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$qr_data  = ath_thank_you_qr_data( $entry_id, $from );

				if ( ! $qr_data ) {
					return $html;
				}

				$qr_html = ath_thank_you_qr_html( $qr_data );
				$needle  = '<div class="thank-you-page__inner">';

				if ( $qr_html && strpos( $html, $needle ) !== false ) {
					$html = str_replace( $needle, $needle . $qr_html, $html );
				}

				return $html;
			}
		);
	},
	5
);
