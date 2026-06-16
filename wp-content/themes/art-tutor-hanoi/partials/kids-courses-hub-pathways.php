<?php
/**
 * Kids courses hub — pathways accordion only (schedule/pricing live in Gutenberg).
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

$pathways = require ATH_THEME_DIR . '/data/kids-pathways.php';
?>
<section class="pathways-section" aria-label="Kids course pathways">
  <div class="pathways-section__inner">
    <?php
    $pathways_panel = 'detail';
    $pathways_hub   = 'kids';
    require ATH_THEME_DIR . '/partials/pathways-list.php';
    ?>
  </div>
</section>
