<?php
/**
 * Structured data — Course, Event, ItemList (Rank Math JSON-LD graph).
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Organization provider node (studio).
 *
 * @return array<string, mixed>
 */
function ath_schema_organization() {
	$facts = ath_local_business_facts();

	return array(
		'@type' => 'Organization',
		'@id'   => home_url( '/#organization' ),
		'name'  => $facts['name'],
		'url'   => $facts['url'],
		'email' => $facts['email'],
		'telephone' => $facts['telephone'],
	);
}

/**
 * Postal address for course / event location.
 *
 * @return array<string, mixed>
 */
function ath_schema_postal_address() {
	$facts = ath_local_business_facts();

	return array(
		'@type'           => 'PostalAddress',
		'streetAddress'   => '82 Ngách 264/15 Ngõ 374 Đường Âu Cơ',
		'addressLocality' => 'Tay Ho',
		'addressRegion'   => 'Hanoi',
		'addressCountry'  => 'VN',
	);
}

/**
 * Place node for onsite classes.
 *
 * @return array<string, mixed>
 */
function ath_schema_place() {
	$facts = ath_local_business_facts();

	return array(
		'@type'   => 'Place',
		'name'    => $facts['name'],
		'address' => ath_schema_postal_address(),
	);
}

/**
 * Parse USD price from display strings ($40, From $28).
 *
 * @param string $price_raw Display price.
 * @param string $url       Booking or page URL.
 * @return array<string, mixed>|null
 */
function ath_schema_usd_offer( $price_raw, $url ) {
	if ( ! is_string( $price_raw ) || $price_raw === '' ) {
		return null;
	}

	if ( ! preg_match( '/\$([\d]+(?:\.\d+)?)/', $price_raw, $match ) ) {
		return null;
	}

	return array(
		'@type'         => 'Offer',
		'url'           => $url,
		'price'         => $match[1],
		'priceCurrency' => 'USD',
		'availability'  => 'https://schema.org/InStock',
	);
}

/**
 * Plain-text description from a course / experience / kids row.
 *
 * @param array<string, mixed> $row Data row.
 */
function ath_schema_row_description( array $row ) {
	if ( ! empty( $row['intro'] ) ) {
		return wp_strip_all_tags( (string) $row['intro'] );
	}

	if ( ! empty( $row['desc'] ) ) {
		return wp_strip_all_tags( (string) $row['desc'] );
	}

	$parts = array();
	if ( ! empty( $row['sessions'] ) ) {
		$parts[] = (string) $row['sessions'];
	}

	foreach ( (array) ( $row['blocks'] ?? array() ) as $block ) {
		if ( ! empty( $block['text'] ) ) {
			$parts[] = wp_strip_all_tags( (string) $block['text'] );
			break;
		}
		if ( ! empty( $block['items'][0] ) ) {
			$parts[] = wp_strip_all_tags( (string) $block['items'][0] );
			break;
		}
	}

	$description = trim( implode( '. ', array_filter( $parts ) ) );
	if ( $description !== '' ) {
		return $description;
	}

	return wp_strip_all_tags( (string) ( $row['title'] ?? '' ) );
}

/**
 * Image URL from row image array.
 *
 * @param array<string, mixed> $row Data row.
 */
function ath_schema_row_image( array $row ) {
	$url = (string) ( $row['image']['url'] ?? '' );
	if ( $url === '' ) {
		return '';
	}

	return esc_url_raw( $url );
}

/**
 * Build a Course schema node.
 *
 * @param string               $name        Course name.
 * @param string               $description Plain description.
 * @param string               $url         Canonical URL.
 * @param array<string, mixed> $extras      Optional keys: image, offer_price, audience.
 * @return array<string, mixed>
 */
function ath_schema_course_node( $name, $description, $url, array $extras = array() ) {
	$course = array(
		'@type'            => 'Course',
		'name'             => $name,
		'description'      => $description,
		'url'              => $url,
		'provider'         => ath_schema_organization(),
		'inLanguage'       => 'en',
		'courseMode'       => 'https://schema.org/OfflineAttendance',
		'availableLanguage'=> array( 'English', 'Vietnamese' ),
		'locationCreated'  => ath_schema_place(),
	);

	if ( ! empty( $extras['image'] ) ) {
		$course['image'] = $extras['image'];
	}

	if ( ! empty( $extras['audience'] ) ) {
		$course['audience'] = array(
			'@type'        => 'EducationalAudience',
			'audienceType' => (string) $extras['audience'],
		);
	}

	if ( ! empty( $extras['offer_price'] ) ) {
		$offer = ath_schema_usd_offer( (string) $extras['offer_price'], $url );
		if ( $offer ) {
			$course['offers'] = $offer;
		}
	}

	return $course;
}

/**
 * Map English weekday name to schema.org day URL.
 *
 * @param string $day Weekday name.
 */
function ath_schema_day_url( $day ) {
	$map = array(
		'monday'    => 'https://schema.org/Monday',
		'tuesday'   => 'https://schema.org/Tuesday',
		'wednesday' => 'https://schema.org/Wednesday',
		'thursday'  => 'https://schema.org/Thursday',
		'friday'    => 'https://schema.org/Friday',
		'saturday'  => 'https://schema.org/Saturday',
		'sunday'    => 'https://schema.org/Sunday',
	);

	$key = strtolower( trim( $day ) );

	return $map[ $key ] ?? '';
}

/**
 * Parse simple weekly schedule from experience meta (e.g. life drawing).
 *
 * @param array<int, array<string, string>> $meta_rows Meta label/value rows.
 * @return array<string, mixed>|null
 */
function ath_schema_weekly_schedule( array $meta_rows ) {
	$schedule_text = '';

	foreach ( $meta_rows as $row ) {
		if ( strtolower( (string) ( $row['label'] ?? '' ) ) === 'schedule' ) {
			$schedule_text = (string) ( $row['value'] ?? '' );
			break;
		}
	}

	if ( $schedule_text === '' ) {
		return null;
	}

	if ( ! preg_match( '/\b(Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday)\b/i', $schedule_text, $day_match ) ) {
		return null;
	}

	$day_url = ath_schema_day_url( $day_match[1] );
	if ( $day_url === '' ) {
		return null;
	}

	if ( ! preg_match( '/(\d{1,2}:\d{2}\s*(?:am|pm)?)\s*[–\-—]\s*(\d{1,2}:\d{2}\s*(?:am|pm)?)/i', $schedule_text, $time_match ) ) {
		return null;
	}

	$start = trim( $time_match[1] );
	$end   = trim( $time_match[2] );

	return array(
		'@type'           => 'Schedule',
		'repeatFrequency' => 'P1W',
		'byDay'           => $day_url,
		'startTime'       => $start,
		'endTime'         => $end,
	);
}

/**
 * Build Event schema when a weekly schedule is known; otherwise null.
 *
 * @param array<string, mixed> $experience Experience row.
 * @param string               $url        Page URL.
 * @return array<string, mixed>|null
 */
function ath_schema_event_node( array $experience, $url ) {
	$schedule = ath_schema_weekly_schedule( (array) ( $experience['meta'] ?? array() ) );
	if ( ! $schedule ) {
		return null;
	}

	$name        = (string) ( $experience['title'] ?? '' );
	$description = ath_schema_row_description( $experience );

	$event = array(
		'@type'               => 'Event',
		'name'                => $name,
		'description'         => $description,
		'url'                 => $url,
		'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
		'eventStatus'         => 'https://schema.org/EventScheduled',
		'location'            => ath_schema_place(),
		'organizer'           => ath_schema_organization(),
		'eventSchedule'       => $schedule,
	);

	$image = ath_schema_row_image( $experience );
	if ( $image !== '' ) {
		$event['image'] = $image;
	}

	$offer = ath_schema_usd_offer( (string) ( $experience['price'] ?? '' ), $url );
	if ( $offer ) {
		$event['offers'] = $offer;
	}

	return $event;
}

/**
 * ItemList schema for hub pages.
 *
 * @param string                              $name  List title.
 * @param array<int, array{name: string, url: string}> $items List entries.
 * @return array<string, mixed>|null
 */
function ath_schema_item_list( $name, array $items ) {
	if ( empty( $items ) ) {
		return null;
	}

	$elements = array();
	$position = 1;

	foreach ( $items as $item ) {
		if ( empty( $item['url'] ) || empty( $item['name'] ) ) {
			continue;
		}

		$elements[] = array(
			'@type'    => 'ListItem',
			'position' => $position,
			'url'      => $item['url'],
			'name'     => $item['name'],
		);
		++$position;
	}

	if ( empty( $elements ) ) {
		return null;
	}

	return array(
		'@type'           => 'ItemList',
		'name'            => $name,
		'itemListElement' => $elements,
	);
}

/**
 * ItemList for adult courses hub.
 *
 * @return array<string, mixed>|null
 */
function ath_schema_courses_hub_list() {
	$items = array();

	foreach ( ath_adult_course_slugs() as $slug ) {
		$course = v2_course_by_slug( $slug );
		if ( ! $course ) {
			continue;
		}

		$items[] = array(
			'name' => (string) ( $course['title'] ?? $slug ),
			'url'  => ath_course_url( $slug ),
		);
	}

	return ath_schema_item_list( 'Adult Art Courses in Hanoi', $items );
}

/**
 * ItemList for workshops hub.
 *
 * @return array<string, mixed>|null
 */
function ath_schema_workshops_hub_list() {
	$items = array();

	foreach ( ath_workshop_slugs() as $slug ) {
		$experience = v2_experience_by_slug( $slug );
		if ( ! $experience ) {
			continue;
		}

		$items[] = array(
			'name' => (string) ( $experience['title'] ?? $slug ),
			'url'  => ath_experience_url( $slug ),
		);
	}

	return ath_schema_item_list( 'Art Workshops in Hanoi', $items );
}

/**
 * ItemList for kids courses hub.
 *
 * @return array<string, mixed>|null
 */
function ath_schema_kids_courses_hub_list() {
	$items  = array();
	$rows   = require ATH_THEME_DIR . '/data/kids-course-pages.php';

	foreach ( array_keys( $rows ) as $slug ) {
		$course = v2_kids_course_by_slug( $slug );
		if ( ! $course ) {
			continue;
		}

		$items[] = array(
			'name' => (string) ( $course['title'] ?? $slug ),
			'url'  => ath_kids_course_url( $slug ),
		);
	}

	return ath_schema_item_list( 'Kids Art Classes in Hanoi', $items );
}

/**
 * Course schema for current adult course detail page.
 *
 * @return array<string, mixed>|null
 */
function ath_schema_current_adult_course() {
	if ( ! ath_is_adult_course_detail_page() ) {
		return null;
	}

	$post = get_queried_object();
	if ( ! $post instanceof WP_Post ) {
		return null;
	}

	$course = v2_course_by_slug( $post->post_name );
	if ( ! $course ) {
		return null;
	}

	$url = get_permalink( $post );

	return ath_schema_course_node(
		(string) ( $course['title'] ?? $post->post_title ),
		ath_schema_row_description( $course ),
		$url,
		array(
			'image'    => ath_schema_row_image( $course ),
			'audience' => 'Adults',
		)
	);
}

/**
 * Course or Event schema for current workshop detail page.
 *
 * @return array<string, mixed>|null
 */
function ath_schema_current_workshop() {
	if ( ! ath_is_workshop_page() ) {
		return null;
	}

	$post = get_queried_object();
	if ( ! $post instanceof WP_Post ) {
		return null;
	}

	$experience = v2_experience_by_slug( $post->post_name );
	if ( ! $experience ) {
		return null;
	}

	$url = get_permalink( $post );

	$event = ath_schema_event_node( $experience, $url );
	if ( $event ) {
		return $event;
	}

	return ath_schema_course_node(
		(string) ( $experience['title'] ?? $post->post_title ),
		ath_schema_row_description( $experience ),
		$url,
		array(
			'image'       => ath_schema_row_image( $experience ),
			'audience'    => 'Adults and travelers',
			'offer_price' => (string) ( $experience['price'] ?? '' ),
		)
	);
}

/**
 * Course schema for kids course detail route.
 *
 * @return array<string, mixed>|null
 */
function ath_schema_current_kids_course() {
	$slug = sanitize_title( (string) get_query_var( 'ath_kids_course' ) );
	if ( $slug === '' ) {
		return null;
	}

	$course = v2_kids_course_by_slug( $slug );
	if ( ! $course ) {
		return null;
	}

	$url = ath_kids_course_url( $slug );

	return ath_schema_course_node(
		(string) ( $course['title'] ?? $slug ),
		ath_schema_row_description( $course ),
		$url,
		array(
			'image'    => ath_schema_row_image( $course ),
			'audience' => 'Children',
		)
	);
}

/**
 * Inject commercial schema into Rank Math graph.
 *
 * @param array<string, mixed> $data   Existing schema graph.
 * @param object               $jsonld Rank Math JSON-LD instance.
 * @return array<string, mixed>
 */
function ath_rank_math_commercial_schema( $data, $jsonld ) {
	unset( $jsonld );

	if ( is_page( 'courses' ) ) {
		$list = ath_schema_courses_hub_list();
		if ( $list ) {
			$data['ItemList'] = $list;
		}
	}

	if ( ath_is_workshops_hub_page() ) {
		$list = ath_schema_workshops_hub_list();
		if ( $list ) {
			$data['ItemList'] = $list;
		}
	}

	if ( is_page( 'kids-courses' ) ) {
		$list = ath_schema_kids_courses_hub_list();
		if ( $list ) {
			$data['ItemList'] = $list;
		}
	}

	$course = ath_schema_current_adult_course();
	if ( $course ) {
		$data['Course'] = $course;
	}

	$workshop = ath_schema_current_workshop();
	if ( $workshop ) {
		$type = (string) ( $workshop['@type'] ?? 'Course' );
		if ( $type === 'Event' ) {
			$data['Event'] = $workshop;
		} else {
			$data['Course'] = $workshop;
		}
	}

	$kids = ath_schema_current_kids_course();
	if ( $kids ) {
		$data['Course'] = $kids;
	}

	return $data;
}

add_filter( 'rank_math/json_ld', 'ath_rank_math_commercial_schema', 97, 2 );
