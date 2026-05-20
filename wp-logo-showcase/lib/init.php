<?php
/**
 * Main initialization class.
 *
 * @package RT_WSL
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'rtWLS' ) ) {
	/**
	 * Main initialization class.
	 */
	class rtWLS {

		public $options;
		public $post_type;
		public $shortCodePT;
		public $taxonomy;
		public $assetsUrl;
		public $defaultSettings;
		public $libPath;
		public $modelsPath;
		public $controllersPath;
		public $widgetsPath;
		public $viewsPath;
		public $objects;

		public function __construct() {
			$this->options         = [
				'settings'          => 'rt_wls_settings',
				'version'           => RT_WLS_PLUGIN_VERSION,
				'installed_version' => 'rt_wls_current_version',
			];
			$this->defaultSettings = [
				'custom_css' => null,
			];
			$this->post_type       = 'wlshowcase';
			$this->shortCodePT     = $this->post_type . 'sc';
			$this->taxonomy        = [
				'category' => $this->post_type . '_category',
			];
			$this->libPath         = dirname( __FILE__ );
			$this->modelsPath      = $this->libPath . '/models/';
			$this->controllersPath = $this->libPath . '/controllers/';
			$this->widgetsPath     = $this->libPath . '/widgets/';
			$this->viewsPath       = $this->libPath . '/views/';
			$this->assetsUrl       = RT_WLS_PLUGIN_URL . '/assets/';

			$this->rtLoadModel( $this->modelsPath );
			$this->rtLoadController( $this->controllersPath );
		}

		/**
		 * Bundled plugin file expected under each lib subdirectory.
		 * Only files whose basename appears here are loaded — guards against
		 * an attacker dropping a stray .php file in lib/ via a separate vuln.
		 */
		private function trustedClassNames( $type ) {
			switch ( $type ) {
				case 'model':
					return [ 'rtWLSField', 'rtWLSReSizer' ];
				case 'controller':
					return [
						'rtWLSAjaxResponse',
						'rtWLSHelper',
						'rtWLSInit',
						'rtWLSMeta',
						'rtWLSOptions',
						'rtWLSSCButton',
						'rtWLSSCMeta',
						'rtWLSSElementor',
						'rtWLSSGutenBurg',
						'rtWLSShortCode',
					];
				case 'widget':
					return [ 'rtWLSWidget' ];
			}
			return [];
		}

		/**
		 * Require a plugin file by class name, only if it is on the allowlist
		 * and lives directly inside the given trusted directory.
		 */
		private function requireTrusted( $dir, $className ) {
			$file = $dir . $className . '.php';
			$real = realpath( $file );
			$base = realpath( $dir );
			if ( ! $real || ! $base || strpos( $real, $base ) !== 0 ) {
				return false;
			}
			require_once $real;
			return class_exists( $className );
		}

		/**
		 * Load Model class
		 *
		 * @param $dir
		 */
		public function rtLoadModel( $dir ) {
			if ( ! is_dir( $dir ) ) {
				return;
			}
			foreach ( $this->trustedClassNames( 'model' ) as $className ) {
				$this->requireTrusted( $dir, $className );
			}
		}

		/**
		 * Load all Controller class
		 *
		 * @param $dir
		 */
		public function rtLoadController( $dir ) {
			if ( ! is_dir( $dir ) ) {
				return;
			}

			foreach ( $this->trustedClassNames( 'controller' ) as $className ) {
				if ( $this->requireTrusted( $dir, $className ) ) {
					$this->objects[] = new $className();
				}
			}
		}

		/**
		 * Load all widget class
		 *
		 * @param $dir
		 */
		public function loadWidget( $dir ) {
			if ( ! is_dir( $dir ) ) {
				return;
			}

			foreach ( $this->trustedClassNames( 'widget' ) as $className ) {
				if ( ! $this->requireTrusted( $dir, $className ) ) {
					continue;
				}

				if ( method_exists( $className, 'register_widget' ) ) {
					$caller = new $className();
					$caller->register_widget();
				} else {
					register_widget( $className );
				}
			}
		}

		public function render( $viewName, $args = [], $return = false ) {
			global $rtWLS;

			$path     = str_replace( '.', '/', $viewName );
			$viewPath = $rtWLS->viewsPath . $path . '.php';

			// Refuse path traversal — viewName must resolve under viewsPath.
			$realViewPath = realpath( $viewPath );
			$realViewsDir = realpath( $rtWLS->viewsPath );
			if ( ! $realViewPath || ! $realViewsDir || strpos( $realViewPath, $realViewsDir ) !== 0 ) {
				return;
			}

			if ( ! file_exists( $realViewPath ) ) {
				return;
			}

			if ( is_array( $args ) && $args ) {
				// EXTR_SKIP keeps existing locals (e.g. $rtWLS, $viewPath) safe from shadowing.
				extract( $args, EXTR_SKIP );
			}

			if ( $return ) {
				ob_start();
				include $realViewPath;
				return ob_get_clean();
			}

			include $realViewPath;
		}


		/**
		 * Dynamically call any method from loaded controller/model classes
		 * via the pluginFramework instance.
		 *
		 * Refuses PHP magic methods and any name that is not a plain
		 * identifier, so even if a future caller passes user input as the
		 * method name the dispatcher cannot invoke __construct/__destruct
		 * or other unintended internals.
		 */
		public function __call( $name, $args ) {
			if ( ! is_array( $this->objects ) ) {
				return;
			}

			if ( ! is_string( $name ) || ! preg_match( '/^[A-Za-z_][A-Za-z0-9_]*$/', $name ) ) {
				return;
			}

			if ( 0 === strncmp( $name, '__', 2 ) ) {
				return;
			}

			foreach ( $this->objects as $object ) {
				if ( method_exists( $object, $name ) && is_callable( [ $object, $name ] ) ) {
					return call_user_func_array( [ $object, $name ], $args );
				}
			}
		}
	}

	global $rtWLS;

	if ( ! is_object( $rtWLS ) ) {
		$rtWLS = new rtWLS();
	}
}
