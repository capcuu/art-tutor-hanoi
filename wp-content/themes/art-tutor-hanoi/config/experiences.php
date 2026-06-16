<?php
/**
 * Experience / workshop page helpers.
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

function v2_experience_url( $slug ) {
	return ath_experience_url( $slug );
}

function v2_experience_by_slug( $slug ) {
	static $experiences = null;
	if ( $experiences === null ) {
		$experiences = require ATH_THEME_DIR . '/data/experience-pages.php';
	}

	if ( ! isset( $experiences[ $slug ] ) ) {
		return null;
	}

	$experience = $experiences[ $slug ];

	if ( ! empty( $experience['book']['url'] ) ) {
		$tab_by_slug = array(
			'trial-art-class'  => 'adult',
			'life-drawing'     => 'adult',
			'silk-painting'    => 'adult',
			'artist-residency' => 'adult',
		);

		if ( isset( $tab_by_slug[ $slug ] ) ) {
			$experience['book']['url'] = ath_book_url( $tab_by_slug[ $slug ] );
		} else {
			$experience['book']['url'] = ath_resolve_url( $experience['book']['url'] );
		}
	}

	return $experience;
}
