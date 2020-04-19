<?php


namespace Xe_GOC\Inc\Controllers\Frontend;


use Xe_GOC\Inc\Lib\PageHandling;
use Xe_GOC\Inc\Lib\TemplateEngine;
use Xe_GOC\Inc\Models\Frontend\BankTransaction;
use Xe_GOC\Inc\Models\Frontend\CarInGarage;

class Garage {

	private $hook = "goc_garage_action";

	function __construct() {

		// Load the bank shortcode
		add_shortcode('goc_garage',array($this,'loadGarage'));

		// Load the action
		add_action("admin_post_{$this->hook}",array($this,"carAction"));

	}

	public function loadGarage(){
		$cars = CarInGarage::getCarsFromUsers(get_current_user_id());

		// Load the template
		$temp = new TemplateEngine();
		$temp->cars = $cars;
		$temp->hook = $this->hook;
		// Display the template
		return $temp->display('frontend/garage/main.php');

	}

	/**
	 * Do a bank transfer
	 */
	public function carAction(){

		if(isset($_POST["sell"])){
			// Get the car in the garage
			$carInGarage = new CarInGarage();
			$carInGarage->setTransactionId($_POST["car_id"]);
			$carInGarage->setOwnerId(get_current_user_id());
			// Sell the car and get a message
			$m = $carInGarage->sell();
		}

		if(isset($_POST["repair"])){
			// Get the car in the garage
			$carInGarage = new CarInGarage();
			$carInGarage->setTransactionId($_POST["car_id"]);
			$carInGarage->setOwnerId(get_current_user_id());
			// Sell the car and get a message
			$m = $carInGarage->repair();
		}


		// Forward back to page
		PageHandling::forwardBack($m);
	}

}