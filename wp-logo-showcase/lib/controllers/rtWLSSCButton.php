<?php
/**
 * ShortCode Button Class
 *
 * This will Add a icon with tinymce editor
 *
 * @package RT_WSL
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'rtWLSSCButton' ) ) :
	/**
	 * ShortCode Button Class
	 */
	class rtWLSSCButton {

		public $shortcode_tag = 'wls_scg';

		public function __construct() {
			if ( is_admin() ) {
				add_action( 'admin_head', [ $this, 'admin_head' ] );
			}
		}

		/**
		 * Calls your functions into the correct filters
		 *
		 * @return void
		 */
		public function admin_head() {
			// check user permissions.
			if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
				return;
			}

			// check if WYSIWYG is enabled.
			if ( 'true' == get_user_option( 'rich_editing' ) ) {
				add_filter( 'mce_external_plugins', [ $this, 'mce_external_plugins' ] );
				add_filter( 'mce_buttons', [ $this, 'mce_buttons' ] );

				global $rtWLS;

				echo '<style>';
				echo 'i.mce-i-wls-scg{';
				echo "background: url('" . esc_url( $rtWLS->assetsUrl ) . "images/icon-scg.png');";
				echo '}';
				echo '</style>';
			}
		}

		/**
		 * Adds our tinymce plugin
		 *
		 * @param  array $plugin_array
		 * @return array
		 */
		public function mce_external_plugins( $plugin_array ) {
			global $rtWLS;

			$plugin_array[ $this->shortcode_tag ] = $rtWLS->assetsUrl . 'js/mce-button.js';
			return $plugin_array;
		}

		/**
		 * Adds our tinymce button
		 *
		 * @param  array $buttons
		 * @return array
		 */
		public function mce_buttons( $buttons ) {
			array_push( $buttons, $this->shortcode_tag );
			return $buttons;
		}
	}
endif;
