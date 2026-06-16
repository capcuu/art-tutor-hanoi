<?php
/**
 * Pricing page — accordion sections and rates.
 */
return array(
	'intro' => array(
		'Clear, simple rates for every way to study at our Hanoi studio.',
		'All prices are in USD. Long-term course packages are valid for 12 months.',
	),
	'faq_url'   => 'faq',
	'faq_label' => 'FAQ',
	'sections'  => array(
		array(
			'id'          => 'trial-art-class',
			'num'         => '01',
			'title'       => 'Trial Art Class',
			'description' => 'A 2-hour introductory session — try our teaching style before committing to a full course.',
			'price'       => '$40',
			'book_tab'    => 'trial',
		),
		array(
			'id'          => 'workshops',
			'num'         => '02',
			'title'       => 'Workshops',
			'description' => 'One-off studio sessions with guided instruction — ideal for trying a new medium or skill.',
			'from_price'  => 'from $40',
			'items'       => array(
				array(
					'title' => 'Figure Drawing',
					'price' => '$40',
				),
				array(
					'title' => 'Silk Painting',
					'price' => '$55',
				),
			),
			'book_tab'    => 'workshops',
		),
		array(
			'id'          => 'adult-classes',
			'num'         => '03',
			'title'       => 'Adult Classes',
			'description' => 'Structured fine-art programs — pencil, charcoal, colour, and oil painting for adults.',
			'from_price'  => 'from $115',
			'items'       => array(
				array(
					'title' => '6-session class',
					'price' => '$115',
				),
				array(
					'title' => '12-session class',
					'price' => '$183',
				),
			),
			'book_tab'    => 'adults',
		),
		array(
			'id'          => 'kids-classes',
			'num'         => '04',
			'title'       => 'Kids Classes',
			'description' => 'Creative art programs for children aged 5–12, exploring drawing and painting in a supportive setting.',
			'price'       => '$183',
			'items'       => array(
				array(
					'title' => '12-session class',
					'price' => '$183',
				),
			),
			'book_tab'    => 'kids',
		),
		array(
			'id'          => 'artist-residency',
			'num'         => '05',
			'title'       => 'Artist Residency',
			'description' => 'Dedicated studio time for independent practice, with optional guidance from our tutors.',
			'from_price'  => 'from $135',
			'items'       => array(
				array(
					'title' => '1 week',
					'price' => '$135',
				),
				array(
					'title' => '2 weeks',
					'price' => '$250',
				),
				array(
					'title' => '4 weeks',
					'price' => '$480',
				),
			),
			'book_tab'    => 'residency',
		),
	),
);
