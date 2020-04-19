<?php
/**
 * Created by PhpStorm.
 * User: Tim Van Onckelen
 * Date: 7/04/2020
 * Time: 20:53
 */

namespace Xe_GOC\Inc\Models\Frontend;


class DuoCrime
{

    private $owner;
    private $partner;
    private $weapon;
    private $missionBudget;
    private $car;
    private $location;
    private $id;
    private $status;
    private $exp = 0;
    private $min_exp = 2000;
    private $missionTime = 3600*5;
    public static $min_budget = 20000;
    private $currentMissionTime = 0;
    private $car_damage = 0;
    private $profit = 0;
    private $percentage = 0;
    private $key = "goc_cron_run_duo_crime";
    private $table = XE_GOC_TABLE_DUOCRIME;


    public function __construct($id = 0)
    {
	    $this->id = $id;

    	if($id > 0){
    		$this->getCrime();
	    }

    }

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Get the status
	 * @return mixed
	 */
    public function getStatus(){

    	return $this->status;
    }

    public function getOwner(){
    	return $this->owner;
    }

	/**
	 * @return mixed
	 */
	public function getPartner() {
		return $this->partner;
	}

    public function getMissionBudget(){
    	return $this->missionBudget;
    }

    public function getLocation(){
    	return $this->location;
    }

	/**
	 * @return int
	 */
	public function getProfit() {
		return $this->profit;
	}

	/**
	 * @return int
	 */
	public function getCarDamage() {
		return $this->car_damage;
	}

	/**
	 * @return int
	 */
	public function getExp() {
		return $this->exp;
	}

	/**
	 * @return int
	 */
	public function getPercentage() {
		return $this->percentage;
	}

    /**
     * @param mixed $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

	/**
	 * @param int $percentage
	 */
	public function setPercentage( $percentage ) {
		$this->percentage = $percentage;
	}

    /**
     * @param mixed $partner
     */
    public function setPartner($partner)
    {
        $this->partner = $partner;
    }

    /**
     * @param mixed $car
     */
    public function setCar($car)
    {
        $this->car = $car;
    }

    /**
     * @param mixed $weapon
     */
    public function setWeapon($weapon)
    {
        $this->weapon = $weapon;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @param mixed $missionBudget
     */
    public function setMissionBudget($missionBudget)
    {
        $this->missionBudget = $missionBudget;
    }

    /**
     * @return bool|false|int
     */
    public function saveCrime(){

    	if(isset($this->id) && $this->id > 0){
    	    // Update the crime
            $this->updateCrime();
            return $this->id;

	    }else{
    		// Create new crime
		    $this->id = $this->insertNewCrime();
	    }

	    return $this->id;

    }

    /**
     * @return bool|false|int
     */
    private function insertNewCrime(){
	    global $wpdb;
	    // Setup mission
	    if($this->owner > 0) {
		    $wpdb->insert( $wpdb->prefix . $this->table, array(
			    "owner"             => $this->owner,
			    "location"          => $this->location,
			    "missionBudget" => $this->missionBudget,
			    "percentage" => $this->percentage
		    ) );

		    return $wpdb->insert_id;
	    }
	    return false;
    }

	/**
	 * Get the crime from the db
	 */
    private function getCrime(){

    	global $wpdb;
	    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}{$this->table} WHERE id = {$this->id}", OBJECT);

	    // Only set if exists
	    if(isset($results[0])) {
		    $this->setOwner( $results[0]->owner );
		    $this->setPartner($results[0]->partner);
		    $this->setMissionBudget( $results[0]->missionBudget );
		    $this->setLocation( $results[0]->location );
		    $this->currentMissionTime = $results[0]->end_date;
		    $this->status = $results[0]->status;
		    $this->percentage = $results[0]->percentage;
	    }

    }

    /**
     * @return bool|false|int
     */
    private function updateCrime(){
        global $wpdb;

        if($this->partner > 0){

        	// Get car in garage
        	$car = new CarInGarage();
        	$car->setTransactionId($this->car);
        	$car->setOwnerId($this->partner);
        	$car->isCarOfOwner();
        	$max_speed = get_post_meta($car->getCarId(),Car::getMetaKey("max_speed"),true);


        	// Set the mission time, based on the car speed :)
        	$this->missionTime = round(current_time("timestamp") + (($this->missionTime/$max_speed) * 120));
        	$this->missionTime = date("Y-m-d H:i:s",$this->missionTime);

            return $wpdb->update($wpdb->prefix.$this->table,
                array(
                    "partner" => $this->partner,
                    "car" => $this->car,
                    "status" => true, // Update status, mission is now ongoing
                    "end_date" => $this->missionTime // Set mission time
                ),
                array(
                    "id" => $this->id
                ));
        }
        return false;
    }

	/**
	 * Cancel the mission
	 * @return false|int
	 */
    public function cancel(){

    	global $wpdb;

        return $wpdb->delete($wpdb->prefix.$this->table,array("id" => $this->id));

    }

    public function doCrime(){

    	global $wpdb;

    	$this->getCrime();

    	// Make som random profit margins
    	$rand = rand(1.5,5);
    	$max_profit = $this->missionBudget * $rand;
    	// Random exp
    	$exp_generator = $this->min_exp;
    	$min_suc_rate = 0.2;

	    /**
	     * Succesrate will be improved on higher budget
	     */
    	if($this->missionBudget > self::$min_budget * 1.5){
    		$min_suc_rate = 0.25;
	    }
	    if($this->missionBudget > self::$min_budget * 2){
		    $min_suc_rate = 0.35;
	    }
	    if($this->missionBudget > self::$min_budget * 3){
		    $min_suc_rate = 0.375;
	    }
	    if($this->missionBudget > self::$min_budget * 4){
		    $min_suc_rate = 0.4;
	    }
	    if($this->missionBudget > self::$min_budget * 5){
		    $min_suc_rate = 0.5;
	    }
	    if($this->missionBudget > self::$min_budget * 6){
	    	$r = rand(5,30)/10;
		    $min_suc_rate = 0.5 + $r;
	    }
	    // Calculate min succes rate in percentage
		$min_suc_rate = round($min_suc_rate * 100);

	    // Calculate the success rate
	    $succes_rate = rand($min_suc_rate,100)/100;

	    // Calculate car damage
	    $car_damage = (1 - $succes_rate) * 100;

	    // Set a profit
	    $this->profit = $max_profit * $succes_rate;
	    // Calculate car damage
    	$this->car_damage = $car_damage;
    	// Calculate xp
	    $this->exp = $exp_generator * $succes_rate;

		// Update the mission
	    $wpdb->update($wpdb->prefix.$this->table,array("profit" => $this->profit,"car_damage" => $this->car_damage,"xp" => $this->exp,"payout" => true),array("id" => $this->id));

	    return true;
    }


    /**
     * @return false|int
     */
    public function getMissionTime(){
        $this->getCrime();
        return strtotime($this->currentMissionTime) - time();
    }
}