<h3>Missie rapport</h3>
<p>De missie van <?php echo $this->other_user->getName(); ?> waar jij aan mee deed is afgelopen.</p>
<p><b>Je hebt hierbij <?php echo $this->mission->getExp(); ?> ervaring opgedaan.</b></p>
<p>De totale opbrengst van de missie is &euro; <?php echo $this->mission->getProfit(); ?>. <?php echo $this->other_user->getName(); ?>  heeft het volledige bedrag ontvangen.
	Volgens de gemaakte afspraken heb je recht op <?php echo $this->mission->getPercentage(); ?> % van het totaal budget. Contacteer <?php echo $this->other_user->getName(); ?> om jouw deel te krijgen.</p>
<p>De auto die je gebruikte voor deze missie heeft een schade van <?php echo $this->mission->getCarDamage(); ?>%. Je kan deze terug vinden in je garage.</p>
<p>Betaalt <?php echo $this->other_user->getName(); ?> je niet uit? Overweeg dan om andere stappen te nemen...</p>