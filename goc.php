<?php
/*
Plugin Name: Good Old Criminals
Plugin URI: https://www.goodoldcriminals.com
Description: The good old criminals game, the new way
Version: 1.0
Author: XeWeb
Author URI: https://www.xeweb.be
License: GPL2
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


// Initialize settings
define( 'XE_GOC_PLUGIN_DIR', plugin_dir_url( __FILE__ ) );
define( 'XE_GOC_FILE', __FILE__ );
define( 'XE_GOC_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

define('XE_GOC_POSTYPE_SHOP_ITEM','goc-shop-item');
define('XE_GOC_POSTYPE_SHOP_ITEM_TYPE','shop-item-type');
define('XE_GOC_POSTYPE_CRIMES_TYPE','goc-crime-type');
define('XE_GOC_POSTYPE_CARS_TYPE','goc-car-type');
// Tables
define('XE_GOC_TABLE_POWER','goc_power');

// Include the autoloader so we can dynamically include the rest of the classes.
require_once( trailingslashit( dirname( __FILE__ ) ) . 'inc/autoloader.php' );

// init class
add_action('plugins_loaded',"xe_goc_load_plugin");

// load the classes
function xe_goc_load_plugin(){

	// load plugin
	$xe = new Xe_GOC\Inc\Main;

	// do init
	$xe->init();

}