<table class="table is-striped">
	<thead>
	<tr>
		<th>#</th>
		<th><?php echo __('Speler','xe_goc'); ?></th>
        <th><?php echo __('Rank','xe_goc'); ?></th>
        <th><?php echo __('Cash','xe_goc'); ?></th>
		<th><?php echo __('Locatie','xe_goc'); ?></th>
		<th><?php echo __('Kracht','xe_goc'); ?></th>
		<th><?php echo __('Acties','xe_goc'); ?></th>
	</tr>

	</thead>
	<tbody>
	<?php
	$count = 0;

	foreach ($this->users as $user){

		// The user data
		$u = get_user_by('ID',$user->getId());
		$count++;

		?>

		<tr>
			<td><?php echo $count; ?></td>
            <td>
	            <?php if($user->getId() != get_current_user_id()){ ?>
                <a href="profiel/?id=<?php echo \Xe_GOC\Inc\Lib\security::encrypt($user->getId()); ?>" style="color:#3BBC4C;"><?php echo $u->display_name; ?></a>
                <?php }else{
	                echo $u->display_name;
                } ?>

                </td>
            <td>
                <?php echo \Xe_GOC\Inc\Models\Rank::getRank($user->getXp()); ?>
            </td>
            <td>&euro; <?php echo \Xe_GOC\inc\lib\Formats::cf($user->getCash()); ?></td>
			<td><?php

				// Get the location
				$l = new \Xe_GOC\inc\models\frontend\location($user->getLocation());
				echo  $l->getName();

				?></td>
			<td><?php echo \Xe_GOC\inc\lib\Formats::cf($user->getTotalPower()); ?></td>
            <td><?php if($user->getId() != get_current_user_id()){ ?><a href="<?php echo get_site_url(); ?>/attack?goc-attack=<?php echo \Xe_GOC\Inc\Lib\security::encrypt($user->getID()); ?>" style="color:#3BBC4C;">Aanvallen</a><?php } ?></td>
		</tr>

		<?php
	}

	?>
	</tbody>
</table>
