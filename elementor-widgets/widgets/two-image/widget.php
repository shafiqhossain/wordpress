<?php
if (!defined( 'ABSPATH' )) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor oEmbed Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class CMMA_TwoImage_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve oEmbed widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'cmma-two-image';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve oEmbed widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Two Image', 'cmma-two-image-widget' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve oEmbed widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-featured-image';
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
	 * Retrieve the list of categories the oEmbed widget belongs to.
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
		return ['widget-two-image-style'];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the oEmbed widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return ['images', 'photos', 'two image'];
	}

	/**
	 * Register oEmbed widget controls.
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
				'label' => esc_html__( 'Content', 'cmma-two-image-widget' ),
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
				'label'   => esc_html__( 'Color scheme', 'cmma-two-image-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'light',
				'dynamic' => [
					'active' => true,
				],
				'options' => array(
					'light' => esc_html__( 'Light', 'cmma-two-image-widget' ),
					'dark'  => esc_html__( 'Dark', 'cmma-two-image-widget' ),
				),
			)
		);

		$this->add_control(
			'large_image',
			array(
				'label'   => esc_html__( 'Choose Large Image', 'cmma-two-image-widget' ),
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
			array(
				'label'       => esc_html__( 'Choose Mobile Image', 'cmma-two-image-widget' ),
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
				'description' => esc_html__( 'Use alternative image for mobile resolution and its optional. Desktop image will be used if no image is uploaded in this field.' )
			)
		);

		$this->add_control(
			'headline',
			array(
				'label'       => esc_html__( 'Headline', 'cmma-two-image-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Headline', 'cmma-two-image-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'description',
			array(
				'label'       => esc_html__( 'Short Description', 'cmma-two-image-widget' ),
				'type'        => \Elementor\Controls_Manager::WYSIWYG,
				'placeholder' => esc_html__( 'Short Description here', 'cmma-two-image-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'       => esc_html__( 'Button Text', 'cmma-two-image-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter Button Text', 'cmma-two-image-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'button_link',
			array(
				'label'         => esc_html__( 'Button Link', 'cmma-two-image-widget' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://your-link.com', 'cmma-two-image-widget' ),
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
				'label'   => esc_html__( 'Choose Small Image', 'cmma-two-image-widget' ),
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
			'position',
			array(
				'label'   => esc_html__( 'Position', 'cmma-two-image-widget' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'default' => 'center',
				'toggle'  => true,
				'options' => array(
					'top'    => array(
						'title' => esc_html__( 'Top', 'cmma-two-image-widget' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'cmma-two-image-widget' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'bottom' => array(
						'title' => esc_html__( 'Bottom', 'cmma-two-image-widget' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
			)
		);

		$this->add_control(
			'short_description',
			array(
				'label'       => esc_html__( 'Short Description', 'cmma-two-image-widget' ),
				'type'        => \Elementor\Controls_Manager::WYSIWYG,
				'placeholder' => esc_html__( 'Short Description here', 'cmma-two-image-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'small_image_button_text',
			array(
				'label'       => esc_html__( 'Small Image Button Text', 'cmma-two-image-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter Button Text', 'cmma-two-image-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'small_image_button_link',
			array(
				'label'         => esc_html__( 'Small Image Button Link', 'cmma-two-image-widget' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://your-link.com', 'cmma-two-image-widget' ),
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
			'placement',
			array(
				'label'   => esc_html__( 'Placement', 'cmma-two-image-widget' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'default' => 'left',
				'toggle'  => true,
				'dynamic' => [
					'active' => true,
				],
				'options' => array(
					'left'  => array(
						'title' => esc_html__( 'Left', 'cmma-two-image-widget' ),
						'icon'  => 'eicon-flex eicon-h-align-left',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'cmma-two-image-widget' ),
						'icon'  => 'eicon-flex eicon-h-align-right',
					),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'hover_content',
			array(
				'label' => esc_html__( 'Hover Content', 'cmma-two-image-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'overlay_overline',
			array(
				'label'       => esc_html__( 'Overline', 'cmma-two-image-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Overline', 'cmma-two-image-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'overlay_headline',
			array(
				'label'       => esc_html__( 'Headline', 'cmma-two-image-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Headline', 'cmma-two-image-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'overlay_button_text',
			array(
				'label'       => esc_html__( 'Button Text', 'cmma-two-image-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter Button Text', 'cmma-two-image-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'overlay_button_link',
			array(
				'label'         => esc_html__( 'Button Link', 'cmma-two-image-widget' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://your-link.com', 'cmma-two-image-widget' ),
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
	}

	/**
	 * Render oEmbed widget output on the frontend.
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
