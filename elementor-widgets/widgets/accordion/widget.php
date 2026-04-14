<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Accordion.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class CMMA_Accordion_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve Accordion name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'cmma-accordion';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Accordion title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'CMMA Accordion', 'cmma-accordion-widget' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Accordion icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-accordion';
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
	 * Retrieve the list of categories the Accordion belongs to.
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
		return ['widget-accordion-style'];
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
		return ['widget-accordion-script'];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the Accordion belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return ['Accordion', 'Collapsible'];
	}

	/**
	 * Register Accordion controls.
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
				'label' => esc_html__( 'Basic Content', 'cmma-accordion-widget' ),
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
					'label'       => esc_html__('Jump Navigation Title', 'cmma-accordion-widget'),
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
				'label'   => esc_html__( 'Color scheme ', 'cmma-accordion-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'light',
				'options' => array(
					'light' => esc_html__( 'Light', 'cmma-accordion-widget' ),
					'dark'  => esc_html__( 'Dark', 'cmma-accordion-widget' ),
				),
			)
		);

		$this->add_control(
			'headline',
			[
				'label'       => esc_html__('Headline', 'cmma-accordion-widget'),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__('Headline', 'cmma-accordion-widget'),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'description',
			[
				'label'       => esc_html__('Short Description', 'cmma-accordion-widget'),
				'type'        => \Elementor\Controls_Manager::WYSIWYG,
				'placeholder' => esc_html__('Description here', 'cmma-accordion-widget'),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'item_title',
			[
				'label'       => esc_html__( 'Item Title', 'cmma-accordion-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Item Title' , 'cmma-accordion-widget' ),
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'item_content',
			[
				'label'      => esc_html__( 'Item Content', 'cmma-accordion-widget' ),
				'type'       => \Elementor\Controls_Manager::WYSIWYG,
				'default'    => esc_html__( 'Item Content' , 'cmma-accordion-widget' ),
				'show_label' => false,
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'item_image',
			[
				'label'      => esc_html__( 'Item Image', 'cmma-accordion-widget' ),
				'type'       => \Elementor\Controls_Manager::MEDIA,
				'show_label' => false,
				'dynamic'     => [
					'active' => true,
				],
				'default'    => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);


		$this->add_control(
			'items',
			[
				'label'       => esc_html__( 'Accordion Items', 'cmma-accordion-widget' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'title_field' => '{{{ item_title }}}',
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'item_title'   => esc_html__( 'Item Title', 'cmma-accordion-widget' ),
						'item_content' => esc_html__( 'Item Content. Click the edit button to change this text', 'cmma-accordion-widget' ),
					],
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render Accordion output on the frontend.
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
