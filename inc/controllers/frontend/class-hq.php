<?php
/**
 * Created by PhpStorm.
 * User: Tim
 * Date: 8/07/2018
 * Time: 21:54
 */

namespace Xe_GOC\Inc\Controllers\Frontend;
use Xe_GOC\Inc\Models\Frontend\CriminalUser;

/**
 * Class Hq
 * @package Xe_GOC\inc\frontend
 */
class Hq {

	public function __construct() {

		// Load the hq shortcode
		add_shortcode('goc_user_hq',array($this,'loadUserHq'));

	}

	/**
	 * Load the hq template
	 */
	public function loadUserHq(){

		/*
		 * 		// Load the template
		$temp = new TemplateEngine();
		$temp->users = $this->users;
		return $temp->display('frontend/attack-list/list.php');

		 */

		$cuser = new CriminalUser();
		echo 'Attack:'.$cuser->getAttack();
		echo '<br />Defence:'.$cuser->getDefence();
		echo '<br />Total Power:'.$cuser->getTotalPower();
		echo '<br />Cash:'.$cuser->getCash();
		echo '<br />Bank:'.$cuser->getBank();

	}

}