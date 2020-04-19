<?php

namespace Xe_GOC\Inc;

use Xe_GOC\Inc\Controllers;
use Xe_GOC\Inc\Controllers\Backend\CarPostType;
use Xe_GOC\Inc\Controllers\Backend\CompanyPostType;
use Xe_GOC\Inc\Controllers\Backend\CompanyProductPostType;
use Xe_GOC\Inc\Controllers\Backend\CrimePostType;
use Xe_GOC\Inc\Controllers\Backend\LocationPostType;
use Xe_GOC\Inc\Controllers\Backend\ShopItem_Postype;
use Xe_GOC\inc\Controllers\Frontend\casinoDice;
use Xe_GOC\Inc\Controllers\Frontend\Company;
use Xe_GOC\Inc\Controllers\Frontend\doCrime;
use Xe_GOC\Inc\Controllers\Frontend\Drugsdealers;
use Xe_GOC\Inc\Controllers\Frontend\DuoCrime;
use Xe_GOC\Inc\Controllers\Frontend\editUser;
use Xe_GOC\Inc\Controllers\Frontend\Garage;
use Xe_GOC\Inc\Controllers\Frontend\Location;
use Xe_GOC\Inc\Controllers\Frontend\Login;
use Xe_GOC\Inc\Controllers\Frontend\Messages;
use Xe_GOC\Inc\Lib;
use Xe_GOC\Inc\Models;
use Xe_GOC\Inc\Models\Frontend\CriminalUser;


class main {

	// Define Hooks
	public static $hook_bankTransfer = "goc_bankTransfer";


	public function init(){

		// Load ness classes
		new Lib\PageHandling() ;

		// Load new post types
		new ShopItem_Postype();

		// Load crime types
		new CrimePostType();

		// Load cars type
		new CarPostType();

		// Load location type
		new LocationPostType();

		// Load company post type
		new CompanyPostType();

		// Load company product post type
		new CompanyProductPostType();

		$this->init_hooks();

		$this->loadControllers();

	}

	/**
	 * Load GOC main hooks
	 */
	private function init_hooks(){
		// User register
		add_action( 'user_register', array($this,'onUserRegister'), 10, 1 );

		add_action('init',array($this,'initStyle'),10);

		// Load cron times
		add_filter('cron_schedules',array($this,'my_cron_schedules'));

		// Schedule crons
		add_action('init', array($this,"scheduleCrons"));
	}

	/**
	 * Load the GOC controllers
	 */
	private function loadControllers(){

		// Load frontendPages
		new Controllers\Frontend\Bank();
		new Controllers\Frontend\Hq();
		new Controllers\Frontend\Shop();
		new Controllers\Frontend\AttackList();
		new Controllers\Frontend\AttackOverview();
		new doCrime();
		new Login();
		new Garage();
		new Location();
		new casinoDice();
		new controllers\Frontend\Stats();
		new editUser();
		new Company();
		new Drugsdealers();
		new DuoCrime();
		new Messages();
		new Controllers\Frontend\Profile();
	}

	/**
	 * On user register
	 */
	public function onUserRegister($id){

		$cuser = new CriminalUser($id);
		// Add the basics of a criminal user
		$cuser->createNewUser();

	}



	public function initStyle(){

		if(!is_admin()) {

			wp_enqueue_script( 'fontawsome', 'https://use.fontawesome.com/releases/v5.3.1/js/all.js' );
			wp_enqueue_style( "bulma", "https://cdn.jsdelivr.net/npm/bulma@0.8.0/css/bulma.min.css" );
			wp_enqueue_style( "fixbulmabg", XE_GOC_PLUGIN_DIR . "/assets/css/fix.css" );

		}

	}

	public function my_cron_schedules($schedules){
		if(!isset($schedules["5min"])){
			$schedules["5min"] = array(
				'interval' => 5*60,
				'display' => __('Once every 5 minutes'));
		}
		if(!isset($schedules["30min"])){
			$schedules["30min"] = array(
				'interval' => 30*60,
				'display' => __('Once every 30 minutes'));
		}
		return $schedules;
	}

	/**
	 * Schedule goc crons
	 */
	public function scheduleCrons(){

		if(!wp_next_scheduled('goc_5min_crons')) {
			wp_schedule_event( time(), '5min', 'goc_5min_crons' );
		}
	}

}