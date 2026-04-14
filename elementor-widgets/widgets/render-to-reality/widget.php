<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Render Reality.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class CMMA_RenderReality_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve Render Reality name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'cmma-render-to-reality';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Render Reality title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Render to Reality', 'cmma-render-to-reality-image' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Render Reality icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-image-before-after';
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
	 * Retrieve the list of categories the Render Reality belongs to.
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
		return array( 'widget-render-to-reality-style' );
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
		return array( 'widget-render-to-reality-script' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the Render Reality belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return array( 'Render to Reality', 'Render Reality' );
	}

	/**
	 * Register Render Reality controls.
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
				'label' => esc_html__( 'Basic Content', 'cmma-render-to-reality-image' ),
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
				'label'   => esc_html__( 'Color scheme ', 'cmma-render-to-reality-image' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'light',
				'dynamic' => [
					'active' => true,
				],
				'options' => array(
					'light' => esc_html__( 'Light', 'cmma-render-to-reality-image' ),
					'dark'  => esc_html__( 'Dark', 'cmma-render-to-reality-image' ),
				),
			)
		);

		$this->add_control(
			'headline',
			array(
				'label'       => esc_html__( 'Headline', 'cmma-render-to-reality-image' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Headline', 'cmma-render-to-reality-image' ),
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'description',
			array(
				'label'       => esc_html__( 'Short Description', 'cmma-render-to-reality-image' ),
				'type'        => \Elementor\Controls_Manager::WYSIWYG,
				'placeholder' => esc_html__( 'Description here', 'cmma-render-to-reality-image' ),
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'       => esc_html__( 'Button Text', 'cmma-render-to-reality-image' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter Button Text', 'cmma-render-to-reality-image' ),
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'button_link',
			array(
				'label'         => esc_html__( 'Button Link', 'cmma-render-to-reality-image' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://your-link.com', 'cmma-render-to-reality-image' ),
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
			'first_image',
			array(
				'label'   => esc_html__( 'Choose First Image ', 'cmma-render-to-reality-image' ),
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
			'final_image',
			array(
				'label'   => esc_html__( 'Choose Final Image ', 'cmma-render-to-reality-image' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render Render Reality output on the frontend.
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
