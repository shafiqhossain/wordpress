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
class CMMA_Downloadable_Content_Widget extends \Elementor\Widget_Base {

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
		return 'downloadable_content';
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
		return esc_html__( 'Downloadable Content', 'cmma-downloadable-content-widget' );
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
		return 'eicon-library-download';
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
		return [ 'widget-downloadable-content-style' ];
	}

	public function get_script_depends() {
		return [ 'widget-downloadable-content-script' ];
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
		return [ 'downloadable-content', 'downloadable content','downloadable' ];
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
				'label' => esc_html__('Content', 'cmma-downloadable-content-widget'),
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
				'label' => esc_html__('Headline', 'cmma-downloadable-content-widget'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__('Headline', 'cmma-downloadable-content-widget'),
			]
		);

		$this->add_control(
			'button_text',
			[
				'label' => esc_html__('Button Text', 'cmma-downloadable-content-widget'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__('Enter Button Text', 'cmma-downloadable-content-widget'),
			]
		);

		$this->add_control(
			'pdf',
			[
				'label' => esc_html__('Choose PDF File', 'cmma-downloadable-content-widget'),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'media_type' => 'file', // Specify file type
				'mime_type' => 'application/pdf', // Restrict to PDF files
			]
		);

		$this->add_control(
			'file_is_gated',
			[
				'label' => esc_html__('Is Gated', 'cmma-downloadable-content-widget'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'false', // Set the default value to false
				'options' => [
					'true' => esc_html__('True', 'cmma-downloadable-content-widget'),
					'false' => esc_html__('False', 'cmma-downloadable-content-widget'),
				],
			]
		);

		$this->add_control(
			'gravity_form_id',
			[
				'label' => esc_html__('Select Form', 'cmma-downloadable-content-widget'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $this->get_gravity_forms_options(),
				'condition' => [
					'file_is_gated' => 'true',
				],
			]
		);

		$this->add_control(
			'modal_color_scheme',
			[
				'label' => esc_html__('Modal Color Scheme', 'cmma-downloadable-content-widget'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'light',
				'options' => [
					'light' => esc_html__('Light', 'cmma-downloadable-content-widget'),
					'dark' => esc_html__('Dark', 'cmma-downloadable-content-widget'),
				],
				'condition' => [
					'file_is_gated' => 'true',
				],
			]
		);

		$this->add_control(
			'color_scheme',
			[
				'label' => esc_html__('Color Scheme', 'cmma-downloadable-content-widget'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'dark',
				'options' => [
					'light' => esc_html__('Light', 'cmma-downloadable-content-widget'),
					'dark' => esc_html__('Dark', 'cmma-downloadable-content-widget'),
				],
			]
		);

		$this->add_control(
			'placement',
			[
				'label' => esc_html__('Content Placement', 'cmma-downloadable-content-widget'),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'cmma-downloadable-content-widget'),
						'icon' => 'eicon-flex eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'cmma-downloadable-content-widget'),
						'icon' => 'eicon-flex eicon-h-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'cmma-downloadable-content-widget'),
						'icon' => 'eicon-flex eicon-h-align-right',
					],
				],
				'default' => 'left',
				'toggle' => true,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		require( __DIR__ . '/view.php' );
	}

	function get_gravity_forms_options() {
		// Check if the Gravity Forms API class exists
		if (!class_exists('GFAPI')) {
			return [];
		}

		// Fetch Gravity Forms
		$forms = \GFAPI::get_forms();
		$form_options = [];

		// Loop through each form and add it to the options array
		foreach ($forms as $form) {
			$form_options[$form['id']] = $form['title'];
		}

		return $form_options;
	}

}