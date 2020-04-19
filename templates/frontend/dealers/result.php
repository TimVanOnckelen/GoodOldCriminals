<h2>Omzet drugsverkoop dealers</h2>
<p>Je dealers zijn klaar en kwamen opdagen met het volgende resultaat.<br />
De producten die niet verkocht zijn, werden terug aangevuld in de voorraad. De winst werd cash uitbetaald.</p>
<div class="columns">
	<div class="column">
		<h3><i class="fas fa-money-bill"></i></h3>
		<h4>Omzet drugsverkoop</h4>
		<h5>&euro; <?php echo $this->dg->getProfit(); ?></h5>
	</div>
	<div class="column">
		<h3><i class="fas fa-check-circle"></i></h3>
		<h4>Producten verkocht</h4>
		<h5><?php echo $this->dg->getAmountOfProduct(); ?></h5>
	</div>
	<div class="column">
		<h3 style="color: red;"><i class="fas fa-ban"></i></h3>
		<h4 style="color: red;">Producten niet verkocht</h4>
		<h5 style="color: red;"><?php echo $this->dg->getProductNotSold(); ?></h5>
	</div>
	<div class="column">
		<h3 style="color: red;"><i class="fas fa-ghost"></i></h3>
		<h4 style="color: red;">Producten kwijtgespeeld</h4>
		<h5 style="color: red;"> <?php echo $this->dg->getProductLost(); ?></h5>
	</div>
	<div class="column">
		<h3 style="color: red;"><i class="fas fa-money-bill"></i></h3>
		<h4 style="color: red;">Kosten dealers</h4>
		<h5 style="color: red;"> <?php echo $this->dg->getAmountOfDealers(); ?> dealers * &euro; <?php echo $this->dg->getDealerRate(); ?> = &euro; <?php echo $this->dg->getAmountOfDealers() * $this->dg->getDealerRate(); ?></h5>
	</div>
</div>


