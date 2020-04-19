<?php


namespace Xe_GOC\Inc\Controllers\Frontend;


use Xe_GOC\Inc\Lib\PageHandling;
use Xe_GOC\Inc\Lib\TemplateEngine;
use Xe_GOC\Inc\Models\Frontend\Crime;
use Xe_GOC\Inc\Models\Frontend\CriminalUser;

class editUser {

	private $usernameChangePage = 3749;
	private $post_hook = "goc_first_changeusername";

	public function __construct() {

		// Force a username change for social users
		add_action('wp', array($this,"forceUsernameChange"));
		// On social register
		add_filter('nsl_register_new_user',array($this,"socialCreateAccount"),10,2);

		// Username changing for social users
		add_shortcode("goc_choose_username",array($this,"chooseUsernameTemplate"));
		add_action("admin_post_{$this->post_hook}",array($this,"handleChangeUsername"));

	}

	public function forceUsernameChange(){

		$c = new CriminalUser();
		$usernameChanged = $c->getUserMeta("goc_social_choose_username",true);

		// Username should be changed, so redirect to correct page.
		if(get_queried_object_id() != $this->usernameChangePage && $usernameChanged == 1){
			wp_safe_redirect(get_page_link($this->usernameChangePage));
			exit;
		}

	}

	/**
	 * Riderct to change username page when creating an account
	 * @param $user_id
	 */
	public function socialCreateAccount($user_id,$provider){

		$c = new CriminalUser($user_id);
		$c->setUserMeta("goc_social_choose_username",true);

		return $user_id;
	}

	/**
	 * Change username
	 */
	public function chooseUsernameTemplate(){

		$t = new TemplateEngine();
		$t->hook = $this->post_hook;
		echo $t->display('frontend/user/changeUsername.php');

	}

	/**
	 *
	 */
	public function handleChangeUsername(){

		global $wpdb;

		$c = new CriminalUser();
		$canChangeUsername = $c->getUserMeta("goc_social_choose_username",true);

		if(isset($_POST["username"]) && $canChangeUsername == 1 && !empty($_POST["username"])) {
			// User can be updated
			if(username_exists($_POST["username"]) === false) {
				if(strlen($_POST) < 2) {
					$return = $wpdb->update( $wpdb->users, array( 'display_name' => $_POST["username"]
					), array( 'ID' => get_current_user_id() ) );
					if ( $return != false ) {
						// update username changing
						$c->setUserMeta( "goc_social_choose_username", false );
						// Redirect to home
						wp_safe_redirect( home_url() );
						exit;
					} else {
						$m = __( 'Er ging iets mis, probeer het opnieuw.', 'xe_goc' );
					}
				}else{
					$m = __( 'Deze gebruikersnaam is te kort. Probeer opnieuw.', 'xe_goc' );
				}
			}else{
				$m = __('Deze gebruikersnaam is niet meer beschikbaar. Kies een andere.','xe_goc');
			}
		}else{
			$m = __('Je kan je gebruikersnaam niet veranderen.','xe_goc');
		}

		// Forward back to page
		PageHandling::forwardBack($m);

	}
}