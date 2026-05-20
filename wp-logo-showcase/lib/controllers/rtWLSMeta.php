<?php
/**
 * WLS Meta Class
 *
 * @package RT_WSL
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'rtWLSMeta' ) ) :
	/**
	 * WLS Meta Class
	 */
	class rtWLSMeta {
		/**
		 * WLS Meta generator construct function
		 */
		public function __construct() {
			add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
			add_action( 'save_post', [ $this, 'save_post' ], 10, 2 );
			add_action( 'do_meta_boxes', [ $this, 'wls_logo_image_box' ] );
			add_filter( 'manage_edit-wlshowcase_columns', [ $this, 'arrange_wlshowcase_columns' ] );
			add_action( 'manage_wlshowcase_posts_custom_column', [ $this, 'manage_wlshowcase_columns' ], 10, 2 );
			add_action( 'edit_form_after_title', [ $this, 'wpls_after_title' ] );

		}

		public function wpls_after_title( $post ) {
			global $rtWLS;

			if ( $rtWLS->post_type !== $post->post_type ) {
				return;
			}

			$pro = 'https://codecanyon.net/item/wp-logo-showcase-responsive-wp-plugin/16396329?ref=RadiusTheme';

			$html  = null;
			$html .= '<div class="postbox" style="margin-bottom: 0;"><div class="inside">';
			$html .= '<p style="text-align: center;"><a style="color: red; text-decoration: none; font-size: 14px;" target="_blank" href="' . esc_url( $pro ) . '" target="_blank">Please check the pro features</a></p>';
			$html .= '</div></div>';

			$rtWLS->print_html( $html );
		}

		/**
		 * @param $columns
		 * @return array
		 */
		public function arrange_wlshowcase_columns( $columns ) {
			$column_thumbnail = [ 'wls_logo_thumb' => esc_html__( 'Logo Image', 'wp-logo-showcase' ) ];

			return array_slice( $columns, 0, 2, true ) + $column_thumbnail + array_slice( $columns, 1, null, true );
		}

		/**
		 * @param $column
		 * @param $id
		 */
		public function manage_wlshowcase_columns( $column, $id ) {
			switch ( $column ) {
				case 'wls_logo_thumb':
					$thumb_id  = get_post_thumbnail_id( $id );
					$thumb_src = $thumb_id ? wp_get_attachment_image_src( $thumb_id, 'thumbnail' ) : false;

					if ( is_array( $thumb_src ) && ! empty( $thumb_src[0] ) ) {
						echo '<img src="' . esc_url( $thumb_src[0] ) . '" alt="" />';
					} else {
						echo esc_html__( 'No logo added.', 'wp-logo-showcase' );
					}
					break;

				default:
					break;
			}
		}

		/**
		 *  Logo image box
		 */
		public function wls_logo_image_box() {
			global $rtWLS;

			remove_meta_box( 'postimagediv', $rtWLS->post_type, 'side' );
			add_meta_box(
				'postimagediv',
				esc_html__( 'Logo Image', 'wp-logo-showcase' ),
				'post_thumbnail_meta_box',
				$rtWLS->post_type,
				'normal',
				'high'
			);
		}

		/**
		 *  Admin Script
		 */
		public function admin_enqueue_scripts() {
			global $pagenow, $typenow, $rtWLS;

			// validate page.
			if ( ! in_array( $pagenow, [ 'post.php', 'post-new.php', 'edit.php' ] ) ) {
				return;
			}
			if ( $typenow != $rtWLS->post_type ) {
				return;
			}

			wp_dequeue_script( 'autosave' );

			// scripts.
			wp_enqueue_script(
				[
					'jquery',
					'ace_code_highlighter_js',
					'ace_mode_js',
					'rt-select2',
					'rt-wls-admin',
				]
			);

			// styles.
			wp_enqueue_style(
				[
					'rt-select2',
					'rt-wls-admin',
				]
			);

			$nonce = wp_create_nonce( $rtWLS->nonceText( 'logo_save' ) );

			wp_localize_script(
				'rt-wls-admin',
				'wls',
				[
					'nonceID' => esc_attr( $rtWLS->nonceId() ),
					'nonce'   => esc_attr( $nonce ),
					'ajaxurl' => esc_url( admin_url( 'admin-ajax.php' ) ),
				]
			);

			add_action( 'admin_head', [ $this, 'admin_head' ] );
		}

		/**
		 *  Add meta info Box
		 */
		public function admin_head() {
			global $rtWLS;
			add_meta_box(
				'rt_wls_logo_info_meta',
				esc_html__( 'Logo Information', 'wp-logo-showcase' ),
				[ $this, 'rt_wls_logo_meta_information' ],
				$rtWLS->post_type,
				'normal',
				'high'
			);
		}

		/**
		 * Meta info function
		 *
		 * @param $post
		 */
		public function rt_wls_logo_meta_information( $post ) {
			global $rtWLS;

			wp_nonce_field( $rtWLS->nonceText( 'logo_save' ), $rtWLS->nonceId() );

			$html  = null;
			$html .= '<div class="rt-wls-meta-holder">';
			$html .= $rtWLS->rtFieldGenerator( $rtWLS->rtLogoMetaFields(), true );
			$html .= '</div>';

			$rtWLS->print_html( $html, true );
		}


		/**
		 * Save logo meta data
		 *
		 * @param $post_id
		 * @param $post
		 * @return mixed
		 */
		public function save_post( $post_id, $post ) {

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post_id;
			}

			global $rtWLS;

			// Only act on our post type — without this the nonce check would reject every other post type save.
			if ( ! isset( $post->post_type ) || $rtWLS->post_type !== $post->post_type ) {
				return $post_id;
			}

			if ( ! $rtWLS->verifyNonce( 'logo_save' ) ) {
				return $post_id;
			}

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}

			$mates = $rtWLS->rtLogoMetaNames();

			foreach ( $mates as $field ) {
				$rValue = ! empty( $_POST[ $field['name'] ] ) ? wp_unslash( $_POST[ $field['name'] ] ) : null;
				$value  = $rtWLS->sanitize( $field, $rValue );

				if ( empty( $field['multiple'] ) ) {
					update_post_meta( $post_id, $field['name'], $value );
				} else {
					delete_post_meta( $post_id, $field['name'] );

					if ( is_array( $value ) && ! empty( $value ) ) {
						foreach ( $value as $item ) {
							add_post_meta( $post_id, $field['name'], $item );
						}
					}
				}
			}
		}
	}
endif;
