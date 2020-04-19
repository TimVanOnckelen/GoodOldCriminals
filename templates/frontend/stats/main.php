<?php
use Xe_GOC\Inc\Models\Frontend\DealersGoOut;
?>
<div class="columns">
    <div class="column">
        <div class="columns is-multiline">
            <div class="column">
                <b><?php echo $this->user->getUserinfo()->display_name; ?></b> - <?php echo \Xe_GOC\Inc\Models\Rank::getRank($this->user->getXp()); ?>
                <br />
                <i class="fas fa-location-arrow"></i>
				<?php
				$l = new \Xe_GOC\Inc\Models\Frontend\location($this->user->getLocation());
				echo $l->getName();
				?>
                <i class="fas fa-money-bill-wave"></i>
                &euro; <?php echo \Xe_GOC\Inc\Lib\Formats::cf($this->user->getCash()); ?>
                -
                <i class="fas fa-piggy-bank"></i>
                &euro; <?php echo \Xe_GOC\Inc\Lib\Formats::cf($this->user->getBank()); ?>
            </div>
        </div>
    </div>
</div>