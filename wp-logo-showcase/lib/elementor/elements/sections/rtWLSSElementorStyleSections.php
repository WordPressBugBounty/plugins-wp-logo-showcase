<?php
/**
 * Style Section Class.
 *
 * Selectors target the markup produced by lib/views/elementor/*.php, which
 * is the same markup the shortcode renders.
 *
 * @package RT_WSL
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'rtWLSSElementorStyleSections' ) ) :
	/**
	 * Style Section Class.
	 */
	class rtWLSSElementorStyleSections {
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
		private $tab = \Elementor\Controls_Manager::TAB_STYLE;

		/**
		 * Item box Section
		 *
		 * @return array
		 */
		public function box() {
			$this->fields[] = [
				'mode'  => 'section_start',
				'id'    => 'wls_el_box_style_section',
				'label' => esc_html__( 'Logo Box', 'wp-logo-showcase' ),
				'tab'   => $this->tab,
			];

			// Drives the --rt-primary-color palette variable, the same one the
			// shortcode's Primary color setting overrides. Every rule in
			// wplogoshowcase.css built on it follows — the grid hover border and
			// shadow (color-mix), slider arrows and dots included.
			$this->fields[] = [
				'type'        => \Elementor\Controls_Manager::COLOR,
				'id'          => 'wls_el_primary_color',
				'label'       => esc_html__( 'Primary Color', 'wp-logo-showcase' ),
				'description' => esc_html__( 'Accent colour used for the hover border, hover shadow and slider navigation.', 'wp-logo-showcase' ),
				'selectors'   => [
					'{{WRAPPER}} .rt-wpls' => '--rt-primary-color: {{VALUE}};',
				],
			];

			$this->fields[] = [
				'type'      => \Elementor\Controls_Manager::COLOR,
				'id'        => 'wls_el_box_bg_color',
				'label'     => esc_html__( 'Background Color', 'wp-logo-showcase' ),
				'selectors' => [
					'{{WRAPPER}} .rt-wpls .single-logo' => 'background-color: {{VALUE}};',
				],
			];

			$this->fields[] = [
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Border::get_type(),
				'id'       => 'wls_el_box_border',
				'selector' => '{{WRAPPER}} .rt-wpls .single-logo',
			];

			$this->fields[] = [
				'mode'       => 'responsive',
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'id'         => 'wls_el_box_radius',
				'label'      => esc_html__( 'Border Radius', 'wp-logo-showcase' ),
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rt-wpls .single-logo' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			];

			$this->fields[] = [
				'mode'       => 'responsive',
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'id'         => 'wls_el_box_padding',
				'label'      => esc_html__( 'Padding', 'wp-logo-showcase' ),
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rt-wpls .single-logo' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			];

			$this->fields[] = [
				'mode'       => 'responsive',
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'id'         => 'wls_el_box_margin',
				'label'      => esc_html__( 'Margin', 'wp-logo-showcase' ),
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rt-wpls .single-logo' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			];

			$this->fields[] = [
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Box_Shadow::get_type(),
				'id'       => 'wls_el_box_shadow',
				'selector' => '{{WRAPPER}} .rt-wpls .single-logo',
			];

			$this->fields[] = [
				'mode'       => 'responsive',
				'type'       => \Elementor\Controls_Manager::CHOOSE,
				'id'         => 'wls_el_box_alignment',
				'label'      => esc_html__( 'Alignment', 'wp-logo-showcase' ),
				'options'    => [
					'left'   => [
						'title' => esc_html__( 'Left', 'wp-logo-showcase' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wp-logo-showcase' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'wp-logo-showcase' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'    => 'center',
				'selectors'  => [
					'{{WRAPPER}} .rt-wpls .single-logo-container> *' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .rt-wpls .single-logo-container' => 'text-align: {{VALUE}};',
				],
			];

			$this->fields = array_merge( apply_filters( 'rtwls_elementor_before_box_style_end', $this->fields ) );

			$this->fields[] = [
				'mode' => 'section_end',
			];

			return $this->fields;
		}

		/**
		 * Logo image Section
		 *
		 * @return array
		 */
		public function image() {
			$this->fields[] = [
				'mode'  => 'section_start',
				'id'    => 'wls_el_image_style_section',
				'label' => esc_html__( 'Logo Image', 'wp-logo-showcase' ),
				'tab'   => $this->tab,
			];

			$this->fields[] = [
				'mode'       => 'responsive',
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'id'         => 'wls_el_image_max_width',
				'label'      => esc_html__( 'Max Width', 'wp-logo-showcase' ),
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 20,
						'max' => 800,
					],
					'%'  => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-wpls .single-logo-container img' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			];

			$this->fields[] = [
				'mode'       => 'responsive',
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'id'         => 'wls_el_image_radius',
				'label'      => esc_html__( 'Border Radius', 'wp-logo-showcase' ),
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rt-wpls .single-logo-container img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			];

			$this->fields[] = [
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Css_Filter::get_type(),
				'id'       => 'wls_el_image_filter',
				'selector' => '{{WRAPPER}} .rt-wpls .single-logo-container img',
			];

			$this->fields[] = [
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Css_Filter::get_type(),
				'id'       => 'wls_el_image_filter_hover',
				'label'    => esc_html__( 'Hover Filter', 'wp-logo-showcase' ),
				'selector' => '{{WRAPPER}} .rt-wpls .single-logo:hover .single-logo-container img',
			];

			$this->fields = array_merge( apply_filters( 'rtwls_elementor_before_image_style_end', $this->fields ) );

			$this->fields[] = [
				'mode' => 'section_end',
			];

			return $this->fields;
		}

		/**
		 * Typography Section
		 *
		 * @return array
		 */
		public function typography() {
			$this->fields[] = [
				'mode'  => 'section_start',
				'id'    => 'wls_el_typography_section',
				'label' => esc_html__( 'Typography', 'wp-logo-showcase' ),
				'tab'   => $this->tab,
			];

			$this->fields[] = [
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'id'              => 'wls_el_title_note',
				'raw'             => sprintf(
					'<h3 class="wls-elementor-group-heading">%s</h3>',
					esc_html__( 'Title', 'wp-logo-showcase' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			];

			$this->fields[] = [
				'type'      => \Elementor\Controls_Manager::COLOR,
				'id'        => 'wls_el_title_color',
				'label'     => esc_html__( 'Title Color', 'wp-logo-showcase' ),
				'selectors' => [
					'{{WRAPPER}} .rt-wpls .single-logo-container h3'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .rt-wpls .single-logo-container h3 a' => 'color: {{VALUE}};',
				],
			];

			$this->fields[] = [
				'type'      => \Elementor\Controls_Manager::COLOR,
				'id'        => 'wls_el_title_hover_color',
				'label'     => esc_html__( 'Title Hover Color', 'wp-logo-showcase' ),
				'selectors' => [
					'{{WRAPPER}} .rt-wpls .single-logo-container h3 a:hover' => 'color: {{VALUE}};',
				],
			];

			$this->fields[] = [
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'wls_el_title_typography',
				'selector' => '{{WRAPPER}} .rt-wpls .single-logo-container h3, {{WRAPPER}} .rt-wpls .single-logo-container h3 a',
			];

			$this->fields[] = [
				'mode'       => 'responsive',
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'id'         => 'wls_el_title_margin',
				'label'      => esc_html__( 'Title Margin', 'wp-logo-showcase' ),
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rt-wpls .single-logo-container h3' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			];

			$this->fields[] = [
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'id'              => 'wls_el_description_note',
				'raw'             => sprintf(
					'<h3 class="wls-elementor-group-heading">%s</h3>',
					esc_html__( 'Description', 'wp-logo-showcase' )
				),
				'content_classes' => 'elementor-panel-heading-title',
				'separator'       => 'before',
			];

			$this->fields[] = [
				'type'      => \Elementor\Controls_Manager::COLOR,
				'id'        => 'wls_el_description_color',
				'label'     => esc_html__( 'Description Color', 'wp-logo-showcase' ),
				'selectors' => [
					'{{WRAPPER}} .rt-wpls .logo-description' => 'color: {{VALUE}};',
				],
			];

			$this->fields[] = [
				'mode'     => 'group',
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'id'       => 'wls_el_description_typography',
				'selector' => '{{WRAPPER}} .rt-wpls .logo-description',
			];

			$this->fields[] = [
				'mode'       => 'responsive',
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'id'         => 'wls_el_description_margin',
				'label'      => esc_html__( 'Description Margin', 'wp-logo-showcase' ),
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .rt-wpls .logo-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			];

			$this->fields = array_merge( apply_filters( 'rtwls_elementor_before_typography_end', $this->fields ) );

			$this->fields[] = [
				'mode' => 'section_end',
			];

			return $this->fields;
		}

		/**
		 * Slider navigation Section
		 *
		 * @return array
		 */
		public function sliderNav() {
			$this->fields[] = [
				'mode'  => 'section_start',
				'id'    => 'wls_el_slider_nav_style_section',
				'label' => esc_html__( 'Slider Navigation', 'wp-logo-showcase' ),
				'tab'   => $this->tab,
			];

			$this->fields[] = [
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'id'              => 'wls_el_arrow_note',
				'raw'             => sprintf(
					'<h3 class="wls-elementor-group-heading">%s</h3>',
					esc_html__( 'Arrows', 'wp-logo-showcase' )
				),
				'content_classes' => 'elementor-panel-heading-title',
			];

			$this->fields[] = [
				'type'      => \Elementor\Controls_Manager::COLOR,
				'id'        => 'wls_el_arrow_color',
				'label'     => esc_html__( 'Arrow Color', 'wp-logo-showcase' ),
				'selectors' => [
					'{{WRAPPER}} .rt-wpls .slick-prev:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .rt-wpls .slick-next:before' => 'color: {{VALUE}};',
				],
			];

			$this->fields[] = [
				'mode'       => 'responsive',
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'id'         => 'wls_el_arrow_size',
				'label'      => esc_html__( 'Arrow Size', 'wp-logo-showcase' ),
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 10,
						'max' => 80,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .rt-wpls .slick-prev:before' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .rt-wpls .slick-next:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			];

			$this->fields[] = [
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'id'              => 'wls_el_dots_note',
				'raw'             => sprintf(
					'<h3 class="wls-elementor-group-heading">%s</h3>',
					esc_html__( 'Dots', 'wp-logo-showcase' )
				),
				'content_classes' => 'elementor-panel-heading-title',
				'separator'       => 'before',
			];

			$this->fields[] = [
				'type'      => \Elementor\Controls_Manager::COLOR,
				'id'        => 'wls_el_dots_color',
				'label'     => esc_html__( 'Dot Color', 'wp-logo-showcase' ),
				'selectors' => [
					'{{WRAPPER}} .rt-wpls .slick-dots li button:before' => 'color: {{VALUE}};',
				],
			];

			$this->fields[] = [
				'type'      => \Elementor\Controls_Manager::COLOR,
				'id'        => 'wls_el_dots_active_color',
				'label'     => esc_html__( 'Active Dot Color', 'wp-logo-showcase' ),
				'selectors' => [
					'{{WRAPPER}} .rt-wpls .slick-dots li.slick-active button:before' => 'color: {{VALUE}};',
				],
			];

			$this->fields = array_merge( apply_filters( 'rtwls_elementor_before_slider_nav_style_end', $this->fields ) );

			$this->fields[] = [
				'mode' => 'section_end',
			];

			return $this->fields;
		}
	}

endif;
