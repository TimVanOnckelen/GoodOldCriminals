<?php

namespace Xe_GOC\Inc\Models\Frontend;


use Xe_GOC\Inc\Lib\security;

class CriminalUser {

	private $user;
	private $attack = 0;
	private $defence = 0;
	private $total_power = 0;
	private $cash = 5000;
	private $bank = 10000;
	private $location = 3622; // standard location :)
	private $traveltime = 0;
	private $total_xp = 0;
	private $db_table_power = XE_GOC_TABLE_POWER;
	private $db_table_xp = "goc_xp";
	private $cash_key = "goc_cash";
	private $bank_key = "goc_bank";
	private $location_key = "goc_location";
	private $traveltime_key = "goc_traveltime";

	/**
	 * CriminalUser constructor.
	 *
	 * @param null $uid
	 */
	public function __construct($uid=null) {

		// Set the user data
		if($uid == null OR !is_numeric($uid)){
			$this->user = get_current_user_id();
		}else{
			$this->user = $uid;
		}

	}

	/**
	 * @return bool
	 */
	public function userExsists(){

		global $wpdb;

		$user_id = $this->user;

		// Check cache:
		if (wp_cache_get($user_id, 'users')) return true;

		// Check database:
		if ($wpdb->get_var($wpdb->prepare("SELECT EXISTS (SELECT 1 FROM $wpdb->users WHERE ID = %d)", $user_id))) return true;

		return false;
	}

	/**
	 * Set standard values for user on register
	 */
	public function createNewUser(){

		// Set cash
		update_user_meta($this->user,$this->cash_key,$this->cash);
		// Set bank
		update_user_meta($this->user,$this->bank_key,$this->bank);


	}

	/**
	 * @return int|string
	 */
	public function getId(){
		return $this->user;
	}

	/**
	 * @return string
	 */
	public function getName(){
		if($this->getId() > 0) {
			$u = $this->getUserinfo();

			return $u->display_name;
		}else{
			return __("Server berichtsysteem");
		}
	}
	/**
	 * @return false|\WP_User
	 */
	public function getUserinfo(){
		return get_userdata($this->user);
	}

	/**
	 * Set user meta by Key
	 * @param $key
	 * @param $value
	 */
	public function setUserMeta($key,$value){
		update_user_meta($this->user,$key,$value);
	}

	/**
	 * Get user meta by key
	 * @param $key
	 * @param bool $single
	 */
	public function getUserMeta($key,$single=false){
		return get_user_meta($this->user,$key,$single);
	}

	/**
	 * @return false|string
	 */
	public function getProfilePic(){
		if($this->getId() > 0) {
			return get_avatar_url( $this->user );
		}else{ // Return logo
			$custom_logo_id = get_theme_mod( 'custom_logo' );
			return wp_get_attachment_image_src($custom_logo_id,"thumbnail")[0];
		}
	}

	/**
	 * Get the user attack power
	 * @return int
	 */
	public function getAttack(){
		// Set the attack
		$this->setAttack();
		return $this->attack;
	}

	/**
	 * Get the user defence power
	 * @return int
	 */
	public function getDefence(){
		$this->setDefence();
		return $this->defence;
	}

	/**
	 * Get the total power
	 * @return int
	 */
	public function getTotalPower(){
		$this->calculatePower();
		return $this->total_power;
	}

	/**
	 * Get user cash
	 * @return int
	 */
	public function getCash(){
		$this->setCash();
		return round($this->cash);
	}

	/**
	 * Get user bank
	 * @return int
	 */
	public function getBank(){
		$this->setBank();
		return round($this->bank);
	}

    /**
     * @return int
     */
	public function getLocation(){
	    $this->setLocation();
	    return $this->location;
    }

	/**
	 * @return int
	 */
	public function getTravelTime(){
		$this->setLastTravelTime();
		return $this->traveltime;
	}

	/**
	 * Get the last 50 attack logs
	 * @return object|null
	 */
	public function getAttackLogs(){
		global $wpdb;

		$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}goc_attack_log WHERE (defender = {$this->getId()}) ORDER by 'time' DESC LIMIT 20",OBJECT);

		return array_reverse($results);
	}
	/**
	 * @return mixed
	 */
	public function getXp(){
		$this->setXp();
		return $this->total_xp;
	}

	/**
	 * @return string
	 * @throws \Exception
	 */
	public function getBankId(){
		$bank_id = $this->getName().'__'.$this->getId();
		return security::encrypt($bank_id);
	}

	/**
	 * Get cash from db
	 */
	private function setCash(){
		$this->cash = get_user_meta($this->user,$this->cash_key,true);
	}

	/**
	 * Get bank from db
	 */
	private function setBank(){
		$this->bank = get_user_meta($this->user,$this->bank_key,true);
	}

	/**
	 * Add Money to account
	 * @param $amount
	 * @param $type
	 */
	public function setMoneyAccount($amount,$type,$action="add"){

		// Get the type
		$a_type = array("cash" => $this->cash_key,"bank" => $this->bank_key);
		$type = $a_type[$type];

		// get the amount
		$c_cash = get_user_meta($this->user,$type,true);

		if($action == "add") {
			// Add the amount
			$n_cash = $c_cash + $amount;
		}else{
			// Remove the amount
			$n_cash = $c_cash - $amount;
		}

		// Update the amount
		update_user_meta($this->user,$type,$n_cash);

	}

	/**
	 * Get the attack from the db & set it
	 */
	private function setAttack(){

		global $wpdb;

		// Get the attack from the db
		$select = "SELECT sum(attack*shop_item_amount) as total_attack FROM {$wpdb->prefix}{$this->db_table_power} WHERE user_id='{$this->user}'";

		// Get the result
		$result = $wpdb->get_results($select);

		if(isset($result[0]->total_attack)) {
			$this->attack = $result[0]->total_attack;
		}else{
			$this->attack = 0;
		}
	}

	/**
	 * Get the defence from the db & set it
	 */
	private function setDefence(){

		global $wpdb;

		// Get the defence from the db
		$select = "SELECT sum(defence*shop_item_amount) as total_defence FROM {$wpdb->prefix}{$this->db_table_power} WHERE user_id='{$this->user}'";

		// Get the result
		$result = $wpdb->get_results($select);

		if(isset($result[0]->total_defence)) {
			$this->defence = $result[0]->total_defence;
		}else{
			$this->defence = 0;
		}

	}

	private function setXp(){

		global $wpdb;

		// Get the defence from the db
		$select = "SELECT sum(amount) as total_xp FROM {$wpdb->prefix}{$this->db_table_xp} WHERE user_id='{$this->user}'";

		// Get the result
		$result = $wpdb->get_results($select);

		if(isset($result[0]->total_xp)) {
			$this->total_xp = $result[0]->total_xp;
		}else{
			$this->total_xp = 0;
		}

	}

    /**
     * Get the location from db
     */
	private function setLocation(){
	    $location = get_user_meta($this->getId(),$this->location_key,true);

	    if($location > 0){
	        $this->location = $location;
        }

    }

	/**
	 * Set the local travel time string
	 */
    private function setLastTravelTime(){

	    $traveltime = get_user_meta($this->getId(),$this->traveltime_key,true);

	    if($traveltime > 0){
		    $this->traveltime = $traveltime;
	    }


    }

	/**
	 * @param $location
	 */
    public function updateLocation($location){
	    update_user_meta($this->getId(),$this->location_key,$location);
	    $this->location = $location;
    }

	/**
	 * @return bool|int
	 */
	public function updateTravelTime(){
		return update_user_meta($this->getId(),$this->traveltime_key,time());
	}


	/**
	 * Calculate the total power of the user
	 */
	private function calculatePower(){

		// Get the Attack and defence from db
		$this->setDefence();
		$this->setAttack();

		// The total power
		$this->total_power = $this->attack + $this->defence;

	}

}