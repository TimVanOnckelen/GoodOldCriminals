<form action="<?php echo(admin_url('admin-post.php')); ?>" method="POST">
	<input type="hidden" name="action" value="<?php echo($this->hook_buy_item); ?>">
	<input type="hidden" name="_wp_http_referer" value="<?php echo(urlencode($_SERVER['REQUEST_URI'])); ?>">

	<h2><?php echo $i->post_title; ?></h2>

	<p><?php  echo $i->post_content;?></p>

	<p><b><?php echo __('Price','xe_goc'); ?>:</b> &euro; <?php echo $shop_i->getPrice(); ?></p>
	<p><b><?php echo __('Attack','xe_goc'); ?>:</b> <?php echo $shop_i->getAttack(); ?></p>
	<p><b><?php echo __('Defence','xe_goc'); ?>:</b> <?php echo $shop_i->getDefence(); ?></p>
    <p><b><?php echo __('Owned','xe_goc'); ?>:</b> <?php echo $shop_i->userHas(); ?>/<?php echo $shop_i->getMax(); ?></b></p>

	<input type="hidden" name="xe_goc_shop_item" value="<?php echo $i->ID; ?>" />
	<input type="number" name="xe_goc_amount" value="0" />
	<input type="submit" value="<?php echo __('Buy','xe_goc');?>"/>

</form>