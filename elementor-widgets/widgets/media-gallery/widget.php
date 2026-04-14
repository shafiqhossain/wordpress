<?php
if (!defined( 'ABSPATH' )) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Media Gallery.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class CMMA_MediaGallery_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve Media Gallery name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'cmma-media-gallery';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Media Gallery title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Media Gallery', 'cmma-media-gallery-widget' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Media Gallery icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-gallery-group';
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
	 * Retrieve the list of categories the Media Gallery belongs to.
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
		return [ 'widget-media-gallery-style', 'cmma-elementor-slick-style' ];
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
		return [ 'cmma-elementor-slick-script', 'widget-media-gallery-script'];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the Media Gallery belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return ['images', 'gallery', 'photo gallery', 'gallery slider'];
	}

	/**
	 * Register Media Gallery controls.
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
				'label' => esc_html__( 'Basic Content', 'cmma-media-gallery-widget' ),
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
				'label'   => esc_html__( 'Color scheme ', 'cmma-media-gallery-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'light',
				'options' => [
					'light' => esc_html__( 'Light', 'cmma-media-gallery-widget' ),
					'dark'  => esc_html__( 'Dark', 'cmma-media-gallery-widget' ),
				],
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'headline',
			[
				'label'       => esc_html__( 'Headline', 'cmma-media-gallery-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Headline', 'cmma-media-gallery-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'description',
			[
				'label'       => esc_html__( 'Short Description', 'cmma-media-gallery-widget' ),
				'type'        => \Elementor\Controls_Manager::WYSIWYG,
				'placeholder' => esc_html__( 'Description here', 'cmma-media-gallery-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'button_text',
			[
				'label'       => esc_html__( 'Button Text', 'cmma-media-gallery-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter Button Text', 'cmma-media-gallery-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'button_link',
			[
				'label'         => esc_html__( 'Button Link', 'cmma-media-gallery-widget' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://your-link.com', 'cmma-media-gallery-widget' ),
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
				'label'   => esc_html__( 'Choose Image', 'cmma-media-gallery-widget' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'modal_slider',
			[
				'label' => esc_html__( 'Modal Slider', 'cmma-media-gallery-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'modal_color_scheme',
			array(
				'label'   => esc_html__( 'Modal Color Scheme ', 'cmma-media-gallery-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'light',
				'dynamic' => [
					'active' => true,
				],
				'options' => array(
					'light' => esc_html__( 'Light', 'cmma-media-gallery-widget' ),
					'dark'  => esc_html__( 'Dark', 'cmma-media-gallery-widget' ),
				),
			)
		);

		$this->add_control(
			'gallery_items',
			[
				'label'  => esc_html__( 'Slider Items', 'cmma-media-gallery-widget' ),
				'type'   => \Elementor\Controls_Manager::REPEATER,
				'title_field' => '{{{ slider_title }}}',
				'fields' => [
					[
						'name'       => 'slider_image',
						'label'      => esc_html__( 'Slider Image', 'cmma-media-gallery-widget' ),
						'type'       => \Elementor\Controls_Manager::MEDIA,
						'show_label' => false,
						'dynamic'     => [
							'active' => true,
						],
						'default'    => [
							'url' => \Elementor\Utils::get_placeholder_image_src(),
						],
					],
					[
						'name'        => 'slider_title',
						'label'       => esc_html__( 'Slider Title', 'cmma-media-gallery-widget' ),
						'type'        => \Elementor\Controls_Manager::TEXT,
						'default'     => esc_html__( 'Slider Title' , 'cmma-media-gallery-widget' ),
						'label_block' => true,
						'dynamic'     => [
							'active' => true,
						],
					],
					[
						'name'       => 'slider_content',
						'label'      => esc_html__( 'Slider Content', 'cmma-media-gallery-widget' ),
						'type'       => \Elementor\Controls_Manager::WYSIWYG,
						'default'    => esc_html__( 'Slider Content' , 'cmma-media-gallery-widget' ),
						'show_label' => false,
						'dynamic'    => [
							'active' => true,
						],
					],

				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render Media Gallery output on the frontend.
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
