<div class="columns">
	<div class="column is-one-third">
		<p>
			In het drugslab kan je rechstreeks drugs aankopen.<br />
			Deze drugs kan je laten verkopen door je dealers, of smokkelen naar een andere land om het daar duurder te verkopen.<br />
			De prijs is afhankelijk van het land en de lokale marktprijs en veranderd geregeld. Rondreizen voor de beste prijs, is dus de beste optie.
		</p>

		<div>
			<h3><?php _e("Jouw voorraad","xe_goc"); ?></h3>
			<?php
			// Get the backpack
				$backpack = new \Xe_GOC\Inc\Models\Frontend\Backpack(get_current_user_id(),$this->company->getId());
				$products = $backpack->getProducts();
				if(count($products)> 0){
					foreach($products as $p){
						echo "<b>".$p->product->getName().": </b> ".$p->amount."<br />";
					}
				}else{
					_e("Je hebt nog geen voorraad van deze producten");
				}

			?>
		</div>
	</div>
	<div class="column">
		<p>
			<i class="fas fa-home"></i> Eigenaar: <?php echo $this->company->ownerName(); ?>
		</p>
		<form action="<?php echo(admin_url('admin-post.php')); ?>" method="POST">
            <?php
                foreach ($this->company->getProducts() as $p){
                	?>

	                <label for="<?php echo $p->getId(); ?>"><?php echo $p->getName(). ' '.$p->getAmount(). ' - &euro; '.$p->getValue(); ?> - In stock: <?php echo $this->company->getProductStock($p->getId())?></label>
	                <input type="number" name="products[<?php echo $p->getId(); ?>]" value="0">

					<?php
                }
            ?>
			<input type="hidden" name="company_id" value="<?php echo $this->company->getId(); ?>" />
			<input type="hidden" name="action" value="<?php echo($this->hook); ?>">
			<input type="hidden" name="_wp_http_referer" value="<?php echo(urlencode($_SERVER['REQUEST_URI'])); ?>">
			<input type="submit" value="<?php _e("Koop","xe_goc"); ?>" name="buy"/>
            <input type="submit" value="<?php _e("Verkoop","xe_goc"); ?>" name="sell"/>
        </form>
	</div>
</div>