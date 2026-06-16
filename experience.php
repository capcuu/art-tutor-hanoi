<?php
/**
 * Workshop / experience page — Art Tutor Hanoi v2.
 */
require __DIR__ . '/config/experiences.php';

$slug       = isset( $_GET['slug'] ) ? trim( (string) $_GET['slug'] ) : '';
$experience = $slug !== '' ? v2_experience_by_slug( $slug ) : null;

if ( ! $experience ) {
	http_response_code( 404 );
	$page_title = 'Experience not found — Art Tutor Hanoi';
	require __DIR__ . '/partials/head.php';
	require __DIR__ . '/partials/header.html';
	?>
<main class="experience-page experience-page--not-found">
  <div class="experience-page__inner">
    <h1 class="experience-page__title">Page not found</h1>
    <p class="experience-page__intro">Browse our workshops and experiences to find your next session.</p>
    <p><a href="pricing.php" class="courses-cta__btn">View programs</a></p>
  </div>
</main>
	<?php
	require __DIR__ . '/partials/footer.html';
	?>
  <script src="assets/js/main.js"></script>
</body>
</html>
	<?php
	exit;
}

$page_title = $experience['title'] . ' — Art Tutor Hanoi';

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.html';
require __DIR__ . '/partials/experience-content.php';
require __DIR__ . '/partials/footer.html';
?>
  <script src="assets/js/main.js"></script>
</body>
</html>
