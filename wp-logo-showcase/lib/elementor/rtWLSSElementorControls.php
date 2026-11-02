<?php
/**
 * Elementor Controls Class.
 *
 * @package RT_WSL
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'rtWLSSElementorControls' ) ) :
	/**
	 * Elementor Controls Class.
	 */
	class rtWLSSElementorControls {
		/**
		 * Controls
		 *
		 * @var array
		 */
		private static $controls;

		/**
		 * Register
		 *
		 * @param object $controls_manager Controls Manager.
		 * @return void
		 */
		public static function register( $controls_manager ) {
			self::includes();

			self::$controls = apply_filters(
				'rtwls_elementor_custom_controls',
				[
					rtWLSSImageSelectorControl::class,
				]
			);

			if ( empty( self::$controls ) ) {
				return;
			}

			self::registerControls( $controls_manager );
		}

		/**
		 * Register Controls
		 *
		 * @param object $controls_manager Controls Manager.
		 * @return void
		 */
		private static function registerControls( $controls_manager ) {
			foreach ( self::$controls as $control ) {
				if ( ! class_exists( $control ) ) {
					continue;
				}

				$controls_manager->register( new $control() );
			}
		}

		/**
		 * Directory includes
		 *
		 * @return void
		 */
		private static function includes() {
			global $rtWLS;

			$rtWLS->loadElementorClass( 'elementor/controls' );
		}
	}

endif;
