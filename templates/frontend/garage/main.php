<table class="table is-striped">
	<thead>
		<tr>
			<td><?php _e("Naam","xe_goc"); ?></td>
			<td><?php _e("Waarde","xe_goc"); ?></td>
			<td><?php _e("Schade","xe_goc"); ?></td>
			<td><?php _e("Locatie","xe_goc"); ?></td>
			<td><?php _e("Actie","xe_goc"); ?></td>
		</tr>
	</thead>
	<tbody>
	<?php
	if(count($this->cars) > 0){
		foreach ($this->cars as $car){

			?>
			<tr>
				<td><?php echo $car->car_name; ?></td>
				<td>&euro; <?php echo $car->value; ?></td>
				<td><?php echo $car->damage; ?></td>
				<td>
					<?php
					$l = new \Xe_GOC\inc\models\frontend\location($car->location);
					echo $l->getName();
					?>
				</td>
				<td>
					<?php
					$cu = new \Xe_GOC\Inc\Models\Frontend\CriminalUser();
					if($cu->getLocation() == $car->location) {
						?>
						<form action="<?php echo( admin_url( 'admin-post.php' ) ); ?>" method="POST">
							<input type="hidden" name="car_id" value="<?php echo( $car->id ); ?>">
							<input type="hidden" name="action" value="<?php echo( $this->hook ); ?>">
							<input type="hidden" name="_wp_http_referer"
							       value="<?php echo( urlencode( $_SERVER['REQUEST_URI'] ) ); ?>">

							<input type="submit" value="<?php echo __( 'Verkoop', 'xe_goc' ); ?>" name="sell"/>
							<?php if ( $car->damage > 0 ) { ?>
								<input type="submit" value="<?php echo __( 'Repareer', 'xe_goc' ); ?>" name="repair"/>
							<?php } ?>
						</form>
						<?php
					}
						?>
				</td>
			</tr>
			<?php

		}
	}
	?>
	</tbody>
</table>
