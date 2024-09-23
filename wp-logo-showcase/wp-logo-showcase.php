<?php
/**
 * Plugin Name: Logo Slider and Showcase
 * Plugin URI: https://radiustheme.com/
 * Description: Logo Slider and Showcase plugin is fully Responsive and Mobile Friendly to display your partner logo in slider and grid views.
 * Author: RadiusTheme
 * Version: 1.4.5
 * Text Domain: wp-logo-showcase
 * Domain Path: /languages
 * Author URI: https://radiustheme.com/
 *
 * @package RT_WSL
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RT_WLS_PLUGIN_VERSION', '1.4.5' );
define( 'RT_WLS_PLUGIN_PATH', dirname( __FILE__ ) );
define( 'RT_WLS_PLUGIN_ACTIVE_FILE_NAME', plugin_basename( __FILE__ ) );
define( 'RT_WLS_PLUGIN_URL', plugins_url( '', __FILE__ ) );
define( 'RT_WLS_PLUGIN_SLUG', basename( dirname( __FILE__ ) ) );
define( 'RT_WLS_PLUGIN_LANGUAGE_PATH', dirname( plugin_basename( __FILE__ ) ) . '/languages' );

require 'lib/init.php';
