<table>
	<thead>
	<tr>
		<th>#</th>
		<th><?php echo __('User','xe_goc'); ?></th>
        <th><?php echo __('Cash','xe_goc'); ?></th>
		<th><?php echo __('Total power','xe_goc'); ?></th>
		<th><?php echo __('Actions','xe_goc'); ?></th>
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
			<td><?php echo $u->display_name; ?></td>
            <td>&euro; <?php echo \Xe_GOC\inc\lib\Formats::cf($user->getCash()); ?></td>
			<td><?php echo \Xe_GOC\inc\lib\Formats::cf($user->getTotalPower()); ?></td>
            <td><?php if($user->getId() != get_current_user_id()){ ?><a href="<?php echo get_site_url(); ?>/attack?goc-attack=<?php echo $user->getID(); ?>">Attack</a><?php } ?></td>
		</tr>

		<?php
	}

	?>
	</tbody>
</table>
