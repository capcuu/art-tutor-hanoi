<?php
/**
 * Individual fine-art course pages — content from arttutorhanoi.com posts.
 */
$uploads = 'https://arttutorhanoi.com/wp-content/uploads/2023/07';

return array(
	'pencil-drawing' => array(
		'slug'           => 'pencil-drawing',
		'title'          => 'Pencil Basics 1',
		'pathway_id'     => 'drawing-foundations',
		'pathway_title'  => 'Drawing Foundations',
		'sessions'       => '6 sessions',
		'image'          => array(
			'url' => $uploads . '/1.png',
			'alt' => 'Pencil Basics 1 at Art Tutor Hanoi',
		),
		'intro'          => 'This course helps you master the basics before moving on to harder topics. You will learn how to draw simple and complex objects with confidence, whether they are still life or human figures.',
		'blocks'         => array(
			array(
				'heading' => 'In this course, you will',
				'text'    => 'Practice drawing cubes, spheres and their surrounding space.',
			),
			array(
				'heading' => 'At the end of the course, you should be able to',
				'items'   => array(
					'Learn how to shade basic shapes with light and shadow',
					'Measure and position the composition on the page correctly',
					'Arrange multiple objects in a good composition',
				),
			),
			array(
				'heading' => 'Note',
				'items'   => array(
					'Real life drawing is very different from copying images or paintings. You need to observe 3D objects and learn how to draw shapes under various lighting conditions.',
					'Your work does not have to be a perfect match with the original model.',
				),
			),
		),
	),
	'chi-co-ban-2' => array(
		'slug'           => 'chi-co-ban-2',
		'title'          => 'Pencil Basics 2',
		'pathway_id'     => 'drawing-foundations',
		'pathway_title'  => 'Drawing Foundations',
		'sessions'       => '6 sessions',
		'image'          => array(
			'url' => $uploads . '/2.png',
			'alt' => 'Pencil Basics 2 at Art Tutor Hanoi',
		),
		'blocks'         => array(
			array(
				'heading' => 'In this course, you\'ll draw',
				'items'   => array(
					'2 Planar Head Model Studies (a different angle per session)',
					'1 Classical Bust Study (2 sessions)',
					'1 Still Life Study — fabric, metal, and other textures (2 sessions)',
				),
			),
			array(
				'heading' => 'This course will help you',
				'items'   => array(
					'Learn how to draw the head with simple shapes and planes',
					'See how complex head planes look with a bust example',
					'Make different effects with your pencil on paper',
					'Have your own still-life artwork to show off',
				),
			),
			array(
				'heading' => 'Note',
				'text'    => 'We highly recommend that you have finished Pencil Basics 1 before this course.',
			),
		),
	),
	'sketch' => array(
		'slug'           => 'sketch',
		'title'          => 'Sketch',
		'pathway_id'     => 'drawing-foundations',
		'pathway_title'  => 'Drawing Foundations',
		'sessions'       => '6 sessions',
		'image'          => array(
			'url' => $uploads . '/5.png',
			'alt' => 'Sketch course at Art Tutor Hanoi',
		),
		'blocks'         => array(
			array(
				'heading' => 'During the course, you will',
				'text'    => 'Use pencils and charcoal to draw subjects on location — outdoors with people, trees, streets, and buildings; or indoors with household objects and favourite corners.',
			),
			array(
				'heading' => 'At the end of the course, you can',
				'items'   => array(
					'Capture moments from your life and use them for creative projects later. Sketching can also be a form of art by itself.',
					'Draw moving objects in different situations and perspectives, ready for any change in the subject\'s surroundings.',
				),
			),
			array(
				'heading' => 'Note',
				'items'   => array(
					'Sketches should be in black and white',
					'Complete Composition Basics before this course',
				),
			),
		),
		'sessions_breakdown' => array(
			array( 'title' => 'Session 1', 'text' => 'Practice observing and quickly sketching the forms of basic objects.' ),
			array( 'title' => 'Session 2', 'text' => 'Continue practicing sketching with objects and groups of objects that have more complex shapes.' ),
			array( 'title' => 'Session 3', 'text' => 'Experiment with different ways of creating texture for objects.' ),
			array( 'title' => 'Session 4', 'text' => 'Learn basic composition principles and practice arranging compositions using available objects.' ),
			array( 'title' => 'Session 5', 'text' => 'Comprehensive indoor sketching practice.' ),
			array( 'title' => 'Session 6', 'text' => 'Comprehensive outdoor sketching practice — landscape sketching.' ),
		),
	),
	'composition' => array(
		'slug'           => 'composition',
		'title'          => 'Composition Basics',
		'pathway_id'     => 'drawing-foundations',
		'pathway_title'  => 'Drawing Foundations',
		'sessions'       => '6 sessions',
		'image'          => array(
			'url' => $uploads . '/4.png',
			'alt' => 'Composition Basics at Art Tutor Hanoi',
		),
		'blocks'         => array(
			array(
				'heading' => 'During the course, you will',
				'items'   => array(
					'Study Old Masters paintings',
					'Design your painting composition (with unlimited number of designs)',
					'Analyze and improve your composition designs',
				),
			),
			array(
				'heading' => 'This course will help you',
				'items'   => array(
					'Learn some basic rules for making good compositions',
					'Analyze how paintings are composed and how the elements work together',
					'Create your own cool designs with confidence',
				),
			),
		),
	),
	'charcoal-basics-1' => array(
		'slug'           => 'charcoal-basics-1',
		'title'          => 'Charcoal Basics 1',
		'pathway_id'     => 'charcoal-drawing',
		'pathway_title'  => 'Charcoal Drawing',
		'sessions'       => '6 sessions',
		'image'          => array(
			'url' => $uploads . '/7.png',
			'alt' => 'Charcoal Basics 1 at Art Tutor Hanoi',
		),
		'blocks'         => array(
			array(
				'heading' => 'You\'ll learn how to',
				'items'   => array(
					'Draw simple shapes in 2 sessions',
					'Draw a sculpture in 2 sessions',
					'Draw still-life objects in 4 sessions',
				),
			),
			array(
				'heading' => 'Here\'s what you\'ll learn in this course',
				'items'   => array(
					'How to work with charcoal and different smudge tools (fingers, erasers, dry tissue paper, etc.)',
					'How to see the big picture — locating areas where significant differences in value occur',
					'How to make your own art (you are taking your first steps!)',
				),
			),
			array(
				'heading' => 'Note',
				'items'   => array(
					'To control charcoal, adjust how hard you press and how well you see the textures',
					'Charcoal works have more contrast than pencil works',
					'Know the basics of drawing shapes and space before this course (see Pencil Basics 1 & 2)',
				),
			),
		),
	),
	'charcoal-basics-2' => array(
		'slug'           => 'charcoal-basics-2',
		'title'          => 'Charcoal Basics 2',
		'pathway_id'     => 'charcoal-drawing',
		'pathway_title'  => 'Charcoal Drawing',
		'sessions'       => '12 sessions',
		'image'          => array(
			'url' => $uploads . '/3.png',
			'alt' => 'Charcoal Basics 2 at Art Tutor Hanoi',
		),
		'blocks'         => array(
			array(
				'heading' => 'During the course, you\'ll draw',
				'items'   => array(
					'Still life',
					'Portrait of live model (half or full body)',
				),
			),
			array(
				'heading' => 'At the end of the course, you can',
				'items'   => array(
					'Use the skills learned from Charcoal Basics 1',
					'Draw a complete portrait with charcoal',
					'Know more about human anatomy, facial features, and values (light, dark, middle, etc.)',
				),
			),
			array(
				'heading' => 'Note',
				'text'    => 'See Charcoal Basics 1 before enrolling.',
			),
		),
	),
	'charcoal-advanced' => array(
		'slug'           => 'charcoal-advanced',
		'title'          => 'Charcoal Advanced',
		'pathway_id'     => 'charcoal-drawing',
		'pathway_title'  => 'Charcoal Drawing',
		'sessions'       => '12 sessions',
		'image'          => array(
			'url' => $uploads . '/6.png',
			'alt' => 'Charcoal Advanced at Art Tutor Hanoi',
		),
		'blocks'         => array(
			array(
				'heading' => 'During the course, you will',
				'text'    => 'Paint a live nude model.',
			),
			array(
				'heading' => 'At the end of the course, you can',
				'items'   => array(
					'Get to know human body anatomy',
					'Gradually master charcoal techniques',
				),
			),
		),
	),
	'color-theory' => array(
		'slug'           => 'color-theory',
		'title'          => 'Colour Basics',
		'pathway_id'     => 'colour-painting',
		'pathway_title'  => 'Colour & Painting',
		'sessions'       => '6 sessions',
		'image'          => array(
			'url' => $uploads . '/8.png',
			'alt' => 'Colour Basics at Art Tutor Hanoi',
		),
		'blocks'         => array(
			array(
				'heading' => 'During the course, you will',
				'items'   => array(
					'Draw a Munsell colour wheel, using gouache or acrylic',
					'Practice mixing colours and create values',
				),
			),
			array(
				'heading' => 'At the end of the course, you should be able to',
				'items'   => array(
					'Make secondary colours from primary colours',
					'Understand warm/cool palettes, primary, secondary, tertiary, and complementary colours',
					'Understand hue, value and saturation',
				),
			),
		),
	),
	'color-composition' => array(
		'slug'           => 'color-composition',
		'title'          => 'Colour Composition',
		'pathway_id'     => 'colour-painting',
		'pathway_title'  => 'Colour & Painting',
		'sessions'       => '4 sessions',
		'image'          => array(
			'url' => $uploads . '/9.png',
			'alt' => 'Colour Composition at Art Tutor Hanoi',
		),
		'blocks'         => array(
			array(
				'heading' => 'During the course, you will',
				'items'   => array(
					'Design a 2D composition with geometric or organic shapes',
					'Make a square design with red, yellow and blue shapes',
					'Make a circle design with green, orange and purple shapes',
					'Make a polygon design with colours that look good together',
					'Make your own abstract artwork with any colours you want',
				),
			),
			array(
				'heading' => 'At the end of the course, you should be able to',
				'items'   => array(
					'Use mass, shapes, and lines to plan a balanced composition',
					'Choose and arrange colour segments to create a harmonious painting with colour schemes',
				),
			),
			array(
				'heading' => 'Note',
				'text'    => 'You\'ll also need to learn how to use gouache or acrylic paint.',
			),
		),
	),
	'oil-painting-basics-1' => array(
		'slug'           => 'oil-painting-basics-1',
		'title'          => 'Oil Painting Basics 1',
		'pathway_id'     => 'colour-painting',
		'pathway_title'  => 'Colour & Painting',
		'sessions'       => '6 sessions',
		'image'          => array(
			'url' => $uploads . '/12.png',
			'alt' => 'Oil Painting Basics 1 at Art Tutor Hanoi',
		),
		'blocks'         => array(
			array(
				'heading' => 'During the course, you will',
				'items'   => array(
					'Draw a single still-life object on white cloth',
					'Draw a single still-life object on printed fabric',
					'Draw different objects of various materials on printed fabric',
					'Draw different objects in their real time and space',
				),
			),
			array(
				'heading' => 'At the end of the course, you should be able to',
				'items'   => array(
					'See values when painting still-life in colours and apply the basics you\'ve learned',
					'Observe and draw textures (porcelain, fabric, metal, skin, etc.)',
				),
			),
			array(
				'heading' => 'Note',
				'items'   => array(
					'Experiment with different mediums (gouache, acrylic, oil, etc.) to get different results',
					'Complete Pencil Basics and Composition Basics first for better learning',
				),
			),
		),
	),
	'oil-painting-basics-2' => array(
		'slug'           => 'oil-painting-basics-2',
		'title'          => 'Oil Painting Basics 2',
		'pathway_id'     => 'colour-painting',
		'pathway_title'  => 'Colour & Painting',
		'sessions'       => '6 sessions',
		'image'          => array(
			'url' => $uploads . '/10.png',
			'alt' => 'Oil Painting Basics 2 at Art Tutor Hanoi',
		),
		'blocks'         => array(
			array(
				'heading' => 'During the course, you will',
				'items'   => array(
					'Learn from nature and design your own composition',
					'Paint outdoors with simple shapes and colours',
					'Finish your painting with details and harmony',
				),
			),
			array(
				'heading' => 'At the end of the course, you should be able to',
				'items'   => array(
					'Collect and interpret visual data from your surroundings',
					'Create your own landscape paintings based on your observations',
				),
			),
			array(
				'heading' => 'Note',
				'text'    => 'It\'s essential to complete fundamental courses (Pencil Basics, Composition Basics, etc.) before undertaking this course.',
			),
		),
	),
);
