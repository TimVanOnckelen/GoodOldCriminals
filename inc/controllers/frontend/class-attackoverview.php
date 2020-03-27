<?php
/**
 * Created by PhpStorm.
 * User: Tim
 * Date: 24/07/2018
 * Time: 20:31
 */

namespace Xe_GOC\Inc\Controllers\Frontend;


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

		if(!isset($_GET["goc-attack"]) OR !is_numeric($_GET["goc-attack"])){
			echo 'invalid attack';
			return;
		}
		// Perform an attack
		$a = new Attack($_GET["goc-attack"]);

		// Do the attack
		if($a->doAttack() === true){

			echo 'Defender power '.$a->defender_power.'<br />';
			echo 'Attacker power '.$a->attacker_power.'<br />';
			echo 'Winner '.$a->winner->getUserinfo()->user_login.'<br />';
			echo 'Money Win: '.$a->amount_won.'<br />';
			echo 'Defender got away: '.$a->defender_got_away.'<br />';
			echo 'Transaction message '.$a->transaction_result;

		}else{
			// Not valid attack
			echo $a->result;
		}

	}

}