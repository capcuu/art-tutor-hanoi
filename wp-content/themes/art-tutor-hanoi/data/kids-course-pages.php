<?php
/**
 * Individual kids course pages — content from arttutorhanoi.com posts.
 */
$uploads = 'https://arttutorhanoi.com/wp-content/uploads/2023/08';

return array(
	'art-theory-for-kids' => array(
		'slug'           => 'art-theory-for-kids',
		'title'          => 'Contemporary art for Kids',
		'pathway_id'     => 'contemporary-art',
		'pathway_title'  => 'Contemporary Art',
		'hub_url'        => 'kids-courses.php',
		'hub_label'      => 'Kids courses',
		'sessions'       => '12 sessions',
		'image'          => array(
			'url' => $uploads . '/14.png',
			'alt' => 'Contemporary art for kids at Art Tutor Hanoi',
		),
		'blocks'         => array(
			array(
				'heading' => 'In this course, you will get to learn about',
				'items'   => array(
					'Impressionism',
					'Post-Impressionism / Pointilism',
					'Fauvism',
					'Expressionism',
					'Cubism',
					'Surrealism',
					'Pop Art',
					'Action Painting',
					'Abstract Shapes and Colors: Kandinsky',
					'Graffiti Art',
					'Create and present an artwork inspired by styles / movements that you like',
				),
			),
			array(
				'heading' => 'You will also',
				'items'   => array(
					'Explore the life and influence of some key artists in history, such as Vincent van Gogh, René Magritte …',
					'Practice painting styles using different mediums, such as watercolor, acrylic',
				),
			),
			array(
				'heading' => 'After the course, you will',
				'items'   => array(
					'Know about the origins, influences and impacts of various art movements',
					'Be able to create a painting inspired by different artistic styles',
				),
			),
		),
		'gallery'        => array(
			array( 'url' => $uploads . '/impressionist-landscapes-1.png', 'caption' => 'Impressionist landscapes' ),
			array( 'url' => $uploads . '/kandinsky.png', 'caption' => 'Kandinsky' ),
			array( 'url' => $uploads . '/47fed7d6eeb9491fbee24404b7c0e753.jpg' ),
		),
	),
);
