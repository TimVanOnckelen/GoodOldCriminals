<?php


namespace Xe_GOC\Inc\Models\Frontend;


class CompanyProduct {

	private $id;
	private $min_value = 0;
	private $name;
	private $amount;
	private $current_value = 0;
	private $market_multiplier;
	private static $meta_keys = array("min_value" => "min_value","amount" => "amount");
	public static $save_needle = "goc_company_product";

	public function __construct($id = false) {

		if($id != false){
			$this->id = $id;
			$this->loadProductData();
		}

	}

	public function getId(){
		return $this->id;
	}

	public function getName(){
		return $this->name;
	}

	public function getMinValue(){
		return $this->min_value;
	}

	public function getAmount(){
		return $this->amount;
	}

	public function getValue(){
		return $this->current_value;
	}

	/**
	 * Set the product current value
	 * @param $v
	 *
	 * @return mixed
	 */
	public function setProductCurrentValue($v){
		return $this->current_value = $v;
	}

	public static function getMetaKey($v){

		return self::$save_needle.self::$meta_keys[$v];

	}

	/**
	 * Set the product data from the current product
	 */
	public function loadProductData(){
		$this->name = get_the_title($this->id);
		$this->min_value = get_post_meta($this->id,self::getMetaKey("min_value"), true);
		$this->amount = get_post_meta($this->id,self::getMetaKey("amount"), true);

	}
}