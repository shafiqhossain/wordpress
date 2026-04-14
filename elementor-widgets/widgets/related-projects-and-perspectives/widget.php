<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Related Projects and Perspectives.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class CMMA_Related_Projects_and_Perspectives_Widget extends \Elementor\Widget_Base {


	/**
	 * Get widget name.
	 *
	 * Retrieve Related Projects and Perspectives name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'cmma-related_projects_and_perspectives';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Related Projects and Perspectives title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Related Projects and Perspectives', 'elementor-related_projects_and_perspectives-widget' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Related Projects and Perspectives icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-code';
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
	 * Retrieve the list of categories the Related Projects and Perspectives belongs to.
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
		return array( 'widget-related-projects-and-perspectives-style', 'cmma-elementor-slick-style' );
	}


	public function get_script_depends() {
		return array( 'widget-related-projects-and-perspectives-script', 'cmma-elementor-slick-script' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the Related Projects and Perspectives belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return array( 'Projects', 'Perspectives' );
	}

	/**
	 * Register Related Projects and Perspectives controls.
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
				'label' => esc_html__( 'Basic Content', 'elementor-related_projects_and_perspectives-widget' ),
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
					'label'       => esc_html__('Jump Navigation Menu Title', 'related_projects_and_perspectives-widget'),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'placeholder' => esc_html__('Jump Navigation Menu Title', 'related_projects_and_perspectives-widget'),
					'condition'   => [
						'enable_jump_navigation' => 'yes',
					],
				]
			);
		}

		$this->add_control(
			'headline',
			array(
				'label'       => esc_html__( 'Headline', 'elementor-related_projects_and_perspectives-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Title', 'elementor-related_projects_and_perspectives-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'       => esc_html__( 'Button Text', 'elementor-related_projects_and_perspectives-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter Button Text', 'elementor-related_projects_and_perspectives-widget' ),
				'dynamic'     => [
					'active' => true,
				],
			)
		);

		$this->add_control(
			'button_link',
			array(
				'label'         => esc_html__( 'Button Link', 'elementor-related_projects_and_perspectives-widget' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://your-link.com', 'elementor-related_projects_and_perspectives-widget' ),
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

		// Add the toggle button control
		$this->add_control(
			'toggle_show_markets',
			array(
				'label'        => esc_html__( 'Show Projects/Perspectives Based On Markets', 'elementor-related_projects_and_perspectives-widget' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'elementor-related_projects_and_perspectives-widget' ),
				'label_off'    => esc_html__( 'No', 'elementor-related_projects_and_perspectives-widget' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'selected_market',
			array(
				'label'     => esc_html__( 'Select Market', 'elementor-related_projects_and_perspectives-widget' ),
				'type'      => \Elementor\Controls_Manager::SELECT2,
				'options'   => $this->get_market_options(), // Fetch the options
				'dynamic'   => [
					'active' => true,
				],
				'condition' => array(
					'toggle_show_markets' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_by_category',
			array(
				'label'     => esc_html__( 'Show Posts By Category', 'elementor-related_projects_and_perspectives-widget' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'elementor-related_projects_and_perspectives-widget' ),
				'label_off'    => esc_html__( 'No', 'elementor-related_projects_and_perspectives-widget' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition' => array(
					'toggle_show_markets' => '',
				),
			)
		);

		$this->add_control(
			'selected_post_category',
			array(
				'label'     => esc_html__( 'Select Category', 'elementor-related_projects_and_perspectives-widget' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => $this->get_taxonomy_terms_options(),
				'condition' => array(
					'show_by_category' => 'yes',
				),
			)
		);

		$this->add_control(
			'selected_category_post_limit',
			array(
				'label'       => esc_html__( 'Category Posts Max Limit', 'elementor-related_projects_and_perspectives-widget' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'placeholder' => esc_html__( 'Enter limit', 'elementor-related_projects_and_perspectives-widget' ),
				'default'     => 10,
				'min'         => 5,
				'max'         => 25,
				'condition'   => array(
					'toggle_show_markets' => '',
					'show_by_category'    => 'yes',
				),
			)
		);


		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'post_id',
			[
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => $this->get_post_perspective_options(),
				'default'     => esc_html__( 'Post' , 'related_projects_and_perspectives-widget' ),
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-control-post_id select' => 'data-post-id',
				],
			]
		);

		$this->add_control(
			'selected_posts',
			[
				'label'       => esc_html__( 'Select Projects & Perspective Posts', 'related_projects_and_perspectives-widget' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'title_field' => '#{{{ post_id }}}',
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'post_id'   => esc_html__( 'Choose Post', 'related_projects_and_perspectives-widget' ),
					],
				],
				'condition' => array(
					'toggle_show_markets' => '',
					'show_by_category' => '',
				),
			]
		);

		$this->add_control(
			'selected_post_orderby',
			array(
				'label'     => esc_html__( 'Order By', 'elementor-related_projects_and_perspectives-widget' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default' => 'date',
				'options'   => [
					"ID" => "Post ID",
					"author" => "Post Author",
					"title" => "Post Title",
					"name" => "Post Slug",
					"date" => "Post Date",
					"rand" => "Random order",
					"post__in" => "By Selected Post",
				],
				'condition' => array(
					'toggle_show_markets' => '',
					'show_by_category' => '',
				),
			)
		);

		$this->add_control(
			'selected_post_order',
			array(
				'label'   => esc_html__( 'Order ', 'elementor-related_projects_and_perspectives-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'ASC',
				'dynamic'     => [
					'active' => true,
				],
				'options' => array(
					"ASC" => "Ascending",
					"DESC" => "Descending",
				),
				'condition' => array(
					'toggle_show_markets' => '',
					'show_by_category' => '',
				),
			)
		);

		$this->add_control(
			'color_scheme',
			array(
				'label'   => esc_html__( 'Color scheme ', 'elementor-related_projects_and_perspectives-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'light',
				'dynamic'     => [
					'active' => true,
				],
				'options' => array(
					'light' => esc_html__( 'Light', 'elementor-related_projects_and_perspectives-widget' ),
					'dark'  => esc_html__( 'Dark', 'elementor-related_projects_and_perspectives-widget' ),
				),
			)
		);
	}

	/**
	 * Render Related Projects and Perspectives output on the frontend.
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

	private function get_market_options() {
		$args    = array(
			'post_type'      => 'market',
			'posts_per_page' => -1, // Retrieve all posts
		);
		$markets = get_posts( $args );
		$options = array();
		if ( $markets ) {
			foreach ( $markets as $market ) {
				$options[ $market->ID ] = $market->post_title;
			}
		}
		return $options;
	}

	private function get_post_perspective_options() {
		$args_project = array(
			'post_type'      => 'project',
			'posts_per_page' => -1,
			'orderby'        => 'rand',
		);

		$args_perspective = array(
			'post_type'      => 'perspective',
			'posts_per_page' => -1,
			'orderby'        => 'rand',
		);

		$testimonials_project     = get_posts( $args_project );
		$testimonials_perspective = get_posts( $args_perspective );

		$options = array();

		// Add options from the 'project' post type
		if ( $testimonials_project ) {
			foreach ( $testimonials_project as $testimonial ) {
				$options[ $testimonial->ID ] = $testimonial->post_title;
			}
		}

		// Add options from the 'perspective' post type
		if ( $testimonials_perspective ) {
			foreach ( $testimonials_perspective as $testimonial ) {
				$options[ $testimonial->ID ] = $testimonial->post_title;
			}
		}

		return $options;
	}

	protected function get_taxonomy_terms_options( $taxonomy = 'category' ) {
		$terms = get_terms( array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
		) );

		$options = array();

		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$options[ $term->term_id ] = $term->name;
			}
		}

		return $options;
	}
}
