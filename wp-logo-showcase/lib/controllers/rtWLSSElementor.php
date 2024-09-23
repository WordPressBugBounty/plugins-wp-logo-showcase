<?php
/**
 * Elementor init.
 *
 * @package RT_WSL
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'rtWLSSElementor' ) ) :
	/**
	 * Elementor init.
	 */
	class rtWLSSElementor {
		public function __construct() {
			if ( did_action( 'elementor/loaded' ) ) {
				add_action( 'elementor/widgets/register', [ $this, 'init' ] );
			}
		}

		public function init(  $widgets_manager  ) {
			global $rtWLS;

			require_once $rtWLS->libPath . '/vendor/rtWLSSElementorWidget.php';

			$widgets_manager->register( new rtWLSSElementorWidget() );
		}
	}

endif;
