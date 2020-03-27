<?php
/**
 * Created by PhpStorm.
 * User: Tim
 * Date: 28/07/2018
 * Time: 18:21
 */

namespace Xe_GOC\inc\controllers\frontend;


use Xe_GOC\Inc\Lib\PageHandling;
use Xe_GOC\Inc\Lib\TemplateEngine;

class Login {

	private $hook_login = 'goc_dologin';
	private $hook_logout = 'goc_dologout';

	public function __construct() {
		// Load the logout shortcode
		add_shortcode('goc_login',array($this,'loadTemplate'));

		// Load the logout shortcode
		add_shortcode('goc_logout',array($this,'loadLogoutTemplate'));

		// Do login action
		add_action("admin_post_nopriv_{$this->hook_login}",array($this,"doLogin"));

		// Do Logout
		add_action("admin_post_{$this->hook_logout}",array($this,"doLogout"));

		// Login redirect
		add_filter('login_redirect', array($this,'loginRedirect'));

		// Disable admin bar
		add_action('after_setup_theme', array($this,'remove_admin_bar'));
	}

	/**
	 * After login, redirect user to homepage
	 * @return string
	 */
	public function loginRedirect(){

		return get_home_url();

	}

	/**
	 * Remove the admin bar
	 */
	public function remove_admin_bar() {
		if (!current_user_can('administrator') && !is_admin()) {
			show_admin_bar(false);
		}
	}

	/**
	 * @return string
	 */
	public function loadTemplate(){

		// Load the template
		$temp = new TemplateEngine();
		$temp->hook_login = $this->hook_login;

		if(isset($_GET["goc_m"])){

			$temp->m = $_GET["goc_m"];
			return $temp->display( 'frontend/auth/message.php' );
		}

		if(!is_user_logged_in()) {
			return $temp->display( 'frontend/auth/login.php' );
		}else{
			// user already logged in
			return $temp->display( 'frontend/auth/login.php' );
		}

	}

	/**
	 * @return string
	 */
	public function loadLogoutTemplate(){

		// Load the template
		$temp = new TemplateEngine();
		$temp->hook_logout = $this->hook_logout;
		return $temp->display( 'frontend/auth/logout.php' );
	}

	/**
	 * Do a login
	 */
	public function doLogin(){

		$m = '';

		// check if username and password are set
		if(isset($_POST["username"]) && isset($_POST["password"])){
			$login_data = array();
			$login_data["user_login"] = $_POST["username"];
			$login_data["user_password"] = $_POST["password"];

			// Do a login
			$r = wp_signon($login_data);

			// Find errors, if so
			if ( is_wp_error($r) ){
				$m = $r->get_error_message();
			}else{
				$m = __('Login succesfull!','xe_goc');
			}

		}else{

			$m = __('Login failed, try again!','xe_goc');
		}

		// Forward back to page
		PageHandling::forwardBack($m);

	}

	/**
	 * Do a logout
	 */
	public function doLogout(){

		wp_logout();

		$m = __('You are now succesfully logged out.','xe_goc');
		// Forward back to page
		PageHandling::forwardBack($m);
	}
}