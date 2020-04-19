<?php
/**
 * Created by PhpStorm.
 * User: Tim Van Onckelen
 * Date: 5/04/2020
 * Time: 16:56
 */

namespace Xe_GOC\Inc\Models\Frontend;


class DealersGoOut
{

    private $location = 0;
    private $amount_of_dealers = 0;
    private $amount_of_product = 0;
    private $time_left = 0;
    private $profit = 0;
    private $price = 0;
    private $dealer_price = 50;
    private $max_product_per_dealer = 30;
    private $product_lost = 0;
    private $product_id;
    private $product_not_sold = 0;
    private $user;
    private $key;
    private $timeToWait = 3600;

    public function __construct($user, $location, $product_id = 0,$amount_of_dealers=0,$amount_of_product = 0,$price = 0)
    {
        // Setup vars
        $this->amount_of_dealers = $amount_of_dealers;
        $this->amount_of_product = $amount_of_product;
        $this->product_id = $product_id;
        $this->location = $location;
        $this->user = $user;
        $this->price = $price;
        // Set the key
        $this->setKey();
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
	public function getMaxProductPerDealer() {
		return $this->max_product_per_dealer;
	}
	/**
	 * @return int
	 */
	public function getProductLost() {
		return $this->product_lost;
	}

	/**
	 * @return int
	 */
	public function getProductNotSold() {
		return $this->product_not_sold;
	}

	public function getAmountOfProduct(){
		return $this->amount_of_product;
	}

	/**
	 * @return int
	 */
	public function getAmountOfDealers() {
		return $this->amount_of_dealers;
	}

	/**
	 * @return int
	 */
	public function getProductId() {
		return $this->product_id;
	}

	/**
	 * Get the current market price
	 * @return false|float|mixed
	 */
    private function getCurrentMarketPrice(){

	    $market_price = "goc_dealers_market_price_l".$this->location."_p".$this->getProductId();
	    $price = get_transient( $market_price );

	    // Set new stock
	    if(!$price){
	    	// Expierence every 2 hours
	    	$time = rand(0,7200);

		    $location = new location($this->location);
		    $product = new CompanyProduct($this->product_id);

		    // Current buy price
		    $product_price_location = "goc_product_price_l".$location->getId().'_p'. $product->getId();
		    $current_price = get_transient( $product_price_location );

		    // Rand calculator
		    $day_price = rand( 1, 2 );
		    $rand = rand(2,3);
		    // Calculate price based on location;
		    $price = $current_price / 2 * (( $location->getCountryWealth()/$rand) * ( $day_price));

		    if($price < $current_price){
			    //		    	// Always get minimum value
		    	$price = $current_price + 20;
		    }

		    if($price > ($current_price * 7)){
			    // Don't make product higher than 7 times
			    $price = $current_price * 7;
		    }


		    // Set the transient
		    set_transient( $market_price, $price, $time );
	    }

	    return $price;

    }

	/**
	 * Get available dealers
	 * @return false|float|mixed
	 */
    public function getAvailableDealers(){

	    $dealers_available = "goc_dealers_stock_l".$this->location;
	    $current_stock = get_transient( $dealers_available );

	    // Set new stock
	    if(!$current_stock){
			$current_stock = round(rand(100,10000));
		    set_transient( $dealers_available, $current_stock, 86400 );
	    }

	    return $current_stock;

    }

	/**
	 * Update the available dealers
	 * @param $amount
	 */
    public function updateAvailableDealers($amount){

	    $dealers_available = "goc_dealers_stock_l".$this->location;
	    set_transient( $dealers_available, $amount, 86400 );

    }


	/**
	 * @return int
	 */
	public function getDealerRate(){
		return $this->dealer_price;
	}


	/**
	 * Get the status of the current event
	 * @return bool
	 */
    public function getStatus(){


        if($this->getData() == true){

            // Dealers are done
            if(time() >= $this->time_left){
                return true;
            }else{ // Dealers are still dealing
                return true;
            }

        }else{
        	// No dealing yet
        	return false;
        }

    }

    /**
     * Start a new dealing proces
     * @return bool|int
     */
    public function startDealing(){
    	// Set time to wait
        $this->time_left = time()+$this->timeToWait;

        // Setup array to save
        $array = array();
        $array["time_left"] = $this->time_left;
        $array["profit"] = 0;
        $array["product_id"] = $this->product_id;
        $array["product_lost"] = 0;
        $array["amount_of_product"] = $this->amount_of_product;
        $array["amount_of_dealers"] = $this->amount_of_dealers;

        // Update the dealing
        return update_user_meta($this->user,$this->key,$array);
    }

    /**
     * @return bool
     */
    private function getData(){
        $data = get_user_meta($this->user,$this->key,true);

        // If data is set
        if(is_array($data)){
        	$this->product_id = $data["product_id"];
            $this->time_left = $data["time_left"];
            $this->profit = $data["profit"];
            $this->product_lost = $data["product_lost"];
            $this->amount_of_product = $data["amount_of_product"];
            $this->amount_of_dealers = $data["amount_of_dealers"];
            return true;
        }

        return false;

    }

    /**
     * Show current process
     * @return string
     */
    public function currentProcess(){
    	if($this->time_left-time() > 0){
    		return false;
	    }else{
    		// Generate the result.
		    $this->doDealing();

		    return true;
	    }
    }

	/**
	 * Get the time
	 * @return false|string
	 */
    public function getTime(){
	    return $this->time_left - time();
    }

	/**
	 * Do the deal
	 */
    private function doDealing(){

    	// Product lost
		$damage = rand(0,10);
		$this->product_lost = round(($this->amount_of_product/100)*$damage);
		// Remove from products
		$this->amount_of_product -= $this->product_lost;

		if($this->amount_of_product >= 0) {

			// Amount of sold product
			// More dealers, means more change to sell all products
			$products_sold = round( rand( 1, ($this->amount_of_product * $this->amount_of_dealers)/1.2 ) );

			// Calculate amount of products not sold
			if ( $products_sold < $this->amount_of_product ) {
				$this->product_not_sold = $this->amount_of_product - $products_sold;
			}

			// You can not sell more of the product than you own
			if ( $products_sold > $this->amount_of_product ) {
				$products_sold = $this->amount_of_product;
			}

			// You cannot lose more than you have
			if($products_sold < 0){$products_sold = 0;}
			// Set the amount of products that are sold
			$this->amount_of_product = $products_sold;

		}else{ // Nothing anymore
			$this->amount_of_product = 0;
		}

		// The profit
		$this->profit = $this->getCurrentMarketPrice() * $this->amount_of_product;

		// Delete the user meta
	    delete_user_meta($this->user,$this->key);
    }

    private function setKey(){

        $this->key = "goc_dealers_".$this->location;
    }
}