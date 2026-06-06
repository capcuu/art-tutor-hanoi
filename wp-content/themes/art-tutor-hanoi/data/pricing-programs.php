<?php
/**
 * Programs & pricing cards — content from arttutorhanoi.com/programs/
 */
return array(
	'intro' => array(
		'Not sure where to start?',
		'Here are a few ways to begin your art journey at our studio.',
		'At Art Tutor Hanoi, we focus on building strong foundations — whether you are learning, practicing, or simply making time for your own art.',
	),
	'faq_url'   => 'https://arttutorhanoi.com/faq/',
	'faq_label' => 'FAQ',
	'programs'  => array(
		array(
			'id'          => 'trial',
			'title'       => 'Trial Art Class',
			'tag'         => 'For beginners',
			'price'       => '$40',
			'description' => 'Intro to drawing and painting.',
			'cta'         => array(
				'label' => 'Start Trial',
				'url'   => 'trial',
			),
		),
		array(
			'id'          => 'six-session',
			'title'       => '6-session Course',
			'tag'         => 'Specific skill',
			'price'       => '$115',
			'description' => 'Focus on one core technique.',
			'features'    => array( 'Pencil', 'Charcoal', 'Oil', 'Watercolor' ),
			'cta'         => array(
				'label' => 'Start Course',
				'url'   => 'adults',
			),
		),
		array(
			'id'          => 'twelve-session',
			'title'       => '12-session Program',
			'tag'         => 'Long-term study',
			'price'       => '$183',
			'description' => 'Develop your skills over time.',
			'badge'       => 'Most Popular',
			'featured'    => true,
			'cta'         => array(
				'label' => 'Join Program',
				'url'   => 'adults',
			),
		),
		array(
			'id'          => 'kids',
			'title'       => 'Kids Art Program',
			'tag'         => 'Age 5–12',
			'price'       => '$183',
			'description' => 'Explore creativity through art styles.',
			'features'    => array( 'Impressionism', 'Expressionism', 'Surrealism', 'Pop Art' ),
			'cta'         => array(
				'label' => 'Book for Kids',
				'url'   => 'kids',
			),
		),
	),
	'solo_note' => array(
		'heading' => 'Not looking for a class?',
		'text'    => 'You can also work independently at our studio.',
	),
	'residency' => array(
		'title'       => 'Artist Residency',
		'tag'         => 'Independent practice',
		'price'       => '$135 / week',
		'description' => 'A quiet studio for independent art practice.',
		'cta'         => array(
			'label' => 'Work Independently',
			'url'   => 'residency',
		),
	),
);
