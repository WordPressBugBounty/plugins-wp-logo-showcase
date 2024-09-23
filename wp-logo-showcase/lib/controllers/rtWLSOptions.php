<?php
/**
 * WLS Options Class
 *
 * @package RT_WSL
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'rtWLSOptions' ) ) :
	/**
	 * WLS Options Class
	 */
	class rtWLSOptions {

		/**
		 * Generate Getting field option
		 *
		 * @return array
		 */
		public function rtWLSGeneralSettings() {
			global $rtWLS;

			$settings = get_option( $rtWLS->options['settings'] );

			return [
				'image_resize' => [
					'type'  => 'checkbox',
					'name'  => 'image_resize',
					'id'    => 'wls_image_resize',
					'label' => esc_html__( 'Enable Image Re-Size', 'wp-logo-showcase' ),
					'value' => isset( $settings['image_resize'] ) ? trim( $settings['image_resize'] ) : null,
				],
				'image_width'  => [
					'type'        => 'number',
					'name'        => 'image_width',
					'id'          => 'wls_image_width',
					'label'       => esc_html__( 'Image Width', 'wp-logo-showcase' ),
					'holderClass' => 'hidden',
					'default'     => 250,
					'holderID'    => 'wls_image_width_holder',
					'value'       => isset( $settings['image_width'] ) ? (int) ( $settings['image_width'] ) : null,
				],
				'image_height' => [
					'type'        => 'number',
					'name'        => 'image_height',
					'id'          => 'wls_image_height',
					'label'       => esc_html__( 'Image Height', 'wp-logo-showcase' ),
					'holderClass' => 'hidden',
					'default'     => 190,
					'holderID'    => 'wls_image_height_holder',
					'value'       => isset( $settings['image_height'] ) ? (int) ( $settings['image_height'] ) : null,
				],
			];

		}

		/**
		 * Generate Custom css Field for setting page
		 *
		 * @return array
		 */
		public function rtWLSCustomCss() {
			global $rtWLS;

			$settings = get_option( $rtWLS->options['settings'] );

			return [
				'custom_css' => [
					'type'        => 'custom_css',
					'name'        => 'custom_css',
					'id'          => 'custom-css',
					'holderClass' => 'full',
					'value'       => isset( $settings['custom_css'] ) ? trim( $settings['custom_css'] ) : null,
				],
			];
		}

		/**
		 * Layout array
		 *
		 * @return array
		 */
		public function scLayout() {
			return [
				'grid-layout'     => esc_html__( 'Grid Layout', 'wp-logo-showcase' ),
				'carousel-layout' => esc_html__( 'Carousel Layout', 'wp-logo-showcase' ),
			];
		}

		/**
		 * Layout item list
		 *
		 * @return array
		 */
		public function scLayoutItems() {
			return [
				'logo'        => esc_html__( 'Logo', 'wp-logo-showcase' ),
				'title'       => esc_html__( 'Title', 'wp-logo-showcase' ),
				'description' => esc_html__( 'Description', 'wp-logo-showcase' ),
			];
		}


		/**
		 * Style field
		 *
		 * @return array
		 */
		public function scStyleItems() {
			$items = $this->scLayoutItems();
			unset( $items['logo'] );

			return $items;
		}

		/**
		 * Align options
		 *
		 * @return array
		 */
		public function scWlsAlign() {
			return [
				'left'   => esc_html__( 'Left', 'wp-logo-showcase' ),
				'center' => esc_html__( 'Center', 'wp-logo-showcase' ),
				'right'  => esc_html__( 'Right', 'wp-logo-showcase' ),
			];
		}

		/**
		 * FontSize Options
		 *
		 * @return array
		 */
		public function scWlsFontSize() {
			$size = [];

			for ( $i = 14; $i <= 60; $i ++ ) {
				$size[ $i ] = "{$i} px";
			}

			return $size;
		}

		/**
		 * Order By Options
		 *
		 * @return array
		 */
		public function scOrderBy() {
			return [
				'menu_order' => esc_html__( 'Menu Order', 'wp-logo-showcase' ),
				'title'      => esc_html__( 'Name', 'wp-logo-showcase' ),
				'date'       => esc_html__( 'Date', 'wp-logo-showcase' ),
			];
		}

		/**
		 * Order Options
		 *
		 * @return array
		 */
		public function scOrder() {
			return [
				'ASC'  => esc_html__( 'Ascending', 'wp-logo-showcase' ),
				'DESC' => esc_html__( 'Descending', 'wp-logo-showcase' ),
			];
		}

		/**
		 * Style field options
		 *
		 * @return array
		 */
		public function scStyleFields() {
			return [
				'primary_color' => [
					'type'  => 'colorpicker',
					'name'  => 'wls_primary_color',
					'label' => esc_html__( 'Primary color', 'wp-logo-showcase' ),
				],
				'title_color'   => [
					'type'  => 'colorpicker',
					'name'  => 'wls_title_color',
					'label' => esc_html__( 'Title color', 'wp-logo-showcase' ),
				],
			];
		}


		/**
		 * Column Options
		 *
		 * @return array
		 */
		public function scColumns() {
			return [
				1 => esc_html__( '1 Column', 'wp-logo-showcase' ),
				2 => esc_html__( '2 Column', 'wp-logo-showcase' ),
				3 => esc_html__( '3 Column', 'wp-logo-showcase' ),
				4 => esc_html__( '4 Column', 'wp-logo-showcase' ),
				5 => esc_html__( '5 Column', 'wp-logo-showcase' ),
				6 => esc_html__( '6 Column', 'wp-logo-showcase' ),
			];
		}

		/**
		 * Filter Options
		 *
		 * @return array
		 */
		public function scFilterMetaFields() {
			global $rtWLS;

			return [
				'wls_limit'      => [
					'name'        => 'wls_limit',
					'label'       => esc_html__( 'Limit', 'wp-logo-showcase' ),
					'type'        => 'number',
					'class'       => 'full',
					'description' => esc_html__( 'The number of posts to show. Set empty to show all found posts.', 'wp-logo-showcase' ),
				],
				'wls_categories' => [
					'name'        => 'wls_categories',
					'label'       => esc_html__( 'Categories', 'wp-logo-showcase' ),
					'type'        => 'select',
					'class'       => 'rt-select2',
					'id'          => 'wls_categories',
					'multiple'    => true,
					'description' => esc_html__( 'Select the category you want to filter, Leave it blank for All category', 'wp-logo-showcase' ),
					'options'     => $rtWLS->getAllWlsCategoryList(),
				],
				'wls_order_by'   => [
					'name'    => 'wls_order_by',
					'label'   => esc_html__( 'Order By', 'wp-logo-showcase' ),
					'type'    => 'select',
					'class'   => 'rt-select2',
					'default' => 'date',
					'options' => $this->scOrderBy(),
				],
				'wls_order'      => [
					'name'      => 'wls_order',
					'label'     => esc_html__( 'Order', 'wp-logo-showcase' ),
					'type'      => 'radio',
					'class'     => 'rt-select2',
					'options'   => $this->scOrder(),
					'default'   => 'DESC',
					'alignment' => 'vertical',
				],
			];
		}

		/**
		 * ShortCode Layout Options
		 *
		 * @return array
		 */
		public function scLayoutMetaFields() {
			global $rtWLS;

			return [
				'wls_layout'                   => [
					'name'    => 'wls_layout',
					'type'    => 'select',
					'id'      => 'wls_layout',
					'label'   => esc_html__( 'Layout', 'wp-logo-showcase' ),
					'class'   => 'rt-select2',
					'options' => $this->scLayout(),
				],
				'wls_column'                   => [
					'name'        => 'wls_column',
					'type'        => 'select',
					'label'       => esc_html__( 'Desktop column', 'wp-logo-showcase' ),
					'holderClass' => 'hidden wls_column_holder',
					'id'          => 'wls_column',
					'class'       => 'rt-select2',
					'default'     => 4,
					'options'     => $this->scColumns(),
				],
				'wls_tab_column'               => [
					'name'        => 'wls_tab_column',
					'type'        => 'select',
					'label'       => esc_html__( 'Tab column', 'wp-logo-showcase' ),
					'id'          => 'wls_tab_column',
					'holderClass' => 'hidden wls_column_holder',
					'class'       => 'rt-select2',
					'default'     => 2,
					'options'     => $this->scColumns(),
				],
				'wls_mobile_column'            => [
					'name'        => 'wls_mobile_column',
					'type'        => 'select',
					'label'       => esc_html__( 'Mobile column', 'wp-logo-showcase' ),
					'id'          => 'wls_mobile_column',
					'holderClass' => 'hidden wls_column_holder',
					'class'       => 'rt-select2',
					'default'     => 1,
					'options'     => $this->scColumns(),
				],

				'wls_link_type'                => [
					'name'    => 'wls_link_type',
					'type'    => 'select',
					'label'   => esc_html__( 'Link Type', 'wp-logo-showcase' ),
					'id'      => 'wls_link_type',
					'class'   => 'rt-select2',
					'options' => $this->scLinkTypes(),
				],
				'wls_nofollow'                 => [
					'name'   => 'wls_nofollow',
					'type'   => 'checkbox',
					'label'  => esc_html__( 'Nofollow', 'wp-logo-showcase' ),
					'option' => esc_html__( 'Enable', 'wp-logo-showcase' ),
				],
				'wls_carousel_logo_per_slider' => [
					'name'        => 'wls_carousel_slidesToShow',
					'label'       => esc_html__( 'Slides To Show', 'wp-logo-showcase' ),
					'holderClass' => 'hidden wls_carousel_options_holder',
					'type'        => 'number',
					'default'     => 3,
					'description' => esc_html__( 'Number of logo to display each slider', 'wp-logo-showcase' ),
				],
				'wls_carousel_slidesToScroll'  => [
					'name'        => 'wls_carousel_slidesToScroll',
					'label'       => esc_html__( 'Slides To Scroll', 'wp-logo-showcase' ),
					'holderClass' => 'hidden wls_carousel_options_holder',
					'type'        => 'number',
					'default'     => 3,
					'description' => esc_html__( 'Number of logo to to scroll, Recommended > same as  slides to show', 'wp-logo-showcase' ),
				],
				'wls_carousel_speed'           => [
					'name'        => 'wls_carousel_speed',
					'label'       => esc_html__( 'Speed', 'wp-logo-showcase' ),
					'holderClass' => 'hidden wls_carousel_options_holder',
					'type'        => 'number',
					'default'     => 2000,
					'description' => esc_html__( 'Auto play Speed in milliseconds', 'wp-logo-showcase' ),
				],
				'wls_carousel_options'         => [
					'name'        => 'wls_carousel_options',
					'label'       => esc_html__( 'Carousel Options', 'wp-logo-showcase' ),
					'holderClass' => 'hidden wls_carousel_options_holder',
					'type'        => 'checkbox',
					'multiple'    => true,
					'alignment'   => 'vertical',
					'options'     => $rtWLS->carouselProperty(),
					'default'     => [ 'autoplay', 'arrows', 'dots', 'responsive', 'infinite' ],
				],
				'wls_tooltip'                  => [
					'name'   => 'wls_tooltip',
					'type'   => 'checkbox',
					'label'  => __( 'ToolTip  (<span style="color:red;">Pro</span>)', 'wp-logo-showcase' ),
					'option' => 'Enable',
					'attr'   => 'disabled',
					'id'     => 'wls_tooltip',
				],
				'wls_box_highlight'            => [
					'name'   => 'wls_box_highlight ',
					'type'   => 'checkbox',
					'label'  => __( 'Box Highlight (<span style="color:red;">Pro</span>)', 'wp-logo-showcase' ),
					'option' => 'Enable',
					'attr'   => 'disabled',
					'id'     => 'wls_box_highlight',
				],
				'wls_grayscale'                => [
					'name'   => 'wls_grayscale',
					'type'   => 'checkbox',
					'label'  => __( 'Grayscale (<span style="color:red;">Pro</span>)', 'wp-logo-showcase' ),
					'option' => 'Enable',
					'attr'   => 'disabled',
					'id'     => 'wls_grayscale',
				],
				'wls_image_size'               => [
					'name'    => 'wls_image_size',
					'type'    => 'select',
					'label'   => esc_html__( 'Image Size', 'wp-logo-showcase' ),
					'id'      => 'wls_image_size',
					'class'   => 'rt-select2',
					'options' => $rtWLS->get_image_sizes(),
				],
				'wls_custom_image_size'        => [
					'name'        => 'wls_custom_image_size',
					'type'        => 'image_size',
					'label'       => esc_html__( 'Custom Image Size', 'wp-logo-showcase' ),
					'holderClass' => 'hidden wls_image_size_holder',
				],
			];
		}

		/**
		 * Link type options
		 *
		 * @return array
		 */
		public function scLinkTypes() {
			return [
				'new_window' => esc_html__( 'Open in new window', 'wp-logo-showcase' ),
				'self'       => esc_html__( 'Open in same window', 'wp-logo-showcase' ),
				'no_link'    => esc_html__( 'No link', 'wp-logo-showcase' ),
			];
		}
		/**
		 * ShortCode Layout Options
		 *
		 * @return array
		 */
		public function scFieldSelectionMetaFields() {
			return [
				'_wls_items' => [
					'name'      => '_wls_items',
					'type'      => 'checkbox',
					'multiple'  => true,
					'alignment' => 'vertical',
					'id'        => '_wls_items',
					'label'     => esc_html__( 'Field Selection', 'wp-logo-showcase' ),
					'default'   => [ 'logo' ],
					'options'   => $this->scLayoutItems(),
				],
			];
		}


		/**
		 * Carousel Property
		 *
		 * @return array
		 */
		public function carouselProperty() {
			return [
				'autoplay'     => esc_html__( 'Auto Play', 'wp-logo-showcase' ),
				'arrows'       => esc_html__( 'Arrow nav button', 'wp-logo-showcase' ),
				'dots'         => esc_html__( 'Dots', 'wp-logo-showcase' ),
				'pauseOnHover' => esc_html__( 'Pause on hover', 'wp-logo-showcase' ),
				'infinite'     => esc_html__( 'Infinite loop', 'wp-logo-showcase' ),
				'rtl'          => esc_html__( 'Right to Left', 'wp-logo-showcase' ),
			];
		}

		/**
		 * Custom Meta field for logo post type
		 *
		 * @return array
		 */
		public function rtLogoMetaFields() {
			return [
				'site_url'         => [
					'type'        => 'url',
					'name'        => '_wls_site_url',
					'label'       => esc_html__( 'Client website URL', 'wp-logo-showcase' ),
					'placeholder' => esc_html__( 'Client URL e.g: http://example.com', 'wp-logo-showcase' ),
					'description' => 'Link to open when image is clicked (if links are active)',
				],
				'logo_description' => [
					'type'        => 'textarea',
					'name'        => '_wls_logo_description',
					'class'       => 'rt-textarea',
					'esc_html'    => true,
					'label'       => esc_html__( 'Logo Description', 'wp-logo-showcase' ),
					'placeholder' => esc_html__( 'Logo description', 'wp-logo-showcase' ),
				],
				'logo_alt'         => [
					'type'        => 'text',
					'name'        => '_wls_logo_alt',
					'label'       => esc_html__( 'Alternate Text', 'wp-logo-showcase' ),
					'placeholder' => esc_html__( 'Alt for url and image', 'wp-logo-showcase' ),
				],
			];
		}

		public function get_pro_feature_list() {
			$pro = 'https://1.envato.market/4jmQ9';
			return '<ol>
						<li>Isotope layout</li>
						<li>Carousel Slider with multiple features.</li>
						<li>Custom Logo Re-sizing.</li>
						<li>Drag & Drop Layout builder.</li>
						<li>Drag & Drop Logo ordering.</li>
						<li>Tooltip Enable/Disable option.</li>
					</ol>
					<a href="' . esc_url( $pro ) . '" class="rt-admin-btn" target="_blank">Get Pro Version</a>';
		}
	}
endif;
