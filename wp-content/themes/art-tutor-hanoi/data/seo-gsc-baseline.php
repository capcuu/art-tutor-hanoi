<?php
/**
 * GSC baseline (export doc/seo/Pages.csv) — dùng so sánh khi monitor Sprint 4.
 *
 * @package Art_Tutor_Hanoi
 */
return array(
	array(
		'path'                 => '/',
		'label'                => 'Homepage',
		'baseline_impressions' => 193698,
		'baseline_ctr'         => 0.18,
		'baseline_position'    => 5.04,
		'target_ctr'           => 1.0,
		'priority'             => 'P0',
	),
	array(
		'path'                 => '/workshops/',
		'label'                => 'Workshops hub',
		'baseline_impressions' => 22004,
		'baseline_ctr'         => 0.07,
		'baseline_position'    => 6.79,
		'target_ctr'           => 0.5,
		'priority'             => 'P0',
	),
	array(
		'path'                 => '/courses/',
		'label'                => 'Courses pillar',
		'baseline_impressions' => 0,
		'baseline_ctr'         => 0,
		'baseline_position'    => 0,
		'target_ctr'           => 0.5,
		'priority'             => 'P0',
		'note'                 => 'Legacy /art-classes-in-hanoi/ ~28k imp → redirect tới /courses/',
	),
	array(
		'path'                 => '/art-classes-in-hanoi/',
		'label'                => 'Legacy art-classes (redirect)',
		'baseline_impressions' => 27974,
		'baseline_ctr'         => 0,
		'baseline_position'    => 5.87,
		'target_ctr'           => 0,
		'priority'             => 'P0',
		'note'                 => '301 → /courses/ — theo dõi impressions giảm, clicks chuyển sang /courses/',
	),
	array(
		'path'                 => '/book/',
		'label'                => 'Book',
		'baseline_impressions' => 190,
		'baseline_ctr'         => 0.53,
		'baseline_position'    => 6.25,
		'target_ctr'           => 2.0,
		'priority'             => 'P1',
	),
	array(
		'path'                 => '/faq/',
		'label'                => 'FAQ',
		'baseline_impressions' => 0,
		'baseline_ctr'         => 0,
		'baseline_position'    => 0,
		'target_ctr'           => 1.0,
		'priority'             => 'P1',
		'note'                 => 'Mới optimize Sprint 3',
	),
	array(
		'path'                 => '/free-art-feedback/',
		'label'                => 'Art feedback',
		'baseline_impressions' => 5159,
		'baseline_ctr'         => 3.55,
		'baseline_position'    => 8.73,
		'target_ctr'           => 3.0,
		'priority'             => 'P2',
	),
	array(
		'path'                 => '/hanoi-art-supply-map/',
		'label'                => 'Art supplies map',
		'baseline_impressions' => 6557,
		'baseline_ctr'         => 2.61,
		'baseline_position'    => 5.71,
		'target_ctr'           => 2.5,
		'priority'             => 'P2',
	),
);
