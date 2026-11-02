<?php
/**
 * Elementor Init class.
 *
 * Boots the native Elementor widgets. It is additive: the legacy
 * rtWLSSElementor shortcode-wrapper widget and every shortcode code path
 * keep working untouched.
 *
 * @package RT_WSL
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'rtWLSSElementorInit' ) ) :
	/**
	 * Elementor Init class.
	 */
	class rtWLSSElementorInit {

		/**
		 * Class Constructor.
		 */
		public function __construct() {
			// These callbacks are attached unconditionally: with Elementor
			// inactive the hooks never fire, and every callback re-checks
			// elementor/loaded before touching an Elementor class. Testing
			// did_action() here instead would depend on plugin load order,
			// since Elementor fires elementor/loaded while its own main file
			// is still being included.
			add_action( 'elementor/widgets/register', [ $this, 'registerWidgets' ] );
			add_action( 'elementor/controls/register', [ $this, 'registerControls' ] );
			add_action( 'elementor/elements/categories_registered', [ $this, 'addCategory' ] );
			add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'editorStyles' ] );
		}

		/**
		 * Register widgets
		 *
		 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
		 * @return void
		 */
		public function registerWidgets( $widgets_manager ) {
			if ( ! $this->includes() ) {
				return;
			}

			rtWLSSElementorWidgets::register( $widgets_manager );
		}

		/**
		 * Register controls
		 *
		 * @param object $controls_manager Controls Manager.
		 * @return void
		 */
		public function registerControls( $controls_manager ) {
			if ( ! $this->includes() ) {
				return;
			}

			rtWLSSElementorControls::register( $controls_manager );
		}

		/**
		 * Add the plugin widget category to the Elementor panel.
		 *
		 * @param object $elements_manager Elements manager.
		 * @return void
		 */
		public function addCategory( $elements_manager ) {
			if ( ! did_action( 'elementor/loaded' ) ) {
				return;
			}

			$categories = [
				'wls-elementor-widgets' => [
					'title' => esc_html__( 'Logo Slider and Showcase', 'wp-logo-showcase' ),
					'icon'  => 'fa fa-plug',
				],
			];

			$el_categories = $elements_manager->get_categories();
			$categories    = array_merge(
				array_slice( $el_categories, 0, 1 ),
				$categories,
				array_slice( $el_categories, 1 )
			);

			$set_categories = function ( $categories ) {
				$this->categories = $categories;
			};

			$set_categories->call( $elements_manager, $categories );
		}

		/**
		 * Editor only styles for the panel controls.
		 *
		 * @return void
		 */
		public function editorStyles() {
			global $rtWLS;

			$icon = esc_url( $rtWLS->assetsUrl . 'images/icon-vc.png' );

			$css = '
				.elementor-element .icon .wls-element {
					display: inline-block;
					width: 30px;
					height: 30px;
				}

				.elementor-element .icon .wls-element::before {
					content: "";
					position: absolute;
					width: 30px;
					height: 30px;
					margin-left: -16px;
					top: 20px;
					background-image: url( ' . $icon . ' );
					background-repeat: no-repeat;
					background-position: center;
					background-size: 32px 32px;
					border-radius: 4px;
				}

				.elementor-element .wls-element::after {
					background: #93003c;
					margin: 3px;
					content: "RT";
					font-family: Roboto,Arial,Helvetica,Verdana,sans-serif;
					font-size: 9px;
					position: absolute;
					top: 5px;
					left: 5px;
					padding: 4px 5px 2px;
					color: #fff;
					font-weight: bold;
				}

				.elementor-control[class*="elementor-control-wls_el"] .elementor-control-title {
					font-size: 13px;
					font-weight: 500;
				}

				.elementor-control[class*="elementor-control-wls_el"] .elementor-control-field-description {
					font-size: 13px;
				}

				.elementor-control .wls-elementor-group-heading {
					font-weight: bold;
					border-left: 4px solid #2271b1;
					padding: 10px;
					background: #f1f1f1;
					color: #495157;
				}
			';

			wp_add_inline_style( 'elementor-editor', $css );
		}

		/**
		 * Load the Elementor bootstrap classes.
		 *
		 * Every class under lib/elementor/ extends or references an
		 * \Elementor\* class, so nothing is loaded unless Elementor is
		 * actually running.
		 *
		 * @return bool True when the bootstrap classes are available.
		 */
		private function includes() {
			global $rtWLS;

			if ( ! did_action( 'elementor/loaded' ) ) {
				return false;
			}

			$rtWLS->loadElementorClass( 'elementor' );

			return class_exists( 'rtWLSSElementorWidgets' ) && class_exists( 'rtWLSSElementorControls' );
		}
	}

endif;
