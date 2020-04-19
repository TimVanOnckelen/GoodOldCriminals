<?php
namespace Xe_GOC\Inc\Models\Frontend;

class CarInGarage {

	private $owner_id = 0;
	private $car_id = 0;
	private $value = 0;
	private $damage = 0;
	private $transaction_id = 0;
	private $location = 0;
	private static $db_table_bank = "goc_car_garage";


	/**
	 * @param int $owner_id
	 */
	public function setOwnerId( $owner_id ) {
		$this->owner_id = $owner_id;
	}

	/**
	 * @param int $car_id
	 */
	public function setCarId( $car_id ) {
		$this->car_id = $car_id;
	}

	/**
	 * @param int $value
	 */
	public function setValue( $value ) {
		$this->value = $value;
	}

	/**
	 * @param int $damage
	 */
	public function setDamage( $damage ) {
		$this->damage = $damage;
	}

	/**
	 * @param $location
	 */
	public function setLocation($location){
		$this->location = $location;
	}

	/**
	 * @param int $damage
	 */
	public function setTransactionId( $transaction_id ) {
		$this->transaction_id = $transaction_id;
	}

	public function getLocation(){
		return $this->location;
	}

	public function getCarId(){
		return $this->car_id;
	}

	/**
	 * @return int
	 */
	public function getTransactionId() {
		return $this->transaction_id;
	}

	public function create(){

		global $wpdb;
		$this->transaction_id = $wpdb->insert($wpdb->prefix.self::$db_table_bank,array("location" => $this->location, "user_id"=> $this->owner_id,"car_id" => $this->car_id,"damage" => $this->damage,"value" => $this->value));

		return $this->transaction_id;
	}

	/**
	 * Sell a car
	 * @return string|void
	 */
	public function sell(){

		// Check if user is owner of car
		if($this->isCarOfOwner() === true){

			// Get car name
			$car_name = get_the_title($this->car_id);

			// Check if the car is the right location
			if(!$this->isCarInRightLocation()){
				return vsprintf(__("De %s bevindt zich niet in je huidige locatie.","xe_goc"),array($car_name));
			}

			// Check value & delete car
			if($this->value > -0.01 && $this->removeFromGarage() !== false){
				// Make a transaction
				$transactions = new BankTransaction(false,$this->owner_id,$this->value,"sell","cash");
				$transactions->doTransaction();

				return vsprintf(__("The %s is sold succesfully for &euro; %s. The amount is transferd to your cash balance","xe_goc"),array($car_name,$this->value));

			}else{
				return __("There went something wrong during selling. Try again","xe_goc");
			}

		}else{
			return __("The car you want to sell is not yours.","xe_goc");
		}

	}

	public function repair(){

		if($this->isCarOfOwner() === true){ // Does the user owns the car?


			// The current state of the care
			$nondamge = 100 - $this->damage;
			// The total price to repair
			$total_repair_price = ($this->value/$nondamge) * 100;
			$car_name = get_the_title($this->car_id);

			// Check if the car is the right location
			if(!$this->isCarInRightLocation()){
				return vsprintf(__("De %s bevindt zich niet in je huidige locatie.","xe_goc"),array($car_name));
			}

			$transaction = new BankTransaction($this->user_id,false,$total_repair_price,"buy","cash");
			$t = $transaction->doTransaction();

			if($t === true){ // Transaction done, so repair the car :)
				$total_price = $this->value + $total_repair_price;
				$this->updateDamage($total_price);
				return vsprintf(__("Your %s is repair and now has the maximum value.","xe_goc"),array($car_name));

			}else{
				return vsprintf(__("You don't have enough cash to repair the %s","xe_goc"),array($car_name));
			}

		}else{
			return __("The car you want to repair is not yours.","xe_goc");
		}


	}

	private function updateDamage($value){
		global  $wpdb;
		$wpdb->update($wpdb->prefix.self::$db_table_bank,array("damage"=> 0,"value" => $value),array("id" => $this->transaction_id));
	}

	private function removeFromGarage(){

		global $wpdb;

		return $wpdb->delete($wpdb->prefix.self::$db_table_bank,array("id" => $this->transaction_id));
	}

	/**
	 *
	 * @return bool
	 */
	public function isCarOfOwner(){

		global $wpdb;
		$db_table = self::$db_table_bank;

		// Get all cars from users
		$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}{$db_table} WHERE user_id='{$this->owner_id}' AND id='{$this->transaction_id}' ",OBJECT);

		if(count($results) === 1){
			$this->transaction_id = $results[0]->id;
			$this->damage = $results[0]->damage;
			$this->value = $results[0]->value;
			$this->car_id = $results[0]->car_id;
			$this->location = $results[0]->location;
			return true;
		}else{
			return false;
		}

	}

	/**
	 * Check if car is in the right location
	 * @return bool
	 */
	private function isCarInRightLocation(){

		$c = new CriminalUser();
		if($this->location == $c->getLocation()){
			return true;
		}

		return false;

	}

	/**
	 * Get the cars in the garage
	 * @return mixed
	 */
	public static function getCarsFromUsers($user){

		global $wpdb;
		$db_table = self::$db_table_bank;

		// Get all cars from users
		$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}{$db_table} WHERE user_id='{$user}' ",OBJECT);

		if(count($results) > 0){
			foreach ($results as $key => $car){
				// Add the car title :)
				$results[$key]->car_name = get_the_title($car->car_id);
				$results[$key]->speed = get_post_meta($car->car_id,Car::getMetaKey("max_speed"),true);
			}
		}

		return $results;
	}
}