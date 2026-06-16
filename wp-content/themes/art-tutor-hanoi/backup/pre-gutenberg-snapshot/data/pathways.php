<?php
/**
 * Learning pathways — groups fine-art courses by direction, not curriculum level.
 */
return array(
	array(
		'id'          => 'drawing-foundations',
		'num'         => '01',
		'title'       => 'Drawing Foundations',
		'description' => 'Build observational skills with pencil, line, and composition — the essential starting point for any artist.',
		'image'       => array(
			'url' => 'https://arttutorhanoi.com/wp-content/uploads/2023/07/1.png',
			'alt' => 'Pencil drawing foundations at Art Tutor Hanoi',
		),
		'courses'     => array(
			array(
				'title' => 'Pencil Basics 1',
				'slug'  => 'pencil-drawing',
			),
			array(
				'title' => 'Pencil Basics 2',
				'slug'  => 'chi-co-ban-2',
			),
			array(
				'title' => 'Sketch',
				'slug'  => 'sketch',
			),
			array(
				'title' => 'Composition Basics',
				'slug'  => 'composition',
			),
		),
	),
	array(
		'id'          => 'charcoal-drawing',
		'num'         => '02',
		'title'       => 'Charcoal Drawing',
		'description' => 'Explore rich tonal drawing with charcoal — from bold contrasts to atmospheric shading and advanced techniques.',
		'image'       => array(
			'url' => 'https://arttutorhanoi.com/wp-content/uploads/2023/07/7.png',
			'alt' => 'Charcoal drawing class at Art Tutor Hanoi',
		),
		'courses'     => array(
			array(
				'title' => 'Charcoal Basics 1',
				'slug'  => 'charcoal-basics-1',
			),
			array(
				'title' => 'Charcoal Basics 2',
				'slug'  => 'charcoal-basics-2',
			),
			array(
				'title' => 'Charcoal Advanced',
				'slug'  => 'charcoal-advanced',
			),
		),
	),
	array(
		'id'          => 'colour-painting',
		'num'         => '03',
		'title'       => 'Colour & Painting',
		'description' => 'Move from colour theory into oil painting with guided studio practice and composition in colour.',
		'image'       => array(
			'url' => 'https://arttutorhanoi.com/wp-content/uploads/2023/07/8.png',
			'alt' => 'Colour and oil painting at Art Tutor Hanoi',
		),
		'courses'     => array(
			array(
				'title' => 'Colour Basics',
				'slug'  => 'color-theory',
			),
			array(
				'title' => 'Colour Composition',
				'slug'  => 'color-composition',
			),
			array(
				'title' => 'Oil Painting Basics 1',
				'slug'  => 'oil-painting-basics-1',
			),
			array(
				'title' => 'Oil Painting Basics 2',
				'slug'  => 'oil-painting-basics-2',
			),
		),
	),
	array(
		'id'          => 'life-drawing',
		'num'         => '04',
		'title'       => 'Life Drawing',
		'description' => 'Draw the human figure from life with professional models in an academic studio setting.',
		'image'       => array(
			'url' => 'https://arttutorhanoi.com/wp-content/uploads/2025/09/IMG_0175-768x576.jpg',
			'alt' => 'Life drawing session with model at Art Tutor Hanoi',
		),
		'detail'      => array(
			'Study proportion, anatomy, and gesture by drawing from a live model — the classical approach to figure drawing in an academic studio.',
			'Sessions include short warm-up poses and longer studies, with personal feedback from experienced instructors. All materials provided.',
			'Open to all levels. Nude model sessions are held regularly — check our calendar for upcoming dates or contact us to book.',
		),
		'courses'     => array(
			array(
				'title'            => 'Book Life Drawing',
				'experience_slug'  => 'life-drawing',
			),
		),
	),
);
