<?php
/**
 * Created by PhpStorm.
 * User: Tim
 * Date: 24/07/2018
 * Time: 20:31
 */

namespace Xe_GOC\Inc\Controllers\Frontend;


use Xe_GOC\Inc\Lib\TemplateEngine;
use Xe_GOC\Inc\Models\Frontend\Attack;
use Xe_GOC\Inc\Models\Frontend\CriminalUser;

class Stats {

	public function __construct() {

		// Load the shop shortcode
		add_shortcode('goc_stats',array($this,'loadTemplate'));
		add_shortcode('goc_stats_timers',array($this,'loadTimersTemplate'));
	}

	public function loadTemplate(){

		// Load the template
		$temp = new TemplateEngine();
		$temp->user = new CriminalUser();
		// Display the template
		echo $temp->display('frontend/stats/main.php');

	}

	public function loadTimersTemplate(){

		// Load the template
		$temp = new TemplateEngine();
		$temp->user = new CriminalUser();
		// Display the template
		echo $temp->display('frontend/stats/timers.php');

	}
}