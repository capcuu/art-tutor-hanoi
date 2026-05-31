<?php
/**
 * Experience / workshop page helpers.
 */
function v2_experience_url( $slug ) {
	return 'experience.php?slug=' . rawurlencode( $slug );
}

function v2_experience_by_slug( $slug ) {
	static $experiences = null;
	if ( $experiences === null ) {
		$experiences = require dirname( __DIR__ ) . '/data/experience-pages.php';
	}
	return isset( $experiences[ $slug ] ) ? $experiences[ $slug ] : null;
}
