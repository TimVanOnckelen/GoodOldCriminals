<form action="<?php echo(admin_url('admin-post.php')); ?>" method="POST">
	<input type="hidden" name="action" value="<?php echo($this->hook_login); ?>">
	<input type="hidden" name="_wp_http_referer" value="<?php echo(urlencode($_SERVER['REQUEST_URI'])); ?>">
	<label for="username"><?php echo __('Username or email','xe_goc'); ?></label>
	<input type="text" name="username" id="username" />
	<label for="password"><?php echo __('Password','xe_goc'); ?></label>
	<input type="password" name="password" id="password" />
	<input type="submit" value="<?php echo __('Login','xe_goc'); ?>" />
</form>