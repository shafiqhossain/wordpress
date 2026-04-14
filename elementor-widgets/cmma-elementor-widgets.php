<?php
/**
 * Plugin Name: CMMA Elementor Widgets
 * Description: Enhance Elementor with Custom Widgets for seamless design flexibility.
 * Version:     1.0.7
 * Author:      Knectar
 * Author URI:  https://www.knectar.com
 * Text Domain: cmma-elementor-widgets
 *
 * Elementor tested up to: 3.16.0
 * Elementor Pro tested up to: 3.16.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define('PLUGIN_VERSION', '1.0.5');

/**
 * Register CMMA Widgets
 *
 * Include widget file and register widget class.
 *
 * @since 1.0.0
 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
 * @return void
 */
function register_cmma_elementor_widgets( $widgets_manager ) {

	// List of widget files and their corresponding classes
	$widgets = [
		'single-video'                      => '\CMMA_SingleVideo_Widget',
		'single-image'                      => '\CMMA_SingleImage_Widget',
		'media-gallery'                     => '\CMMA_MediaGallery_Widget',
		'multiple-images'                   => '\CMMA_MultipleImages_Widget',
		'large-image'                       => '\CMMA_LargeImage_Widget',
		'accordion'                         => '\CMMA_Accordion_Widget',
		'render-to-reality'                 => '\CMMA_RenderReality_Widget',
		'two-image'                         => '\CMMA_TwoImage_Widget',
		'image-with-deep-dive'              => '\CMMA_ImageWithDeepDive_Widget',
		'image-with-story'                  => '\CMMA_ImageWithStory_Widget',
		'image-with-text'                   => '\CMMA_ImageWithText_Widget',
		'image-video-carousel'              => '\CMMA_ImageVideoCarousel_Widget',
		'hero-slider'                       => '\CMMA_HeroSlider_Widget',
		'wysiwyg'                           => '\CMMA_WYSIWYG_Widget',
		'downloadable-content'              => '\CMMA_Downloadable_Content_Widget',
		'stats'                             => '\CMMA_STATS_Widget',
		'services'                          => '\CMMA_Services_Widget',
		'social-media'                      => '\CMMA_SocialMedia_Widget',
		'markets'                           => '\CMMA_Markets_Widget',
		'quotes'                            => '\CMMA_Quotes_Widget',
		'article-listing'                   => '\CMMA_ArticleListing_Widget',
		'featured-articles'                 => '\CMMA_FeaturedArticles_Widget',
		'related-projects-and-perspectives' => '\CMMA_Related_Projects_and_Perspectives_Widget',
		'thinglink'                         => '\CMMA_ThingLink_Widget',
		'featured-text'                     => '\CMMA_FeaturedText_Widget',
		'post-listing-with-filter'          => '\CMMA_PostListingWithFilter_Widget',
		'single-collection'					=> '\CMMA_SingleCollection_Widget',
		'our-people-listing'				=> '\CMMA_OurPeopleListing_Widget',
		// Add more widgets if needed
	];

	// Loop through the widgets and register each one
	foreach ( $widgets as $dir => $class ) {
		require_once( __DIR__ . '/widgets/' . $dir . '/widget.php' );

		$widgets_manager->register( new $class() );
	}
}
add_action( 'elementor/widgets/register', 'register_cmma_elementor_widgets' );

/**
 * Add Elementor Widget Categories.
 *
 * Function to add a custom category for Elementor widgets.
 *
 * @since 1.0.0
 * @param \Elementor\Elements_Manager $elements_manager Elementor elements manager.
 * @return void
 */
function add_elementor_widget_categories( $elements_manager ) {
	// Add a custom category for Elementor widgets.
	$categories = [ 'cmma-widgets' => [
		'title' => esc_html__( 'CMMA', 'cmma-elementor-widgets' ),
		'icon' => 'fa fa-plug',
	] ];

	// Set the category to the first option
	$categories = array_merge( $categories, $elements_manager->get_categories() );
	$set_categories = function ( $categories ) {
		$this->categories = $categories;
	};

	$set_categories->call( $elements_manager, $categories );
}
add_action( 'elementor/elements/categories_registered', 'add_elementor_widget_categories' );

/**
 * Unregister CMMA widgets
 *
 * @since 1.0.0
 * @param \Elementor\Widgets_Manager $widgets_manager
 * @return void
 */
function unregister_cmma_elementor_widgets( $widgets_manager ) {
	if ( 'project' === get_post_type() ) {
		$widgets_manager->unregister( 'image-with-text' );
	}
}
add_action( 'elementor/widgets/register', 'unregister_cmma_elementor_widgets' );

/**
 * Enqueue styles and scripts for the CMMA Elementor Widgets.
 *
 * This function is hooked into the 'wp_enqueue_scripts' action, which is
 * fired on the front end when scripts and styles are enqueued.
 */
function register_cmma_assets() {
	$widgets_style = [
		'two-image',
		'single-image',
		'image-with-deep-dive',
		'image-with-story',
		'image-with-text',
		'large-image',
		'accordion',
		'render-to-reality',
		'multiple-images',
		'media-gallery',
		'image-video-carousel',
		'hero-slider',
		'wysiwyg',
		'downloadable-content',
		'stats',
		'services',
		'social-media',
		'markets',
		'quotes',
		'article-listing',
		'featured-articles',
		'related-projects-and-perspectives',
		'thinglink',
		'featured-text',
		'post-listing-with-filter',
		'single-collection',
		'our-people-listing',
		'single-video',

	];

	// Register widget styles
	foreach ( $widgets_style as $style ) {
		$handle = 'widget-' . $style . '-style';
		$file_path = plugins_url('widgets/' . $style . '/assets/css/style.css', __FILE__);

		wp_register_style($handle, $file_path, [], PLUGIN_VERSION);
	}

	$widget_js = [
		'accordion',
		'markets',
		'stats',
		'services',
		'media-gallery',
		'image-with-story',
		'image-video-carousel',
		'hero-slider',
		'downloadable-content',
		'related-projects-and-perspectives',
		'wysiwyg',
		'quotes',
		'article-listing',
		'featured-articles',
		'render-to-reality',
		'post-listing-with-filter',
		'single-collection',
		'our-people-listing',
	];

	// Register widget scripts
	foreach ( $widget_js as $script ) {
		$handle    = 'widget-' . $script . '-script';
		$file_path = plugins_url('widgets/' . $script . '/assets/js/custom.js', __FILE__);

		wp_register_script($handle, $file_path, [], PLUGIN_VERSION, true);
	}

	wp_enqueue_style( 'cmma-elementor-widgets-style', plugin_dir_url( __FILE__ ) . 'css/style.css?v=' . PLUGIN_VERSION );
	wp_register_script( 'cmma-elementor-widgets-slider-script', plugin_dir_url( __FILE__ ) . 'js/slider.js?v=' . PLUGIN_VERSION, [], '', true);
	wp_enqueue_script( 'cmma-elementor-widgets-script', plugin_dir_url(__FILE__) . 'js/custom.js?v=' . PLUGIN_VERSION, [], '', true );
	wp_enqueue_script( 'jquery' );

	// Enqueue Slick Slider JS

  	wp_enqueue_script( 'slick-js', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js', array( 'jquery' ), '1.8.1', true );
	//Enqueue Slick Slider CSS
	wp_enqueue_style( 'slick-css', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.css', [], '1.8.1');

	// Enqueue ProgressBar.js
	wp_enqueue_script( 'progressbar-js', 'https://cdnjs.cloudflare.com/ajax/libs/progressbar.js/1.1.0/progressbar.min.js', array(), '1.1.0', true);
}
add_action( 'wp_enqueue_scripts', 'register_cmma_assets', 11 );

/**
 * Retrieve image URL and srcset for responsive images in CMMA Elementor Widgets.
 *
 * Given an attachment ID and optional image size, this function fetches the image URL,
 * calculates the srcset, and returns an array with the URL and srcset.
 *
 * @param int    $attachment_id The ID of the image attachment.
 * @param string $image_size    Optional. The image size to retrieve. Default is 'full'.
 *
 * @return array An array containing the image URL and srcset.
 */
function cmma_elementor_widgets_get_responsive_image_data( $attachment_id, $image_size = 'full' ) {
	// Retrieve image data and metadata
	$image_data     = wp_get_attachment_image_src( $attachment_id, $image_size );
	$image_metadata = wp_get_attachment_metadata( $attachment_id );
	$image_mime     = get_post_mime_type( $attachment_id );

	//We don't be resizing/compressing gif since there might be chances of lossing animation effects.
	if ( $image_mime === 'image/gif' ) {
		return [
			'url' => $image_data[0],
			'srcset' => $image_data[0] . ' 320w',
		];
	}

	if ( ! isset( $image_metadata['sizes'] ) ) {
		return [
			'url'    => '',
			'srcset' => '',
		];
	}

	// Extract width and height information
	$image_info = array(
		0 => $image_metadata['width'],
		1 => $image_metadata['height'],
	);

	// Calculate the srcset
	$srcset = wp_calculate_image_srcset( $image_info, $image_data[0], $image_metadata, $attachment_id );

	// Return the image URL and srcset in an array
	return [
		'url'    => $image_data[0],
		'srcset' => $srcset,
	];
}

/**
 * Returns the SVG of the icon used in CMMA Elementor Widgets
 *
 * @param string $name Name of the icon
 * @param string $fill Optional. The color of the icon
 *
 * @return string Returns the svg of the icon
 */
function cmma_elementor_icons($name, $fill = 'currentColor' ) {
	switch ($name) {
		case 'arrow':
			return '<svg width="8" height="24" viewBox="0 0 8 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M0 18.0072L6.18055 13.484L0 8.99255V7L8 12.9147V14.085L0 20V18.0072Z" fill="' . $fill . '" />
			</svg>';
			break;
		case 'arrow-up-right':
			return '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
				<g clip-path="url(#clip0_3696_1025)">
					<path d="M1 13L13 1" stroke="' . $fill . '" stroke-width="1.5"/>
					<path d="M1.92188 1H12.9988V12.0769" stroke="' . $fill . '" stroke-width="1.5"/>
				</g>
				<defs>
					<clipPath id="clip0_3696_1025">
						<rect width="14" height="14" fill="' . $fill . '"/>
					</clipPath>
				</defs>
			</svg>
      ';
			break;
		case 'search':
			return '<svg width="22" height="23" viewBox="0 0 22 23" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M21 21.5L14.6572 14.6572M14.6572 14.6572C15.4001 13.9143 15.9894 13.0324 16.3914 12.0618C16.7935 11.0911 17.0004 10.0508 17.0004 9.00021C17.0004 7.9496 16.7935 6.90929 16.3914 5.93866C15.9894 4.96803 15.4001 4.08609 14.6572 3.34321C13.9143 2.60032 13.0324 2.01103 12.0618 1.60898C11.0911 1.20693 10.0508 1 9.00021 1C7.9496 1 6.90929 1.20693 5.93866 1.60898C4.96803 2.01103 4.08609 2.60032 3.34321 3.34321C1.84288 4.84354 1 6.87842 1 9.00021C1 11.122 1.84288 13.1569 3.34321 14.6572C4.84354 16.1575 6.87842 17.0004 9.00021 17.0004C11.122 17.0004 13.1569 16.1575 14.6572 14.6572Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>';
			break;

		case 'plus':
			return '<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
				<rect x="4.28516" width="1.42857" height="10"  fill="currentColor"/>
				<rect x="10" y="4.28564" width="1.42857" height="10" transform="rotate(90 10 4.28564)"  fill="currentColor"/>
			</svg>';
			break;

		case 'replay':
			return '<svg width="512" height="512" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M256.125 -0.31245C257.12 -0.310295 258.114 -0.308139 259.139 -0.305919C273.622 -0.255552 287.765 0.103708 302 3.00005C302.998 3.19341 303.996 3.38677 305.024 3.58599C351.781 12.8261 392.252 33.8384 428 65.0001C428.503 65.4388 429.007 65.8776 429.525 66.3296C434.763 70.9235 439.742 75.5716 444.219 80.9141C445.49 82.4032 446.809 83.8528 448.156 85.2735C460.261 98.0465 470.52 112.598 479 128C479.7 129.26 479.7 129.26 480.414 130.546C487.809 143.968 494.101 157.474 499 172C499.413 173.223 499.413 173.223 499.835 174.47C503.073 184.241 505.605 194.122 507.75 204.188C507.949 205.112 508.148 206.037 508.352 206.99C511.645 223.196 512.375 239.13 512.313 255.625C512.31 256.643 512.308 257.662 512.306 258.711C512.256 273.335 511.942 287.628 509 302C508.807 302.997 508.613 303.993 508.414 305.02C499.192 351.691 478.032 394.858 445.324 429.523C443.404 431.57 441.593 433.646 439.813 435.813C437.205 438.894 434.304 441.473 431.227 444.078C429.689 445.406 428.185 446.774 426.711 448.172C413.941 460.27 399.396 470.524 384 479C382.74 479.7 382.74 479.7 381.454 480.414C368.032 487.809 354.526 494.101 340 499C338.778 499.413 338.778 499.413 337.53 499.835C327.759 503.073 317.878 505.605 307.813 507.75C306.888 507.949 305.963 508.148 305.011 508.352C288.973 511.611 273.198 512.376 256.875 512.313C255.88 512.31 254.886 512.308 253.861 512.306C239.378 512.256 225.235 511.896 211 509C210.002 508.807 209.004 508.613 207.976 508.414C149.773 496.912 95.0087 465.827 58.0001 419C57.2743 418.142 56.5486 417.283 55.8008 416.398C20.6139 374.764 -0.262151 317.559 -0.0246057 263.105C-0.0228257 262.322 -0.021038 261.54 -0.0192041 260.734C-0.0145968 258.823 -0.00752994 256.911 5.25471e-05 255C11.4566 254.977 22.913 254.959 34.3696 254.948C39.6887 254.943 45.0078 254.936 50.327 254.925C55.4557 254.914 60.5844 254.908 65.7132 254.905C67.6745 254.903 69.6358 254.9 71.5971 254.894C74.3342 254.887 77.0713 254.886 79.8084 254.887C80.6277 254.883 81.4469 254.879 82.291 254.876C87.886 254.886 87.886 254.886 89.0001 256C89.2403 257.907 89.4213 259.822 89.5782 261.738C90.8821 276.531 92.5181 290.773 97.0001 305C97.3631 306.161 97.3631 306.161 97.7335 307.346C105.028 330.043 116.759 350.456 133.027 367.871C134.736 369.715 136.307 371.601 137.875 373.563C141.979 378.377 147.032 382.124 152 386C152.918 386.734 153.836 387.467 154.781 388.223C174.552 403.621 196.765 413.064 221 419C221.703 419.179 222.406 419.359 223.13 419.544C244.369 424.758 270.827 424.154 292 419C292.832 418.801 293.664 418.603 294.522 418.398C337.204 407.9 374.627 381.36 398 344C413.932 316.941 422.453 288.029 422.375 256.563C422.375 255.377 422.375 255.377 422.374 254.168C422.34 241.086 421.277 228.711 418 216C417.818 215.254 417.636 214.507 417.449 213.738C407.312 172.729 379.631 136.292 344 114C328.994 105.165 313.718 98.5754 296.875 94.2501C296.1 94.0511 295.325 93.8521 294.527 93.647C281.964 90.6026 269.446 89.5859 256.563 89.6251C255.822 89.6263 255.081 89.6275 254.317 89.6287C226.545 89.7509 200.334 96.4538 176 110C174.903 110.598 173.806 111.196 172.676 111.813C166.342 115.373 160.587 119.358 155 124C156.663 125.691 158.331 127.378 160 129.063C160.458 129.529 160.915 129.995 161.387 130.475C164.772 133.883 168.316 137.055 171.947 140.199C175.292 143.134 178.517 146.193 181.75 149.25C188.448 155.558 195.198 161.804 202 168C198.507 170.329 196.688 170.392 192.536 170.696C191.547 170.772 191.548 170.772 190.539 170.85C188.326 171.018 186.113 171.173 183.899 171.329C182.314 171.446 180.729 171.565 179.144 171.684C174.835 172.006 170.524 172.317 166.214 172.626C161.703 172.951 157.193 173.285 152.683 173.619C145.104 174.178 137.524 174.73 129.944 175.278C120.224 175.98 110.505 176.693 100.786 177.41C92.4251 178.026 84.0638 178.638 75.7024 179.249C73.0183 179.445 70.3342 179.642 67.6502 179.839C63.425 180.148 59.1996 180.455 54.974 180.76C53.4256 180.872 51.8772 180.985 50.3289 181.098C48.21 181.254 46.091 181.406 43.9718 181.558C42.7875 181.644 41.6032 181.73 40.383 181.818C36.9153 182.005 33.4721 182.031 30.0001 182C29.8932 171.756 30.1906 161.604 30.8104 151.376C30.8978 149.879 30.9848 148.383 31.0715 146.886C31.3051 142.875 31.5441 138.863 31.7845 134.852C32.0363 130.638 32.2837 126.424 32.5318 122.211C32.9481 115.153 33.3682 108.095 33.7903 101.037C34.2785 92.8748 34.7603 84.7124 35.2388 76.5497C35.6508 69.5233 36.0663 62.4972 36.4847 55.4712C36.7341 51.2818 36.9824 47.0924 37.2276 42.9028C37.4575 38.9766 37.6917 35.0506 37.9293 31.1249C38.0159 29.6826 38.101 28.2402 38.1845 26.7976C38.2983 24.8349 38.418 22.8725 38.5381 20.9101C38.6363 19.2608 38.6363 19.2608 38.7364 17.5781C39 15.0001 39.0001 15.0001 40.0001 13.0001C43.5748 14.5181 45.8582 16.6158 48.585 19.358C49.4856 20.2587 50.3863 21.1595 51.3142 22.0876C52.2877 23.0713 53.2611 24.0552 54.2344 25.0391C55.2372 26.0456 56.2404 27.0516 57.244 28.0573C59.8788 30.6997 62.5075 33.348 65.135 35.9977C67.8195 38.7026 70.5096 41.4019 73.1993 44.1016C78.4718 49.3956 83.7379 54.6958 89.0001 60C93.0916 57.2998 96.976 54.4897 100.813 51.4375C119.914 36.5736 141.359 24.522 164 16C164.891 15.6638 165.782 15.3275 166.7 14.981C195.675 4.25282 225.354 -0.432514 256.125 -0.31245Z" fill="white"/>
			</svg>
			';
			break;

		case 'arrow-left':
			return '<svg width="8" height="24" viewBox="0 0 8 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M8 18.0072L1.81945 13.484L8 8.99255V7L0 12.9147V14.085L8 20V18.0072Z" fill="white"/>
			</svg>';
			break;

		case 'mute':
			return '<svg class="mute" xmlns="http://www.w3.org/2000/svg" width="19" height="16" viewBox="0 0 19 16" fill="none">
				<path d="M12.4 12L11 10.6L13.6 8L11 5.4L12.4 4L15 6.6L17.6 4L19 5.4L16.4 8L19 10.6L17.6 12L15 9.4L12.4 12ZM0 11V5H4L9 0V16L4 11H0Z" fill="' . $fill . '" />
			</svg>';
			break;

		case 'unmute':
			return '<svg class="unmute" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M14 3.23047V5.29047C16.89 6.15047 19 8.83047 19 12.0005C19 15.1705 16.89 17.8405 14 18.7005V20.7705C18 19.8605 21 16.2805 21 12.0005C21 7.72047 18 4.14047 14 3.23047ZM16.5 12.0005C16.5 10.2305 15.5 8.71047 14 7.97047V16.0005C15.5 15.2905 16.5 13.7605 16.5 12.0005ZM3 9.00047V15.0005H7L12 20.0005V4.00047L7 9.00047H3Z" fill="' . $fill . '" />
			</svg>';
			break;

		case 'pause':
			return '<svg class="pause" xmlns="http://www.w3.org/2000/svg" width="10" height="15" viewBox="0 0 10 15" fill="none">
				<rect width="3.33333" height="14.4444" fill="' . $fill . '" />
				<rect x="6.6665" width="3.33333" height="14.4444" fill="' . $fill . '" />
			</svg>';
			break;

		case 'play':
			return '<svg class="play" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M8 5.14062V19.1406L19 12.1406L8 5.14062Z" fill="' . $fill . '" />
			</svg>';
			break;

		case 'close':
			return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
				<rect x="1.41406" width="20" height="2" transform="rotate(45 1.41406 0)" fill="' . $fill . '" />
				<rect y="14.1421" width="20" height="2" transform="rotate(-45 0 14.1421)" fill="' . $fill . '" />
			</svg>';
			break;

		case 'download':
			return '<svg width="14" height="13" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M6.88209 9.13281C6.89609 9.15024 6.91399 9.16434 6.93442 9.17403C6.95485 9.18373 6.97727 9.18877 7 9.18877C7.02273 9.18877 7.04515 9.18373 7.06558 9.17403C7.08601 9.16434 7.10391 9.15024 7.11791 9.13281L9.21417 6.54974C9.29091 6.45495 9.22166 6.31458 9.09626 6.31458H7.70936V0.145833C7.70936 0.065625 7.64198 0 7.55963 0H6.43663C6.35428 0 6.2869 0.065625 6.2869 0.145833V6.31276H4.90374C4.77834 6.31276 4.70909 6.45313 4.78583 6.54792L6.88209 9.13281ZM13.8503 8.49479H12.7273C12.6449 8.49479 12.5775 8.56042 12.5775 8.64063V11.4479H1.42246V8.64063C1.42246 8.56042 1.35508 8.49479 1.27273 8.49479H0.149733C0.0673797 8.49479 0 8.56042 0 8.64063V12.25C0 12.5727 0.267647 12.8333 0.59893 12.8333H13.4011C13.7324 12.8333 14 12.5727 14 12.25V8.64063C14 8.56042 13.9326 8.49479 13.8503 8.49479Z" fill="' . $fill . '"/>
			</svg>';
			break;

		case 'gallery':
			return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<g clip-path="url(#clip0_3763_1451)">
					<path d="M5 19V23H23V5.97H19M2 18L7 12L10 15L14 10L19 16M1 1H19V19H1V1ZM6 8C6.26522 8 6.51957 7.89464 6.70711 7.70711C6.89464 7.51957 7 7.26522 7 7C7 6.73478 6.89464 6.48043 6.70711 6.29289C6.51957 6.10536 6.26522 6 6 6C5.73478 6 5.48043 6.10536 5.29289 6.29289C5.10536 6.48043 5 6.73478 5 7C5 7.26522 5.10536 7.51957 5.29289 7.70711C5.48043 7.89464 5.73478 8 6 8Z" stroke="white" stroke-width="2"/>
				</g>
				<defs>
					<clipPath id="clip0_3763_1451">
						<rect width="24" height="24" fill="white"/>
					</clipPath>
				</defs>
			</svg>';
			break;

		case 'minus':
			return '<svg width="17" height="3" viewBox="0 0 17 3" fill="none" xmlns="http://www.w3.org/2000/svg">
				<rect y="0.751953" width="17" height="1.49515" fill="' . $fill . '"/>
			</svg>';
			break;

		case 'loader':
			return '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="50px" height="50px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
				<g transform="rotate(0 50 50)">
					<rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#b1afaf">
						<animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.9166666666666666s" repeatCount="indefinite"></animate>
					</rect>
				</g>
				<g transform="rotate(30 50 50)">
					<rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#b1afaf">
						<animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.8333333333333334s" repeatCount="indefinite"></animate>
					</rect>
				</g>
				<g transform="rotate(60 50 50)">
					<rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#b1afaf">
						<animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.75s" repeatCount="indefinite"></animate>
					</rect>
				</g>
				<g transform="rotate(90 50 50)">
					<rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#b1afaf">
						<animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.6666666666666666s" repeatCount="indefinite"></animate>
					</rect>
				</g>
				<g transform="rotate(120 50 50)">
					<rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#b1afaf">
						<animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.5833333333333334s" repeatCount="indefinite"></animate>
					</rect>
				</g>
				<g transform="rotate(150 50 50)">
					<rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#b1afaf">
						<animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.5s" repeatCount="indefinite"></animate>
					</rect>
				</g>
				<g transform="rotate(180 50 50)">
					<rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#b1afaf">
						<animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.4166666666666667s" repeatCount="indefinite"></animate>
					</rect>
				</g>
				<g transform="rotate(210 50 50)">
					<rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#b1afaf">
						<animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.3333333333333333s" repeatCount="indefinite"></animate>
					</rect>
				</g>
				<g transform="rotate(240 50 50)">
					<rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#b1afaf">
						<animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.25s" repeatCount="indefinite"></animate>
					</rect>
				</g>
				<g transform="rotate(270 50 50)">
					<rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#b1afaf">
						<animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.16666666666666666s" repeatCount="indefinite"></animate>
					</rect>
				</g>
				<g transform="rotate(300 50 50)">
					<rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#b1afaf">
						<animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.08333333333333333s" repeatCount="indefinite"></animate>
					</rect>
				</g>
				<g transform="rotate(330 50 50)">
					<rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#b1afaf">
						<animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"></animate>
					</rect>
				</g>
			</svg>';
			break;

		default:
			return '';
	}
}

function mailtrap( $phpmailer ) {
	$phpmailer->isSMTP();
	$phpmailer->Host = 'sandbox.smtp.mailtrap.io';
	$phpmailer->SMTPAuth = true;
	$phpmailer->Port = 2525;
	$phpmailer->Username = '155f863afe5e39';
	$phpmailer->Password = '0cd60d16f6dc39';
}
add_action( 'phpmailer_init', 'mailtrap' );

/**
 * Custom notification attachments for Gravity Forms.
 *
 * @param array $notification The notification settings.
 * @param array $form         The form data.
 * @param array $entry        The entry data.
 *
 * @return array The updated notification settings.
 */
function cmma_elementor_widgets_custom_notification_attachments( $notification, $form, $entry ) {
	$attachments = [];
	$upload_root = RGFormsModel::get_upload_root();
	$pdf_id      = get_transient( 'cmma_elementor_pdf_id' );

	if ( $pdf_id ) {
		$url = get_attached_file( $pdf_id );
		$attachment = preg_replace( '|^(.*?)/gravity_forms/|' , $upload_root, $url );
		$attachments[] = $attachment;
	}

	$notification['attachments'] = $attachments;

	return $notification;
}
// Hook the function to the 'gform_notification' filter
add_filter( 'gform_notification', 'cmma_elementor_widgets_custom_notification_attachments', 10, 3 );

/**
 * Checks if a given URL belongs to the current site.
 *
 * @param string $url The URL to be checked.
 * @return bool True if the URL belongs to the current site, false otherwise.
 */
function cmma_elementor_widgets_check_external_links($url) {
	$site_url = site_url();
	if (strpos($url, $site_url) !== 0) {
		return true;
	}
	return false;
}

/**
 * Returns embedded code of the iframe by adding custom params
 *
 * @param string $video URL of the video
 * @param string $type Type of the video
 *
 * @return string Returns the html code of the video
 */
function cmma_video_oembed_get( $video, $type , $autoplay = 1) {
	// Extract the 'clip' and 'clipt' parameters from the video URL
	$clip = null;
	$clipt = null;

	// Parse the URL to get query parameters
	$parsed_url = get_url_params($video);
	if (count($parsed_url)) {
		if (isset($parsed_url['clip'])) {
			$clip = $parsed_url['clip'];
		}
		if (isset($parsed_url['clipt'])) {
			$clipt = $parsed_url['clipt'];
		}
	}

	if (str_contains($video, "/embed/")) {
		$query_string = http_build_query($parsed_url, '', '&', PHP_QUERY_RFC3986);
		$video = "https://youtu.be/"._cmma_get_youtube_video_id($video) . '?' .$query_string;
	}


	$output = wp_oembed_get( $video );
	$params = [
		'youtube' => [
			'rel'				=> '0',
			'loop'				=> '1',
			'mute'				=> $autoplay,
			'autoplay'			=> $autoplay,
			'controls'			=> '0',
			'enablejsapi'		=> '1',
			'playsinline'		=> '1',
			'modestbranding'	=> '1',
			'playlist'			=> _cmma_get_youtube_video_id($video),
			'clip'				=> $clip,
			'clipt'				=> $clipt
		]
	];

	if ( is_wp_error( $output ) ) {
		return;
	}

	if ( isset( $params[ $type ] ) ) {
		$params_string = '';

		foreach ( $params[ $type ] as $key => $val ) {
			$params_string .= '&'. $key .'='. $val;
		}

		if ( $params_string ) {
			$output = str_replace( '?feature=oembed', '?feature=oembed'. $params_string, $output );
		}
	}

	return $output;
}

function get_url_params($url) {
    $parsed_url = parse_url($url);
    $params = [];

    // Process both 'query' and 'fragment' parts
    foreach (['query', 'fragment'] as $part) {
        if (isset($parsed_url[$part])) {
            $str = str_replace(['&amp;', '#038;', '038;'], '&', $parsed_url[$part]);
            parse_str($str, $parsed_params);
            $params += $parsed_params;
        }
    }

    return $params;
}

function _cmma_get_youtube_video_id($url) {
    $pattern = '/^((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube\.com|youtu.be))(\/(?:[\w\-]+\?v=|embed\/|v\/)?)([\w\-]+)(\S+)?$/';
    if (preg_match($pattern, $url, $matches)) {
        return $matches[5];
    }
    return false;
}

// Removes delimiter from excerpt
add_filter( 'excerpt_more', '__return_empty_string' );

add_action( 'wp_ajax_cmma_elementor_widgets_landing_page_load_posts', 'cmma_elementor_widgets_landing_page_load_posts' );
add_action( 'wp_ajax_nopriv_cmma_elementor_widgets_landing_page_load_posts', 'cmma_elementor_widgets_landing_page_load_posts' );

function cmma_elementor_widgets_landing_page_load_posts() {
	$paged       = isset( $_POST['page'] ) ? $_POST['page'] : 1;
	$category_id = isset( $_POST['category_id'] ) ? $_POST['category_id'] : 0;
	$query       = new WP_Query( array(
		'posts_per_page' => 8,
		'post_status'    => 'publish',
		'paged'          => $paged,
		'cat'            => $category_id
	) );

	$html = '';
	$post_count = 0;

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
            $query->the_post();
            $html .= cmma_elementor_widgets_generate_post_html( $post );
            $post_count++;
        }
		wp_reset_postdata();
	}

	$response = array(
		'html' => $html,
		'post_count' => $post_count
	);

	wp_send_json( $response );
}


add_action('wp_ajax_cmma_elementor_widgets_landing_page_search', 'cmma_elementor_widgets_landing_page_search');
add_action('wp_ajax_nopriv_cmma_elementor_widgets_landing_page_search', 'cmma_elementor_widgets_landing_page_search');

function cmma_elementor_widgets_landing_page_search() {
	$search_query = isset($_POST['search_query']) ? sanitize_text_field($_POST['search_query']) : '';
	$category_id = isset($_POST['category_id']) ? $_POST['category_id'] : '';
	$args = array(
		'post_type' 	 => 'post',
		'posts_per_page' => -1,
		's'				 => $search_query,
		'cat' 			 => $category_id
	);
	$query = new WP_Query($args);
	$html = '';
	$post_count = 0;

	if ($query->have_posts()) {
		while ( $query->have_posts() ) {
            $query->the_post();
            $html .= cmma_elementor_widgets_generate_post_html( $post );
            $post_count++;
        }
	} else {
		$html = 'No results found.';
	}

	$response = array(
		'html' => $html,
		'post_count' => $post_count
	);

	wp_send_json($response);
	wp_reset_postdata();
	wp_die();
}

add_action( 'wp_ajax_cmma_elementor_widgets_landing_page_filter_posts', 'cmma_elementor_widgets_landing_page_filter_posts' );
add_action( 'wp_ajax_nopriv_cmma_elementor_widgets_landing_page_filter_posts', 'cmma_elementor_widgets_landing_page_filter_posts' );

function cmma_elementor_widgets_landing_page_filter_posts() {
	$category_id = isset( $_POST['category_id'] ) && ! empty( $_POST['category_id'] ) ? $_POST['category_id'] : 'all';

	$args = array(
		'posts_per_page' => 8,
		'post_status'    => 'publish'
	);

	if ( $category_id != 'all' ) {
		$args['cat'] = $category_id;
	}

	$query = new WP_Query( $args );
	$html = '';
	$post_count = 0;

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
            $query->the_post();
            $html .= cmma_elementor_widgets_generate_post_html( $post );
            $post_count++;
        }
		wp_reset_postdata();
	} else {
		$html = 'No posts found.';
	}

	$response = array(
		'html' => $html,
		'post_count' => $post_count
	);

	wp_send_json( $response );
}

function cmma_elementor_widgets_generate_post_html( $post ) {
    $img = get_the_post_thumbnail( $post->ID, 'medium' );
    ob_start();
    ?>
		<div class="cmma-post">
			<div class="cmma-post-date">
				<?= get_the_date( 'n.j.y', $post ); ?>
			</div>
			<div class="cmma-post-content">
				<h3><a href="<?= get_permalink( $post ); ?>"><?= get_the_title( $post ); ?></a></h3>
				<div class="cmma-date-wrapper">
					<div class="cmma-mobile-date"><?= get_the_date( 'n.j.y', $post ); ?></div>
					<a href="<?= get_permalink( $post ); ?>" class="cmma-button cmma-button-type-text">
						<span class="cmma-button-text">Read More</span>
						<span class="cmma-button-icon">
							<svg width="8" height="24" viewBox="0 0 8 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M0 18.0072L6.18055 13.484L0 8.99255V7L8 12.9147V14.085L0 20V18.0072Z" fill="currentColor"></path>
							</svg>
						</span>
					</a>
				</div>
			</div>
			<div class="cmma-post-img">
				<a href="<?= get_permalink( $post ); ?>">
					<?= $img; ?>
				</a>
			</div>
		</div>
    <?php
    return ob_get_clean();
}

function cmma_elementor_widgets_our_people_posts_count() {
    $args = array(
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'post_type'      => 'member',
        'meta_key'       => 'last_name',
        'orderby'        => 'meta_value',
        'order'          => 'ASC',
        'meta_query'     => array(
            array(
                'key'     => 'show_bio',
                'value'   => '1',
                'compare' => '=='
            ),
        ),
    );

    $query = new WP_Query($args);
    $total_posts = $query->found_posts;

    return $total_posts;
}


add_action( 'wp_ajax_cmma_elementor_widgets_our_people_posts', 'cmma_elementor_widgets_our_people_posts' );
add_action( 'wp_ajax_nopriv_cmma_elementor_widgets_our_people_posts', 'cmma_elementor_widgets_our_people_posts' );

function cmma_elementor_widgets_our_people_posts() {
	$paged	= $_POST['page'];
	$category_id = isset( $_POST['category_id'] ) && ! empty( $_POST['category_id'] ) ? $_POST['category_id'] : '';
	if ($category_id == 'view-all') {
		if (isset($_POST['videos'])) {
			$videos_and_images = $_POST['videos'];
		}
		include(plugin_dir_path(__FILE__).'widgets/our-people-listing/peple-listing.php');
		wp_die();
	} elseif ($category_id == 'all' && $paged == '1') {
		$paged++;
	}

	$args = array(
		'posts_per_page'	=> 24,
		'post_status'		=> 'publish',
		'post_type'			=> 'member',
		'meta_key'			=> 'last_name',
		'orderby'			=> 'meta_value',
		'order'				=> 'ASC',
		'paged'				=> $paged,
		'meta_query'   		=> array(
			array(
				'key'       => 'show_bio',
				'value'		=> '1',
				'compare'	=> '=='
			),
		),
	);

	if ($category_id && $category_id != 'all') {
		$args['tax_query'] = array(
			array(
				'taxonomy'	=> 'role',
				'field'    	=> 'id',
				'terms'    	=> $category_id,
			),
		);
	}
	$query = new WP_Query( $args );
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$postID = get_the_ID();
			$modal_id = 'cmma-modal-' . $postID;
			$image_id = get_post_thumbnail_id($postID);
			$image_info = cmma_elementor_widgets_get_responsive_image_data($image_id, 'full');
			$custom_fields	= get_fields( $postID );
			$member_landscape_image = get_field('member_landscape_image');
			$landscape_image = get_field('member_landscape_image', $postID);
			$classes = array('red', 'green', 'yellow');
        	$random_class = $classes[array_rand($classes)];
			?>
				<div class="cmma-collection panel-content cmma-post cmma-members-list-block <?= $random_class ?>" data-post-id="<?= $postID; ?>">
					<a href="<?= get_permalink($postID); ?>">
						<?php if ($landscape_image): ?>
							<div class="landscape-img">
								<img src="<?php echo esc_url($landscape_image); ?>" />
							</div>
						<?php elseif (isset($image_info) && !empty($image_info['srcset'])) : ?>
							<img srcset="<?= esc_attr($image_info['srcset']) ?>" src="<?= esc_url($image_info['url']) ?>" loading="lazy" height="100%" width="100%" alt="" />
						<?php else: ?>
							<img srcset="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/placeholder.jpg" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/placeholder.jpg" loading="lazy" height="100%" width="100%" alt="" />
						<?php endif;  ?>
						<p><?= $custom_fields['first_name']; ?> <?= $custom_fields['last_name']; ?></p>
					</a>
				</div>
			<?php
		}
		wp_reset_postdata();
	}

	die();
}

add_action('wp_ajax_cmma_elementor_widgets_our_people_page_search', 'cmma_elementor_widgets_our_people_page_search');
add_action('wp_ajax_nopriv_cmma_elementor_widgets_our_people_page_search', 'cmma_elementor_widgets_our_people_page_search');

function cmma_elementor_widgets_our_people_page_search() {
	$search_query = $_POST['search_query'];
	$category_id = isset( $_POST['category_id'] ) && ! empty( $_POST['category_id'] ) ? $_POST['category_id'] : '';
	$args = array(
		'posts_per_page'	=> -1,
		'post_type'			=> 'member',
		'meta_query'		=> array(
			'relation'	=> 'AND',
			array(
				'relation'	=> 'OR',
				array(
					'key'		=> 'last_name',
					'value'		=> $search_query,
					'compare'	=> 'LIKE',
				),
				array(
					'key'		=> 'first_name',
					'value'		=> $search_query,
					'compare'	=> 'LIKE',
				),
			),
			array(
				'key'		=> 'show_bio',
				'value'		=> '1',
				'compare'	=> '==',
			),
		),
	);


	if ($category_id && $category_id != 'all') {
		$args['tax_query'] = array(
				array(
					'taxonomy'	=> 'role',
					'field'    	=> 'id',
					'terms'    	=> $category_id,
				),
		);
	}
	$query = new WP_Query($args);
	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$postID = get_the_ID();
			$modal_id = 'cmma-modal-' . $postID;
			$image_id = get_post_thumbnail_id($postID);
			$image_info = cmma_elementor_widgets_get_responsive_image_data($image_id, 'full');
			$custom_fields	= get_fields( $postID );
			$classes = array('red', 'green', 'yellow');
			$random_class = $classes[array_rand($classes)];
			?>
				<div class="cmma-collection panel-content cmma-post cmma-members-list-block <?= $random_class ?>" data-post-id="<?= $postID; ?>">
					<a href="<?= get_permalink($postID); ?>">
						<?php if (isset($image_info) && !empty($image_info['srcset'])) : ?>
							<img srcset="<?= esc_attr($image_info['srcset']) ?>" src="<?= esc_url($image_info['url']) ?>" loading="lazy" height="100%" width="100%" alt="" />
						<?php endif; ?>
						<p><?= $custom_fields['first_name']; ?> <?= $custom_fields['last_name']; ?></p>
					</a>
				</div>
			<?php
		}
		wp_reset_postdata();
	}
	die();
}



add_action('wp_ajax_cmma_elementor_widgets_our_work_page_filter_posts', 'cmma_elementor_widgets_our_work_page_filter_posts');
add_action('wp_ajax_nopriv_cmma_elementor_widgets_our_work_page_filter_posts', 'cmma_elementor_widgets_our_work_page_filter_posts');

function cmma_elementor_widgets_our_work_page_filter_posts() {
	$post_type = isset($_POST['post_type']) ? $_POST['post_type'] : '';
	$market_id = isset($_POST['market_id']) ? $_POST['market_id'] : '';

	$default_posts = isset($_POST['default_posts']) ? $_POST['default_posts'] : '';

	if ($post_type=='all') {
		$post_type = array('project', 'perspective');
	}

	$args = array(
		'post_type'      	=> $post_type,
		'posts_per_page'	=> 24,
		'orderby'        	=> 'rand',
	);

	if (isset($market_id) && !empty($market_id)) {
		$args['meta_query'] = array(
			array(
				'key'     	=> 'select_market_post',
				'value'   	=> $market_id,
				'compare'	=> 'LIKE',
			),
		);
	}

	$query = new WP_Query($args);

	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$post_type = get_post_type();
			$image_id = get_post_thumbnail_id(get_the_ID());
			$image_info = cmma_elementor_widgets_get_responsive_image_data($image_id, 'full');
			$card_type = get_field('select_card', get_the_ID());
			$post_title = get_the_title();
			echo generate_work_html($post_type, $card_type, $image_info, $post,$post_title);
		}
		wp_reset_postdata();
	} else {
		echo 'No posts found.';
	}
	die();
}


add_action('wp_ajax_cmma_elementor_widgets_content_listing_load_more_posts', 'cmma_elementor_widgets_content_listing_load_more_posts');
add_action('wp_ajax_nopriv_cmma_elementor_widgets_content_listing_load_more_posts', 'cmma_elementor_widgets_content_listing_load_more_posts');

function cmma_elementor_widgets_content_listing_load_more_posts() {
	$default_posts = isset($_POST['default_posts']) ? $_POST['default_posts'] : '';
	$post_type = isset($_POST['post_type']) ? $_POST['post_type'] : '';
	$market_id = isset($_POST['market_id']) ? $_POST['market_id'] : '';
	$myArray = explode(',', $default_posts);
	$defaultPosts = array();
	if ($post_type == 'all') {
		$post_type = array('project', 'perspective');
		$defaultPosts = ($myArray);
	}
	$paged = $_POST['page'];
	$args = array(
		'post_type'      	=> $post_type,
		'posts_per_page'	=> 24,
		'paged'          	=> $paged,
		'post__not_in'   	=> $defaultPosts,
	);
	if (isset($market_id) && !empty($market_id)) {
		$args['meta_query'] = array(
			'relation' => 'OR',
			array(
				'key'     	=> 'select_market_post', // ACF field for project post
				'value'   	=> $market_id,
				'compare'	=> 'LIKE',
			),
		);
	}
	$query = new WP_Query($args);
	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$post_type = get_post_type();
			$image_id = get_post_thumbnail_id(get_the_ID());
			$image_info = cmma_elementor_widgets_get_responsive_image_data($image_id, 'full');
			$card_type = get_field('select_card', get_the_ID());
			$post_title = get_the_title();
			echo generate_work_html($post_type, $card_type, $image_info, $post,$post_title);
		}
		wp_reset_postdata();
	} else {
		echo 'no-posts';
	}
	die();
}



function generate_work_html($post_type, $card_type, $image_info, $post, $post_title, $key = null) {
  ob_start();
  $colors = ['red', 'yellow', 'green'];
  shuffle($colors);
  $random_class = $colors[array_rand($colors)];
	?>
		<div class="cmma-work <?= $card_type . ' ' . $random_class; ?>" data-post-id="<?= $post->ID; ?>" <?= $key ? 'data-key-id="'.$key.'"' : ''; ?>>
			<?php if ($post_type == 'project') : ?>
				<?php if (isset($image_info) && !empty($image_info['srcset'])) : ?>
					<div class="cmma-work-img">
						<a href="<?= esc_url(get_permalink($post)); ?>">
							<img srcset="<?= esc_attr($image_info['srcset']) ?>" src="<?= esc_url($image_info['url']) ?>" loading="lazy" height="100%" width="100%" alt="" />
						</a>
					</div>
				<?php endif; ?>
				<p><?= $post_title ?></p>
			<?php else : ?>
				<div class="panel-item-perspective">
					<a href="<?= esc_url(get_permalink($post)); ?>"></a>
					<p>Perspective</p>
					<h4><?= $post_title ?></h4>
				</div>
			<?php endif; ?>
		</div>
	<?php
  return ob_get_clean();
}

/**
 * Allows us to inject controls in existing widgets
 *
 * @param \Elementor\Controls_Stack $element    The element type.
 * @param string                    $section_id Section ID.
 * @param array                     $args       Section arguments.
 */
function inject_control_in_element( $element, $section_id ) {
	if ( $element->get_name() === 'spacer' && $section_id === 'section_spacer' ) {
		$element->add_control(
			'color_scheme',
			[
				'label'   => esc_html__( 'Color scheme ', 'elementor-spacer' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'light',
				'dynamic' => [
					'active' => true,
				],
				'options' => [
					''      => esc_html__( 'Select color scheme', 'elementor-spacer' ),
					'light' => esc_html__( 'Light', 'elementor-spacer' ),
					'dark'  => esc_html__( 'Dark', 'elementor-spacer' ),
				],
			]
		);
	}
}
add_action( 'elementor/element/after_section_start', 'inject_control_in_element', 10, 2 );


/**
 * Add a custom class and a data attribute to specific elements
 * containing a specific setting defined through the element control.
 *
 * @since 1.0.0
 * @param \Elementor\Element_Base $element The element instance.
 */
function add_attributes_to_elements( $element ) {
	if ( $element->get_name() === 'spacer' ) {
		$element->add_render_attribute(
			'_wrapper',
			[
				'class' => $element->get_settings( 'color_scheme' )
					? ('color-scheme-' . $element->get_settings( 'color_scheme' )) : '',
			]
		);
	}
}
add_action( 'elementor/frontend/before_render', 'add_attributes_to_elements' );


add_filter('wp_lazy_loading_enabled', '__return_false');