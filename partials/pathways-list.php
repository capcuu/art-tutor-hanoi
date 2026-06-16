<?php
/**
 * Pathway cards — panel type: 'courses' (links) or 'detail' (description).
 *
 * @var array  $pathways
 * @var string $pathways_panel
 */
require_once __DIR__ . '/../config/courses.php';

if ( ! function_exists( 'v2_img_url' ) ) {
	require_once __DIR__ . '/../config/images.php';
}

$pathways_panel = isset( $pathways_panel ) ? $pathways_panel : 'detail';
$pathways_hub   = isset( $pathways_hub ) ? $pathways_hub : 'adults';

if ( $pathways_hub === 'kids' && ! function_exists( 'v2_kids_course_url' ) ) {
	require_once __DIR__ . '/../config/kids-courses.php';
}
?>
<div class="pathways-list" id="pathways-list">
  <?php foreach ( $pathways as $pathway ) : ?>
    <?php
    $panel_id    = 'pathway-panel-' . $pathway['id'];
    $header_id   = 'pathway-header-' . $pathway['id'];
    $detail      = array();
    $use_detail  = ! empty( $pathway['detail'] );
    $has_courses = ! empty( $pathway['courses'] );

    if ( $use_detail ) {
      $detail = is_array( $pathway['detail'] ) ? $pathway['detail'] : array( $pathway['detail'] );
    }

    $has_panel = $use_detail || $has_courses || ! empty( $pathway['course_slug'] );
    ?>
    <article class="pathway" id="<?php echo htmlspecialchars( $pathway['id'], ENT_QUOTES, 'UTF-8' ); ?>" data-pathway="<?php echo htmlspecialchars( $pathway['id'], ENT_QUOTES, 'UTF-8' ); ?>">
      <button
        type="button"
        class="pathway__header"
        id="<?php echo htmlspecialchars( $header_id, ENT_QUOTES, 'UTF-8' ); ?>"
        aria-expanded="false"
        aria-controls="<?php echo htmlspecialchars( $panel_id, ENT_QUOTES, 'UTF-8' ); ?>"
        <?php echo $has_panel ? '' : 'disabled'; ?>
      >
        <div class="pathway__media">
          <?php
          $pathway_img = v2_img_url( $pathway['image']['url'], 'pathway' );
          $pathway_size = v2_img_display_size( 'pathway' );
          ?>
          <img
            src="<?php echo htmlspecialchars( $pathway_img, ENT_QUOTES, 'UTF-8' ); ?>"
            alt="<?php echo htmlspecialchars( $pathway['image']['alt'], ENT_QUOTES, 'UTF-8' ); ?>"
            width="<?php echo (int) $pathway_size['width']; ?>"
            height="<?php echo (int) $pathway_size['height']; ?>"
            loading="lazy"
            decoding="async"
          >
        </div>
        <div class="pathway__intro">
          <span class="pathway__num"><?php echo htmlspecialchars( $pathway['num'], ENT_QUOTES, 'UTF-8' ); ?></span>
          <h2 class="pathway__title"><?php echo htmlspecialchars( $pathway['title'], ENT_QUOTES, 'UTF-8' ); ?></h2>
          <p class="pathway__desc"><?php echo htmlspecialchars( $pathway['description'], ENT_QUOTES, 'UTF-8' ); ?></p>
        </div>
        <?php if ( $has_panel ) : ?>
          <span class="pathway__chevron" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="20" height="20"><path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </span>
        <?php endif; ?>
      </button>

      <?php if ( $has_panel ) : ?>
        <div
          class="pathway__panel"
          id="<?php echo htmlspecialchars( $panel_id, ENT_QUOTES, 'UTF-8' ); ?>"
          role="region"
          aria-labelledby="<?php echo htmlspecialchars( $header_id, ENT_QUOTES, 'UTF-8' ); ?>"
          hidden
        >
          <?php if ( $use_detail ) : ?>
            <div class="pathway__detail">
              <?php foreach ( $detail as $paragraph ) : ?>
                <p><?php echo htmlspecialchars( $paragraph, ENT_QUOTES, 'UTF-8' ); ?></p>
              <?php endforeach; ?>
              <?php if ( ! empty( $pathway['course_slug'] ) && ! $has_courses ) : ?>
                <?php
                $detail_course_url = $pathways_hub === 'kids'
                  ? v2_kids_course_url( $pathway['course_slug'] )
                  : v2_course_url( $pathway['course_slug'] );
                ?>
                <p class="pathway__detail-link">
                  <a href="<?php echo htmlspecialchars( $detail_course_url, ENT_QUOTES, 'UTF-8' ); ?>" class="pathway__course-link">
                    <span class="pathway__course-title">View full course details</span>
                    <span class="pathway__course-arrow" aria-hidden="true">
                      <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                  </a>
                </p>
              <?php endif; ?>
            </div>
          <?php endif; ?>

          <?php if ( $has_courses ) : ?>
            <ul class="pathway__courses<?php echo $use_detail ? ' pathway__courses--after-detail' : ''; ?>">
              <?php foreach ( $pathway['courses'] as $course ) : ?>
                <?php
                if ( ! empty( $course['slug'] ) ) {
                  $course_url = $pathways_hub === 'kids'
                    ? v2_kids_course_url( $course['slug'] )
                    : v2_course_url( $course['slug'] );
                } else {
                  $course_url = isset( $course['url'] ) ? $course['url'] : '#';
                }
                ?>
                <li>
                  <a href="<?php echo htmlspecialchars( $course_url, ENT_QUOTES, 'UTF-8' ); ?>" class="pathway__course-link">
                    <span class="pathway__course-title"><?php echo htmlspecialchars( $course['title'], ENT_QUOTES, 'UTF-8' ); ?></span>
                    <span class="pathway__course-arrow" aria-hidden="true">
                      <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </article>
  <?php endforeach; ?>
</div>
