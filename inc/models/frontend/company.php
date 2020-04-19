<?php


namespace Xe_GOC\Inc\Models\Frontend;


class Company {

	private $id;
	private $owner = 0; // Zero = state is owner
	private $name = "";
	private $location = 0; // zero = no location set
	private $price = "";
	private $products = array();
	private static $meta_keys = array("owner" => "owner","price" => "price","products" => "products");
	public static $save_needle = "goc_company";

	public function __construct($id = false) {

		if($id != false){
			$this->id = $id;
		}

	}

	/**
	 * @param $l
	 */
	public function setLocation($l){
		$this->location = $l;
	}

	/**
	 * @return string
	 */
	public function getName(){
		$this->name = get_the_title($this->id);
		return $this->name;
	}

	/**
	 * @return bool
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Check if company exists
	 * @return bool
	 */
	public function exists(){

		if ( get_post ( $this->id ) !== null ) {
			return true;
		}

		return false;
	}


	/**
	 * Get owner of company
	 * This is based on the location of the company
	 * If the location of company is not set, no owner will be given
	 * @return int|string
	 */
	public function getOwner(){

		// No location? No owner
		if($this->location === 0 OR !is_numeric($this->location)){
			return $this->owner;
		}

		// Get owner from meta
		$owner = get_post_meta($this->id,self::getMetaKey("owner")."_location_".$this->location,true);

		// Only set owner if there is one
		if($owner > 0 && is_numeric($owner)){
			$this->owner = $owner;
		}

		return $this->owner;

	}

	/**
	 * Get the owner name
	 * @return string|void
	 */
	public function ownerName(){

		$this->getOwner();

		// No owner? Than local maffia is owner
		if($this->owner === 0){
			$l = new location($this->location);
			return vsprintf(__("de lokale drugsmaffia van %s"),$l->getName());
		}

		// Return the owner name
		$c = new CriminalUser($this->owner);
		return $c->getName();
	}


	/**
	 * @return array
	 */
	public function getProducts(){

		// Get the products
		$products_array = get_post_meta($this->id,self::getMetaKey("products"),true);
		$l = new location($this->location);

		foreach ($products_array as $p){

			$p = new CompanyProduct($p);
			// Get the price of product on curernt location
			$product_price_location = "goc_product_price_l".$l->getId().'_p'. $p->getId();
			$current_price = get_transient( $product_price_location );

			// No price set? Set a price now then :)
			if(!$current_price) {
				// Time to expiernce, may be short, may be long
				$time_exp = rand(30000,86400);
				// Rand calculator
				$rand      = rand( 0, 5 );
				$day_price = rand( 1, 100 );
				// Calculate price based on location;
				$current_price = $p->getMinValue() / 10 * ( $l->getCountryWealth() + $rand ) * ( $day_price / 10 );
				// Set transient
				set_transient( $product_price_location, $current_price, $time_exp );
			}

			// Set product value
			$p->setProductCurrentValue($current_price);
			$this->products[$p->getId()] = $p;
		}

		return $this->products;
	}

	public function getProductStock($product_id){

        $l = new location($this->location);
        $product_stock_location = "goc_product_stock_l".$l->getId().'_p'. $product_id;
        $current_stock = get_transient( $product_stock_location );

        // No current stock is set
        if($current_stock === false){
            // Time to expiernce, may be short, may be long
            $time_exp = rand(86400,120000);

            // Set the current stock
            $rand = rand(1,1000);
            $current_stock = $l->getCountryWealth() * $rand;

            set_transient( $product_stock_location, $current_stock, $time_exp );
        }

        return $current_stock;
    }

    /**
     * Update stock with new amount
     * @param $product_id
     * @param $amount
     */
    public function updateProductStock($product_id,$amount){

        $l = new location($this->location);
        $product_stock_location = "goc_product_stock_l".$l->getId().'_p'. $product_id;
        // Update the stock
        set_transient( $product_stock_location, $amount );

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