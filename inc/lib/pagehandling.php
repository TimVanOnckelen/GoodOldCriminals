<?php
/**
 * Created by PhpStorm.
 * User: Tim
 * Date: 8/07/2018
 * Time: 21:41
 */

namespace Xe_GOC\Inc\Lib;

class PageHandling {

	private static $key_message = "goc_m";

	public function __construct() {

		// Filter messages
		add_filter( 'the_content', array($this,'frontendMessage') );

	}

	/**
	 * Render a frontend Message
	 * @param $content
	 *
	 * @return string
	 */
	public function frontendMessage( $content ) {

		// Add message if needed
		if(isset($_GET[self::$key_message])){

			// Display the template
			$t = new TemplateEngine();
			$t->message = $_GET[self::$key_message];
			$custom_content = $t->display('frontend/messages/info.php');

			// Add to original content
			$custom_content .= $content;
		}else{
			$custom_content = $content;
		}

		return $custom_content;

	}

    /**
     * Message layout for a timer inside page
     * @param $timer
     * @param $message
     */
	public static function timerMessage($timer,$message){

	    $t = new TemplateEngine();
	    $timer_span = '<span class="gocTimer" href="#" data-time="'.$timer.'">'.gmdate("H:i:s",$timer).'</span>';
	    $t->message = vsprintf($message,array($timer_span));
	    echo $t->display("frontend/messages/timer.php");

    }

	/**
	 * @param $m
	 */
	public static function showMessage($m){

		// Display the template
		$t = new TemplateEngine();
		$t->message = $m;
		$custom_content = $t->display('frontend/messages/info.php');

		echo $custom_content;
	}

	public static function returnMessage($m){
		$message = $m;


		$t = new TemplateEngine();
		$t->message = $m;
		$custom_content = $t->display('frontend/messages/info.php');

		return $custom_content;
	}

	/**
	 * Forward the user back with or without a message
	 * @param $m
	 */
	public static function forwardBack($m=null){

		// Forward to bank page
		$url = urldecode($_POST['_wp_http_referer']);

		// Add message if needed
		if($m != null) {
			$url = add_query_arg( self::$key_message, urlencode($m), $url );
		}

		wp_safe_redirect($url);
		exit;

	}

}