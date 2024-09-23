<?php
/**
 * AjaxResponse Class
 *
 * @package RT_WSL
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'rtWLSAjaxResponse' ) ) :
	/**
	 * AjaxResponse Class
	 */
	class rtWLSAjaxResponse {
		public function __construct() {
			add_action( 'wp_ajax_rtWLSSettings', [ $this, 'rtWLSSaveSettings' ] );
			add_action( 'wp_ajax_wlsShortCodeList', [ $this, 'shortCodeList' ] );
		}

		/**
		 *  Update settings option
		 */
		public function rtWLSSaveSettings() {
			global $rtWLS;
			$msg   = null;
			$error = true;
			if ( wp_verify_nonce($rtWLS->getNonce(),$rtWLS->nonceText()) && current_user_can( 'administrator' ) ) {
				unset( $_REQUEST['action'] );
				unset( $_REQUEST[ $rtWLS->nonceId() ] );
				unset( $_REQUEST['_wp_http_referer'] );

				$value  = [];
				$fields = $rtWLS->allSettingsFields();

				foreach ( $fields as $field ) {
					$type   = ! empty( $field['type'] ) ? $field['type'] : '';
					$rValue = ( ! empty( $_REQUEST[ $field['name'] ] ) ? $_REQUEST[ $field['name'] ] : null );

					if ( $type == 'custom_css' ) {
						$value[ $field['name'] ] = wp_filter_nohtml_kses( $rValue );
					} elseif ( $type == 'text' || $type == 'number' || $type == 'select' || $type == 'checkbox' || $type == 'radio' ) {
						$value[ $field['name'] ] = sanitize_text_field( $rValue );
					} elseif ( $type == 'url' ) {
						$value[ $field['name'] ] = esc_url( $rValue );
					} elseif ( $type == 'textarea' ) {
						$value[ $field['name'] ] = wp_kses_post( $rValue );
					} elseif ( $type == 'colorpicker' ) {
						$value[ $field['name'] ] = $rtWLS->sanitize_hex_color( $rValue );
					} else {
						$value[ $field['name'] ] = sanitize_text_field( $rValue );
					}
				}

				update_option( $rtWLS->options['settings'], $value );
				$error = true;
				$msg   = esc_html__( 'Settings successfully updated', 'wp-logo-showcase' );
			} else {

					$msg = esc_html__( 'Security Error !!', 'wp-logo-showcase' );
			}
			wp_send_json(
				[
					'error' => $error,
					'msg'   => $msg,
				]
			);
			die();
		}

		/**
		 *  Short code list for editor
		 */
		public function shortCodeList() {
			global $rtWLS;

			$html = null;
			$scQ  = new WP_Query(
				[
					'post_type'      => $rtWLS->shortCodePT,
					'order_by'       => 'title',
					'order'          => 'DESC',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
				]
			);

			if ( $scQ->have_posts() ) {
				$html .= "<div class='mce-container mce-form'>";
				$html .= "<div class='mce-container-body'>";
				$html .= '<label class="mce-widget mce-label" style="padding: 20px;font-weight: bold;" for="scid">' . __( 'Select Short code', 'wp-logo-showcase' ) . '</label>';
				$html .= "<select name='id' id='scid' style='width: 150px;margin: 15px;'>";
				$html .= "<option value=''>" . esc_html__( 'Default', 'wp-logo-showcase' ) . '</option>';
				while ( $scQ->have_posts() ) {
					$scQ->the_post();
					$html .= "<option value='" . get_the_ID() . "'>" . get_the_title() . '</option>';
				}
				$html .= '</select>';
				$html .= '</div>';
				$html .= '</div>';
			} else {
				$html .= '<div>' . esc_html__( 'No shortcode found.', 'wp-logo-showcase' ) . '</div>';
			}

			$rtWLS->print_html( $html, true );

			die();
		}
	}
endif;
