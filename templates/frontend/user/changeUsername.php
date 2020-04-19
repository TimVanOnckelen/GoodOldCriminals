<form action="<?php echo(admin_url('admin-post.php')); ?>" method="POST">
	<input type="hidden" name="action" value="<?php echo($this->hook); ?>">
	<input type="hidden" name="_wp_http_referer" value="<?php echo(urlencode($_SERVER['REQUEST_URI'])); ?>">
	<label for="username"><?php echo __('Kies een gebruikersnaam','xe_goc'); ?></label>
	<input type="text" name="username" id="username" />
	<input type="submit" value="<?php echo __('Go','xe_goc'); ?>" />
</form>