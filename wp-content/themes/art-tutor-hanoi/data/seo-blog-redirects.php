<?php
/**
 * Sprint 3 — blog post slugs that duplicate commercial pages (301 targets).
 *
 * Keys: post slug (single path segment).
 * Values: array{ type: page|experience, key: ath_page_slugs key OR experience slug }
 *
 * @package Art_Tutor_Hanoi
 */
return array(
	// Life drawing cluster → workshop page.
	'why-nude-model-drawing-is-essential'        => array( 'type' => 'experience', 'key' => 'life-drawing' ),
	'5-powerful-reasons-nude-model-drawing-class'  => array( 'type' => 'experience', 'key' => 'life-drawing' ),

	// Art classes pillar duplicates → /courses/.
	'english-speaking-art-classes-hanoi-2025'    => array( 'type' => 'page', 'key' => 'courses' ),
	'best-korean-students-in-hanoi-art-classes'  => array( 'type' => 'page', 'key' => 'kids-courses' ),

	// Studio / programs duplicates.
	'private-art-studio-in-hanoi'                => array( 'type' => 'page', 'key' => 'about' ),
	'hidden-art-studio-in-hanoi'                 => array( 'type' => 'page', 'key' => 'about' ),
	'programs'                                   => array( 'type' => 'page', 'key' => 'courses' ),

	// Workshop tutorial posts → workshop pages.
	'vietnamese-silk-painting-step-by-step'      => array( 'type' => 'experience', 'key' => 'silk-painting' ),
	'silk-painting-advanced-workshop'            => array( 'type' => 'experience', 'key' => 'silk-painting' ),
	'life-drawing-in-hanoi-join-live-model-session' => array( 'type' => 'experience', 'key' => 'life-drawing' ),
);
