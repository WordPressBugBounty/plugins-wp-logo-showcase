<?php
/**
 * Content Section Class.
 *
 * Every control here maps 1:1 onto an option the shortcode already
 * supports, so the Elementor widgets render through the same views.
 *
 * @package RT_WSL
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'rtWLSSElementorContentSections' ) ) :
	/**
	 * Content Section Class.
	 */
	class rtWLSSElementorContentSections {
		/**
		 * Accumulates tab fields.
		 *
		 * @var array
		 */
		private $fields = [];

		/**
		 * Tab name.
		 *
		 * @var string
		 */
		private $tab = \Elementor\Controls_Manager::TAB_CONTENT;

		/**
		 * Responsive column control shared by both layouts.
		 *
		 * @return void
		 */
		private function columns() {
			global $rtWLS;

			$this->fields[] = [
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'id'              => 'wls_el_layout_col_note',
				'raw'             => sprintf(
					'<h3 class="wls-elementor-group-heading">%s</h3>',
					esc_html__( 'Responsive Columns', 'wp-logo-showcase' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			];

			$this->fields[] = [
				'type'        => \Elementor\Controls_Manager::SELECT,
				'id'          => 'wls_el_cols',
				'mode'        => 'responsive',
				'label'       => esc_html__( 'Number of Columns', 'wp-logo-showcase' ),
				'description' => esc_html__( 'Please select the number of columns to show per row.', 'wp-logo-showcase' ),
				'options'     => $rtWLS->scColumns(),
				'default'     => 4,
				'device_args' => [
					\Elementor\Controls_Stack::RESPONSIVE_TABLET => [
						'default' => 3,
					],
					\Elementor\Controls_Stack::RESPONSIVE_MOBILE => [
						'default' => 2,
					],
				],
			];
		}

		/**
		 * Layout Section - Grid
		 *
		 * @return array
		 */
		public function gridLayout() {
			$this->fields[] = [
				'mode'  => 'section_start',
				'id'    => 'wls_el_layout_section',
				'label' => esc_html__( 'Layout', 'wp-logo-showcase' ),
				'tab'   => $this->tab,
			];

			$this->columns();

			$this->fields = array_merge( apply_filters( 'rtwls_elementor_before_grid_layout_end', $this->fields ) );

			$this->fields[] = [
				'mode' => 'section_end',
			];

			return $this->fields;
		}

		/**
		 * Layout Section - Slider
		 *
		 * @return array
		 */
		public function sliderLayout() {
			$this->fields[] = [
				'mode'  => 'section_start',
				'id'    => 'wls_el_layout_section',
				'label' => esc_html__( 'Layout', 'wp-logo-showcase' ),
				'tab'   => $this->tab,
			];

			$this->columns();

			$this->fields = array_merge( apply_filters( 'rtwls_elementor_before_slider_layout_end', $this->fields ) );

			$this->fields[] = [
				'mode' => 'section_end',
			];

			return $this->fields;
		}

		/**
		 * Image Section
		 *
		 * @return array
		 */
		public function image() {
			global $rtWLS;

			$sizes                          = $rtWLS->get_image_sizes();
			$sizes['full']                  = esc_html__( 'Full', 'wp-logo-showcase' );
			$sizes['wls_custom_image_size'] = esc_html__( 'Custom Size', 'wp-logo-showcase' );

			$this->fields[] = [
				'mode'  => 'section_start',
				'id'    => 'wls_el_image_section',
				'label' => esc_html__( 'Image', 'wp-logo-showcase' ),
				'tab'   => $this->tab,
			];

			$this->fields[] = [
				'type'        => \Elementor\Controls_Manager::SELECT,
				'id'          => 'wls_el_image',
				'label'       => esc_html__( 'Select Image Size', 'wp-logo-showcase' ),
				'options'     => $sizes,
				'default'     => 'full',
				'label_block' => true,
			];

			$this->fields[] = [
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'id'        => 'wls_el_image_width',
				'label'     => esc_html__( 'Image Width (px)', 'wp-logo-showcase' ),
				'default'   => 180,
				'min'       => 1,
				'condition' => [ 'wls_el_image' => 'wls_custom_image_size' ],
			];

			$this->fields[] = [
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'id'        => 'wls_el_image_height',
				'label'     => esc_html__( 'Image Height (px)', 'wp-logo-showcase' ),
				'default'   => 90,
				'min'       => 1,
				'condition' => [ 'wls_el_image' => 'wls_custom_image_size' ],
			];

			$this->fields[] = [
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'id'           => 'wls_el_image_crop',
				'label'        => esc_html__( 'Hard Crop', 'wp-logo-showcase' ),
				'description'  => esc_html__( 'Switch on to crop the image to the exact dimension.', 'wp-logo-showcase' ),
				'label_on'     => esc_html__( 'Yes', 'wp-logo-showcase' ),
				'label_off'    => esc_html__( 'No', 'wp-logo-showcase' ),
				'return_value' => 'yes',
				'condition'    => [ 'wls_el_image' => 'wls_custom_image_size' ],
			];

			$this->fields = array_merge( apply_filters( 'rtwls_elementor_before_image_end', $this->fields ) );

			$this->fields[] = [
				'mode' => 'section_end',
			];

			return $this->fields;
		}

		/**
		 * Query / filtering Section
		 *
		 * @return array
		 */
		public function filtering() {
			global $rtWLS;

			$this->fields[] = [
				'mode'  => 'section_start',
				'id'    => 'wls_el_filtering_section',
				'label' => esc_html__( 'Query', 'wp-logo-showcase' ),
				'tab'   => $this->tab,
			];

			$this->fields[] = [
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'id'          => 'wls_el_categories',
				'label'       => esc_html__( 'Categories', 'wp-logo-showcase' ),
				'options'     => $rtWLS->getAllWlsCategoryList(),
				'multiple'    => true,
				'label_block' => true,
				'description' => esc_html__( 'Please select the categories to show. Leave it blank to include all categories.', 'wp-logo-showcase' ),
			];

			$this->fields[] = [
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'id'              => 'wls_el_filtering_note',
				'raw'             => sprintf(
					'<h3 class="wls-elementor-group-heading">%s</h3>',
					esc_html__( 'Include / Exclude', 'wp-logo-showcase' )
				),
				'content_classes' => 'elementor-panel-heading-title',
				'separator'       => 'before',
			];

			$this->fields[] = [
				'mode' => 'tabs_start',
				'id'   => 'wls_el_filtering_tab',
			];

			$this->fields[] = [
				'mode'  => 'tab_start',
				'id'    => 'wls_el_filtering_include_tab',
				'label' => esc_html__( 'Include', 'wp-logo-showcase' ),
			];

			$this->fields[] = [
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'id'          => 'wls_el_include_posts',
				'label'       => esc_html__( 'Include Logos', 'wp-logo-showcase' ),
				'options'     => $rtWLS->getLogoList(),
				'multiple'    => true,
				'label_block' => true,
				'description' => esc_html__( 'Please select the logos to show. Leave it blank to include all logos.', 'wp-logo-showcase' ),
			];

			$this->fields[] = [
				'mode' => 'tab_end',
			];

			$this->fields[] = [
				'mode'  => 'tab_start',
				'id'    => 'wls_el_filtering_exclude_tab',
				'label' => esc_html__( 'Exclude', 'wp-logo-showcase' ),
			];

			$this->fields[] = [
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'id'          => 'wls_el_exclude_posts',
				'label'       => esc_html__( 'Exclude Logos', 'wp-logo-showcase' ),
				'options'     => $rtWLS->getLogoList(),
				'multiple'    => true,
				'label_block' => true,
				'description' => esc_html__( 'Please select the logos to exclude. Leave it blank to exclude none.', 'wp-logo-showcase' ),
			];

			$this->fields[] = [
				'mode' => 'tab_end',
			];

			$this->fields[] = [
				'mode' => 'tabs_end',
			];

			$this->fields[] = [
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'id'          => 'wls_el_posts_limit',
				'label'       => esc_html__( 'Logo Limit', 'wp-logo-showcase' ),
				'min'         => 1,
				'description' => esc_html__( 'The number of logos to show. Leave it blank to show all logos.', 'wp-logo-showcase' ),
				'separator'   => 'before',
			];

			$this->fields[] = [
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'id'              => 'wls_el_sorting_note',
				'raw'             => sprintf(
					'<h3 class="wls-elementor-group-heading">%s</h3>',
					esc_html__( 'Sorting', 'wp-logo-showcase' )
				),
				'content_classes' => 'elementor-panel-heading-title',
				'separator'       => 'before',
			];

			$this->fields[] = [
				'type'        => \Elementor\Controls_Manager::SELECT,
				'id'          => 'wls_el_posts_order_by',
				'label'       => esc_html__( 'Order By', 'wp-logo-showcase' ),
				'options'     => $rtWLS->scOrderBy(),
				'default'     => 'menu_order',
				'label_block' => true,
			];

			$this->fields[] = [
				'type'        => \Elementor\Controls_Manager::SELECT,
				'id'          => 'wls_el_posts_order',
				'label'       => esc_html__( 'Order', 'wp-logo-showcase' ),
				'options'     => $rtWLS->scOrder(),
				'default'     => 'ASC',
				'label_block' => true,
			];

			$this->fields = array_merge( apply_filters( 'rtwls_elementor_before_filtering_end', $this->fields ) );

			$this->fields[] = [
				'mode' => 'section_end',
			];

			return $this->fields;
		}

		/**
		 * Content visibility Section
		 *
		 * @return array
		 */
		public function contentVisibility() {
			global $rtWLS;

			$this->fields[] = [
				'mode'  => 'section_start',
				'id'    => 'wls_el_field_display_section',
				'label' => esc_html__( 'Content Visibility', 'wp-logo-showcase' ),
				'tab'   => $this->tab,
			];

			$this->fields[] = [
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'id'          => 'wls_el_items',
				'label'       => esc_html__( 'Field Selection', 'wp-logo-showcase' ),
				'options'     => $rtWLS->scLayoutItems(),
				'multiple'    => true,
				'label_block' => true,
				'default'     => [ 'logo' ],
				'description' => esc_html__( 'Please select the fields to display, in the order you want them rendered.', 'wp-logo-showcase' ),
			];

			// Shown once Description is picked above (Elementor matches a single
			// value against a multi-select as "contains").
			// render_type template: the clamp changes the box height, so the
			// widget re-renders and the equal-height pass runs again in the editor.
			$this->fields[] = [
				'type'        => \Elementor\Controls_Manager::SELECT,
				'id'          => 'wls_el_description_limit',
				'label'       => esc_html__( 'Content Limit', 'wp-logo-showcase' ),
				'options'     => $rtWLS->scDescriptionLimits(),
				'default'     => '',
				'description' => esc_html__( 'Default shows the full description.', 'wp-logo-showcase' ),
				'selectors'   => [
					'{{WRAPPER}} .rt-wpls .logo-description' => 'display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: {{VALUE}}; overflow: hidden;',
				],
				'render_type' => 'template',
				'condition'   => [ 'wls_el_items' => 'description' ],
			];

			$this->fields = array_merge( apply_filters( 'rtwls_elementor_before_content_visibility_end', $this->fields ) );

			$this->fields[] = [
				'mode' => 'section_end',
			];

			return $this->fields;
		}

		/**
		 * Link Section
		 *
		 * @return array
		 */
		public function link() {
			global $rtWLS;

			$this->fields[] = [
				'mode'  => 'section_start',
				'id'    => 'wls_el_link_section',
				'label' => esc_html__( 'Link', 'wp-logo-showcase' ),
				'tab'   => $this->tab,
			];

			$this->fields[] = [
				'type'        => \Elementor\Controls_Manager::SELECT,
				'id'          => 'wls_el_link_type',
				'label'       => esc_html__( 'Link Type', 'wp-logo-showcase' ),
				'options'     => $rtWLS->scLinkTypes(),
				'default'     => 'new_window',
				'label_block' => true,
				'description' => esc_html__( 'Where the logo site URL opens.', 'wp-logo-showcase' ),
			];

			$this->fields[] = [
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'id'           => 'wls_el_nofollow',
				'label'        => esc_html__( 'Add nofollow to logo links', 'wp-logo-showcase' ),
				'label_on'     => esc_html__( 'Yes', 'wp-logo-showcase' ),
				'label_off'    => esc_html__( 'No', 'wp-logo-showcase' ),
				'return_value' => 'yes',
				'condition'    => [ 'wls_el_link_type!' => 'no_link' ],
			];

			$this->fields = array_merge( apply_filters( 'rtwls_elementor_before_link_end', $this->fields ) );

			$this->fields[] = [
				'mode' => 'section_end',
			];

			return $this->fields;
		}

		/**
		 * Slider settings Section
		 *
		 * @return array
		 */
		public function slider() {
			$this->fields[] = [
				'mode'  => 'section_start',
				'id'    => 'wls_el_slider_section',
				'label' => esc_html__( 'Slider Settings', 'wp-logo-showcase' ),
				'tab'   => $this->tab,
			];

			$this->fields[] = [
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'id'          => 'wls_el_slides_to_scroll',
				'label'       => esc_html__( 'Slides to Scroll', 'wp-logo-showcase' ),
				'default'     => 3,
				'min'         => 1,
				'description' => esc_html__( 'Number of logos scrolled per slide.', 'wp-logo-showcase' ),
			];

			$this->fields[] = [
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'id'      => 'wls_el_slide_speed',
				'label'   => esc_html__( 'Slide Speed (in ms)', 'wp-logo-showcase' ),
				'default' => 2000,
				'min'     => 100,
				'step'    => 100,
			];

			$this->fields[] = [
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'id'           => 'wls_el_slider_infinite',
				'label'        => esc_html__( 'Infinite Loop', 'wp-logo-showcase' ),
				'label_on'     => esc_html__( 'On', 'wp-logo-showcase' ),
				'label_off'    => esc_html__( 'Off', 'wp-logo-showcase' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			];

			$this->fields[] = [
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'id'           => 'wls_el_slider_nav',
				'label'        => esc_html__( 'Navigation Arrows', 'wp-logo-showcase' ),
				'label_on'     => esc_html__( 'On', 'wp-logo-showcase' ),
				'label_off'    => esc_html__( 'Off', 'wp-logo-showcase' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			];

			$this->fields[] = [
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'id'           => 'wls_el_slider_pagi',
				'label'        => esc_html__( 'Dot Pagination', 'wp-logo-showcase' ),
				'label_on'     => esc_html__( 'On', 'wp-logo-showcase' ),
				'label_off'    => esc_html__( 'Off', 'wp-logo-showcase' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			];

			$this->fields[] = [
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'id'           => 'wls_el_slider_rtl',
				'label'        => esc_html__( 'Right to Left', 'wp-logo-showcase' ),
				'label_on'     => esc_html__( 'On', 'wp-logo-showcase' ),
				'label_off'    => esc_html__( 'Off', 'wp-logo-showcase' ),
				'return_value' => 'yes',
			];

			$this->fields[] = [
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'id'              => 'wls_el_slider_autoplay_note',
				'raw'             => sprintf(
					'<h3 class="wls-elementor-group-heading">%s</h3>',
					esc_html__( 'Autoplay', 'wp-logo-showcase' )
				),
				'content_classes' => 'elementor-panel-heading-title',
				'separator'       => 'before',
			];

			$this->fields[] = [
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'id'           => 'wls_el_slide_autoplay',
				'label'        => esc_html__( 'Enable Autoplay?', 'wp-logo-showcase' ),
				'label_on'     => esc_html__( 'On', 'wp-logo-showcase' ),
				'label_off'    => esc_html__( 'Off', 'wp-logo-showcase' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			];

			$this->fields[] = [
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'id'           => 'wls_el_pause_hover',
				'label'        => esc_html__( 'Pause on Mouse Hover?', 'wp-logo-showcase' ),
				'label_on'     => esc_html__( 'On', 'wp-logo-showcase' ),
				'label_off'    => esc_html__( 'Off', 'wp-logo-showcase' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [ 'wls_el_slide_autoplay' => 'yes' ],
			];

			$this->fields = array_merge( apply_filters( 'rtwls_elementor_before_slider_end', $this->fields ) );

			$this->fields[] = [
				'mode' => 'section_end',
			];
			return $this->fields;
		}
	}
endif;
