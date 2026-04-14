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
class CMMA_ImageVideoCarousel_Widget extends \Elementor\Widget_Base {

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
		return 'cmma-image-carousel';
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
		return esc_html__( 'Image Video Carousel', 'cmma-image-video-carousel-widget' );
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
		return array( 'widget-image-video-carousel-style' );
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
		return array( 'widget-image-video-carousel-script' );
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
		return array( 'image', 'carousel' );
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
				'label' => esc_html__( 'Content', 'cmma-image-video-carousel-widget' ),
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
				'label'   => esc_html__( 'Color scheme ', 'cmma-image-video-carousel-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'light',
				'options' => array(
					'light' => esc_html__( 'Light', 'cmma-image-video-carousel-widget' ),
					'dark'  => esc_html__( 'Dark', 'cmma-image-video-carousel-widget' ),
				),
				'dynamic' => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'headline',
			array(
				'label'       => esc_html__( 'Headline', 'cmma-image-video-carousel-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Headline', 'cmma-image-video-carousel-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'description',
			array(
				'label'       => esc_html__( 'Short Description', 'cmma-image-video-carousel-widget' ),
				'type'        => \Elementor\Controls_Manager::WYSIWYG,
				'placeholder' => esc_html__( 'Description here', 'cmma-image-video-carousel-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'       => esc_html__( 'Button Text', 'cmma-image-video-carousel-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter Button Text', 'cmma-image-video-carousel-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'button_link',
			array(
				'label'         => esc_html__( 'Button Link', 'cmma-image-video-carousel-widget' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://your-link.com', 'cmma-image-video-carousel-widget' ),
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
				),
				'default'     => array(
					array(
						'image'     => '',
						'video_url' => '',
						'caption'   => '',
					),
					array(
						'image'     => '',
						'video_url' => '',
						'caption'   => '',
					),
					array(
						'image'     => '',
						'video_url' => '',
						'caption'   => '',
					),
				),
				'title_field' => '{{{ elementor.helpers.renderIcon( this, "file-code-o", "center", "fixed" ) }}} {{{ image.url }}}',
			)
		);

		$this->add_control(
			'mute_icon_display',
			[
				'label'        => esc_html__( 'Mute Icon Display', 'cmma-single-video-widget' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Allow', 'cmma-single-video-widget' ),
				'label_off'    => esc_html__( 'Disallow', 'cmma-single-video-widget' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'dynamic'      => [
					'active' => true,
				],
			]
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
