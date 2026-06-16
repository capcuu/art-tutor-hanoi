<?php
/**
 * Workshops hub page — /workshops/ intro and links to detail pages.
 */
return array(
	'hero' => array(
		'title'    => 'Workshops',
		'subtitle' => 'Creative art workshops in Hanoi for travelers and local artists — most sessions run 2–2.5 hours in our Tay Ho studio.',
	),
	'intro' => array(
		'Join a one-day art workshop in Hanoi: trial classes, life drawing with a model, silk painting, or a flexible artist residency. Sessions include materials, English-friendly instruction, and a calm studio atmosphere near West Lake.',
		'Looking for weekly classes instead? Explore our adult art courses or book a trial session before you commit.',
	),
	'schedule' => array(
		'heading' => 'Schedule',
		'items'   => array(
			'Most workshops: 2–2.5 hours per session',
			'Nude model drawing: Saturdays only, 13:30–16:00 (weekly)',
			'Silk painting & residency: see each workshop page for dates',
		),
	),
	'workshops' => array(
		array(
			'slug'     => 'life-drawing',
			'title'    => 'Nude Model Drawing',
			'desc'     => 'Academic life drawing with a professional model — anatomy, proportion, and observation in a small-group studio.',
			'duration' => '2.5 hours',
			'schedule' => 'Saturdays, 13:30–16:00',
		),
		array(
			'slug'     => 'silk-painting',
			'title'    => 'Silk Painting',
			'desc'     => 'Advanced Vietnamese silk painting with an instructor from the Vietnam University of Fine Arts.',
			'duration' => '12 sessions · 2 hours each',
			'schedule' => 'Tue, Thu & Fri — see workshop page',
		),
		array(
			'slug'     => 'artist-residency',
			'title'    => 'Artist Residency',
			'desc'     => 'Independent studio access for 1–4 weeks — work at your own pace with optional guidance.',
			'duration' => 'Flexible weekly access',
			'schedule' => 'Book your preferred dates',
		),
	),
	'cta' => array(
		'text'   => 'Ready to join a workshop? Complete your booking online.',
		'button' => 'Book a Workshop',
		'book'   => 'adult',
	),
);
