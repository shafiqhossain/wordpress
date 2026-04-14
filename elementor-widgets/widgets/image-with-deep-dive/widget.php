<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * CMMA ImageWithText Widget.
 *
 * Elementor widget that inserts an image with text block into the page.
 *
 * @since 1.0.0
 */
class CMMA_ImageWithDeepDive_Widget extends \Elementor\Widget_Base {

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
		return 'image-with-deep-dive';
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
		return esc_html__( 'Image With Deep Dive', 'cmma-image-with-deep-dive-widget' );
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
		return 'eicon-image-bold';
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
		return array( 'widget-image-with-deep-dive-style' );
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
		return array( 'image-with-deep-dive', 'image', 'deep', 'dive' );
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
			array(
				'label' => esc_html__( 'Content', 'cmma-image-with-deep-dive-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
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
			array(
				'label'   => esc_html__( 'Color scheme ', 'cmma-image-with-deep-dive-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'light',
				'dynamic' => [
					'active' => true,
				],
				'options' => array(
					'light' => esc_html__( 'Light', 'cmma-image-with-deep-dive-widget' ),
					'dark'  => esc_html__( 'Dark', 'cmma-image-with-deep-dive-widget' ),
				),
			)
		);

		$this->add_control(
			'large_image',
			array(
				'label'   => esc_html__( 'Choose Large Image', 'cmma-image-with-deep-dive-widget' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);

		$this->add_control(
			'mobile_image',
			[
				'label'       => esc_html__( 'Choose Mobile Image', 'cmma-image-with-deep-dive-widget' ),
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
				'description' => esc_html__( 'Use alternative image for mobile resolution and its optional. Desktop image will be used if no image is uploaded in this field.' )
			]
		);

		$this->add_control(
			'headline',
			array(
				'label'       => esc_html__( 'Headline', 'cmma-image-with-deep-dive-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Headline', 'cmma-image-with-deep-dive-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'description',
			array(
				'label'       => esc_html__( 'Short Description', 'cmma-image-with-deep-dive-widget' ),
				'type'        => \Elementor\Controls_Manager::WYSIWYG,
				'placeholder' => esc_html__( 'Short Description here', 'cmma-image-with-deep-dive-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'       => esc_html__( 'Button Text', 'cmma-image-with-deep-dive-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter Button Text', 'cmma-image-with-deep-dive-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'button_link',
			array(
				'label'         => esc_html__( 'Button Link', 'cmma-image-with-deep-dive-widget' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://your-link.com', 'cmma-image-with-deep-dive-widget' ),
				'show_external' => true,
				'dynamic'       => [
					'active' => true,
				],
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),
			)
		);

		$this->add_control(
			'small_image',
			array(
				'label'   => esc_html__( 'Choose Small Image', 'cmma-image-with-deep-dive-widget' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);

		$this->add_control(
			'short_description',
			array(
				'label'       => esc_html__( 'Short Description', 'cmma-image-with-deep-dive-widget' ),
				'type'        => \Elementor\Controls_Manager::WYSIWYG,
				'placeholder' => esc_html__( 'Short Description here', 'cmma-image-with-deep-dive-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'small_image_button_text',
			array(
				'label'       => esc_html__( 'Button Text', 'cmma-image-with-deep-dive-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter Button Text', 'cmma-image-with-deep-dive-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'placement',
			array(
				'label'   => esc_html__( 'Placement', 'cmma-image-with-deep-dive-widget' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'default' => 'left',
				'toggle'  => true,
				'options' => array(
					'left'  => array(
						'title' => esc_html__( 'Left', 'cmma-image-with-deep-dive-widget' ),
						'icon'  => 'eicon-flex eicon-h-align-left',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'cmma-image-with-deep-dive-widget' ),
						'icon'  => 'eicon-flex eicon-h-align-right',
					),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'hover_content',
			array(
				'label' => esc_html__( 'Hover Content', 'cmma-image-with-deep-dive-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'overlay_overline',
			array(
				'label'       => esc_html__( 'Overline', 'cmma-image-with-deep-dive-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Overline', 'cmma-image-with-deep-dive-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'overlay_headline',
			array(
				'label'       => esc_html__( 'Headline', 'cmma-image-with-deep-dive-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Headline', 'cmma-image-with-deep-dive-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'overlay_button_text',
			array(
				'label'       => esc_html__( 'Button Text', 'cmma-image-with-deep-dive-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter Button Text', 'cmma-image-with-deep-dive-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'overlay_button_link',
			array(
				'label'         => esc_html__( 'Button Link', 'cmma-image-with-deep-dive-widget' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://your-link.com', 'cmma-image-with-deep-dive-widget' ),
				'show_external' => true,
				'dynamic'       => [
					'active' => true,
				],
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'modal_contents',
			array(
				'label' => esc_html__( 'Modal', 'cmma-image-with-deep-dive-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'modal_color_scheme',
			array(
				'label'   => esc_html__( 'Modal Color Scheme ', 'cmma-image-with-deep-dive-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'dark',
				'dynamic' => [
					'active' => true,
				],
				'options' => array(
					'light' => esc_html__( 'Light', 'cmma-image-with-deep-dive-widget' ),
					'dark'  => esc_html__( 'Dark', 'cmma-image-with-deep-dive-widget' ),
				),
			)
		);

		$this->add_control(
			'modal_label',
			array(
				'label'       => esc_html__( 'Modal Label', 'cmma-image-with-deep-dive-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter Modal Label', 'cmma-image-with-deep-dive-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'modal_content',
			array(
				'label'       => esc_html__( 'Modal Content', 'cmma-image-with-deep-dive-widget' ),
				'type'        => \Elementor\Controls_Manager::WYSIWYG,
				'placeholder' => esc_html__( 'Short Description Here', 'cmma-image-with-deep-dive-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			)
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
