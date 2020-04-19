<h3>Missie rapport</h3>
<p>Jouw missie met <?php echo $this->other_user->getName(); ?> is afgelopen.</p>
<p><b>Je hebt hierbij <?php echo $this->mission->getExp(); ?> ervaring opgedaan.</b></p>
<p>De totale opbrengst van de missie is &euro; <?php echo $this->mission->getProfit(); ?>. Jij hebt het volledige bedrag ontvangen.
	Volgens de gemaakte afspraken heeft <?php echo $this->other_user->getName(); ?> op <?php echo $this->mission->getPercentage(); ?> %  van de winst. Contacteer <?php echo $this->other_user->getName(); ?> om verdere afspraken te maken.</p>
