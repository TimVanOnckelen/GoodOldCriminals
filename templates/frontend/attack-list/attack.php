<div class="notification is-info"><b><?php echo $this->a->result; ?></div>
<div class="columns">
	<div class="column">
		<h2><?php echo $this->a->winner->getName(); ?></h2>
		<img src="<?php echo $this->a->winner->getProfilePic(); ?>" width="100%" />
	</div>

	<div class="column">
		<h2><?php echo $this->a->loser->getName(); ?></h2>
		<img src="<?php echo $this->a->loser->getProfilePic(); ?>" width="100%" />
	</div>

</div>
