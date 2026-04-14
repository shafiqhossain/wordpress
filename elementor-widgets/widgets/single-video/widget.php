<?php
if (!defined( 'ABSPATH' )) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Single Video.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class CMMA_SingleVideo_Widget extends \Elementor\Widget_Base {

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
		return 'cmma-single-video';
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
		return esc_html__( 'Single Video', 'cmma-single-video-widget' );
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
		return 'eicon-video-camera';
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
		return ['widget-single-video-style'];
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
		return ['video', 'youtube'];
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
			[
				'label' => esc_html__( 'Content', 'cmma-single-video-widget' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
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
			'headline',
			[
				'label'       => esc_html__( 'Headline', 'cmma-single-video-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Headline', 'cmma-single-video-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'description',
			[
				'label'       => esc_html__( 'Short Description', 'cmma-single-video-widget' ),
				'type'        => \Elementor\Controls_Manager::WYSIWYG,
				'placeholder' => esc_html__( 'Description here', 'cmma-single-video-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'       => esc_html__( 'Button Text', 'cmma-single-video-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter Button Text', 'cmma-single-video-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'button_link',
			[
				'label'         => esc_html__( 'Button Link', 'cmma-single-video-widget' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://your-link.com', 'cmma-single-video-widget' ),
				'show_external' => true,
				'dynamic'       => [
					'active' => true,
				],
				'default'       => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],
			]
		);

		$this->add_control(
			'video_url',
			[
				'label'       => esc_html__( 'URL to embed', 'cmma-single-video-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'input_type'  => 'url',
				'placeholder' => esc_html__( 'https://your-link.com', 'cmma-single-video-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'placement',
			[
				'label'   => esc_html__( 'Content Placement', 'cmma-single-video-widget' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'default' => 'left',
				'toggle'  => true,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'cmma-single-video-widget' ),
						'icon'  => 'eicon-flex eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'cmma-single-video-widget' ),
						'icon'  => 'eicon-flex eicon-h-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'cmma-single-video-widget' ),
						'icon'  => 'eicon-flex eicon-h-align-right',
					],
				],
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'   => esc_html__( 'Video Autoplay', 'cmma-single-video-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'yes',
				'options' => [
					'yes' => esc_html__( 'Yes', 'cmma-single-video-widget' ),
					'no' => esc_html__( 'No', 'cmma-single-video-widget' ),
				],

			]
		);

		$this->add_control(
			'color_scheme',
			[
				'label'   => esc_html__( 'Color Scheme', 'cmma-single-video-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'light',
				'options' => [
					'light' => esc_html__( 'Light', 'cmma-single-video-widget' ),
					'dark' => esc_html__( 'Dark', 'cmma-single-video-widget' ),
				],
				'dynamic' => [
					'active' => true,
				],
			]
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

		require( __DIR__ . '/view.php' );
	}
}
