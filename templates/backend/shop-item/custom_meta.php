<p>
	<label for="<?php echo $this->getMetaKey("attack"); ?>">
		<?php echo __('Attack power','xe_goc'); ?>
	</label>
	<input type="number" value="<?php echo get_post_meta($post_ID,$this->getMetaKey("attack"),true) ?>" name="<?php echo $this->getMetaKey("attack"); ?>" />
</p>
<p>
	<label for="<?php echo $this->getMetaKey("defence"); ?>">
		<?php echo __('Defence power','xe_goc'); ?>
	</label>
	<input type="number" value="<?php echo get_post_meta($post_ID,$this->getMetaKey("defence"),true) ?>" name="<?php echo $this->getMetaKey("defence"); ?>" />
</p>
<p>
	<label for="<?php echo $this->getMetaKey("price"); ?>">
		<?php echo __('Price','xe_goc'); ?>
	</label>
	<input type="number" value="<?php echo get_post_meta($post_ID,$this->getMetaKey("price"),true) ?>" name="<?php echo $this->getMetaKey("price"); ?>" />
</p>
<p>
	<label for="<?php echo $this->getMetaKey("max"); ?>">
		<?php echo __('Max owned','xe_goc'); ?>
	</label>
	<input type="number" value="<?php echo get_post_meta($post_ID,$this->getMetaKey("max"),true) ?>" name="<?php echo $this->getMetaKey("max"); ?>" />
</p>