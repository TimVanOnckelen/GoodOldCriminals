<div class="columns">
    <div class="column is-one-third">
        <p>Je joint te missie van <?php echo $this->owner->getName();?><br />
        Je kan alleen auto's selecteren die zich in je garage in <?php echo $this->location->getName(); ?> en 0% schade hebben</p>
        <p>Als je auto niet volledig kapot is na de missie, staat hij terug in je garage. Een snelle auto zorgt ervoor dat de missie tijd aanzienlijk verkort wordt.</p>
    </div>
    <div class="column">
        <h3>Missie joinen van <?php echo $this->owner->getName(); ?>.</h3>
        <form action="<?php echo(admin_url('admin-post.php')); ?>" method="post">
            <input type="hidden" name="_wp_http_referer" value="<?php echo(urlencode($_SERVER['REQUEST_URI'])); ?>">
            <label for="car">Missie auto</label>
            <select name="car">
                <?php
                if(count($this->cars) > 0){
	                foreach ($this->cars as $car){
	                    // Only cars in same locations
	                    if($car->location == $this->mission->getLocation() && $car->damage == 0) {
		                    echo '<option value="' . $car->id . '">' . $car->car_name . ' - '.$car->speed.' km/h</option>';
	                    }
	                }
                }
                ?>
            </select>

            <div class="control">
                <br />
                <input type="hidden" name="id" value="<?php echo($this->mission->getId()); ?>">
                <input type="hidden" name="action" value="<?php echo($this->hook); ?>">
                <input type="submit" value="<?php echo __('Doe mee','xe_goc'); ?>" />
            </div>
        </form>
    </div>
</div>