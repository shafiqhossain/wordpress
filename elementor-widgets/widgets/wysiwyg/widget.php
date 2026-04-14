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
class CMMA_WYSIWYG_Widget extends \Elementor\Widget_Base {

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
		return 'wysiwyg';
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
		return esc_html__( 'WYSISWYG', 'cmma-wysiwyg-widget' );
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
		return 'eicon-colors-typography';
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
		return array( 'widget-wysiwyg-style' );
	}

 	/**
 	 * Load widget script dependencies
 	 *
 	 * Retrieve an aaray of the style dependencies for the widget.
 	 *
 	 * @since 1.0.0
 	 * @access public
 	 * @return array Wiget script dependencies
 	 */
    public function get_script_depends() {
		return array( 'widget-wysiwyg-script' );
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
		return array( 'wysiwyg', 'block' );
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
				'label' => esc_html__( 'Content', 'cmma-wysiwyg-widget' ),
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
				'label'   => esc_html__( 'Color scheme ', 'cmma-wysiwyg-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'light',
				'dynamic' => [
					'active' => true,
				],
				'options' => array(
					'light' => esc_html__( 'Light', 'cmma-wysiwyg-widget' ),
					'dark'  => esc_html__( 'Dark', 'cmma-wysiwyg-widget' ),
				),
			)
		);

		$this->add_control(
			'wysiwyg',
			array(
				'label'       => esc_html__( 'WYSIWYG Block', 'cmma-wysiwyg-widget' ),
				'type'        => \Elementor\Controls_Manager::WYSIWYG,
				'placeholder' => esc_html__( ' Description ', 'cmma-wysiwyg-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'placement',
			array(
				'label'   => esc_html__( 'Placement', 'cmma-wysiwyg-widget' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'dynamic' => [
					'active' => true,
				],
				'options' => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'cmma-wysiwyg-widget' ),
						'icon'  => 'eicon-flex eicon-h-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'elementor-SingleVideo-widget' ),
						'icon'  => 'eicon-flex eicon-h-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'cmma-wysiwyg-widget' ),
						'icon'  => 'eicon-flex eicon-h-align-right',
					),
				),
				'default' => 'left',
				'toggle'  => true,
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
