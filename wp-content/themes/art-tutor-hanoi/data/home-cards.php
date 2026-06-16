<?php
/**
 * Homepage cards — 4 promo tiles below hero.
 *
 * link_type: experience | page | book | url
 * link: slug (experience/page/book tab key) or full URL when link_type=url
 * button: optional per-card button label (falls back to shortcode default)
 */
return array(
	array(
		'title'     => 'Workshops',
		'desc'      => 'Perfect for travelers. Discover local culture and create your own unique Hanoi artwork',
		'button'    => 'Book a Workshop',
		'link'      => 'workshops',
		'link_type' => 'page',
	),
	array(
		'title'     => 'Courses',
		'desc'      => 'Weekly classes for adults and kids to build long-term art skills',
		'button'    => 'View Courses',
		'link'      => 'courses',
		'link_type' => 'page',
	),
	array(
		'title'     => 'Trial Class',
		'desc'      => 'Try a single taster session to test our teaching style before joining long-term',
		'button'    => 'About the Trial Session',
		'link'      => 'trial-art-class',
		'link_type' => 'experience',
	),
	array(
		'title'     => 'Artist Residency',
		'desc'      => 'Dedicated studio space and mentorship for local and international professional artists',
		'button'    => 'Apply now',
		'link'      => 'artist-residency',
		'link_type' => 'experience',
	),
);
