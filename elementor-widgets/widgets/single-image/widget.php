<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Single Image.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class CMMA_SingleImage_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve Single Image name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'cmma-single-image';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Single Image title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Single Image', 'cmma-single-image' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Single Image icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-image';
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
	 * Retrieve the list of categories the Single Image belongs to.
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
		return [ 'widget-single-image-style' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the Single Image belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return ['image', 'photo', 'single image'];
	}

	/**
	 * Register Single Image controls.
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
				'label' => esc_html__( 'Basic Content', 'cmma-single-image' ),
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
			'color_scheme',
			[
				'label'   => esc_html__( 'Color scheme ', 'cmma-single-image' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'light',
				'options' => [
					'light' => esc_html__( 'Light', 'cmma-single-image' ),
					'dark'  => esc_html__( 'Dark', 'cmma-single-image' ),
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'headline',
			[
				'label'       => esc_html__( 'Headline', 'cmma-single-image' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Headline', 'cmma-single-image' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'description',
			[
				'label'       => esc_html__( 'Short Description', 'cmma-single-image' ),
				'type'        => \Elementor\Controls_Manager::WYSIWYG,
				'placeholder' => esc_html__( 'Description here', 'cmma-single-image' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);


		$this->add_control(
			'button_text',
			[
				'label'       => esc_html__( 'Button Text', 'cmma-single-image' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter Button Text', 'cmma-single-image' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'button_link',
			[
				'label'         => esc_html__( 'Button Link', 'cmma-single-image' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://your-link.com', 'cmma-single-image' ),
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
			'image',
			[
				'label'   => esc_html__( 'Choose Image', 'cmma-single-image' ),
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
				'label'       => esc_html__( 'Choose Mobile Image', 'cmma-single-image' ),
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'description' => esc_html__( 'Use alternative image for mobile resolution and its optional. Desktop image will be used if no image is uploaded in this field.' )
			]
		);
		$this->add_control(
			'image_caption',
			[
				'label'       => esc_html__( 'Image Caption', 'cmma-single-image' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter image caption', 'cmma-single-image' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'placement',
			[
				'label'   => esc_html__( 'Content Placement', 'cmma-single-image' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'default' => 'left',
				'toggle'  => true,
				'options' => [
					'left'   => [
						'title' => esc_html__( 'Left', 'cmma-single-image' ),
						'icon'  => 'eicon-flex eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'cmma-single-image' ),
						'icon'  => 'eicon-flex eicon-h-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'cmma-single-image' ),
						'icon'  => 'eicon-flex eicon-h-align-right',
					],
				],
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'hover_content',
			[
				'label' => esc_html__( 'Hover Content', 'cmma-single-image' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'overlay_overline',
			[
				'label'       => esc_html__( 'Overline', 'cmma-single-image' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Overline', 'cmma-single-image' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'overlay_headline',
			[
				'label'       => esc_html__( 'Headline', 'cmma-single-image' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Headline', 'cmma-single-image' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'overlay_button_text',
			[
				'label'       => esc_html__( 'Button Text', 'cmma-single-image' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter Button Text', 'cmma-single-image' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'overlay_button_link',
			[
				'label'         => esc_html__( 'Button Link', 'cmma-single-image' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://your-link.com', 'cmma-single-image' ),
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

		$this->end_controls_section();
	}

	/**
	 * Render Single Image output on the frontend.
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
