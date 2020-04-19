<?php
/**
 * Created by PhpStorm.
 * User: Tim
 * Date: 8/07/2018
 * Time: 21:54
 */

namespace Xe_GOC\Inc\Controllers\Frontend;
use Xe_GOC\Inc\Lib\PageHandling;
use Xe_GOC\Inc\Lib\TemplateEngine;
use Xe_GOC\Inc\Models\Frontend\CriminalUser;

/**
 * Class Hq
 * @package Xe_GOC\inc\frontend
 */
class Hq {

	public $changeProfileTextKey = "goc_change_profile_key";

	public function __construct() {

		// Load the hq shortcode
		add_shortcode('goc_user_hq',array($this,'loadUserHq'));

		add_action("admin_post_{$this->changeProfileTextKey}",array($this,"changeProfileText"));

	}

	/**
	 * Load the hq template
	 */
	public function loadUserHq(){

		$cuser = new CriminalUser();
		$t = new TemplateEngine();
		$t->user = $cuser;
		$t->hook_save_text =$this->changeProfileTextKey;
		echo $t->display("frontend/user/hq.php");

	}

	/**
	 * Change the profile text
	 */
	public function changeProfileText(){

		$c = new CriminalUser();

		if($c->userExsists()){
			if(isset($_POST["profile-text"]) && strlen($_POST["profile-text"]) <= 2000){

				if($this->checkText($_POST["profile-text"]) == true) {
					$c->setUserMeta( "goc_profile_text", $_POST["profile-text"] );
					$m = __( "Profiel tekst succesvol opgeslagen.", "xe_goc" );
				}else{
					$m = __("Een profiel tekst mag geen links of scripts bevatten.","xe_goc");
				}
			}else{
				$m = __("Een profieltekst moet minimum 1 & maximum 2000 tekens bevatten.","xe_goc");

			}
		}

		PageHandling::forwardBack($m);
	}

	/**
	 * Check text
	 * @param $text
	 *
	 * @return bool
	 */
	public function checkText($text){
		$pattern = '~[a-z]+://\S+~';

		// Link found, no links allowed
		if($num_found = preg_match_all($pattern, $text, $out))
		{
			return false;
		}

		// Script tag found
		if($num_found =  preg_match_all('~<a[^>]*>\K[^<]*(?=</a>)~i',  $text)){
			return  false;
		}

		// Script tag found
		if($num_found =  preg_match_all('~<script[^>]*>\K[^<]*(?=</script>)~i', $text)){
			return  false;
		}
		return true;
	}

}