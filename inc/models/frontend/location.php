<?php


namespace Xe_GOC\Inc\Models\Frontend;


class location {

    private $name;
    private $id;
    private $map_id;
    private $country_wealth = 0;
    private $location_x = 0;
    private $location_y = 0;
    private static $meta_keys = array("country_wealth" => "country_wealth","location_x" => "location_x","location_y" => "location_y","map_id" => "map_id");
    // Needle of the shop item
    public static $save_needle = "goc_location";

    function __construct($location_id)
    {
        $this->id = $location_id;
        $this->loadLocationData();

    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getMapId()
    {
        return $this->map_id;
    }


    /**
     * @return int
     */
    public function getCountryWealth()
    {
        return $this->country_wealth;
    }


    /**
     * @return int
     */
    public function getLocationX()
    {
        return $this->location_x;
    }

    /**
     * @return int
     */
    public function getLocationY()
    {
        return $this->location_y;
    }


	/**
	 * Get the ticket price
	 * @param $otherLocation
	 *
	 * @return float|int|mixed
	 */
    public function getTicketPriceTo($otherLocation){

    	$current_price = 0;

	    if($otherLocation === $this->getId()){
	    	return false;
	    }

    	if(!$otherLocation instanceof location) {
		    $otherLocation = new self( $otherLocation );
		    $otherLocation->loadLocationData();
	    }

	    // No map id? No location
	    if(!$this->getMapId() > 0 OR !$otherLocation->getMapId() > 0){
		    return  false;
	    }


    	// Get the name
        $travel_price_name = "goc_location".$this->getId(). $otherLocation->getId();
		$current_price = get_transient( $travel_price_name );


        // create new day price if there is no price
        if(!$current_price){

        	// Time to expiernce, may be short, may be long
        	$time_exp = rand(30000,86400);

        	// base on travel distance
        	$travel_distance = abs($this->getLocationX() - $otherLocation->getLocationX());

        	if($travel_distance <= 0){
        		$travel_distance = 1;
	        }

        	// Calculate current price based on wealth, travel distance & wealth
	        $current_price = ($this->getCountryWealth() * rand(1,150)) * rand(1,10) * $travel_distance;

	        set_transient( $travel_price_name, $current_price, $time_exp );

        }

        // Return the price
        return $current_price;

    }

    /**
     * Set the location data from the current location
     */
    public function loadLocationData(){
        $this->name = get_the_title($this->id);
        $this->country_wealth = get_post_meta($this->id,self::getMetaKey("country_wealth"), true);
        $this->location_x = get_post_meta($this->id,self::getMetaKey("location_x"), true);
        $this->location_y = get_post_meta($this->id,self::getMetaKey("location_y"), true);
        $this->map_id = get_post_meta($this->id,self::getMetaKey("map_id"), true);
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