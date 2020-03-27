<form action="<?php echo(admin_url('admin-post.php')); ?>" method="POST">
	<input type="hidden" name="action" value="<?php echo($this->hook_logout); ?>">
	<input type="hidden" name="_wp_http_referer" value="<?php echo(urlencode($_SERVER['REQUEST_URI'])); ?>">
	<input type="submit" value="<?php echo __('Do you really wan\'t to logout?','xe_goc'); ?>" />
</form>