<?php
/**
 * Created by PhpStorm.
 * User: Tim
 * Date: 8/07/2018
 * Time: 22:42
 */

namespace Xe_GOC\Inc\Models\Frontend;


class shopItem {

	private $id = 0;
	private $attack = 0;
	private $defence = 0;
	private $price = 0;
	private $max = 0;
	private $db_table_power = XE_GOC_TABLE_POWER;
	private $user_id = 0;

	// Needle of the shop item
	public static $save_needle = "goc_shop";
	// Meta keys of a shop item
	private static $meta_keys = array("attack" => "attack","defence" => "defence","price" => "price", "max" => "max");

	public function __construct($id, $user = null) {

		if(is_numeric($id)){
			$this->id = $id;
		}

		// Set the user
		if(isset($user) OR $user == null){
			$this->user_id = get_current_user_id();
		}

	}

	/**
	 * @return int
	 */
	public function getAttack() {
		$this->attack = get_post_meta($this->id,self::getMetaKey("attack"),true);
		return $this->attack;
	}

	/**
	 * @return int
	 */
	public function getDefence() {
		$this->defence = get_post_meta($this->id,self::getMetaKey("defence"),true);
		return $this->defence;
	}


	/**
	 * @return int
	 */
	public function getPrice() {
		$this->price = get_post_meta($this->id,self::getMetaKey("price"),true);
		return $this->price;
	}

	public function getMax(){
		$this->max = get_post_meta($this->id,self::getMetaKey("max"),true);
		return $this->max;
	}

	/**
	 * Buy an item
	 * @param $amount
	 *
	 * @return string
	 */
	public function buy($amount){

		// Amount of items user ownes if bought
		$userHas = $this->userHas() + $amount;
		// Check if user already has max of items
		if($userHas > $this->getMax()){

			return __('You already own the maximum amount of this item','xe_goc');

		}

		// Not numeric
		if(!is_numeric($amount)){
			return __('Geen geldige input.','xe_goc');
		}

		// Calculate the total price
		$total_price = $this->getPrice() * $amount;
		// Do a transaction
		$transaction = new BankTransaction($this->user_id,false,$total_price,"buy","cash");
		$t = $transaction->doTransaction();

		// Transaction succefull?
		if($t === true){

				/**
				 * HERE THE BUY EVENT
				 */
				$this->addToDb($amount);

				// Do a buy, and send a message :)
				return __("Aankoop gelukt!",'xe_goc');


		}else{
			// Buy failed
			return $t;
		}

	}

	/**
	 * Add the item to the db
	 * @param $amount
	 *
	 * @return array|null|object
	 */
	private function addToDb($amount){

		global $wpdb;

		// Get the defence from the db
		$select = "INSERT INTO {$wpdb->prefix}{$this->db_table_power} (user_id,attack,defence,shop_item_id,shop_item_amount) VALUES ('{$this->user_id}','{$this->getAttack()}','{$this->getDefence()}','{$this->id}','{$amount}')";

		// Get the result
		return $wpdb->get_results($select);

	}

	/**
	 * Get the amount of items that the user has
	 * @return mixed
	 */
	public function userHas(){

		global $wpdb;

		$select = "SELECT sum(shop_item_amount) as userHas FROM {$wpdb->prefix}{$this->db_table_power} WHERE user_id='{$this->user_id}' AND shop_item_id='{$this->id}'";

		// Get the result
		$r = $wpdb->get_results($select);

		if(!isset($r[0]->userHas) or $r[0]->userHas == null){
			$return = 0;
		}else{
			$return = $r[0]->userHas;
		}

		return $return;

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