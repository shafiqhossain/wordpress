<?php
function jumpNavigationBar() {
    ob_start();
    $jump_navigation_bar = get_field('enable_jump_navigation_bar',get_queried_object_id());
    if ($jump_navigation_bar == 1) : ?>
        <div class="panel-wrapper-wrap">
            <div class="cmma-footer-jump-navigation">
                <div class="cmma-container">
                    <div class="cmma-footer-jump-navigation-wrapper">
                        <div class="cmma-footer-jump-navigation-heading">Jump to</div>
                        <ul></ul>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            jQuery(function () {
                var jumpNavigation = jQuery('.cmma-footer-jump-navigation ul');
                jQuery('.cmma-widget-jump-navigation').each(function (index) {
                    var parentId = jQuery(this).closest('[data-id]').data('id');
                    var textContent = jQuery(this).data('title') || jQuery(this).find('h2, h3, h4').filter(function() {
						return jQuery(this).text().trim();
					}).first().text().trim();
                    var listItem = '<li data-id="' + index + '"><a href="#' + parentId + '">' + textContent + '</a></li>';
                    jumpNavigation.append(listItem);
                });

                jQuery('a[href^="#"]').on('click', function (event) {
                    event.preventDefault();
                    var targetDataId = jQuery(this).attr('href').replace('#', '');
                    var targetElement = jQuery('[data-id="' + targetDataId + '"]');
                    jQuery('html, body').animate({
                        scrollTop: targetElement.offset().top
                    }, 100);
                    jQuery('li').removeClass('scrolled');
                    jQuery(this).closest('li').addClass('scrolled');
                });

                jQuery('.cmma-footer-jump-navigation').on('mouseleave', function () {
                    var liHeight = jQuery(this).find('li').outerHeight();
                    var activeLi = jQuery('li.scrolled').attr('data-id');
                    jQuery('.cmma-footer-jump-navigation ul').animate({
                        scrollTop: (parseInt(activeLi) * liHeight)
                    }, 0);
                });
            });

            document.addEventListener('DOMContentLoaded', function () {
                updateProgressBar();
                calculateLargeImageContentHeight('.collection-panel-slider-item');
            });
            window.addEventListener('resize', function() {
                calculateLargeImageContentHeight('.collection-panel-slider-item');
            });

            function calculateLargeImageContentHeight(widgetClass) {
                var $j = jQuery;
                var $widget = $j(widgetClass);
                var $content = $widget.find('.panel-collection .cmma-collection-list');

                if ($content.length) {
                    $widget.find('.collection-panel-slider-item').css({'--large-image-content-height': $content.height() + 'px'});
                }
            }

            function updateProgressBar() {
                const footerFixedLinks = document.querySelector('.cmma-footer-jump-navigation');
                const totalHeight = document.body.scrollHeight - window.innerHeight;
                const progress = (window.pageYOffset / totalHeight) * 100;
                footerFixedLinks.style.setProperty('--progress-bar-width', progress + '%');

                window.onload = function () {
                    const footerWrapper = document.querySelector('.cmma-footer-jump-navigation-wrapper');
                    const footerLinks = footerWrapper.querySelector('ul');
                    const items = footerLinks.querySelectorAll('li');
                    if (items.length > 4) {
                        footerWrapper.classList.add('overlay-item');
                    }
                };
            }

            window.addEventListener('scroll', updateProgressBar);
            window.addEventListener('resize', updateProgressBar);

            //Default hide
            let jumpNavigation = jQuery('.cmma-footer-jump-navigation').closest('.elementor-element').parent('.elementor-element');
            jumpNavigation.hide();
            jQuery(window).scroll(function (event) {
                let jumpItems = jQuery('.cmma-footer-jump-navigation-wrapper>ul>li');
				if (!jumpItems.length) {
					return
				}

                var scrollPosition = jQuery(window).scrollTop();
                var windowHeight = jQuery(window).height();
                let elementorWidgetContainer = jQuery('.elementor-scroll-widget-container').parent('.elementor-element');

				if (!elementorWidgetContainer.length) {
					elementorWidgetContainer = jQuery('.page-content .elementor-widget');;
				}

                let firstWidgetPosition = elementorWidgetContainer.first().offset().top + 100;
                let lastWidgetPosition = elementorWidgetContainer.last().offset().top + elementorWidgetContainer.last().outerHeight() + 100;

                // Check if the scroll position is within the range of the first and last widgets
                if ((scrollPosition + windowHeight) >= firstWidgetPosition && scrollPosition <= lastWidgetPosition) {
                    jumpNavigation.fadeIn();
                } else {
                    jumpNavigation.fadeOut();
                }
            });
        </script>
    <?php
    endif;
    return ob_get_clean();
}
add_shortcode('cmma_jump_navigation', 'jumpNavigationBar');