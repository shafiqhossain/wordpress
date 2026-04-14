<?php
function _heroDescription() {
    ob_start();
    $description = get_field('description_columns'); ?>
    <div class="cmma-two-column-description">
        <div class="cmma-description">
            <?= $description; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('cmma_hero_description', '_heroDescription');