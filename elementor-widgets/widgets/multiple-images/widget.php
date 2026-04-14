<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Multiple Images.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class CMMA_MultipleImages_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve Multiple Images name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'cmma-multiple-images';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Multiple Images title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Multiple Images', 'cmma-multiple-images-widget' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Multiple Images icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-posts-grid';
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
	 * Retrieve the list of categories the Multiple Images belongs to.
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
		return [ 'widget-multiple-images-style' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the Multiple Images belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return ['Multiple Column Images', '2 Column Images', '3 Column Images', '4 Column Images'];
	}

	/**
	 * Register Multiple Images controls.
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
				'label' => esc_html__( 'Basic Content', 'cmma-multiple-images-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
 
 		$this->add_control(
			'color_scheme',
			array(
				'label'   => esc_html__( 'Color scheme ', 'cmma-multiple-images-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'light',
				'options' => array(
					'light' => esc_html__( 'Light', 'cmma-multiple-images-widget' ),
					'dark'  => esc_html__( 'Dark', 'cmma-multiple-images-widget' ),
				),
				'dynamic' => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'images',
			array(
				'label'      => esc_html__( 'Images', 'cmma-multiple-images-widget' ),
				'type'       => \Elementor\Controls_Manager::GALLERY,
				'show_label' => false,
				'default'    => [],
				'max'        => 4,
				'dynamic'    => [
					'active' => true,
				],
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render Multiple Images output on the frontend.
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
