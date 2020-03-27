<?php
/**
 * Created by PhpStorm.
 * User: Tim
 * Date: 28/07/2018
 * Time: 15:32
 */

namespace Xe_GOC\inc\controllers\frontend;


use Xe_GOC\Inc\Lib\PageHandling;
use Xe_GOC\Inc\Lib\TemplateEngine;
use Xe_GOC\inc\models\frontend\Crime;
use Xe_GOC\Inc\Models\Frontend\CriminalUser;

class doCrime {

	private $templateEng;
	private $temp;
	private $hook_post = "goc_do_crime";

	public function __construct() {

		// Load the shop shortcode
		add_shortcode('goc_do_crime',array($this,'doCrime'));

		// Load the action
		add_action("admin_post_{$this->hook_post}",array($this,"handleCrimeTry"));


	}

	/**
	 * @param array $atts
	 *
	 * @return bool|string
	 */
	public function doCrime($atts = array()){

		// If theres a message to show, do not go further
		if(isset($_GET["goc_m"]))
			return false;

		$this->templateEng = new TemplateEngine();
		// set the post hook
		$this->templateEng->hook_post = $this->hook_post;

		// Do a regular crime
		$this->regularCrime($atts);

		return $this->templateEng->display($this->temp);

	}

	/**
	 * Do a regular Crime
	 */
	private function regularCrime($atts = array()){

		$crime_id = 0;

		// Setup crime id
		if(isset($atts["id"]) && is_numeric($atts["id"])){
			$crime_id = $atts["id"];
		}

		// Load crime
		$crime = new Crime($crime_id,get_current_user_id());

		if($crime->canDoCrime() === true){

			// Set crime id
			$this->templateEng->crimeId = $crime_id;
			// Set the crimes
			$this->templateEng->crimes = $crime->getCrimes();

			$this->temp = 'frontend/crime/regular-crime.php';

		}else{

			// User has to wait
			$this->waitTemplate($crime->result);
		}

	}

	/**
	 * Handle a crime try request
	 */
	public function handleCrimeTry(){


		if(isset($_POST["crime"]) && is_numeric($_POST["crime"])) {

			// Setup crime
			$c = new Crime($_POST["crimeId"],get_current_user_id());
			$c->setOptionId($_POST["crime"]); // set option
			$c->doCrime();
			$m = $c->result;

		}else{

			$m = __('Er ging iets mis, probeer het opnieuw.','xe_goc');

		}

		// Forward back to page
		PageHandling::forwardBack($m);

	}

	/**
	 * The wait template
	 * @param $r
	 */
	public function waitTemplate($r){

		$this->templateEng->m = $r;
		$this->temp = 'frontend/crime/wait.php';

	}

}