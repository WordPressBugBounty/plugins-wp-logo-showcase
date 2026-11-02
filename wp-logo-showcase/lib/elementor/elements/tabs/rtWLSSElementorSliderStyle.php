<?php
/**
 * Elementor Slider Style Tab Class.
 *
 * @package RT_WSL
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'rtWLSSElementorSliderStyle' ) ) :
	/**
	 * Elementor Slider Style Tab Class.
	 */
	class rtWLSSElementorSliderStyle {
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

			return apply_filters( 'rtwls_elementor_slider_style_fields', self::$fields );
		}

		/**
		 * Method to add required fields.
		 *
		 * @return void
		 */
		public static function settings() {
			$sectionsObj = new rtWLSSElementorStyleSections();

			$sections = [
				'box',
				'image',
				'typography',
				'sliderNav',
			];

			foreach ( $sections as $section ) {
				self::$fields = $sectionsObj->$section();
			}
		}
	}
endif;
