<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Quotes.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class CMMA_Quotes_Widget extends \Elementor\Widget_Base {
	/**
	 * Get widget name.
	 *
	 * Retrieve Quotes name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'cmma-quotes';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Quotes title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Quotes', 'cmma-quotes-widget' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Quotes icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-editor-quote';
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
	 * Retrieve the list of categories the Quotes belongs to.
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
		return array( 'widget-quotes-style' );
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
		return array( 'widget-quotes-script' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the Quotes belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return array( 'quotes', 'testimonials' );
	}

	/**
	 * Register Quotes controls.
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
				'label' => esc_html__( 'Basic Content', 'cmma-quotes-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'color_scheme',
			array(
				'label'   => esc_html__( 'Color scheme ', 'cmma-quotes-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'light',
				'options' => array(
					'light' => esc_html__( 'Light', 'cmma-quotes-widget' ),
					'dark'  => esc_html__( 'Dark', 'cmma-quotes-widget' ),
				),
			)
		);

		$this->add_control(
			'content_length',
			[
				'label'   => esc_html__( 'Content Length', 'cmma-stats-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'Full Width',
				'options' => [
					'full' => esc_html__( 'Full Width', 'cmma-stats-widget' ),
					'small'  => esc_html__( 'Small Width', 'cmma-stats-widget' ),
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'quote',
			array(
				'label'   => esc_html__( 'Select Quote', 'cmma-quotes-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT2,
				'options' => $this->get_quotes_options(), // Fetch the options
			)
		);

		$this->add_control(
			'placement',
			array(
				'label'   => esc_html__( 'Content Placement', 'cmma-quotes-widget' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'default' => 'left',
				'toggle'  => true,
				'options' => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'cmma-quotes-widget' ),
						'icon'  => 'eicon-flex eicon-h-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'cmma-quotes-widget' ),
						'icon'  => 'eicon-flex eicon-h-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'cmma-quotes-widget' ),
						'icon'  => 'eicon-flex eicon-h-align-right',
					),
				),
			)
		);

		$this->add_control(
			'modal_color_scheme',
			array(
				'label'   => esc_html__( 'Modal Color scheme ', 'cmma-quotes-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'light',
				'options' => array(
					'light' => esc_html__( 'Light', 'cmma-quotes-widget' ),
					'dark'  => esc_html__( 'Dark', 'cmma-quotes-widget' ),
				),
			)
		);

    $this->add_control(
			'hide_quote_img',
			array(
				'label'   => esc_html__( 'Quotes Without Image ', 'cmma-quotes-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'No',
				'options' => array(
					'yes' => esc_html__( 'Yes', 'cmma-quotes-widget' ),
					'no'  => esc_html__( 'No', 'cmma-quotes-widget' ),
				),
			)
		);
	}

	/**
	 * Render Quotes output on the frontend.
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

	/**
	 * Fetch the Quotes based on the post type
	 *
	 * @since 1.0.0
	 * @access private
	 * @return array Returns the list of the Quotes
	 */
	private function get_quotes_options() {
		$args = array(
			'post_type'      => 'quote',
			'posts_per_page' => -1, // Retrieve all posts
			'orderby'        => 'title',
			'order'          => 'ASC',
		);

		$quotes  = get_posts( $args );
		$options = array();

		if ( $quotes ) {
			foreach ( $quotes as $quote ) {
				$options[ 'id_'.$quote->ID ] = $quote->post_title;
			}
		}

		return $options;
	}
}
