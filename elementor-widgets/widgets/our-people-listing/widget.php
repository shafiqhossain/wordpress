<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Article Listing.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class CMMA_OurPeopleListing_Widget extends \Elementor\Widget_Base {
	/**
	 * Get widget name.
	 *
	 * Retrieve Article Listing name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'cmma-our-people-listing';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Article Listing title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Our People Listing', 'cmma-our-people-listing-widget' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Article Listing icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-editor-quote';
	}

	/**
	 * Get custom help URL.
	 *
	 * Retrieve a URL where the user can get more information about the widget.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget help URL.
	 */
	public function get_custom_help_url() {
		return 'https://developers.elementor.com/docs/widgets/';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the Article Listing belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'cmma-widgets' );
	}

	/**
	 * Get widget style dependencies.
	 *
	 * Retrieve an array of style dependencies for the widget.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends() {
		return array( 'widget-our-people-listing-style' );
	}

	/**
	 * Get widget script dependencies.
	 *
	 * Retrieve an array of script dependencies for the widget.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget style dependencies.
	 */
	public function get_script_depends() {
		return array( 'widget-our-people-listing-script' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the Article Listing belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return array( 'our people listing', 'people', 'listing' );
	}

	/**
	 * Register Article Listing controls.
	 *
	 * Add input fields to allow the user to customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Basic Content', 'cmma-article-listing-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
  
		$this->add_control(
			'videos',
			array(
					'label'       => esc_html__( 'Upload Video & Images', 'cmma-image-video-carousel-widget' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => array(
							array(
									'name'        => 'image',
									'label'       => esc_html__( 'Upload Image/Video', 'cmma-image-video-carousel-widget' ),
									'type'        => \Elementor\Controls_Manager::MEDIA,
									'default'     => array(
											'url' => '',
									),
									'dynamic'     => [
											'active' => true,
									],
									'description' => esc_html__( 'Upload your image or video here.', 'cmma-image-video-carousel-widget' ),
							),
							array(
									'name'        => 'video_url',
									'label'       => esc_html__( 'Youtube Link', 'cmma-image-video-carousel-widget' ),
									'type'        => \Elementor\Controls_Manager::TEXT,
									'input_type'  => 'url',
									'placeholder' => esc_html__( 'https://your-link.com', 'cmma-image-video-carousel-widget' ),
									'dynamic'     => [
											'active' => true,
									],
							),
							array(
									'name'        => 'caption',
									'label'       => esc_html__( 'Caption', 'cmma-image-video-carousel-widget' ),
									'type'        => \Elementor\Controls_Manager::TEXT,
									'placeholder' => esc_html__( 'Enter caption', 'cmma-image-video-carousel-widget' ),
									'dynamic'     => [
											'active' => true,
									],
							),
							array(
									'name'        => 'order',
									'label'       => esc_html__( 'Order', 'cmma-image-video-carousel-widget' ),
									'type'        => \Elementor\Controls_Manager::NUMBER,
									'default'     => 0, 
									'label_block' => true,
									'description' => esc_html__( 'Specify the order of appearance for this video/image.', 'cmma-image-video-carousel-widget' ),
							),
					),
					'default'     => array(
							array(
									'image'     => '',
									'video_url' => '',
									'caption'   => '',
									'order'     => 0, 
							),
							array(
									'image'     => '',
									'video_url' => '',
									'caption'   => '',
									'order'     => 0, 
							),
							array(
									'image'     => '',
									'video_url' => '',
									'caption'   => '',
									'order'     => 0, 
							),
					),
					'title_field' => '{{{ elementor.helpers.renderIcon( this, "file-code-o", "center", "fixed" ) }}} {{{ image.url }}}',
			)
	);
	


	}

	
	/**
	 * Render Article Listing output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		require __DIR__ . '/view.php';
	}
}
