<form action="<?php echo(admin_url('admin-post.php')); ?>" method="POST">
	<input type="hidden" name="action" value="<?php echo($this->hook_bankTransfer); ?>">
	<input type="hidden" name="_wp_http_referer" value="<?php echo(urlencode($_SERVER['REQUEST_URI'])); ?>">
	<p>
		Cash: &euro; <?php echo $this->user->getCash(); ?> -
		Bank: &euro; <?php echo $this->user->getBank(); ?>
	</p>
	<h2><?php echo __('Geld overzetten','xe_goc');?></h2>
	<p>
		<label for="bank_amount">Bedrag</label>
		<input type="number" value="0" name="bank_amount" />
	</p>
	<input type="submit" value="<?php echo __('Bank naar cash','xe_goc'); ?>" name="banktocash" />
	<input type="submit" value="<?php echo __('Cash naar bank','xe_goc'); ?>" name="cashtobank" />
</form>