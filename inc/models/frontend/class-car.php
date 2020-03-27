<?php
/**
 * Created by PhpStorm.
 * User: Tim
 * Date: 27/07/2018
 * Time: 22:49
 */

namespace Xe_GOC\inc\models\frontend;


class Car {


	public $result = "";
	private $post_type = XE_GOC_POSTYPE_CARS_TYPE;
	private $luck;
	private $car = null;

	private $meta_key_crime = "goc_car_data";

	// Needle of the shop item
	public static $save_needle = "goc_crime";
	// Meta keys of a crime item
	private static $meta_keys = array("value" => "value","damage" => "damage","luck" => "luck");

	/**
	 * Crime constructor.
	 *
	 * @param int $crime
	 */
	function __construct($luck) {

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
				'relation' => 'AND',
				array(
					'key'     => self::getMetaKey("luck"),
					'value'   => $this->luck,
					'compare' => '>',
				),
			)
		));

		$amountOfCars = count($cars);

		// Are there cars in this category?
		if($amountOfCars > 0){

			$carselecter = rand(0,$amountOfCars);

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

	public function save(){

	}

}