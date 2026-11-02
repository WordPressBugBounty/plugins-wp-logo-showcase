<?php
/**
 * Gutenberg init.
 *
 * @package RT_WSL
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'rtWLSSGutenBurg' ) ) :
	/**
	 * Gutenberg init.
	 */
	class rtWLSSGutenBurg {
		protected $version;

		public function __construct() {
			$this->version = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? time() : RT_WLS_PLUGIN_VERSION;

			add_action( 'enqueue_block_assets', [ $this, 'block_assets' ] );
			add_action( 'enqueue_block_editor_assets', [ $this, 'block_editor_assets' ] );

			if ( function_exists( 'register_block_type' ) ) {
				register_block_type( 'radiustheme/wplss', [ 'render_callback' => [ $this, 'render_shortcode' ] ] );
			}
		}

		static function render_shortcode( $atts ) {
			if ( ! empty( $atts['scId'] ) && $id = absint( $atts['scId'] ) ) {
				return do_shortcode( '[logo-showcase id="' . $id . '"]' );
			}
		}


		public function block_assets() {
			wp_enqueue_style( 'wp-blocks' );
		}

		public function block_editor_assets() {
			global $rtWLS;

			// Scripts.
			wp_enqueue_script(
				'rt-wplss-gb-block-js',
				$rtWLS->assetsUrl . 'js/wplss-blocks.min.js',
				[ 'wp-blocks', 'wp-i18n', 'wp-element' ],
				$this->version,
				true
			);

			wp_localize_script(
				'rt-wplss-gb-block-js',
				'wplss',
				[
					'short_codes' => array_map( 'esc_attr', $rtWLS->getWlsShortCodeList() ),
					'icon'        => esc_url( $rtWLS->assetsUrl . 'images/icon-scg.png' ),
				]
			);

			wp_enqueue_style( 'wp-edit-blocks' );
		}
	}
endif;
