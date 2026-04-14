<?php
function _heroDescription() {
    ob_start();
    $description = get_field('description_columns'); ?>
    <div class="smma-two-column-description">
        <div class="smma-description">
            <?= $description; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('smma_hero_description', '_heroDescription');