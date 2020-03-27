<form action="<?php echo(admin_url('admin-post.php')); ?>" method="post">
	<input type="hidden" name="_wp_http_referer" value="<?php echo(urlencode($_SERVER['REQUEST_URI'])); ?>">
	<fieldset id="regular-crime">
		<?php
		foreach($this->crimes as $key => $c){
			?>
			<input type="radio" id="crime-<?php echo $key; ?>" value="<?php echo $c["id"]; ?>" name="crime">
			<label for="crime-<?php echo $c["id"]; ?>"><?php echo $c["name"]; ?> - <?php echo $c["chance"]; ?>%</label><br />
		<?php
		}
		?>
	</fieldset>
    <input type="hidden" value="<?php echo  $this->crimeId; ?>" name="crimeId">
    <input type="hidden" name="action" value="<?php echo($this->hook_post); ?>">
    <input type="submit" value="<?php echo __('Voer uit','xe_goc'); ?>" />
</form>