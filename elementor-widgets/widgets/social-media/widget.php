<?php
if (!defined( 'ABSPATH' )) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Social Media.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class CMMA_SocialMedia_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve Social Media name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'cmma-social-media';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Social Media title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title()
	{
		return esc_html__( 'Social Media', 'cmma-social-media-widget' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Social Media icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-social-icons';
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
	 * Retrieve the list of categories the Social Media belongs to.
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
		return [ 'widget-social-media-style' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the Social Media belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return ['social feed', 'social media'];
	}

	/**
	 * Register Social Media controls.
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
				'label' => esc_html__( 'Basic Content', 'cmma-social-media-widget' ),
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
				'label'       => esc_html__( 'Headline', 'cmma-social-media-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Headline', 'cmma-social-media-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'social_media_feed_id',
			array(
				'label'   => esc_html__('Select Social Media Feed', 'cmma-social-media-widget'),
				'type'    => \Elementor\Controls_Manager::SELECT2,
				'options' => $this->get_social_media_feeds(),
			)
		);

		$this->add_control(
			'color_scheme',
			[
				'label'   => esc_html__( 'Color scheme ', 'cmma-single-image' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'light',
				'dynamic' => [
					'active' => true,
				],
				'options' => [
					'light' => esc_html__( 'Light', 'cmma-single-image' ),
					'dark'  => esc_html__( 'Dark', 'cmma-single-image' ),
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render Social Media output on the frontend.
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
	 * Retrieves social media feeds from the database.
	 *
	 * This function fetches social media feeds from the WordPress database.
	 * It selects feed IDs and names from the wp_sbi_feeds table where status is 'publish'.
	 *
	 * @return array Associative array containing feed IDs as keys and feed names as values.
	 */
	private function get_social_media_feeds() {
		global $wpdb;

		$query = $wpdb->prepare("SELECT id, feed_name FROM {$wpdb->prefix}sbi_feeds WHERE status = %s", 'publish');
		$results = $wpdb->get_results($query);

		$socialFeed = [];

		if ($results) {
			foreach ($results as $result) {
				$socialFeed['id_' . $result->id] = $result->feed_name;
			}
		}

		return $socialFeed;
	}

}