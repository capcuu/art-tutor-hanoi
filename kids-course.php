<?php
/**
 * Individual kids course page — Art Tutor Hanoi v2.
 */
require __DIR__ . '/config/kids-courses.php';

$slug   = isset( $_GET['slug'] ) ? trim( (string) $_GET['slug'] ) : '';
$course = $slug !== '' ? v2_kids_course_by_slug( $slug ) : null;

if ( ! $course ) {
	http_response_code( 404 );
	$page_title = 'Course not found — Art Tutor Hanoi';
	require __DIR__ . '/partials/head.php';
	require __DIR__ . '/partials/header.html';
	?>
<main class="course-page course-page--not-found">
  <div class="course-page__inner">
    <h1 class="course-page__title">Course not found</h1>
    <p class="course-page__intro">We couldn&rsquo;t find that course. Browse our kids programs to see what&rsquo;s available.</p>
    <p><a href="kids-courses.php" class="courses-cta__btn">View kids courses</a></p>
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

$page_title = $course['title'] . ' — Art Tutor Hanoi';

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.html';
require __DIR__ . '/partials/course-content.php';
require __DIR__ . '/partials/footer.html';
?>
  <script src="assets/js/main.js"></script>
</body>
</html>
