<?php

namespace Xe_GOC\Inc;

use Xe_GOC\Inc\Controllers;
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
		new controllers\Backend\ShopItem_Postype();

		// Load crime types
		new controllers\Backend\CrimePostType();

		// Load cars type
		new controllers\Backend\CarPostType();

		$this->init_hooks();

		$this->loadControllers();

	}

	/**
	 * Load GOC main hooks
	 */
	private function init_hooks(){
		// User register
		add_action( 'user_register', array($this,'onUserRegister'), 10, 1 );
	}

	/**
	 * Load the GOC controllers
	 */
	private function loadControllers(){

		// Load frontendPages
		new controllers\Frontend\Bank();
		new controllers\Frontend\Hq();
		new controllers\Frontend\Shop();
		new controllers\Frontend\AttackList();
		new controllers\Frontend\AttackOverview();
		new controllers\Frontend\doCrime();
		new controllers\Frontend\Login();

	}

	/**
	 * On user register
	 */
	public function onUserRegister($id){

		$cuser = new CriminalUser($id);
		// Add the basics of a criminal user
		$cuser->createNewUser();

	}
}