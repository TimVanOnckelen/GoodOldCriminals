<?php

namespace Xe_GOC\Inc\Controllers\Frontend;

use Xe_GOC\Inc\Lib\PageHandling;
use Xe_GOC\Inc\Lib\security;
use Xe_GOC\Inc\Lib\TemplateEngine;
use Xe_GOC\Inc\Models\Frontend\CriminalUser;

class Profile {

	public function __construct() {

		// Load the hq shortcode
		add_shortcode('goc_user_profile',array($this,'loadUserProfile'));

	}

	/**
	 * Load the user profile template
	 */
	public function loadUserProfile(){

		if(isset($_GET["id"])) {

			$cuser   = new CriminalUser(security::decrypt($_GET["id"]));

			if($cuser->userExsists() && $cuser->getId() != wp_get_current_user()) {
				$t       = new TemplateEngine();
				$t->user = $cuser;
				echo $t->display( "frontend/user/profile.php" );
			}else{
				echo PageHandling::returnMessage(__("Deze gebruiker bestaat niet.","xe_goc"));

			}
		}else{
			echo PageHandling::returnMessage(__("Deze gebruiker bestaat niet.","xe_goc"));
		}

	}

}