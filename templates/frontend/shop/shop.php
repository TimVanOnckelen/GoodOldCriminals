<div class="columns is-multiline">
<?php

/*
    $shopItem = new \Xe_GOC\inc\frontend\shopItem(24);
    echo $shopItem->buy(1);
*/


// Foreach shop item
foreach($this->items as $i){

	// Load the shop item
	$shop_i = new \Xe_GOC\Inc\Models\frontend\shopItem($i->ID);

	require (XE_GOC_PLUGIN_PATH.'/templates/frontend/shop/shop-item.php');

}
?>
</div>
