<?php


namespace Xe_GOC\Inc\Models\Frontend;


class Backpack {

	private $company = false;
	private $user;
	private $table_name = "goc_backpack";

	public function __construct($user,$company=0) {

		$this->user = $user;
		$this->company = $company;

	}

	/**
	 * Get the amount of a specific item
	 * @param $product
	 *
	 * @return int|mixed
	 */
	public function amountOfProduct($product){

		global $wpdb;

		$select = "SELECT amount FROM {$wpdb->prefix}{$this->table_name} WHERE user_id = {$this->user} AND product_id = {$product}";
		$result = $wpdb->get_results($select, OBJECT);
		$return = 0;

		if(count($result)> 0){
			return $result[0]->amount;
		}

		return $return;

	}

	/**
	 * Get the products from the given user in the given company
	 * @return array|object|null
	 */
	public function getProducts(){

		global $wpdb;

		$select = "SELECT * FROM {$wpdb->prefix}{$this->table_name} WHERE company_id = {$this->company} AND user_id = {$this->user}";
		$result = $wpdb->get_results($select, OBJECT);
		$return = array();

		if(count($result)> 0){
			foreach($result as $key => $p){
				$return[$p->product_id] = $p;
				$return[$p->product_id]->product = new CompanyProduct($p->product_id);
			}
		}

		return $return;

	}
}