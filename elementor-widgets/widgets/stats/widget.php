<?php
if (!defined( 'ABSPATH' )) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Stats.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class CMMA_Stats_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve Stats name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'cmma-stats';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Stats title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Stats', 'cmma-stats-widget' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Stats icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-number-field';
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
	 * Retrieve the list of categories the Stats belongs to.
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
		return ['widget-stats-style', 'cmma-elementor-slick-style'];
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
		return ['widget-stats-script', 'cmma-elementor-slick-script'];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the Stats belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return ['stats', 'statstics'];
	}

	/**
	 * Register Stats controls.
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
				'label' => esc_html__( 'Basic Content', 'cmma-stats-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'color_scheme',
			[
				'label'   => esc_html__( 'Color scheme ', 'cmma-stats-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'light',
				'options' => [
					'light' => esc_html__( 'Light', 'cmma-stats-widget' ),
					'dark'  => esc_html__( 'Dark', 'cmma-stats-widget' ),
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'stats',
			[
				'label'  => esc_html__( 'Stats Items', 'cmma-stats-widget' ),
				'type'   => \Elementor\Controls_Manager::REPEATER,
				'title_field' => '{{{ heading }}} {{{ description }}}',
				'fields' => [
					[
						'name'        => 'heading',
						'label'       => esc_html__( 'Heading', 'cmma-stats-widget' ),
						'type'        => \Elementor\Controls_Manager::TEXT,
						'default'     => esc_html__( 'Heading' , 'cmma-stats-widget' ),
						'label_block' => true,
						'dynamic'     => [
							'active' => true,
						],
					],
					[
						'name'       => 'description',
						'label'      => esc_html__( 'Description', 'cmma-stats-widget' ),
						'type'       => \Elementor\Controls_Manager::TEXT,
						'default'    => esc_html__( '' , 'cmma-stats-widget' ),
						'show_label' => true,
						'dynamic'     => [
							'active' => true,
						],
					],
					[
						'name'       => 'short_description',
						'label'      => esc_html__( 'Short Description', 'cmma-stats-widget' ),
						'type'       => \Elementor\Controls_Manager::TEXT,
						'default'    => esc_html__( '' , 'cmma-stats-widget' ),
						'show_label' => true,
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
	 * Render Stats output on the frontend.
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
