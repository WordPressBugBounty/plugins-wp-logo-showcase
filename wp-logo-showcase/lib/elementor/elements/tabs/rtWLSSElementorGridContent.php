<?php
/**
 * Elementor Grid Content Tab Class.
 *
 * @package RT_WSL
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'rtWLSSElementorGridContent' ) ) :
	/**
	 * Elementor Grid Content Tab Class.
	 */
	class rtWLSSElementorGridContent {
		/**
		 * Accumulates tab fields.
		 *
		 * @var array
		 */
		private static $fields = [];

		/**
		 * Registering the tab contents.
		 *
		 * @return array
		 */
		public static function register() {
			self::$fields = [];

			self::settings();

			return apply_filters( 'rtwls_elementor_grid_content_fields', self::$fields );
		}

		/**
		 * Method to add required fields.
		 *
		 * @return void
		 */
		public static function settings() {
			$sectionsObj = new rtWLSSElementorContentSections();

			$sections = [
				'gridLayout',
				'image',
				'filtering',
				'contentVisibility',
				'link',
			];
			foreach ( $sections as $section ) {
				self::$fields = $sectionsObj->$section();
			}
			// Promotional content.
			self::$fields = rtWLSSElementorWidgets::contentPromotion( self::$fields );
		}
	}
endif;
