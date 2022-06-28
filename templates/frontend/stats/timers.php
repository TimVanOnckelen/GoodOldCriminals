<?php
use Xe_GOC\Inc\Models\Frontend\DealersGoOut;
?>
<a href="<?php echo home_url('regular-crime')?>">
	<?php echo '<b>'.__("Gemakkelijke misdaad","xe_goc").'</b>'; ?>:
	<?php
	echo do_shortcode("[goc_crime_timer id='4']");
	?>
</a> -
<a href="<?php echo home_url('moeilijke-misdaad')?>">
	<?php echo '<b>'.__("Moeilijke misdaad","xe_goc").'</b>'; ?>:
	<?php
	echo do_shortcode("[goc_crime_timer id='26']");
	?>
</a> -
<a href="<?php echo home_url('auto-stelen')?>">
	<?php echo '<b>'.__("Auto stelen","xe_goc").'</b>'; ?>:
	<?php
	echo do_shortcode("[goc_crime_timer id='27']");
	?>
</a> -
<a href="<?php echo home_url('reizen')?>">
	<?php echo '<b>'.__("Reizen","xe_goc").'</b>'; ?>:
	<?php
	echo do_shortcode("[goc_travel_time]");
	?>
</a> -
<a href="<?php echo home_url('drugsdealers')?>">
	<?php echo '<b>'.__("Drugs verkopen","xe_goc").'</b>'; ?>:
	<?php
	echo do_shortcode("[goc_drugsdealers_time]");
	?>
</a>



<script type="text/javascript">
    (function($) {

        window.onload = function () {

            $(".gocTimer").each(function(index){

                let time = $(this).data("time") - 1; // To fix the interval :)
                let theItem = $(this);

                let currentTimer = setInterval(function () {

                    let hours = 0;
                    let minutes = 0;
                    let seconds = time;
                    if(time > 59){
                        minutes = Math.floor(seconds/60);
                        hours = Math.floor(minutes/60);
                        minutes = minutes - (hours * 60);
                        seconds = time - (60* minutes) - (60 * 60 * hours);
                    }

                    // Set time
                    theItem.html(n(hours)+":"+n(minutes)+":"+n(seconds));

                    if(time > 0) {
                        time--;
                    }else{
                        $(theItem).html("<?php _e("Klaar","xe_goc") ?>");
                        clearInterval(currentTimer);
                    }

                }, 1000);

            });

        };
    })( jQuery );

    function n(n){
        return n > 9 ? "" + n: "0" + n;
    }
</script>

