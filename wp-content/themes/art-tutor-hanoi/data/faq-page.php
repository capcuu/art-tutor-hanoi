<?php
/**
 * FAQ page — sections and Q&A (seed for Gutenberg migration).
 *
 * Inline links in copy: {page:Label} or {page}text{/page}
 * Pages: book, book-kids, pricing, courses, kids-courses, workshops, calendar, maps
 * Workshops: life-drawing, silk, residency
 */
return array(
	'intro' => array(
		'We\'ve collected common questions from our students — whether you\'re a traveler, expat, or local looking for {courses}art classes in Hanoi{/courses}, this FAQ covers booking, pricing, schedules, and materials.',
	),
	'sections' => array(
		array(
			'id'    => 'general-course-info',
			'title' => 'General Course Info',
			'qa'    => array(
				array(
					'q' => 'I\'m a complete beginner. Can I still join your art class?',
					'a' => 'Yes! Many of our students are beginners. Our instructors guide you step by step in a calm studio near West Lake.',
				),
				array(
					'q' => 'Which course should I take as a beginner?',
					'a' => 'We recommend Pencil Basics 1 — it builds essential skills like line, shape, and shading. It\'s the foundation for charcoal, colour, and oil painting.',
				),
				array(
					'q' => 'Are the classes taught in English?',
					'a' => 'Yes. All classes are available in English and Vietnamese — ideal if you\'re looking for English-speaking art classes in Hanoi.',
				),
				array(
					'q' => 'How long is each session?',
					'a' => 'Adult studio classes are typically 2 hours. Life drawing runs 2.5 hours. Kids\' classes are 1.5 hours.',
				),
				array(
					'q' => 'Can I try a class before booking the full course?',
					'a' => 'Absolutely. Book a {book:trial class} ($40) to experience our teaching style before committing to a course package.',
				),
				array(
					'q' => 'What level am I?',
					'a' => 'Send us your past drawings, or {book:book a trial class} so we can assess your level and recommend a course.',
				),
				array(
					'q' => 'What\'s the difference between a trial class, a workshop, and a weekly course?',
					'a' => 'A trial class is a single 2-hour introductory session. Workshops are one-off studio experiences (2–2.5 hours) such as life drawing or silk painting. Weekly courses are structured 6- or 12-session programs. See {workshops}workshops{/workshops} and {courses}courses{/courses} for details.',
				),
				array(
					'q' => 'How much does an art class in Hanoi cost?',
					'a' => 'Trial class: $40. Workshops from $40. Adult course packages from $115 (6 sessions). All prices are in USD — see our full {pricing}pricing page{/pricing}.',
				),
			),
			'footer' => '{book:Book a class} · {pricing:View pricing}',
		),
		array(
			'id'    => 'booking',
			'title' => 'Booking Online',
			'qa'    => array(
				array(
					'q' => 'How do I book a class online?',
					'a' => 'Go to {book:Book a Class}, choose the Adult or Kids tab, select your program, and submit the form. We confirm your slot by WhatsApp, Zalo, or email.',
				),
				array(
					'q' => 'What happens after I submit the booking form?',
					'a' => 'Our admin team confirms availability, sends payment details, and shares a reminder before your session. If a slot is full, we\'ll suggest the nearest alternative.',
				),
				array(
					'q' => 'I\'m visiting Hanoi for 2–3 days — which class should I take?',
					'a' => 'Try a {book:trial class}, join {life-drawing:Saturday life drawing}, or browse our {workshops}2-hour workshops{/workshops}. Most sessions include materials and English instruction.',
				),
			),
			'footer' => '{book:Book now} · {calendar:Weekly calendar}',
		),
		array(
			'id'    => 'schedules-frequency-flexibility',
			'title' => 'Schedules, Frequency & Flexibility',
			'qa'    => array(
				array(
					'q' => 'I\'m only in Hanoi for 1–2 months. Can I take art classes daily?',
					'a' => 'Yes. We offer structured courses and flexible options for short stays — workshops and trial classes work well for travelers.',
				),
				array(
					'q' => 'Can I take multiple classes at the same time?',
					'a' => 'Yes, many students do. We\'ll guide you on the best sequence — e.g. Pencil Basics before Charcoal or Oil.',
				),
				array(
					'q' => 'Can I change the schedule midway through a course?',
					'a' => 'Yes. Please let us know at least 1 week in advance so we can adjust your class or teacher.',
				),
				array(
					'q' => 'Can I pause the course if I need to travel?',
					'a' => 'Yes. Notify our admin as early as possible before you leave so we can pause your package and resume when you return.',
				),
				array(
					'q' => 'Can I cancel a class if I\'m sick or have a change of plans?',
					'a' => 'Please notify our admin (WhatsApp, Zalo, or email) at least 24 hours before the scheduled class. You may reschedule the missed class within 7 days at no extra fee. Cancellations less than 24 hours in advance are normally charged as a used session — private or custom sessions are charged in full.',
				),
				array(
					'q' => 'How do I read the weekly calendar?',
					'a' => 'Our {calendar}weekly calendar{/calendar} shows upcoming group sessions and events. Book your spot via {book}Book a Class{/book} once you find a suitable time.',
				),
			),
		),
		array(
			'id'    => 'pricing-materials-payment',
			'title' => 'Pricing, Materials & Payment',
			'qa'    => array(
				array(
					'q' => 'What are the tuition fees?',
					'a' => 'All prices are in USD. Trial class: $40. Workshops from $40. Adult 6-session packages from $115. See the full breakdown on our {pricing}pricing page{/pricing}.',
				),
				array(
					'q' => 'Are materials included in the price?',
					'a' => 'Yes. Most beginner classes include all needed tools. Extra items such as canvas frames or oil painting medium may have an additional cost — we\'ll tell you in advance.',
				),
				array(
					'q' => 'What materials are used in each class?',
					'a_list' => array(
						'Pencil Basics: pencils, erasers, paper, measuring tools',
						'Charcoal: as above, plus vine charcoal',
						'Oil Painting: paint tubes, brushes, palette, gloves, oil, apron',
						'Sketch: a mix of dry and colour media (pastels, coloured pencils, markers)',
					),
				),
				array(
					'q' => 'Are there any hidden fees?',
					'a' => 'No. We\'ll inform you if extra materials or schedule changes apply.',
				),
				array(
					'q' => 'How can I pay?',
					'a' => 'We accept Stripe, PayPal, bank transfer, and cash. Online card payment is available when you book; bank transfer is also welcome to reserve your spot.',
				),
			),
			'footer' => '{pricing:View pricing}',
		),
		array(
			'id'    => 'policies',
			'title' => 'Policies (Refunds, Attendance & Discounts)',
			'intro' => 'All classes and packages are non-refundable and non-transferable.',
			'qa'    => array(
				array(
					'q' => 'Rescheduling',
					'a' => 'Notify us at least 24 hours before your class to reschedule without losing a session.',
				),
				array(
					'q' => 'Late cancellations',
					'a' => 'Cancellations made less than 24 hours before class are normally counted as a used session.',
				),
				array(
					'q' => 'Emergency exceptions',
					'a' => 'Each package includes limited emergency exceptions for unexpected illness or urgent work. 6-session package: 1 exception. 12-session package: 2 exceptions.',
				),
				array(
					'q' => 'Package validity',
					'a' => 'Course packages must be completed within their validity period. Long-term packages are valid for 12 months — see {pricing}pricing{/pricing} for details.',
				),
				array(
					'q' => 'Pausing the course',
					'a' => 'Packages may be paused for travel with at least 2 weeks\' notice to admin.',
				),
			),
		),
		array(
			'id'    => 'class-types',
			'title' => 'Class Types & Special Requests',
			'qa'    => array(
				array(
					'q' => 'Do you offer life drawing / nude model sketch classes?',
					'a' => 'Yes. We host regular {life-drawing:life drawing sessions} with a professional model — great for figure drawing and observational practice.',
				),
				array(
					'q' => 'When is life drawing held?',
					'a' => 'Life drawing runs on Saturdays, 13:30–16:00 (2.5 hours). Book via {book:Book a Class} or see {life-drawing:life drawing details}.',
				),
				array(
					'q' => 'I\'ve never drawn from a model before — is it OK?',
					'a' => 'Yes. Beginners are welcome. Sessions follow a calm, academic approach — focus on observation and practice, not finished artworks.',
				),
				array(
					'q' => 'Can I take photos during life drawing?',
					'a' => 'Photography of the model is not allowed. You may photograph your own work. Please respect the quiet studio atmosphere.',
				),
				array(
					'q' => 'I want to sketch landscapes or people while traveling. Any class for that?',
					'a' => 'Try our life drawing, sketching courses, or Pencil Basics — all suit travelers who want structured practice in Hanoi.',
				),
				array(
					'q' => 'What is the silk painting workshop?',
					'a' => 'Our {silk:silk painting workshop} is a guided studio program with an instructor from Vietnam University of Fine Arts — ideal if you want to try a traditional Vietnamese medium.',
				),
				array(
					'q' => 'What is the Artist Residency?',
					'a' => 'The {residency:Artist Residency} gives you dedicated studio access for 1–4 weeks ($135–$480). Work at your own pace with optional tutor guidance — different from a single 2-hour workshop.',
				),
				array(
					'q' => 'Can I change my class or teacher if I want to?',
					'a' => 'Yes — just let us know and we\'ll help find a better match.',
				),
			),
			'footer' => '{workshops:Browse workshops} · {book:Book a class}',
		),
		array(
			'id'    => 'kids-teens',
			'title' => 'Kids & Teens Classes',
			'qa'    => array(
				array(
					'q' => 'What age can my child start art classes?',
					'a_list' => array(
						'Contemporary Art for Kids: usually 7+ (younger children may join a trial first)',
						'Pencil Basics 1: 11+ or kids preparing for academic art',
					),
				),
				array(
					'q' => 'How long is a Contemporary Art for Kids class?',
					'a' => '1.5 hours per session.',
				),
				array(
					'q' => 'Do parents stay during kids class?',
					'a' => 'Parents may wait in the studio lounge or drop off for older children. Let us know your preference when you book.',
				),
				array(
					'q' => 'Kids drawing class vs Contemporary Art — which for my child?',
					'a' => 'Contemporary Art suits younger children exploring creativity. Pencil Basics and structured courses suit older kids building academic skills. See {kids-courses}kids courses{/kids-courses} for pathways.',
				),
			),
			'footer' => '{kids-courses:Kids courses} · {book-kids:Book kids class}',
		),
		array(
			'id'    => 'location-logistics',
			'title' => 'Studio Location & What to Bring',
			'qa'    => array(
				array(
					'q' => 'Where is the studio? How do I get there?',
					'a' => 'We\'re in Tây Hồ (West Lake), Hanoi — 82 Ngách 264/15 Ngõ 374 Đường Âu Cơ. Grab or taxi is easiest; {maps:open Google Maps} for directions.',
				),
				array(
					'q' => 'What should I wear or bring?',
					'a' => 'Wear comfortable clothes you don\'t mind getting paint or charcoal on. We provide materials for most classes. Bring reference photos if your project needs them.',
				),
				array(
					'q' => 'Do you offer home tuition?',
					'a' => 'All regular classes take place at our Tay Ho studio. Contact us if you have a special request — we\'ll let you know what\'s possible.',
				),
			),
		),
		array(
			'id'    => 'inspiration-hanoi',
			'title' => 'Inspiration & Local Art in Hanoi',
			'qa'    => array(
				array(
					'q' => 'Are there any art museums in Hanoi you recommend?',
					'a' => 'Yes! These are great for inspiration beyond the classroom:',
				),
				array(
					'q'      => '',
					'a_list' => array(
						'{vnfam:Vietnam National Fine Arts Museum} — traditional and modern Vietnamese art',
						'{vme:Vietnam Museum of Ethnology} — ethnic crafts and folk art',
					),
					'skip_q'       => true,
					'linkify_list' => true,
				),
				array(
					'q' => '',
					'a' => 'They\'re perfect for finding artistic inspiration on your own visit. Our kids courses may include guided museum visits — ask us when you enrol.',
					'skip_q' => true,
				),
			),
		),
	),
);
