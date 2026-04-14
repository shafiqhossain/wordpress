<?php
if (!defined( 'ABSPATH' )) {
	exit; // Exit if accessed directly.
}

/**
 * CMMA ImageWithText Widget.
 *
 * Elementor widget that inserts an image with text block into the page.
 *
 * @since 1.0.0
 */
class CMMA_ImageWithText_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve ImageWithText widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'image-with-text';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Image With Text', 'cmma-image-with-text-widget' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-image-box';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'cmma-widgets' ];
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
		return [ 'widget-image-with-text-style', 'cmma-elementor-slick-style' ];
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
		return [ 'widget-image-with-text-script', 'cmma-elementor-slick-script' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'image with text', 'image' ];
	}

	/**
	 * Register widget controls.
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
				'label' => esc_html__( 'Content', 'cmma-image-with-text-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		if (get_field('enable_jump_navigation_bar')) {
			$this->add_control(
				'enable_jump_navigation',
				[
					'label'        => esc_html__( 'Enable Jump Navigation', 'cmma-featured-text-widget' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'cmma-featured-text-widget' ),
					'label_off'    => esc_html__( 'No', 'cmma-featured-text-widget' ),
					'return_value' => 'yes',
					'default'      => 'no',
				]
			);

			$this->add_control(
				'jump_navigation_title',
				[
					'label'       => esc_html__('Jump Navigation Menu Title', 'cmma-accordion-widget'),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'placeholder' => esc_html__('Jump Navigation Menu Title', 'cmma-accordion-widget'),
					'condition'   => [
						'enable_jump_navigation' => 'yes',
					],
				]
			);
		}

		$this->add_control(
			'image',
			[
				'label'   => esc_html__( 'Choose Image', 'cmma-image-with-text-widget' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_control(
			'mobile_image',
			[
				'label'       => esc_html__( 'Choose Mobile Image', 'cmma-image-with-text-widget' ),
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'description' => esc_html__( 'Use alternative image for mobile resolution and its optional. Desktop image will be used if no image is uploaded in this field.' ),
				'default'     => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'headline',
			[
				'label'       => esc_html__( 'Headline', 'cmma-image-with-text-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Headline', 'cmma-image-with-text-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'tagline',
			[
				'label'       => esc_html__( 'Tagline', 'cmma-image-with-text-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Tagline', 'cmma-image-with-text-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'       => esc_html__( 'Button Text', 'cmma-image-with-text-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter Button Text', 'cmma-image-with-text-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'button_link',
			[
				'label'         => esc_html__( 'Button Link', 'cmma-image-with-text-widget' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://your-link.com', 'cmma-image-with-text-widget' ),
				'show_external' => true,
				'dynamic'       => [
					'active' => true,
				],
				'default'       => [
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				],
			]
		);

		$this->add_control(
			'placement',
			[
				'label'   => esc_html__( 'Content Placement', 'cmma-image-with-text-widget' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'default' => 'left',
				'toggle'  => true,
				'options' => [
					'left'  => [
						'title' => esc_html__( 'Left', 'cmma-image-with-text-widget' ),
						'icon'  => 'eicon-flex eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'cmma-image-with-text-widget' ),
						'icon'  => 'eicon-flex eicon-h-align-right',
					],
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

 		require __DIR__ . '/view.php';
	}
}
