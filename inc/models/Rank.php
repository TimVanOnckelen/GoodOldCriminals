<?php


namespace Xe_GOC\Inc\Models;


use Xe_GOC\Inc\Lib\TemplateEngine;

class Rank {

	private static $ranks = array("Landloper","Krantenjongen","Ronselaar","Oplichter","Dealer","Gangster","Bodyguard","Huurmoordenaar","2de Officier","1ste Officieer","Organisatie manager","Tactisch Manager","Maffiabaas","Godfather");
	private static $ranking_exp = array(0,10000,30000,80000,150000,250000,300000,500000,800000,1000000,1500000,2500000,5000000,20000000);


	/**
	 * Get rank based on exp
	 * @param $exp
	 *
	 * @return string
	 */
	public static function getRank($exp){

		return self::$ranks[self::getClosest($exp)];

	}

	/**
	 * Check if user has the right rank
	 * @param $exp
	 * @param $rank_need
	 *
	 * @return bool
	 */
	public static function checkRank($exp,$rank_need){
		$currentrank = self::getClosest($exp);

		if($currentrank >= $rank_need){
			return true;
		}

		return self::$ranks[$rank_need];
	}

	/**
	 * @param $exp
	 *
	 * @return float|int
	 */
	public static function progressToRank($exp){

		$currentrank = self::getClosest($exp);
		$next_rank = $currentrank + 1;

		$percentage = round(($exp/self::$ranking_exp[$next_rank]) * 100,2);

		return $percentage;

	}

	/**
	 * Get closest rank key
	 * @param $exp
	 *
	 * @return int|string|null
	 */
	private static function getClosest($exp){

		$closest = null;
		foreach (self::$ranking_exp as $key => $item) {

			$prev_key = $key - 1;

			if($prev_key < 0){
				$prev_key = 0;
			}

			if (abs($exp) < abs($item) && abs($exp) >= self::$ranks[$prev_key] ) { // If it's higher, then ranking is higher
				$closest = $prev_key;
				break;
			}
		}
		return $closest;

	}

}