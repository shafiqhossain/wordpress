<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Article Listing.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class CMMA_PostListingWithFilter_Widget extends \Elementor\Widget_Base {
	/**
	 * Get widget name.
	 *
	 * Retrieve Article Listing name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'cmma-post-listing-with-filter';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Article Listing title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Post Listing Filter', 'cmma-post-listing-with-filter-widget' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Article Listing icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-filter';
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
	 * Retrieve the list of categories the Article Listing belongs to.
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
		return array( 'widget-post-listing-with-filter-style' );
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
		return array( 'widget-post-listing-with-filter-script' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the Article Listing belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return array( 'posts', 'listing','filter' );
	}

	/**
	 * Register Article Listing controls.
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
				'label' => esc_html__( 'Basic Content', 'cmma-post-listing-with-filter-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'selected_post',
			array(
				'label' => esc_html__( 'Select Post', 'cmma-featured-articless-widget' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => [
					[
						'name'      => 'post_select',
						'label'     => esc_html__( 'Select Post', 'cmma-featured-articsles-widget' ),
						'type'      => \Elementor\Controls_Manager::SELECT2,
						'multiple'  => false,
						'options'   => $this->get_load_posts()
					],
				],
				'sortable' => true,
			)
		);
	}

	/**
	 * Render post listing output on the frontend.
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
	 * Return list of all the posts
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	private function get_load_posts() {
		$posts = get_posts( array(
			'posts_per_page' => -1,
			'post_type'      => array( 'project', 'perspective' ),
		) );

		$options = array();
		if ( $posts ) {
			foreach ( $posts as $post ) {
				$options[ $post->ID ] = $post->post_title;
			}
		}

		return $options;
	}
}
