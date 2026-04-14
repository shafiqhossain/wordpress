<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Thing Link.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class CMMA_ThingLink_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve Thing Link name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'cmma-thing-link';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Thing Link title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Thing Link', 'cmma-thing-link-widget' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Thing Link icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-google-maps';
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
	 * Retrieve the list of categories the Thing Link belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return ['cmma-widgets'];
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
		return ['widget-thing-link-style'];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the Thing Link belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return ['Thing Link'];
	}

	/**
	 * Register Thing Link controls.
	 *
	 * Add input fields to allow the user to customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Basic Content', 'cmma-thing-link-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'color_scheme',
			[
				'label'   => esc_html__( 'Color Scheme ', 'cmma-thing-link-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'light',
				'options' => [
					'light' => esc_html__( 'Light', 'cmma-thing-link-widget' ),
					'dark'  => esc_html__( 'Dark', 'cmma-thing-link-widget' ),
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'embed_code',
			[
				'label'   => esc_html__( 'Embed Code', 'cmma-thing-link-widget' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => esc_html__( '', 'cmma-thing-link-widget' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->end_controls_section(); 
	}

	/**
	 * Render Thing Link output on the frontend.
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
