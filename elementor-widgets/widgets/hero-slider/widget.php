<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Single Video.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class CMMA_HeroSlider_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve Single Video name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'cmma-hero-slider';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Single Video title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Hero Slider', 'cmma-hero-slider-widget' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Single Video icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-nested-carousel';
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
	 * Retrieve the list of categories the Single Video belongs to.
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
		return array( 'widget-hero-slider-style' );
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
		return array( 'widget-hero-slider-script' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the Single Video belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return array( 'slider', 'carousel' );
	}

	/**
	 * Register Single Video controls.
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
				'label' => esc_html__( 'Content', 'cmma-hero-slider-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'height',
			[
				'label' => esc_html__( 'Height', 'cmma-hero-slider-widget' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'vh','custom' ],
				'range' => [
					'vh' => [
						'min' => 0,
						'max' => 100,
						'step' => 5,
					],
				],
				'default' => [
					'range' => 'vh',
					'unit' => 'vh',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .hero-slider-wrapper' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'images',
			array(
				'label'       => esc_html__( 'Slides', 'cmma-hero-slider-widget' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => array(
					array(
						'name'        => 'image',
						'label'       => esc_html__( 'Upload Image', 'cmma-hero-slider-widget' ),
						'type'        => \Elementor\Controls_Manager::MEDIA,
						'default'     => array(
							'url' => '',
						),
						'dynamic'     => [
							'active' => true,
						],
						'description' => esc_html__( 'Upload your image or video here.', 'cmma-hero-slider-widget' ),
					),

					array(
						'name'        => 'caption',
						'label'       => esc_html__( 'Caption', 'cmma-hero-slider-widget' ),
						'type'        => \Elementor\Controls_Manager::TEXT,
						'placeholder' => esc_html__( 'Enter caption', 'cmma-hero-slider-widget' ),
						'dynamic'     => [
							'active' => true,
						],
					),
					array(
						'name'        => 'caption-link',
						'label'         => esc_html__( 'Caption Link', 'cmma-hero-slider-widget' ),
						'type'          => \Elementor\Controls_Manager::URL,
						'placeholder'   => esc_html__( 'https://your-link.com', 'cmma-hero-slider-widget' ),
						'show_external' => true,
						'dynamic'       => [
							'active' => true,
						],
						'default'       => [
							'url'         => false,
							'is_external' => true,
							'nofollow'    => true,
						],
					),

				),
				'title_field' => '{{{ elementor.helpers.renderIcon( this, "file-code-o", "center", "fixed" ) }}} {{{ image.url }}}',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render Single Video output on the frontend.
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
