<p>
	<label for="<?php echo $this->getMetaKey("value"); ?>">
		<?php echo __('Max value of the car','xe_goc'); ?>
	</label>
	<input type="number" value="<?php echo get_post_meta($post_ID,$this->getMetaKey("value"),true) ?>" name="<?php echo $this->getMetaKey("value
	 ); ?>" />
</p>
<p>
	<label for="<?php echo $this->getMetaKey("luck"); ?>">
		<?php echo __('Luck percentage (higher is better change)','xe_goc'); ?>
	</label>
	<input type="text" value="<?php echo get_post_meta($post_ID,$this->getMetaKey("luck"),true) ?>" name="<?php echo $this->getMetaKey("luck"); ?>" />
</p>
<p>
	<label for="<?php echo $this->getMetaKey("damage"); ?>">
		<?php echo __('Max damage','xe_goc'); ?>
	</label>
	<input type="text" value="<?php echo get_post_meta($post_ID,$this->getMetaKey("damage"),true) ?>" name="<?php echo $this->getMetaKey("damage"); ?>" />
</p>