<?php
/**
 * SEO — Rank Math integration, default meta seed, robots, LocalBusiness schema.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Page slugs that should never be indexed.
 *
 * @return string[]
 */
function ath_seo_noindex_page_slugs() {
	return array(
		'thank-you',
		'seo',
	);
}

/**
 * Whether the current request should be noindex.
 */
function ath_seo_should_noindex() {
	if ( is_page( ath_seo_noindex_page_slugs() ) ) {
		return true;
	}

	if ( is_page( 'book' ) && isset( $_GET['tab'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return false;
	}

	return false;
}

add_filter(
	'rank_math/frontend/robots',
	function ( $robots ) {
		if ( ! ath_seo_should_noindex() ) {
			return $robots;
		}

		$robots['index']  = 'noindex';
		$robots['follow'] = 'nofollow';

		return $robots;
	}
);

/**
 * Local business facts for schema (Rank Math Local SEO should mirror these).
 *
 * @return array<string, string>
 */
function ath_local_business_facts() {
	return array(
		'name'        => 'Art Tutor Hanoi',
		'url'         => home_url( '/' ),
		'email'       => 'contact@arttutorhanoi.com',
		'telephone'   => '+84-988-288-302',
		'address'     => 'Tay Ho, Hanoi, Vietnam',
		'description' => 'English-speaking fine art studio offering adult and kids art courses, life drawing, and creative workshops in Hanoi.',
	);
}

/**
 * Supplement Rank Math JSON-LD with LocalBusiness when missing.
 *
 * @param array<string, mixed> $data   Existing schema graph.
 * @param object               $jsonld Rank Math JSON-LD instance.
 * @return array<string, mixed>
 */
function ath_rank_math_local_business_schema( $data, $jsonld ) {
	if ( ! is_front_page() ) {
		return $data;
	}

	$facts = ath_local_business_facts();

	$data['LocalBusiness'] = array(
		'@type'       => array( 'LocalBusiness', 'ArtGallery', 'EducationalOrganization' ),
		'name'        => $facts['name'],
		'url'         => $facts['url'],
		'email'       => $facts['email'],
		'telephone'   => $facts['telephone'],
		'description' => $facts['description'],
		'address'     => array(
			'@type'           => 'PostalAddress',
			'addressLocality' => 'Hanoi',
			'addressRegion'   => 'Hanoi',
			'addressCountry'  => 'VN',
			'streetAddress'   => $facts['address'],
		),
		'areaServed'  => array(
			'@type' => 'City',
			'name'  => 'Hanoi',
		),
	);

	return $data;
}

add_filter( 'rank_math/json_ld', 'ath_rank_math_local_business_schema', 99, 2 );

/**
 * Recommended Rank Math title / description per page slug (Sprint 1 commercial pages).
 *
 * @return array<string, array{title: string, description: string, focus_keyword?: string}>
 */
function ath_rank_math_page_defaults() {
	return array(
		'workshops'            => array(
			'title'          => 'Art Workshops in Hanoi | 1-Day Creative Classes',
			'description'    => 'Join creative art workshops in Hanoi: trial class, life drawing, silk painting, artist residency. English-friendly studio in Tay Ho.',
			'focus_keyword'  => 'art workshops hanoi',
		),
		'courses'              => array(
			'title'          => 'Adult Art Courses in Hanoi | Fine Art Programs',
			'description'    => 'Structured fine art courses for adults in Hanoi — drawing, painting, portfolio prep. English-speaking tutors, flexible scheduling in Tay Ho.',
			'focus_keyword'  => 'art classes hanoi',
		),
		'kids-courses'         => array(
			'title'          => 'Kids Art Classes Hanoi | English Art Tutor Tay Ho',
			'description'    => 'Creative art classes for children in Tay Ho, Hanoi. English-friendly instruction, portfolio building, after-school and weekend options.',
			'focus_keyword'  => 'kids art classes hanoi',
		),
		'pricing'              => array(
			'title'          => 'Art Class Pricing in Hanoi | Art Tutor Hanoi',
			'description'    => 'Transparent pricing for adult and kids art classes in Hanoi. Trial sessions, course packages, and workshop fees at our Tay Ho studio.',
			'focus_keyword'  => 'art class pricing hanoi',
		),
		'book'                 => array(
			'title'          => 'Book a Trial Art Class | Art Tutor Hanoi',
			'description'    => 'Reserve your trial art session online. Choose adult or kids program, pick a date, and confirm via VietQR. Studio in Tay Ho, Hanoi.',
			'focus_keyword'  => 'book art class hanoi',
		),
		'about'                => array(
			'title'          => 'About Art Tutor Hanoi | Meet Our Artists',
			'description'    => 'Meet the artists and educators at Art Tutor Hanoi — an English-speaking fine art studio in Tay Ho offering courses, workshops, and community.',
			'focus_keyword'  => 'art tutor hanoi',
		),
		'weekly-calendar'      => array(
			'title'          => 'Weekly Art Class Calendar | Art Tutor Hanoi',
			'description'    => 'This week\'s art classes, workshops, and events at Art Tutor Hanoi. Schedule for adults, kids, and special sessions in Tay Ho.',
			'focus_keyword'  => 'art class calendar hanoi',
		),
		'faq'                  => array(
			'title'          => 'FAQ | Art Classes & Workshops in Hanoi',
			'description'    => 'Answers about booking, pricing, materials, location (Tay Ho), trial classes, life drawing, and kids programs at Art Tutor Hanoi.',
			'focus_keyword'  => 'art classes hanoi faq',
		),
		'hanoi-art-supply-map' => array(
			'title'          => 'Hanoi Art Supply Stores Map | Art Materials Guide',
			'description'    => 'Map and guide to art supply shops in Hanoi — pencils, paints, canvas, silk painting materials. Curated list for artists and students.',
			'focus_keyword'  => 'art supplies hanoi',
		),
		'free-art-feedback'    => array(
			'title'          => 'Free Online Art Feedback | Rate My Art',
			'description'    => 'Submit your artwork for free constructive feedback from Art Tutor Hanoi instructors. Improve your drawing and painting skills online.',
			'focus_keyword'  => 'free art feedback',
		),
		'students-artworks'    => array(
			'title'          => 'Student Artworks Portfolio | Art Tutor Hanoi',
			'description'    => 'Gallery of student drawings, paintings, and portfolio work from Art Tutor Hanoi courses and workshops in Hanoi.',
			'focus_keyword'  => 'student art portfolio hanoi',
		),
		'join-our-art-community' => array(
			'title'          => 'Join Our Art Community | Art Tutor Hanoi',
			'description'    => 'Connect with artists and learners at Art Tutor Hanoi. Community events, exhibitions, and creative meetups in Tay Ho, Hanoi.',
			'focus_keyword'  => 'art community hanoi',
		),
	);
}

/**
 * Homepage Rank Math defaults (uses page_on_front, not slug).
 *
 * @return array{title: string, description: string, focus_keyword: string}
 */
function ath_rank_math_home_defaults() {
	return array(
		'title'          => 'Art Tutor Hanoi | English Art Classes & Workshops',
		'description'    => 'Book a trial art class in Tay Ho, Hanoi. English-speaking instructors, adult & kids courses, life drawing, silk painting workshops.',
		'focus_keyword'  => 'art tutor hanoi',
	);
}

/**
 * Apply Rank Math post meta for core pages (skips pages that already have a custom title).
 *
 * @param bool $overwrite Replace existing Rank Math meta.
 * @return array{updated: string[], skipped: string[], missing: string[]}
 */
function ath_seed_rank_math_meta( $overwrite = false ) {
	$results = array(
		'updated'  => array(),
		'skipped'  => array(),
		'missing'  => array(),
	);

	$front_id = (int) get_option( 'page_on_front' );
	if ( $front_id ) {
		$key = 'home (ID ' . $front_id . ')';
		if ( ath_apply_rank_math_meta( $front_id, ath_rank_math_home_defaults(), $overwrite ) ) {
			$results['updated'][] = $key;
		} else {
			$results['skipped'][] = $key;
		}
	}

	foreach ( ath_rank_math_page_defaults() as $slug => $meta ) {
		$page = get_page_by_path( $slug );
		if ( ! $page ) {
			$results['missing'][] = $slug;
			continue;
		}

		if ( ath_apply_rank_math_meta( (int) $page->ID, $meta, $overwrite ) ) {
			$results['updated'][] = $slug;
		} else {
			$results['skipped'][] = $slug;
		}
	}

	return $results;
}

/**
 * Write Rank Math meta on a single post.
 *
 * @param int                  $post_id   Post ID.
 * @param array<string, mixed> $meta      title, description, focus_keyword.
 * @param bool                 $overwrite Replace when already set.
 */
function ath_apply_rank_math_meta( $post_id, array $meta, $overwrite = false ) {
	$post_id = (int) $post_id;
	if ( ! $post_id ) {
		return false;
	}

	$existing = (string) get_post_meta( $post_id, 'rank_math_title', true );
	if ( ! $overwrite && $existing !== '' ) {
		return false;
	}

	update_post_meta( $post_id, 'rank_math_title', (string) ( $meta['title'] ?? '' ) );
	update_post_meta( $post_id, 'rank_math_description', (string) ( $meta['description'] ?? '' ) );

	if ( ! empty( $meta['focus_keyword'] ) ) {
		update_post_meta( $post_id, 'rank_math_focus_keyword', (string) $meta['focus_keyword'] );
	}

	return true;
}
