<p>
	<label for="<?php
	echo $this->getMetaKey("products"); ?>">
		<?php echo __('Products','xe_goc'); ?>
	</label>
	<select name="<?php echo $this->getMetaKey("products"); ?>[]" multiple>
		<?php

		// Get all company products
		$products = \Xe_GOC\inc\controllers\backend\CompanyProductPostType::getAllCompanyProducts();
		$current_products = get_post_meta($post_ID,$this->getMetaKey("products"),true);

		// setup array if no value is set
		if(!is_array($current_products)){
			$current_products = array();
		}

		foreach ($products as $p){

			$selected = "";

			// If product is in array
			if(in_array($p->getId(),$current_products)){
				$selected = "SELECTED";
			}

			echo '<option value="'.$p->getId().'" '.$selected.'>'.$p->getName().'</option>';
		}

		?>
	</select>
</p>
<p>
	<label for="<?php echo $this->getMetaKey("price"); ?>">
		<?php echo __('Price','xe_goc'); ?>
	</label>
	<input type="number" value="<?php echo get_post_meta($post_ID,$this->getMetaKey("price"),true) ?>" name="<?php echo $this->getMetaKey("price"); ?>" />
</p>