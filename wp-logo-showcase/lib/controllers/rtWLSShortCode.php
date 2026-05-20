<?php
/**
 * ShortCode Render Class
 *
 * This will generate the meta field for ShortCode generator post type
 *
 * @package RT_WSL
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'rtWLSShortCode' ) ) :
	/**
	 * ShortCode Render Class
	 */
	class rtWLSShortCode {
		private $scA = [];

		public function __construct() {
			add_shortcode( 'logo-showcase', [ $this, 'wls_short_code' ] );
		}

		public function register_scripts() {
			$script = [];
			$iso    = false;
			$caro   = false;

			foreach ( $this->scA as $sc ) {
				if ( isset( $sc ) && is_array( $sc ) ) {
					if ( $sc['isCarousel'] ) {
						$caro = true;
					}
				}
			}

			if ( count( $this->scA ) ) {
				array_push( $script, 'jquery' );
				array_push( $script, 'rt-actual-height-js' );

				if ( $caro ) {
					array_push( $script, 'rt-slick' );
				}

				array_push( $script, 'rt-wls' );

				wp_enqueue_script( $script );
			}
		}

		/**
		 * ShortCode Generate
		 *
		 * @param $atts
		 *
		 * @return null|string
		 */
		public function wls_short_code( $atts ) {
			global $rtWLS;

			$html = null;
			$arg  = [];
			$atts = shortcode_atts(
				[
					'id'    => null,
					'title' => null,
				],
				$atts,
				'logo-showcase'
			);
			$scID = $atts['id'];

			if ( $scID && ! is_null( get_post( $scID ) ) ) {
				$scMeta = get_post_meta( $scID );

				$layout = ( isset( $scMeta['wls_layout'][0] ) ? $scMeta['wls_layout'][0] : 'grid-layout' );

				if ( ! in_array( $layout, array_keys( $rtWLS->scLayout() ) ) ) {
					$layout = 'grid-layout';
				}

				$isCarousel = preg_match( '/carousel/', $layout );

				$col  = ( isset( $scMeta['wls_column'][0] ) ? intval( $scMeta['wls_column'][0] ) : 4 );
				$Tcol = ( isset( $scMeta['wls_tab_column'][0] ) ? intval( $scMeta['wls_tab_column'][0] ) : 4 );
				$Mcol = ( isset( $scMeta['wls_mobile_column'][0] ) ? intval( $scMeta['wls_mobile_column'][0] ) : 4 );

				if ( ! in_array( $col, array_keys( $rtWLS->scColumns() ) ) ) {
					$col = 4;
				}

				if ( ! in_array( $Tcol, array_keys( $rtWLS->scColumns() ) ) ) {
					$Tcol = 3;
				}

				if ( ! in_array( $Mcol, array_keys( $rtWLS->scColumns() ) ) ) {
					$Mcol = 2;
				}

				$linkTypeRaw     = ( isset( $scMeta['wls_link_type'][0] ) ? $scMeta['wls_link_type'][0] : 'new_window' );
				$allowedLinks    = array_keys( $rtWLS->scLinkTypes() );
				$arg['linkType'] = in_array( $linkTypeRaw, $allowedLinks, true ) ? $linkTypeRaw : 'new_window';
				$arg['nofollow'] = ! empty( $scMeta['wls_nofollow'][0] );

				/* Argument create */
				$args              = [];
				$itemIdsArgs       = [];
				$args['post_type'] = $rtWLS->post_type;

				// Common filter.
				/* LIMIT */
				$limit                  = ( ! empty( $scMeta['wls_limit'][0] ) ? ( $scMeta['wls_limit'][0] === '-1' ? 10000000 : (int) $scMeta['wls_limit'][0] ) : 10000000 );
				$args['posts_per_page'] = $limit;

				// Taxonomy — coerce to positive integers, drop everything else.
				$taxQ = [];
				$cats = ( ! empty( $scMeta['wls_categories'] ) ? $scMeta['wls_categories'] : [] );
				$cats = is_array( $cats ) ? array_filter( array_map( 'absint', $cats ) ) : [];

				if ( ! empty( $cats ) ) {
					$taxQ[] = [
						'taxonomy' => $rtWLS->taxonomy['category'],
						'field'    => 'term_id',
						'terms'    => $cats,
					];
				}

				if ( ! empty( $taxQ ) ) {
					$args['tax_query'] = $itemIdsArgs['tax_query'] = $taxQ;
				}

				// Order — validate against plugin allowlists before passing to WP_Query.
				$order_by_raw = ( ! empty( $scMeta['wls_order_by'][0] ) ? $scMeta['wls_order_by'][0] : null );
				$order_raw    = ( ! empty( $scMeta['wls_order'][0] ) ? $scMeta['wls_order'][0] : null );

				$allowed_order_by = array_keys( $rtWLS->scOrderBy() );
				$allowed_order    = array_keys( $rtWLS->scOrder() );

				if ( $order_raw && in_array( $order_raw, $allowed_order, true ) ) {
					$args['order'] = $order_raw;
				}

				if ( $order_by_raw && in_array( $order_by_raw, $allowed_order_by, true ) ) {
					$args['orderby'] = $order_by_raw;
				}

				$col          = $col == 5 ? '24' : round( 12 / $col );
				$Tcol         = $Tcol == 5 ? '24' : round( 12 / $Tcol );
				$Mcol         = $Mcol == 5 ? '24' : round( 12 / $Mcol );
				$arg['grid']  = $col;
				$arg['Tgrid'] = $Tcol;
				$arg['Mgrid'] = $Mcol;
				$arg['class'] = 'equal-height';
				$arg['items'] = ! empty( $scMeta['_wls_items'] ) ? $scMeta['_wls_items'] : [];

				/* Some Custom option */
				$logoQuery = new WP_Query( $args );

				if ( $logoQuery->have_posts() ) {
					$rand              = wp_rand();
					$carouselClass     = null;
					$carouselAttribute = null;
					$carouselDir       = null;

					if ( $isCarousel ) {
						$carouselClass  = 'wpls-carousel';
						$slidesToShow   = ( ! empty( $scMeta['wls_carousel_slidesToShow'][0] ) ? absint( $scMeta['wls_carousel_slidesToShow'][0] ) : 3 );
						$slidesToScroll = ( ! empty( $scMeta['wls_carousel_slidesToScroll'][0] ) ? absint( $scMeta['wls_carousel_slidesToScroll'][0] ) : 3 );
						$speed          = ( ! empty( $scMeta['wls_carousel_speed'][0] ) ? absint( $scMeta['wls_carousel_speed'][0] ) : 2000 );
						$options        = [];

						if ( ! empty( $scMeta['wls_carousel_options'] ) && is_array( $scMeta['wls_carousel_options'] ) ) {
							$options = $scMeta['wls_carousel_options'];
						}

						$slick = [
							'slidesToShow'   => $slidesToShow,
							'slidesToScroll' => $slidesToScroll,
							'speed'          => $speed,
							'dots'           => in_array( 'dots', $options, true ),
							'arrows'         => in_array( 'arrows', $options, true ),
							'infinite'       => in_array( 'infinite', $options, true ),
							'pauseOnHover'   => in_array( 'pauseOnHover', $options, true ),
							'autoplay'       => in_array( 'autoplay', $options, true ),
							'rtl'            => in_array( 'rtl', $options, true ),
						];

						$carouselAttribute = sprintf( ' data-slick="%s"', esc_attr( wp_json_encode( $slick ) ) );

						$carouselDir = ( in_array( 'rtl', $options, true ) ? ' dir="rtl"' : '' );
					}

					$containerID = 'rt-container-' . $rand;
					$html       .= $this->layoutStyle( $rand, $scMeta );
					$settings    = get_option( $rtWLS->options['settings'] );
					$imgReSize   = ( ! empty( $settings['image_resize'] ) ? true : false );
					$imgSize     = [];

					if ( $imgReSize ) {
						$imgSize['width']  = isset( $settings['image_width'] ) ? absint( $settings['image_width'] ) : 180;
						$imgSize['height'] = isset( $settings['image_height'] ) ? absint( $settings['image_height'] ) : 90;
						$imgSize['crop']   = false;
					}

					// image size.
					$wls_image_size = ! empty( $scMeta['wls_image_size'][0] ) ? $scMeta['wls_image_size'][0] : null;

					if ( $wls_image_size && 'wls_custom_image_size' == $wls_image_size ) {
						$imgReSize = true;
						// Use keyed get_post_meta so WP handles unserialization safely instead of
						// calling maybe_unserialize() on a raw blob from the all-meta lookup.
						$wls_custom_image_size = get_post_meta( $scID, 'wls_custom_image_size', true );
						if ( ! is_array( $wls_custom_image_size ) ) {
							$wls_custom_image_size = [];
						}
						$imgSize['width']  = isset( $wls_custom_image_size['width'] ) ? absint( $wls_custom_image_size['width'] ) : 180;
						$imgSize['height'] = isset( $wls_custom_image_size['height'] ) ? absint( $wls_custom_image_size['height'] ) : 90;
						$imgSize['crop']   = ! empty( $wls_custom_image_size['crop'] );
					}

					$image_size = ( $wls_image_size && 'wls_custom_image_size' != $wls_image_size ) ? $wls_image_size : 'full';

					$html .= '<div class="rt-container-fluid rt-wpls" id="' . esc_attr( $containerID ) . '" data-sc-id="' . absint( $scID ) . '">';
					// $carouselAttribute and $carouselDir are pre-built with esc_attr() (or fixed literals) so they're safe to inject here.
					$html .= '<div class="rt-row ' . esc_attr( $layout ) . ' ' . esc_attr( $carouselClass ) . '"' . $carouselAttribute . $carouselDir . '>';

					while ( $logoQuery->have_posts() ) :
						$logoQuery->the_post();

						/* Argument for single member */
						$arg['pID']         = $pID = get_the_ID();
						$arg['title']       = get_the_title();
						$arg['description'] = get_post_meta( $pID, '_wls_logo_description', true );
						$arg['alt_text']    = get_post_meta( $pID, '_wls_logo_alt', true );
						$arg['url']         = get_post_meta( $pID, '_wls_site_url', true );
						$imgClass           = 'wls-logo';
						$arg['img_src']     = null;

						if ( has_post_thumbnail() ) {
							$img            = wp_get_attachment_image(
								get_post_thumbnail_id(),
								$image_size,
								'',
								[
									'class' => $imgClass,
									'title' => $arg['title'],
								]
							);
							$imgS           = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
							$arg['img_src'] = $img;

							if ( 'full' == $image_size && ! empty( $imgSize ) && is_array( $imgS ) && ! empty( $imgS[0] ) ) {
								$c       = ! empty( $imgSize['crop'] );
								$cropImg = $rtWLS->rtImageReSize( $imgS[0], $imgSize['width'], $imgSize['height'], $c );

								if ( $cropImg ) {
									$arg['img_src'] = "<img title='" . esc_attr( $arg['title'] ) . "' src='" . esc_url( $cropImg ) . "' width='" . absint( $imgSize['width'] ) . "' height='" . absint( $imgSize['height'] ) . "' class='" . esc_attr( $imgClass ) . "' alt='" . esc_attr( $arg['alt_text'] ) . "'>";
								}
							}
						}

						if ( ! empty( $arg['img_src'] ) ) {
							$html .= $rtWLS->render( 'layouts/' . $layout, $arg, true );
						}

					endwhile;
					wp_reset_postdata();

					$html .= '</div>'; // end row.
					$html .= '</div>';// end container.

					$scriptGenerator               = [];
					$scriptGenerator['isCarousel'] = $isCarousel;
					$this->scA[]                   = $scriptGenerator;

					add_action( 'wp_footer', [ $this, 'register_scripts' ] );

				} else {
					$html .= '<p>' . esc_html__( 'No logo found', 'wp-logo-showcase' ) . '</p>';
				}
			} else {
				$html .= '<p>' . esc_html__( 'No short code found', 'wp-logo-showcase' ) . '</p>';
			}

			return $html;
		}

		/**
		 * Layout inline style
		 *
		 * @param $rand
		 * @param $scMeta
		 *
		 * @return null|string
		 * @internal param $layoutId
		 */
		public function layoutStyle( $rand, $scMeta ) {
			global $rtWLS;

			$css  = null;
			$css .= "<style type='text/css'>";

			if ( ! empty( $scMeta['wls_primary_color'][0] ) ) {
				$css .= "#rt-container-{$rand} .filter-button-group button, #rt-container-{$rand} button.slick-arrow {";
				$css .= "background-color : {$rtWLS->sanitize_hex_color( $scMeta['wls_primary_color'][0] )}";
				$css .= '}';
				$css .= "#rt-container-{$rand} .slick-prev, #rt-container-{$rand} .slick-next, #rt-container-{$rand} .slick-dots li button:before{";
				$css .= "color : {$rtWLS->sanitize_hex_color( $scMeta['wls_primary_color'][0] )}";
				$css .= '}';
			}

			if ( ! empty( $scMeta['wls_title_color'][0] ) ) {
				$css .= "#rt-container-{$rand} .single-logo-container h3, #rt-container-{$rand} .single-logo-container h3 a{";
				$css .= "color : {$rtWLS->sanitize_hex_color( $scMeta['wls_title_color'][0] )}";
				$css .= '}';
			}

			$settings = get_option( $rtWLS->options['settings'] );
			$cCss     = ! empty( $settings['custom_css'] ) ? trim( $settings['custom_css'] ) : null;

			if ( $cCss ) {
				// Sanitize again on output in case a legacy value was stored before tighter sanitization landed.
				$css .= $rtWLS->sanitize_custom_css( $cCss );
			}

			$css .= '</style>';

			return $css;
		}
	}
endif;
