<p>
	<label for="<?php echo $this->getMetaKey("country_wealth"); ?>">
		<?php echo __('Country wealth (higher is better)','xe_goc'); ?>
	</label>
	<input type="number" value="<?php echo get_post_meta($post_ID,$this->getMetaKey("country_wealth"),true) ?>" name="<?php echo $this->getMetaKey("country_wealth"); ?>" />
</p>
<p>
	<label for="<?php echo $this->getMetaKey("location_x"); ?>">
		<?php echo __('Location x coordinate','xe_goc'); ?>
	</label>
	<input type="number" value="<?php echo get_post_meta($post_ID,$this->getMetaKey("location_x"),true) ?>" name="<?php echo $this->getMetaKey("location_x"); ?>" />
</p>
<p>
	<label for="<?php echo $this->getMetaKey("location_y"); ?>">
		<?php echo __('Location y coordinate','xe_goc'); ?>
	</label>
	<input type="number" value="<?php echo get_post_meta($post_ID,$this->getMetaKey("location_y"),true) ?>" name="<?php echo $this->getMetaKey("location_y"); ?>" />
</p>
<p>
	<label for="<?php echo $this->getMetaKey("map_id"); ?>">
		<?php echo __('Map id','xe_goc'); ?>
	</label>
	<input type="number" value="<?php echo get_post_meta($post_ID,$this->getMetaKey("map_id"),true) ?>" name="<?php echo $this->getMetaKey("map_id"); ?>" />
</p>