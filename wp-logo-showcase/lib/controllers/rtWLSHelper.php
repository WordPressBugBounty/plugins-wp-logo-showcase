<?php
/**
 * Helper Class
 *
 * @package RT_WSL
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'rtWLSHelper' ) ) :
	/**
	 * Helper Class
	 */
	class rtWLSHelper {
		/**
		 * Nonce verify upon activity
		 *
		 * @return bool
		 */
		public function verifyNonce() {
			global $rtWLS;

			$nonce     = isset( $_REQUEST[ $this->nonceId() ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ $this->nonceId() ] ) ) : null;
			$nonceText = $rtWLS->nonceText();

			if ( ! wp_verify_nonce( $nonce, $nonceText ) ) {
				return false;
			}

			return true;
		}

		public function getNonce(  ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return isset( $_REQUEST[  $this->nonceId() ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ $this->nonceId() ] ) ) : null;

		}

		/**
		 * Generate nonce text
		 *
		 * @return string
		 */
		public function nonceText() {
			return 'rt_wls_nonce_secret';
		}

		/**
		 * Nonce Id generation
		 *
		 * @return string
		 */
		public function nonceId() {
			return 'rt_wls_nonce';
		}

		/**
		 * Generate meta field name array()
		 *
		 * @return array
		 */
		public function rtLogoMetaNames() {
			global $rtWLS;

			$fields  = [];
			$fieldsA = $rtWLS->rtLogoMetaFields();

			foreach ( $fieldsA as $field ) {
				$fields[] = $field;
			}

			return $fields;
		}

		/**
		 *
		 * Call the Image resize model for resize function
		 *
		 * @param $url
		 * @param null       $width
		 * @param null       $height
		 * @param null       $crop
		 * @param bool|true  $single
		 * @param bool|false $upscale
		 *
		 * @return array|bool|string
		 * @throws Exception
		 * @throws Rt_Exception
		 */
		public function rtImageReSize( $url, $width = null, $height = null, $crop = null, $single = true, $upscale = false ) {
			$rtResize = new rtWLSResizer();

			return $rtResize->process( $url, $width, $height, $crop, $single, $upscale );
		}

		/**
		 * Generate ShortCode List
		 *
		 * @return array
		 */
		public function getWlsShortCodeList() {
			global $rtWLS;

			$scList  = [];
			$scListQ = get_posts(
				[
					'post_type'      => $rtWLS->shortCodePT,
					'order_by'       => 'title',
					'order'          => 'DESC',
					'post_status'    => 'publish',
					'posts_per_page' => - 1,
				]
			);

			if ( ! empty( $scListQ ) ) {
				foreach ( $scListQ as $sc ) {
					$scList[ $sc->ID ] = $sc->post_title;
				}
			}

			return $scList;

		}

		/**
		 * Image Crop type
		 *
		 * @return void
		 */
		public function imageCropType() {
			return [
				false => esc_html__( 'Soft Crop', 'wp-logo-showcase' ),
				true  => esc_html__( 'Hard Crop', 'wp-logo-showcase' ),
			];
		}
		/**
		 * Generate MetaField Name list for shortCode Page
		 *
		 * @return array
		 */
		public function wlsScMetaNames() {
			global $rtWLS;

			$fields  = [];
			$fieldsA = array_merge(
				$rtWLS->scLayoutMetaFields(),
				$rtWLS->scFilterMetaFields(),
				$rtWLS->scStyleFields()
			);

			foreach ( $fieldsA as $field ) {
				$fields[] = $field;
			}

			array_push(
				$fields,
				[
					'name'     => '_wls_items',
					'type'     => 'checkbox',
					'multiple' => true,
				]
			);

			return $fields;
		}

		public function rtFieldGenerator( $fields = [], $multi = false ) {
			$html = null;

			if ( is_array( $fields ) && ! empty( $fields ) ) {
				$rtField = new rtWLSField();

				if ( $multi ) {
					foreach ( $fields as $field ) {
						$html .= $rtField->Field( $field );
					}
				} else {
					$html .= $rtField->Field( $fields );
				}
			}

			return $html;
		}

		/**
		 * Sanitize field value
		 *
		 * @param array $field
		 * @param null  $value
		 *
		 * @return array|null
		 * @internal param $value
		 */
		public function sanitize( $field = [], $value = null ) {
			$newValue = null;

			if ( is_array( $field ) ) {
				$type = ( ! empty( $field['type'] ) ? $field['type'] : 'text' );

				if ( empty( $field['multiple'] ) ) {
					if ( $type == 'text' || $type == 'number' || $type == 'select' || $type == 'checkbox' || $type == 'radio' ) {
						$newValue = sanitize_text_field( $value );
					} elseif ( $type == 'url' ) {
						$newValue = esc_url( $value );
					} elseif ( $type == 'textarea' ) {
						$newValue = wp_kses_post( $value );
					} elseif ( $type == 'colorpicker' ) {
						$newValue = $this->sanitize_hex_color( $value );
					} elseif ( $type == 'image_size' ) {
						$newValue = [];

						if ( is_array( $value ) ) {
							foreach ( $value as $k => $v ) {
								$newValue[ $k ] = esc_attr( $v );
							}
						}
					} else {
						$newValue = sanitize_text_field( $value );
					}
				} else {
					$newValue = [];

					if ( ! empty( $value ) ) {
						if ( is_array( $value ) ) {
							foreach ( $value as $val ) {
								$newValue[] = sanitize_text_field( $val );
							}
						} else {
							$newValue[] = sanitize_text_field( $value );
						}
					}
				}
			}

			return $newValue;
		}

		public function sanitize_hex_color( $color ) {
			if ( '' === $color ) {
				return '';
			}

			// 3 or 6 hex digits, or the empty string.
			if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
				return $color;
			}
		}

		/**
		 * Get the Logo list from the custom post type
		 *
		 * @return array
		 */
		public function getLogoList() {
			global $rtWLS;

			$logos  = [];
			$logosA = get_posts(
				[
					'post_type'      => $rtWLS->post_type,
					'post_status'    => 'publish',
					'posts_per_page' => - 1,
					'orderby'        => 'title',
					'order'          => 'ASC',
				]
			);

			if ( ! empty( $logosA ) ) {
				foreach ( $logosA as $logo ) {
					$logos[ $logo->ID ] = $logo->post_title;
				}
			}

			return $logos;
		}

		/**
		 *  Get all Category list
		 *
		 * @return array
		 */
		public function getAllWlsCategoryList() {
			global $rtWLS;

			$terms = [];
//			$tList = get_terms( [ $rtWLS->taxonomy['category'] ], [ 'hide_empty' => 0 ] );
			$tList = get_terms( array(
				'taxonomy'   => $rtWLS->taxonomy['category'],
				'hide_empty' => false,
			) );

			if ( is_array( $tList ) && ! empty( $tList ) && empty( $tList['errors'] ) ) {
				foreach ( $tList as $term ) {
					$terms[ $term->term_id ] = $term->name;
				}
			}

			return $terms;
		}

		public function allSettingsFields() {
			$fields = [];
			global $rtWLS;

			$fields = array_merge( $rtWLS->rtWLSGeneralSettings(), $rtWLS->rtWLSCustomCss() );

			return $fields;
		}

		/**
		 * Get al image size
		 *
		 * @return void
		 */
		public function get_image_sizes() {
			global $_wp_additional_image_sizes;

			$sizes      = [];
			$interSizes = get_intermediate_image_sizes();

			if ( ! empty( $interSizes ) ) {
				foreach ( get_intermediate_image_sizes() as $_size ) {
					if ( in_array( $_size, [ 'thumbnail', 'medium', 'large' ] ) ) {
						$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
						$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
						$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
					} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
						$sizes[ $_size ] = [
							'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
							'height' => $_wp_additional_image_sizes[ $_size ]['height'],
							'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
						];
					}
				}
			}

			$imgSize = [
				'' => __( 'Select One', 'wp-logo-showcase' ),
			];

			if ( ! empty( $sizes ) ) {
				foreach ( $sizes as $key => $img ) {
					$imgSize[ $key ] = ucfirst( $key ) . " ({$img['width']}*{$img['height']})";
				}
			}

			return $imgSize;
		}

		/**
		 * Prints HTML.
		 *
		 * @param string $html HTML.
		 * @param bool   $allHtml All HTML.
		 *
		 * @return mixed
		 */
		public function print_html( $html, $allHtml = false ) {
			if ( $allHtml ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo stripslashes_deep( $html );
			} else {
				echo wp_kses_post( stripslashes_deep( $html ) );
			}
		}
	}

endif;
