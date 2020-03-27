<?php
/**
 * Created by PhpStorm.
 * User: Tim
 * Date: 23/07/2018
 * Time: 21:25
 */

namespace Xe_GOC\Inc\Controllers\Frontend;

use Xe_GOC\Inc\Lib\TemplateEngine;
use Xe_GOC\Inc\Models\Frontend\CriminalUser;

class AttackList {

	protected $users = array();

	function __construct() {

		// Load the bank shortcode
		add_shortcode('goc_attack_list',array($this,'loadListTemplate'));

	}


	/**
	 * Get the users
	 */
	private function getUsers(){

		// Specify the data
		$args = null;

		$wp_users = get_users($args); // https://codex.wordpress.org/Function_Reference/get_users

		foreach($wp_users as $u){

			if(!empty($u->ID)) {
				// Add goc user object
				$cuser = new CriminalUser( $u->ID );
				//
				$this->users[$u->ID] = $cuser;
			}

		}

	}

	/**
	 * @param $a
	 * @param $b
	 *
	 * @return int
	 */
	private function sortByTotalPower($a,$b){

		if($a->getTotalPower() < $b->getTotalPower()){
			return true;
		}
		return false;
	}

	/**
	 * Show the attack list of users
	 */
	public function loadListTemplate(){

		// Get all users
		$this->getUsers();

		// Sort the users
		usort($this->users, array($this, "sortByTotalPower"));

		// Load the template
		$temp = new TemplateEngine();
		$temp->users = $this->users;
		return $temp->display('frontend/attack-list/list.php');

	}


}