<?php
/**
 * Created by PhpStorm.
 * User: Tim
 * Date: 24/07/2018
 * Time: 20:31
 */

namespace Xe_GOC\Inc\Controllers\Frontend;


use Xe_GOC\Inc\Lib\PageHandling;
use Xe_GOC\Inc\Lib\TemplateEngine;
use Xe_GOC\Inc\Models\Frontend\Attack;

class AttackOverview {

	public function __construct() {

		// Load the shop shortcode
		add_shortcode('goc_attack',array($this,'loadTemplate'));

	}

	public function loadTemplate(){

		// Load the template
		/*$temp = new TemplateEngine();
		return $temp->display('frontend/shop/shop.php');
		*/

		if(!isset($_GET["goc-attack"]) OR !is_numeric(\Xe_GOC\Inc\Lib\security::decrypt($_GET["goc-attack"]))){
			echo 'invalid attack';
			return;
		}
		// Perform an attack
		$a = new Attack(\Xe_GOC\Inc\Lib\security::decrypt($_GET["goc-attack"]));

		// Do the attack
		if($a->doAttack() === true){

			$t = new TemplateEngine();
			$t->a = $a;
			echo $t->display("frontend/attack-list/attack.php");

		}else{
			// Not valid attack
			PageHandling::showMessage($a->result);
		}

	}

}