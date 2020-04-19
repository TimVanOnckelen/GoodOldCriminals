<div class="columns">
    <div class="column is-one-third">
        <p>
            Je kan dealers op pad sturen om je drugs te verkopen.<br />
            Na één uur keren ze terug om je het geld te overhandigen.<br />
            Elke dealer kost je <span>€ <?php echo $this->dg->getDealerRate(); ?> </span> en is te betalen bij de start.<br />
            Een dealer kan maximum <?php echo $this->dg->getMaxProductPerDealer(); ?> stuks meenemen.
        </p>

        <div>
            <h3><?php _e("Jouw voorraad","xe_goc"); ?></h3>
			<?php
			// Get the backpack
			$backpack = new \Xe_GOC\Inc\Models\Frontend\Backpack(get_current_user_id(),$this->company->getId());
			$products = $backpack->getProducts();
			if(count($products)> 0){
				foreach($products as $p){
					echo "<b>".$p->product->getName().": </b> ".$p->amount." Dealers: ".ceil($p->amount/$this->dg->getMaxProductPerDealer()).'<br />';
				}
			}else{
				_e("Je hebt nog geen voorraad van deze producten");
			}

			?>
        </div>
    </div>
    <div class="column">
        <p>
            Er zijn momenteel <?php echo $this->dg->getAvailableDealers(); ?> dealers beschikbaar in <?php echo $this->location->getName(); ?>.
        </p>
        <form action="<?php echo(admin_url('admin-post.php')); ?>" method="POST">
            <label for="product_id">
                <?php _e("Product om te verkopen","xe_goc"); ?>
            </label>
            <select name="product_id">
			<?php
			foreach ($this->company->getProducts() as $p){
			    if($products[$p->getId()]->amount > 0) {
				    ?>
                    <option value="<?php echo $p->getId(); ?>"><?php echo $p->getName(); ?></option>
				    <?php
			    }
			}
			?>
            </select>
            <label for="price"><?php _e("Aantal stuk van het product","xe_goc"); ?></label>
            <input type="text" name="amount_product" value="1" />
            <label for="price"><?php _e("Aantal dealers","xe_goc"); ?></label>
            <input type="text" name="dealers" value="1" />
            <input type="hidden" name="action" value="<?php echo($this->hook); ?>">
            <input type="hidden" name="_wp_http_referer" value="<?php echo(urlencode($_SERVER['REQUEST_URI'])); ?>">
            <input type="submit" value="<?php _e("Stuur dealers op pad","xe_goc"); ?>" />
        </form>
    </div>
</div>