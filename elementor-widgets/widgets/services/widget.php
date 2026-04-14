<?php
if (!defined( 'ABSPATH' )) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Services.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class CMMA_Services_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve Services name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'cmma-services';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Services title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Services', 'cmma-services' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Services icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-handle';
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
	 * Retrieve the list of categories the Services belongs to.
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
		return ['widget-services-style'];
	}

	/**
	 * Get widget script dependencies.
	 *
	 * Retrieve an array of script dependencies for the widget.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget script dependencies.
	 */
	public function get_script_depends() {
		return ['widget-services-script'];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the Services belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return ['Services'];
	}

	/**
	 * Register Services controls.
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
				'label' => esc_html__( 'Basic Content', 'cmma-services' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'color_scheme',
			[
				'label'   => esc_html__( 'Color scheme ', 'cmma-services' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'light',
				'dynamic' => [
					'active' => true,
				],
				'options' => [
					'light' => esc_html__( 'Light', 'cmma-services' ),
					'dark'  => esc_html__( 'Dark', 'cmma-services' ),
				],
			]
		);

		$this->add_control(
			'placement',
			[
				'label'   => esc_html__( 'Content Placement', 'cmma-image-with-story-widget' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'default' => 'left',
				'toggle'  => true,
				'options' => [
					'left'  => [
						'title' => esc_html__( 'Left', 'cmma-image-with-story-widget' ),
						'icon'  => 'eicon-flex eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'cmma-downloadable-content-widget'),
						'icon' => 'eicon-flex eicon-h-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'cmma-image-with-story-widget' ),
						'icon'  => 'eicon-flex eicon-h-align-right',
					],
				],
			]
		);
	}

	/**
	 * Render Services output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		require( __DIR__ . '/view.php' );
	}
}
