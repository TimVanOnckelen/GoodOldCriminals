<p>
	<label for="<?php echo $this->getMetaKey("money"); ?>">
		<?php echo __('Max amount to win','xe_goc'); ?>
	</label>
	<input type="number" value="<?php echo get_post_meta($post_ID,$this->getMetaKey("money"),true) ?>" name="<?php echo $this->getMetaKey("money"); ?>" />
</p>
<p>
    <label for="<?php echo $this->getMetaKey("failed"); ?>">
		<?php echo __('Failed text','xe_goc'); ?>
    </label>
    <input type="text" value="<?php echo get_post_meta($post_ID,$this->getMetaKey("failed"),true) ?>" name="<?php echo $this->getMetaKey("failed"); ?>" />
</p>
<p>
	<label for="<?php echo $this->getMetaKey("success"); ?>">
		<?php echo __('Success text','xe_goc'); ?>
	</label>
	<input type="text" value="<?php echo get_post_meta($post_ID,$this->getMetaKey("success"),true) ?>" name="<?php echo $this->getMetaKey("success"); ?>" />
</p>
<p>
	<label for="<?php echo $this->getMetaKey("chance"); ?>">
		<?php echo __('Chance (%)','xe_goc'); ?>
	</label>
	<input type="text" value="<?php echo get_post_meta($post_ID,$this->getMetaKey("chance"),true) ?>" name="<?php echo $this->getMetaKey("chance"); ?>" />
</p>

<p>
    <label for="<?php echo $this->getMetaKey("timetowait"); ?>">
		<?php echo __('Time to wait (in seconds)','xe_goc'); ?>
    </label>
    <input type="text" value="<?php echo get_post_meta($post_ID,$this->getMetaKey("timetowait"),true) ?>" name="<?php echo $this->getMetaKey("timetowait"); ?>" />
</p>
<p>
    <label for="<?php echo $this->getMetaKey("carcrime"); ?>">
		<?php echo __('Car steal crime?','xe_goc'); ?>
    </label>
    <select name="<?php echo $this->getMetaKey("carcrime"); ?>">
	    <?php $carcrime = get_post_meta($post_ID,$this->getMetaKey("carcrime"),true); ?>
	    <option value="0" <?php selected(0,$carcrime); ?>><?php echo __('No','xe_goc'); ?></option>
	    <option value="1" <?php selected(1,$carcrime); ?>><?php echo __('Yes','xe_goc'); ?></option>
    </select>
</p>