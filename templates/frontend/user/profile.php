<div class="columns is-multiline">
    <div class="column"  style="text-align: center">

        <table class="table is-bordered is-striped" >
            <tbody>
            <tr>
                <td style="position: relative;"><h3><?php echo $this->user->getName(); ?></h3>
					<?php echo \Xe_GOC\Inc\Models\Rank::getRank($this->user->getXp()); ?>
                    <div style="position: absolute; right: 10px; top:10px;">
                            <a href="/berichten/?action=view&id=<?php echo \Xe_GOC\Inc\Lib\security::encrypt($this->user->getId());?>"><button><i class="far fa-paper-plane"></i> <?php _e("Stuur bericht","xe_goc") ?></button></a>
                            <a href="/attack?goc-attack=<?php echo \Xe_GOC\Inc\Lib\security::encrypt($this->user->getId());?>"><button><i class="far fa-meh-blank"></i> <?php _e("Aanvallen","xe_goc") ?></button></a>
                    </div>

                    <p>
						<?php
						echo $this->user->getUserMeta("goc_profile_text",true);
						?>
                    </p>
                </td>
                <td><img src="<?php echo $this->user->getProfilePic(); ?>" width="100%" /></td>
            </tr>
            <tr>
                <td>Totaal kracht</td>
                <td><?php echo \Xe_GOC\inc\lib\Formats::cf($this->user->getTotalPower()); ?></td>
            </tr>
            <tr>
                <td>Cash</td>
                <td>&euro; <?php echo \Xe_GOC\inc\lib\Formats::cf($this->user->getCash()); ?></td>
            </tr>
            </tbody>
        </table>

    </div>
</div>
