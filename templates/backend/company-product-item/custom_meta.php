<p>
	<label for="<?php echo $this->getMetaKey("min_value"); ?>">
		<?php echo __('Min value of product','xe_goc'); ?>
	</label>
	<input type="number" value="<?php echo get_post_meta($post_ID,$this->getMetaKey("min_value"),true) ?>" name="<?php echo $this->getMetaKey("min_value"); ?>" />
</p>
<p>
    <label for="<?php echo $this->getMetaKey("amount"); ?>">
		<?php echo __('The amount of product to be sold (e.g. 5gr)','xe_goc'); ?>
    </label>
    <input type="text" value="<?php echo get_post_meta($post_ID,$this->getMetaKey("amount"),true) ?>" name="<?php echo $this->getMetaKey("amount"); ?>" />
</p>