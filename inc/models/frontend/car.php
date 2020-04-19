<?php
/**
 * Created by PhpStorm.
 * User: Tim
 * Date: 27/07/2018
 * Time: 22:49
 */

namespace Xe_GOC\Inc\Models\Frontend;


class Car {


	public $result = "";
	private $post_type = XE_GOC_POSTYPE_CARS_TYPE;
	private $luck;
	private $car = null;

	private $meta_key_crime = "goc_car_data";

	// Needle of the shop item
	public static $save_needle = "goc_crime";
	// Meta keys of a crime item
	private static $meta_keys = array("value" => "value","damage" => "damage","luck" => "luck","max_speed" => "max_speed");

	/**
	 * Crime constructor.
	 *
	 * @param int $crime
	 */
	function __construct() {


	}

	public function doCarCrime($luck){

		$this->luck = $luck;

		$this->car = $this->findACar();

		// No car found, so return false
		if($this->car === false){
			return false;
		}

		// Prepare the care
		$this->prepareCar();

	}

	private function findACar(){

		$cars = get_posts(array(
			"numberposts" => -1,
			"post_type" => $this->post_type,
			'meta_query' => array(
				array(
					'key'     => self::getMetaKey("luck"),
					'value'   => $this->luck,
					'compare' => '<',
				)
			)
		));

		$amountOfCars = count($cars);

		// Are there cars in this category?
		if($amountOfCars > 0){

			$amountOfCars = $amountOfCars - 1;

			$carselecter = round(rand(0,$amountOfCars));

			if(isset($cars[$carselecter])) {
				return $cars[ $carselecter ];
			}

			// No car found
			return false;
		}

		// No car found
		return false;
	}


	/**
	 * Prepare the car data
	 * @return bool
	 */
	private function prepareCar(){

		if($this->car->ID){

			$max_damage = get_post_meta($this->car->ID,self::getMetaKey("damage"),true);
			$max_value = get_post_meta($this->car->ID,self::getMetaKey("value"),true);
			// Calculate damage & value
			$damage = rand(0,$max_damage);
			$value = $max_value/100 * (100-$damage);

			$this->car->damage = $damage;
			$this->car->value = $value;

			$this->save();

			return true;

		}

		return false;

	}

	/**
	 * Return the car object
	 * @return bool|mixed|null
	 */
	public function getCarObject(){
		return $this->car;
	}

	/**
	 * @param $v
	 *
	 * @return string
	 */
	public static function getMetaKey($v){

		return self::$save_needle.self::$meta_keys[$v];

	}

	/**
	 * Save the car to the users garage
	 */
	public function save(){

		$c = new CriminalUser();

		$carInGarage = new CarInGarage();
		$carInGarage->setCarId($this->car->ID);
		$carInGarage->setDamage($this->car->damage);
		$carInGarage->setValue($this->car->value);
		$carInGarage->setLocation($c->getLocation());
		$carInGarage->setOwnerId(get_current_user_id());
		$carInGarage->create();
	}

}