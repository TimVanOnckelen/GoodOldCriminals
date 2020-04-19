<?php


namespace Xe_GOC\Inc\Controllers\Frontend;


use Xe_GOC\Inc\controllers\backend\LocationPostType;
use Xe_GOC\Inc\Lib\PageHandling;
use Xe_GOC\Inc\Lib\TemplateEngine;
use Xe_GOC\Inc\Models\Frontend\BankTransaction;
use Xe_GOC\Inc\Models\Frontend\CarInGarage;
use Xe_GOC\Inc\Models\Frontend\CriminalUser;

class Location {

	private $traveltime = 86400;
	private $timeleft = 0;

	// private $hook = "goc_garage_action";

	function __construct() {

		// Load the bank shortcode
		add_shortcode('goc_location_map',array($this,'loadLocationMap'));
		add_shortcode('goc_travel',array($this,'travel'));
		add_shortcode('goc_travel_time',array($this,'travelTime'));

	}

	public function loadLocationMap(){

		wp_enqueue_style('cssmap',XE_GOC_PLUGIN_DIR . 'assets/js/cssmap/cssmap-europe/cssmap-europe.css');
		wp_enqueue_script( 'cssmap', XE_GOC_PLUGIN_DIR . 'assets/js/cssmap/jquery.cssmap.min.js', array ( 'jquery' ), 1.1, true);


		// Load the template
		$temp = new TemplateEngine();
		$temp->locations = LocationPostType::getAllLocations();

		// Display the template
		return $temp->display('frontend/location/main.php');

	}

	/**
	 * Travel to country
	 */
	public function travel(){

		if(isset($_GET["country"]) && is_numeric($_GET["country"])){

			$criminal = new CriminalUser();

			if($this->canUserTravel($criminal) === true) {

				// Travel to the location
				$travel_to_location = $_GET["country"];
				$travel_to_location = new \Xe_GOC\inc\models\frontend\location( $travel_to_location );
				$current_location   = new \Xe_GOC\inc\models\frontend\location( $criminal->getLocation() );
				$ticket_price       = $current_location->getTicketPriceTo( $travel_to_location );

				// Check if travel location is valid
				if ( $travel_to_location->getMapId() > 0 && $travel_to_location->getId() !== $current_location->getId() ) {
					$transaction = new BankTransaction( $criminal->getId(), false, $ticket_price, "buy", "cash" );
					if ( $transaction->doTransaction() === true ) {
						// Transaction worked out
						$criminal->updateLocation( $travel_to_location->getId() );
						// Set travel time :)
						$criminal->updateTravelTime();
						$m = vsprintf( __( "Welcome to %s! ", "xe_goc" ), array( $travel_to_location->getName() ) );
					} else { // Not enough money
						$m = vsprintf( __( "You don't have enough cash to travel to %s! ", "xe_goc" ), array( $travel_to_location->getName() ) );
					}
				} else { // Not a valid country
					$m = __( "Your trying to travel nowhere..", "xe_goc" );
				}
			}else{
				$time = gmdate("H:i:s", $this->timeleft);
				$m = vsprintf( __( "You can only travel once in 24 hours. Wait another %s seconds", "xe_goc" ),array($time));
			}

			// Forward back to page
			PageHandling::showMessage($m);

		}

	}

	/**
	 * Check if user can travel already
	 * @param $criminal
	 *
	 * @return bool
	 */
	private function canUserTravel($criminal){

		$time_when_user_can_travel = $criminal->getTravelTime() + $this->traveltime;

		if($time_when_user_can_travel === 0){
			$time_when_user_can_travel = time();
		}

		// User may travel
		if(time() >= $time_when_user_can_travel){
			return true;
		}

		// set time left
		$this->timeleft = $time_when_user_can_travel - time();

		return false;

	}

	/**
	 * Get time when user can travel
	 */
	public function travelTime(){

		$criminal = new CriminalUser();
		if($this->canUserTravel($criminal) === true){
			echo __("Klaar","xe_goc");
		}else{
			$t = new TemplateEngine();
			$t->time = $this->timeleft;
			echo $t->display('frontend/crime/timer.php');
		}
	}

}