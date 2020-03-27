<?php
/**
 * Created by PhpStorm.
 * User: Tim
 * Date: 27/07/2018
 * Time: 22:49
 */

namespace Xe_GOC\inc\models\frontend;


class Crime {

	private $crime;
	private $luck;
	private $current_crime;
	private $won_money;
	private $crimes = array();
	public $result = "";
	public $tranasction_m = "";
	private $user; // The user
	private $last_log;
	private $time_to_wait = 60; // Time to wait until next crime in seconds
	private $max_chanse_rate = 80;
	private $meta_key_crime = "goc_crime_data";
	public  $time_left = 0;
	private $optionId = 0; // Id of the selected option

	// Needle of the shop item
	public static $save_needle = "goc_crime";
	// Meta keys of a crime item
	private static $meta_keys = array("text" => "text","success" => "success","failed" => "failed","chance" => "chance", "money" => "money","timetowait" => "timetowait","carcrime" => "carcrime");

	/**
	 * Crime constructor.
	 *
	 * @param int $crime
	 */
	function __construct($crime = -1,$u) {
		$this->crime = $crime;

		$this->setCrimes();

		// Setup meta key crime
		$this->meta_key_crime = $crime."_".$this->meta_key_crime;

		// Set the user
		$this->user = new CriminalUser($u);
		// get the last log
		$this->last_log = $this->user->getUserMeta($this->meta_key_crime,true);
	}

	/**
	 * Set the available crimes with there vars
	 */
	private function setCrimes($shuffle=true){

		// Only loop over if crime id is set
		if($this->crime > 0) {

			// Reset crimes
			$this->crimes = array();

			// Get crimes from the current crime type
			$crimes = get_posts( array( "post_type" => XE_GOC_POSTYPE_CRIMES_TYPE,
			                            'tax_query' => array(
				                            array(
					                            'taxonomy' => XE_GOC_POSTYPE_CRIMES_TYPE,
					                            'field' => 'term_id',
					                            'terms' => $this->crime,
					                            'include_children' => false
				                            )
			                            ) ));

			// Count if there are crimes
			if ( count( $crimes ) > 0 ) {
				foreach ( $crimes as $key => $c ) {
					// Process the crime array
					$this->crimes[ "crime_".$c->ID ] = array(
						"money"   => get_post_meta( $c->ID, self::getMetaKey( "money" ), true ),
						"name"    => $c->post_title,
						"chance"  => get_post_meta( $c->ID, self::getMetaKey( "chance" ), true ),
						"success" => get_post_meta( $c->ID, self::getMetaKey( "success" ), true ),
						"failed"  => get_post_meta( $c->ID, self::getMetaKey( "failed" ), true ),
						"timetowait" => get_post_meta( $c->ID, self::getMetaKey( "timetowait" ), true ),
						"crimecar" => get_post_meta( $c->ID, self::getMetaKey( "carcrime" ), true ),
						"id" => $c->ID
					);
				}
			}

			// Only shuffle when true
			if($shuffle === true) {
				// Shuffle the array
				shuffle( $this->crimes );
			}
		}

	}

	/**
	 * @return array
	 */
	public function getCrimes(){

		// Add new crime rates
		$this->crimeChanseRate();

		return $this->crimes;

	}

	/**
	 * Increase chanse rate based on trys
	 */
	private function crimeChanseRate(){

		if(is_array($this->crimes)) {

			foreach ( $this->crimes as $key => $c ) {

				// The new chanse
				$new_chanse = $this->crimes[ $key ]["chance"];

				// Expand the chanse based on trys
				if (isset($this->last_log["trys"]) && $this->last_log["trys"] > 0 ) {

					// calculate chanse rate
					$new_chanse = $this->last_log["trys"] / 50 + $this->crimes[ $key ]["chance"];
				}

				// Don't go over max rate
				if($new_chanse > $this->max_chanse_rate){
					$new_chanse = $this->max_chanse_rate;
				}

				// Set the new chanse
				$this->crimes[ $key ]["chance"] = round($new_chanse,2);
			}
		}

	}

	public function setOptionId($id){
		$this->optionId = $id;
	}

	/**
	 * Do a crime
	 * @return bool
	 */
	public function doCrime(){

		if($this->crime >= 0 && $this->optionId > 0){

			// Reset crimes
			$this->setCrimes(false);

			// Set the current crime
			$this->current_crime = $this->crimes["crime_".$this->optionId];

			// set the time to wait
			if($this->current_crime["timetowait"] > 0){
				$this->time_to_wait = $this->current_crime["timetowait"];
			}

			// check if user can do crime
			if($this->canDoCrime() !== true)
				return false;

			// Log the crime
			$this->logLastCrime();

			// Calculate the crime
			if($this->calculateCrime() === false)
				return false;
			else
				return true;

		}

		return false;
	}

	/**
	 * @return bool
	 */
	private function calculateCrime(){


		// Car crime
		if($this->current_crime["carcrime"] === 1){
			return $this->calculateCarCrime();
		}

		// Else regular crime :)
		$this->calculateRegularCrime();

	}

	private function calculateRegularCrime(){

		// calculate luck
		$this->luck = round(rand(0,100));

		// Increase chanses by amount of trys
		if($this->luck <= $this->current_crime["chance"]){
			// Crime worked!
			// Calc win
			$this->calculateWin();
			// Show the result
			$this->result = $this->current_crime["success"]. __(' You got &euro;','xe_goc').$this->won_money;
			return true;

		}else{
			// crime failed
			$this->result = $this->current_crime["failed"];
			return false;
		}

	}

	/**
	 * @return bool
	 */
	private function calculateCarCrime(){

		// calculate luck
		$this->luck = round(rand(0,100));

		// Increase chanses by amount of trys
		if($this->luck <= $this->current_crime["chance"]){
			// Crime worked!
			// Calc win
			$car =  new Car($this->luck);
			$carObject = $car->getCarObject();

			if($carObject !== null){
				// Show the result
				$this->result = $this->current_crime["success"]. __('Nice, you managed to steal a ','xe_goc').$carObject->post_title;
				return true;
			}else{
				// No car found, so crime faild
				$this->result = $this->current_crime["failed"];
				return false;
			}


		}else{
			// crime failed
			$this->result = $this->current_crime["failed"];
			return false;
		}

	}

	private function calculateCar(){

	}

	/**
	 * Calculate the win
	 */
	private function calculateWin(){

		// Calculate the win
		$this->luck = rand(1,4);
		$this->won_money = round($this->current_crime["money"]/$this->luck);

		// Do the transaction
		$t = new BankTransaction(null,get_current_user_id(),$this->won_money,'sell','cash');
		$this->tranasction_m = $t->doTransaction();

	}

	/**
	 * Check if the user can do a crime
	 * @return bool
	 */
	public function canDoCrime(){

		// Not set yet, so can do crime
		if(!isset($this->last_log["last-try"])){
			return true;
		}

		// The end time
		$end_time = $this->last_log["last-try"] + $this->time_to_wait;
		$c_time = current_time('timestamp');
		$this->time_left = $end_time - $c_time;

		// User time is over
		if($c_time >= $end_time)
			return true;
		else
			// Not over yet, show to user
			$this->result = sprintf(__('You have to wait another %d seconds to do a new crime.'),$this->time_left);
		return false;

	}

	/**
	 * Log the crime trys
	 */
	private function logLastCrime(){

		// Create array, if not yet logged

		if(!is_array($this->last_log)){
			$this->last_log = array();
			$this->last_log["trys"] = 0;
		}

		// Add one to the total trys
		$this->last_log["trys"] = $this->last_log["trys"]+ 1;
		$this->last_log["last-try"] = current_time('timestamp');

		// update the user data
		$this->user->setUserMeta($this->meta_key_crime,$this->last_log);

	}

	/**
	 * @param $v
	 *
	 * @return string
	 */
	public static function getMetaKey($v){

		return self::$save_needle.self::$meta_keys[$v];

	}

}