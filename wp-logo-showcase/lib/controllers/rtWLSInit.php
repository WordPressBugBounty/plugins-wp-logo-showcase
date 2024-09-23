<?php
/**
 * Wp service showcase plugin initiate Class
 *
 * @package RT_WSL
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'rtWLSInit' ) ) :
	/**
	 * Wp service showcase plugin initiate Class
	 */
	class rtWLSInit {

		private $version;

		/**
		 *    Plugin Init Construct
		 */
		public function __construct() {
			$this->version = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) || ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? time() : RT_WLS_PLUGIN_VERSION;

			add_action( 'init', [ $this, 'init' ], 1 );
			add_action( 'widgets_init', [ $this, 'initWidget' ] );
			add_action( 'plugins_loaded', [ $this, 'wls_load_text_domain' ] );

			register_activation_hook( RT_WLS_PLUGIN_ACTIVE_FILE_NAME, [ $this, 'activate' ] );
			register_deactivation_hook( RT_WLS_PLUGIN_ACTIVE_FILE_NAME, [ $this, 'deactivate' ] );

			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
			add_filter( 'body_class', [ $this, 'wls_browser_body_class' ] );
			add_filter( 'plugin_action_links_' . RT_WLS_PLUGIN_ACTIVE_FILE_NAME, [ $this, 'rt_wls_marketing' ] );
		}

		public function rt_wls_marketing( $links ) {
			$demo = 'https://www.radiustheme.com/demo/plugins/wp-logo-showcase/';
			$doc  = 'https://www.radiustheme.com/setup-wp-logo-showcase-free-version-wordpress/';
			$pro  = 'https://codecanyon.net/item/wp-logo-showcase-responsive-wp-plugin/16396329?s_rank=1';

			$links[] = '<a target="_blank" href="' . esc_url( $demo ) . '">' . esc_html__( 'Demo', 'wp-logo-showcase' ) . '</a>';
			$links[] = '<a target="_blank" href="' . esc_url( $doc ) . '">' . esc_html__( 'Documentation', 'wp-logo-showcase' ) . '</a>';
			$links[] = '<a target="_blank" style="color: #39b54a;font-weight: 700;" href="' . esc_url( $pro ) . '">' . esc_html__( 'Get Pro', 'wp-logo-showcase' ) . '</a>';

			return $links;
		}

		public function wls_browser_body_class( $classes ) {
			global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

			if ( $is_lynx ) {
				$classes[] = 'wls_lynx';
			} elseif ( $is_gecko ) {
				$classes[] = 'wls_gecko';
			} elseif ( $is_opera ) {
				$classes[] = 'wls_opera';
			} elseif ( $is_NS4 ) {
				$classes[] = 'wls_ns4';
			} elseif ( $is_safari ) {
				$classes[] = 'wls_safari';
			} elseif ( $is_chrome ) {
				$classes[] = 'wls_chrome';
			} elseif ( $is_IE ) {
				$classes[] = 'wls_ie';
			} else {
				$classes[] = 'wls_unknown';
			}

			if ( $is_iphone ) {
				$classes[] = 'wls_iphone';
			}

			return $classes;
		}

		public function initWidget() {
			global $rtWLS;

			$rtWLS->loadWidget( $rtWLS->widgetsPath );
		}

		/**
		 *    Initiate all required registration for post type and category and the style and script
		 *    Init @hock for plugin init
		 */
		public function init() {
			// Create logo post type.
			$labels = [
				'name'               => __( 'Logos', 'wp-logo-showcase' ),
				'singular_name'      => __( 'Logo', 'wp-logo-showcase' ),
				'add_new'            => __( 'Add New Logo', 'wp-logo-showcase' ),
				'menu_name'          => __( 'Logo Showcase', 'wp-logo-showcase' ),
				'all_items'          => __( 'All Logos', 'wp-logo-showcase' ),
				'add_new_item'       => __( 'Add New Logo', 'wp-logo-showcase' ),
				'edit_item'          => __( 'Edit Logo', 'wp-logo-showcase' ),
				'new_item'           => __( 'New Logo', 'wp-logo-showcase' ),
				'view_item'          => __( 'View Logo', 'wp-logo-showcase' ),
				'search_items'       => __( 'Search Logos', 'wp-logo-showcase' ),
				'not_found'          => __( 'No Logos found', 'wp-logo-showcase' ),
				'not_found_in_trash' => __( 'No Logos found in Trash', 'wp-logo-showcase' ),
			];

			global $rtWLS;

			register_post_type(
				$rtWLS->post_type,
				[
					'labels'            => $labels,
					'public'            => true,
					'show_ui'           => current_user_can( 'administrator' ),
					'_builtin'          => false,
					'capability_type'   => 'page',
					'hierarchical'      => false,
					'menu_icon'         => $rtWLS->assetsUrl . 'images/menu-icon.png',
					'rewrite'           => true,
					'query_var'         => false,
					'show_in_nav_menus' => false,
					'supports'          => [
						'title',
						'thumbnail',
						'page-attributes',
					],
					'show_in_menu'      => true,
				]
			);

			$category_labels = [
				'name'                       => esc_html__( 'Category', 'wp-logo-showcase' ),
				'singular_name'              => esc_html__( 'Category', 'wp-logo-showcase' ),
				'menu_name'                  => esc_html__( 'Categories', 'wp-logo-showcase' ),
				'all_items'                  => esc_html__( 'All Category', 'wp-logo-showcase' ),
				'parent_item'                => esc_html__( 'Parent Category', 'wp-logo-showcase' ),
				'parent_item_colon'          => esc_html__( 'Parent Category', 'wp-logo-showcase' ),
				'new_item_name'              => esc_html__( 'New Category Name', 'wp-logo-showcase' ),
				'add_new_item'               => esc_html__( 'Add New Category', 'wp-logo-showcase' ),
				'edit_item'                  => esc_html__( 'Edit Category', 'wp-logo-showcase' ),
				'update_item'                => esc_html__( 'Update Category', 'wp-logo-showcase' ),
				'view_item'                  => esc_html__( 'View Category', 'wp-logo-showcase' ),
				'separate_items_with_commas' => esc_html__( 'Separate Categories with commas', 'wp-logo-showcase' ),
				'add_or_remove_items'        => esc_html__( 'Add or remove Categories', 'wp-logo-showcase' ),
				'choose_from_most_used'      => esc_html__( 'Choose from the most used', 'wp-logo-showcase' ),
				'popular_items'              => esc_html__( 'Popular Categories', 'wp-logo-showcase' ),
				'search_items'               => esc_html__( 'Search Categories', 'wp-logo-showcase' ),
				'not_found'                  => esc_html__( 'Not Found', 'wp-logo-showcase' ),
			];
			$category_args   = [
				'labels'            => $category_labels,
				'hierarchical'      => true,
				'public'            => false,
				'show_ui'           => current_user_can( 'administrator' ),
				'show_admin_column' => true,
				'show_in_nav_menus' => false,
				'show_tagcloud'     => false,
			];

			register_taxonomy( $rtWLS->taxonomy['category'], [ $rtWLS->post_type ], $category_args );

			$sc_args = [
				'label'               => esc_html__( 'Shortcode', 'wp-logo-showcase' ),
				'description'         => esc_html__( 'Wp logo showcase Shortcode generator', 'wp-logo-showcase' ),
				'labels'              => [
					'all_items'          => esc_html__( 'Shortcode Generator', 'wp-logo-showcase' ),
					'menu_name'          => esc_html__( 'Shortcode', 'wp-logo-showcase' ),
					'singular_name'      => esc_html__( 'Shortcode', 'wp-logo-showcase' ),
					'edit_item'          => esc_html__( 'Edit Shortcode', 'wp-logo-showcase' ),
					'new_item'           => esc_html__( 'New Shortcode', 'wp-logo-showcase' ),
					'view_item'          => esc_html__( 'View Shortcode', 'wp-logo-showcase' ),
					'search_items'       => esc_html__( 'Shortcode Locations', 'wp-logo-showcase' ),
					'not_found'          => esc_html__( 'No Shortcode found.', 'wp-logo-showcase' ),
					'not_found_in_trash' => esc_html__( 'No Shortcode found in trash.', 'wp-logo-showcase' ),
				],
				'supports'            => [ 'title' ],
				'public'              => false,
				'rewrite'             => false,
				'show_ui'             => current_user_can( 'administrator' ),
				'show_in_menu'        => 'edit.php?post_type=' . $rtWLS->post_type,
				'show_in_admin_bar'   => true,
				'show_in_nav_menus'   => false,
				'can_export'          => true,
				'has_archive'         => false,
				'exclude_from_search' => false,
				'publicly_queryable'  => false,
				'capability_type'     => 'page',
			];
			register_post_type( $rtWLS->shortCodePT, $sc_args );

			// register all required style and script for this plugin.
			$scripts = [];
			$styles  = [];

			$scripts[] = [
				'handle' => 'rt-actual-height-js',
				'src'    => $rtWLS->assetsUrl . 'vendor/jquery.actual.min.js',
				'deps'   => [ 'jquery' ],
				'footer' => true,
			];

			$scripts[] = [
				'handle' => 'rt-slick',
				'src'    => $rtWLS->assetsUrl . 'vendor/slick.min.js',
				'deps'   => [ 'jquery' ],
				'footer' => true,
			];
			$scripts[] = [
				'handle' => 'rt-wls',
				'src'    => $rtWLS->assetsUrl . 'js/wplogoshowcase.js',
				'deps'   => [ 'jquery' ],
				'footer' => true,
			];

			$styles['rt-wls'] = $rtWLS->assetsUrl . 'css/wplogoshowcase.css';

			if ( is_admin() ) {
				$scripts[] = [
					'handle' => 'ace_code_highlighter_js',
					'src'    => $rtWLS->assetsUrl . 'vendor/ace/ace.js',
					'deps'   => null,
					'footer' => true,
				];
				$scripts[] = [
					'handle' => 'ace_mode_js',
					'src'    => $rtWLS->assetsUrl . 'vendor/ace/mode-css.js',
					'deps'   => [ 'ace_code_highlighter_js' ],
					'footer' => true,
				];

				$scripts[] = [
					'handle' => 'rt-select2',
					'src'    => $rtWLS->assetsUrl . 'vendor/select2/select2.min.js',
					'deps'   => [ 'jquery' ],
					'footer' => false,
				];

				$scripts[]              = [
					'handle' => 'rt-wls-admin',
					'src'    => $rtWLS->assetsUrl . 'js/wls-admin.js',
					'deps'   => [ 'jquery' ],
					'footer' => true,
				];
				$styles['rt-select2']   = $rtWLS->assetsUrl . 'vendor/select2/select2.min.css';
				$styles['rt-wls-admin'] = $rtWLS->assetsUrl . 'css/wls-admin.css';
			}

			foreach ( $scripts as $script ) {
				wp_register_script( $script['handle'], $script['src'], $script['deps'], $this->version, $script['footer'] );
			}

			foreach ( $styles as $k => $v ) {
				wp_register_style( $k, $v, false, $this->version );
			}

			// admin only.
			if ( is_admin() ) {
				add_action( 'admin_menu', [ $this, 'admin_menu' ] );
			}
		}

		/**
		 *    Create admin menu for logo showcase
		 */
		public function admin_menu() {
			global $rtWLS;
			add_submenu_page(
				'edit.php?post_type=' . $rtWLS->post_type,
				esc_html__( 'Settings', 'wp-logo-showcase' ),
				esc_html__( 'Settings', 'wp-logo-showcase' ),
				'administrator',
				'wls_settings',
				[
					$this,
					'rt_wls_settings',
				]
			);
		}

		public function rt_wls_settings() {
			global $rtWLS;

			$rtWLS->render( 'settings' );
		}


		/**
		 *    Register text domain for WLS
		 */
		public function wls_load_text_domain() {
			load_plugin_textdomain( 'wp-logo-showcase', false, RT_WLS_PLUGIN_LANGUAGE_PATH );
		}

		/**
		 *    Run when plugin in activated
		 */
		public function activate() {
			$this->insertDefaultData();
		}

		public function deactivate() {
			// Not thing to now.
		}

		/**
		 *    Insert some default data on plugin activation
		 */
		private function insertDefaultData() {
			global $rtWLS;

			update_option( $rtWLS->options['installed_version'], $rtWLS->options['version'] );

			if ( get_option( $rtWLS->options['settings'] ) ) {
				update_option( $rtWLS->options['settings'], $rtWLS->defaultSettings );
			}
		}

		/**
		 *    Include default style for front end
		 */
		public function enqueue_scripts() {
			wp_enqueue_style( 'rt-wls' );
		}
	}
endif;
