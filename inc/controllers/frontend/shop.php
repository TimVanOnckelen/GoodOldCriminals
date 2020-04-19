<?php
/**
 * Created by PhpStorm.
 * User: Tim
 * Date: 8/07/2018
 * Time: 22:14
 */

namespace Xe_GOC\Inc\Controllers\Frontend;
use Xe_GOC\Inc\Lib\TemplateEngine;
use Xe_GOC\Inc\Lib\PageHandling;
use Xe_GOC\Inc\Models\Frontend\CriminalUser;
use Xe_GOC\Inc\Models\Frontend\shopItem;


/**
 * Class Shop
 * @package Xe_GOC\inc\frontend
 */
class Shop {

	private $hook_buy_item = 'goc_buy_item';

	/**
	 * Shop constructor.
	 */
	public function __construct() {

		// Load the shop shortcode
		add_shortcode('goc_shop',array($this,'loadTemplate'));

		// Buy an item
		// Load the action
		add_action("admin_post_{$this->hook_buy_item}",array($this,"buyItem"));

	}

	/**
	 * @param $args
	 *
	 * @return string
	 */
	public function loadTemplate($args){

		// Load the template
		$temp = new TemplateEngine();
		// Load vars
		$temp->items = $this->loadShopItems($args["type"]);
		$temp->user = new CriminalUser();
		$temp->hook_buy_item = $this->hook_buy_item;

		return $temp->display('frontend/shop/shop.php');

	}

	/**
	 * Get all items from a given type
	 * @param null $type
	 *
	 * @return array
	 */
	private function loadShopItems($type = null){

		$args = array(
			'post_type' => XE_GOC_POSTYPE_SHOP_ITEM,
			'posts_per_page' => -1,
			'order' => 'ASC',
			'tax_query' => array(
				array(
					'taxonomy' => XE_GOC_POSTYPE_SHOP_ITEM_TYPE,
					'terms' => $type,
					'field' => 'slug',
					'include_children' => true,
					'operator' => 'IN',
				)
			),
			'orderby' => 'meta_value',
			'meta_key' => shopItem::getMetaKey("price")
		);

		$posts_array = query_posts($args);

		return $posts_array;


	}

	/**
	 * Buy an item
	 */
	public function buyItem(){

		if(is_numeric($_POST["xe_goc_shop_item"]) && is_numeric($_POST["xe_goc_amount"])){

			// Buy the item
			$shopItem = new shopItem($_POST["xe_goc_shop_item"]);

			$m = $shopItem->buy($_POST["xe_goc_amount"]);


		}else{ // Not valid

			$m = __('Something went wrong will buying this item. Try again.','xe_goc');

		}

		// Forward back to page
		PageHandling::forwardBack($m);

	}
}