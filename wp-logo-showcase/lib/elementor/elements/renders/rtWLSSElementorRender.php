<?php
/**
 * Elementor Render Class.
 *
 * Produces the same markup as the [logo-showcase] shortcode so the existing
 * stylesheet and slick initialisation keep working unchanged.
 *
 * @package RT_WSL
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'rtWLSSElementorRender' ) ) :
	/**
	 * Class for Elementor Frontend Render.
	 */
	class rtWLSSElementorRender {

		/**
		 * Render a widget.
		 *
		 * @param array  $scMeta Elementor settings for display.
		 * @param string $layout Layout key, 'grid-layout' or 'carousel-layout'.
		 * @return string
		 */
		public function logoRender( $scMeta, $layout = 'grid-layout' ) {
			global $rtWLS;

			if ( ! array_key_exists( $layout, $rtWLS->scLayout() ) ) {
				$layout = 'grid-layout';
			}

			$isCarousel = ( 'carousel-layout' === $layout );

			$arg  = [];
			$html = null;

			// Columns.
			$columns = array_keys( $rtWLS->scColumns() );
			$col     = $this->column( $scMeta, 'wls_el_cols', 4, $columns );
			$Tcol    = $this->column( $scMeta, 'wls_el_cols_tablet', 3, $columns );
			$Mcol    = $this->column( $scMeta, 'wls_el_cols_mobile', 2, $columns );

			$slidesToShow = $col;

			// Link behaviour.
			$linkTypeRaw     = ! empty( $scMeta['wls_el_link_type'] ) ? $scMeta['wls_el_link_type'] : 'new_window';
			$arg['linkType'] = array_key_exists( $linkTypeRaw, $rtWLS->scLinkTypes() ) ? $linkTypeRaw : 'new_window';
			$arg['nofollow'] = ! empty( $scMeta['wls_el_nofollow'] );

			$arg['grid']  = 5 === $col ? '24' : round( 12 / $col );
			$arg['Tgrid'] = 5 === $Tcol ? '24' : round( 12 / $Tcol );
			$arg['Mgrid'] = 5 === $Mcol ? '24' : round( 12 / $Mcol );
			$arg['class'] = 'equal-height';
			$arg['items'] = $this->items( $scMeta );

			$logoQuery = new WP_Query( $this->queryArgs( $scMeta ) );

			if ( ! $logoQuery->have_posts() ) {
				wp_reset_postdata();

				return '<p>' . esc_html__( 'No logo found', 'wp-logo-showcase' ) . '</p>';
			}

			$rand              = wp_rand();
			$containerID       = 'rt-container-' . $rand;
			$carouselClass     = '';
			$carouselAttribute = '';
			$carouselDir       = '';

			if ( $isCarousel ) {
				$carouselClass = 'wpls-carousel';

				$slick = [
					'slidesToShow'   => $slidesToShow,
					'slidesToScroll' => ! empty( $scMeta['wls_el_slides_to_scroll'] ) ? absint( $scMeta['wls_el_slides_to_scroll'] ) : 3,
					'speed'          => ! empty( $scMeta['wls_el_slide_speed'] ) ? absint( $scMeta['wls_el_slide_speed'] ) : 2000,
					'dots'           => ! empty( $scMeta['wls_el_slider_pagi'] ),
					'arrows'         => ! empty( $scMeta['wls_el_slider_nav'] ),
					'infinite'       => ! empty( $scMeta['wls_el_slider_infinite'] ),
					'pauseOnHover'   => ! empty( $scMeta['wls_el_pause_hover'] ),
					'autoplay'       => ! empty( $scMeta['wls_el_slide_autoplay'] ),
					'rtl'            => ! empty( $scMeta['wls_el_slider_rtl'] ),
					'responsive'     => [
						[
							'breakpoint' => 1024,
							'settings'   => [
								'slidesToShow'   => $Tcol,
								'slidesToScroll' => min( $Tcol, ! empty( $scMeta['wls_el_slides_to_scroll'] ) ? absint( $scMeta['wls_el_slides_to_scroll'] ) : 3 ),
							],
						],
						[
							'breakpoint' => 767,
							'settings'   => [
								'slidesToShow'   => $Mcol,
								'slidesToScroll' => min( $Mcol, ! empty( $scMeta['wls_el_slides_to_scroll'] ) ? absint( $scMeta['wls_el_slides_to_scroll'] ) : 3 ),
							],
						],
					],
				];

				$carouselAttribute = sprintf( ' data-slick="%s"', esc_attr( wp_json_encode( $slick ) ) );
				$carouselDir       = ! empty( $scMeta['wls_el_slider_rtl'] ) ? ' dir="rtl"' : '';
			}

			list( $image_size, $imgSize ) = $this->imageSize( $scMeta );

			$html .= '<div class="rt-container-fluid rt-wpls" id="' . esc_attr( $containerID ) . '" data-sc-id="elementor">';
			// $carouselAttribute and $carouselDir are pre-built with esc_attr() (or fixed literals) so they're safe to inject here.
			$html .= '<div class="rt-row ' . esc_attr( $layout ) . ' ' . esc_attr( $carouselClass ) . '"' . $carouselAttribute . $carouselDir . '>';

			while ( $logoQuery->have_posts() ) :
				$logoQuery->the_post();

				$arg['pID']         = $pID = get_the_ID();
				$arg['title']       = get_the_title();
				$arg['description'] = get_post_meta( $pID, '_wls_logo_description', true );
				$arg['alt_text']    = get_post_meta( $pID, '_wls_logo_alt', true );
				$arg['url']         = get_post_meta( $pID, '_wls_site_url', true );
				$arg['img_src']     = $this->featureImage( $arg, $image_size, $imgSize );

				if ( ! empty( $arg['img_src'] ) ) {
					$html .= $rtWLS->render( 'elementor/' . $layout, $arg, true );
				}

			endwhile;

			wp_reset_postdata();

			$html .= '</div>'; // end row.
			$html .= '</div>'; // end container.

			return $html;
		}

		/**
		 * Read a responsive column setting, falling back to a known-good value.
		 *
		 * @param array  $scMeta   Widget settings.
		 * @param string $key      Setting key.
		 * @param int    $fallback Fallback column count.
		 * @param array  $allowed  Allowed column counts.
		 * @return int
		 */
		private function column( $scMeta, $key, $fallback, $allowed ) {
			$value = isset( $scMeta[ $key ] ) && '' !== $scMeta[ $key ] ? absint( $scMeta[ $key ] ) : $fallback;

			return in_array( $value, $allowed, false ) ? $value : $fallback; // phpcs:ignore WordPress.PHP.StrictInArray.FoundNonStrictFalse
		}

		/**
		 * Fields to display, in the order chosen in the panel.
		 *
		 * @param array $scMeta Widget settings.
		 * @return array
		 */
		private function items( $scMeta ) {
			global $rtWLS;

			$items   = ! empty( $scMeta['wls_el_items'] ) ? (array) $scMeta['wls_el_items'] : [ 'logo' ];
			$allowed = array_keys( $rtWLS->scLayoutItems() );

			return array_values( array_intersect( array_map( 'sanitize_text_field', $items ), $allowed ) );
		}

		/**
		 * Build the WP_Query arguments from the widget settings.
		 *
		 * @param array $scMeta Widget settings.
		 * @return array
		 */
		private function queryArgs( $scMeta ) {
			global $rtWLS;

			$args = [
				'post_type'      => $rtWLS->post_type,
				'post_status'    => 'publish',
				'posts_per_page' => ! empty( $scMeta['wls_el_posts_limit'] ) ? absint( $scMeta['wls_el_posts_limit'] ) : 10000000,
			];

			$cats = ! empty( $scMeta['wls_el_categories'] ) ? (array) $scMeta['wls_el_categories'] : [];
			$cats = array_filter( array_map( 'absint', $cats ) );

			if ( ! empty( $cats ) ) {
				$args['tax_query'] = [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					[
						'taxonomy' => $rtWLS->taxonomy['category'],
						'field'    => 'term_id',
						'terms'    => $cats,
					],
				];
			}

			$postIn = ! empty( $scMeta['wls_el_include_posts'] ) ? (array) $scMeta['wls_el_include_posts'] : [];
			$postIn = array_filter( array_map( 'absint', $postIn ) );

			if ( ! empty( $postIn ) ) {
				$args['post__in'] = $postIn;
			}

			$postNotIn = ! empty( $scMeta['wls_el_exclude_posts'] ) ? (array) $scMeta['wls_el_exclude_posts'] : [];
			$postNotIn = array_filter( array_map( 'absint', $postNotIn ) );

			if ( ! empty( $postNotIn ) ) {
				$args['post__not_in'] = $postNotIn;
			}

			$orderBy = ! empty( $scMeta['wls_el_posts_order_by'] ) ? $scMeta['wls_el_posts_order_by'] : null;
			$order   = ! empty( $scMeta['wls_el_posts_order'] ) ? $scMeta['wls_el_posts_order'] : null;

			if ( $orderBy && array_key_exists( $orderBy, $rtWLS->scOrderBy() ) ) {
				$args['orderby'] = $orderBy;
			}

			if ( $order && array_key_exists( $order, $rtWLS->scOrder() ) ) {
				$args['order'] = $order;
			}

			return $args;
		}

		/**
		 * Resolve the registered image size plus any manual resize dimensions.
		 *
		 * Mirrors the shortcode: the plugin-wide resize settings apply, and a
		 * per-widget custom size overrides them.
		 *
		 * @param array $scMeta Widget settings.
		 * @return array [ string $image_size, array $imgSize ]
		 */
		private function imageSize( $scMeta ) {
			global $rtWLS;

			$settings  = get_option( $rtWLS->options['settings'] );
			$imgReSize = ! empty( $settings['image_resize'] );
			$imgSize   = [];

			if ( $imgReSize ) {
				$imgSize['width']  = isset( $settings['image_width'] ) ? absint( $settings['image_width'] ) : 180;
				$imgSize['height'] = isset( $settings['image_height'] ) ? absint( $settings['image_height'] ) : 90;
				$imgSize['crop']   = false;
			}

			$selected = ! empty( $scMeta['wls_el_image'] ) ? sanitize_text_field( $scMeta['wls_el_image'] ) : 'full';

			if ( 'wls_custom_image_size' === $selected ) {
				$imgSize['width']  = ! empty( $scMeta['wls_el_image_width'] ) ? absint( $scMeta['wls_el_image_width'] ) : 180;
				$imgSize['height'] = ! empty( $scMeta['wls_el_image_height'] ) ? absint( $scMeta['wls_el_image_height'] ) : 90;
				$imgSize['crop']   = ! empty( $scMeta['wls_el_image_crop'] );

				return [ 'full', $imgSize ];
			}

			return [ $selected, $imgSize ];
		}

		/**
		 * Build the logo <img> markup for the current post in the loop.
		 *
		 * @param array  $arg        Item args (title, alt_text).
		 * @param string $image_size Registered image size.
		 * @param array  $imgSize    Manual resize dimensions.
		 * @return string|null
		 */
		private function featureImage( $arg, $image_size, $imgSize ) {
			global $rtWLS;

			if ( ! has_post_thumbnail() ) {
				return null;
			}

			$imgClass = 'wls-logo';
			$img      = wp_get_attachment_image(
				get_post_thumbnail_id(),
				$image_size,
				'',
				[
					'class' => $imgClass,
					'title' => $arg['title'],
				]
			);

			if ( 'full' !== $image_size || empty( $imgSize ) ) {
				return $img;
			}

			$imgS = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );

			if ( ! is_array( $imgS ) || empty( $imgS[0] ) ) {
				return $img;
			}

			$cropImg = $rtWLS->rtImageReSize( $imgS[0], $imgSize['width'], $imgSize['height'], ! empty( $imgSize['crop'] ) );

			if ( ! $cropImg ) {
				return $img;
			}

			return "<img title='" . esc_attr( $arg['title'] ) . "' src='" . esc_url( $cropImg ) . "' width='" . absint( $imgSize['width'] ) . "' height='" . absint( $imgSize['height'] ) . "' class='" . esc_attr( $imgClass ) . "' alt='" . esc_attr( $arg['alt_text'] ) . "'>";
		}
	}
endif;
