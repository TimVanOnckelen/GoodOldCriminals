<?php
/**
 * Created by PhpStorm.
 * User: Tim
 * Date: 27/07/2018
 * Time: 22:02
 */

namespace Xe_GOC\Inc\Lib;


class Formats {

	/**
	 * Show number in currency format
	 * @param $num
	 *
	 * @return float|string
	 */
	static public function cf($num) {

		if($num>1000) {

			$x = round($num);
			$x_number_format = number_format($x);
			$x_array = explode(',', $x_number_format);
			$x_parts = array('k', 'm', 'b', 't');
			$x_count_parts = count($x_array) - 1;
			$x_display = $x;
			$x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
			$x_display .= $x_parts[$x_count_parts - 1];

			return $x_display;

		}

		return $num;

	}

}