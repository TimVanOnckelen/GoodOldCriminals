<form action="<?php echo(admin_url('admin-post.php')); ?>" method="post">
	<input type="hidden" name="_wp_http_referer" value="<?php echo(urlencode($_SERVER['REQUEST_URI'])); ?>">
	<fieldset id="regular-crime" class="thecrime">
        <div class="columns">
		<?php
		foreach($this->crimes as $key => $c){
			?>
			<div class="column">
				<label class="radio radio-label" for="crime-<?php echo $key; ?>">
				<input type="radio" class="option-input" name="crime" id="crime-<?php echo $key; ?>" value="<?php echo $c["id"]; ?>">
					<div class="inside">
                    <span></span>
                    <b> <?php echo $c["chance"]; ?>%</b>
                    <p><?php echo $c["name"]; ?></p>
					</div>
				</label><br />

			</div>
			<?php
		}
		?>
        </div>
	</fieldset>
    <div class="control">
		<br />
		<input type="hidden" value="<?php echo  $this->crimeId; ?>" name="crimeId">
		<input type="hidden" name="action" value="<?php echo($this->hook_post); ?>">
		<input type="submit" value="<?php echo __('Voer uit','xe_goc'); ?>" />
	</div>
</form>
<style type="text/css">
    fieldset {
        border: 0px;
        background-color: rgba(0,0,0,0.2);
        border-radius: 10px;
    }
    .thecrime [type="radio"] {
        width: 20px;
        height: 20px;
        z-index: -1;
        visibility: hidden;
    }

    .thecrime [type="radio"] ~ div{
	    padding: 10px;
	    height: 150px;
	    vertical-align: top;
	    transition: all 0.5s;
	    border-radius: 10px;
    }

    .thecrime [type="radio"] ~ div span {
        display: block;
        width: 20px;
        height: 20px;
        padding: 1px;
        background-color: white;
        border-radius: 5px;
        border: 5px solid #fff;
        margin: 5px;
        /* margin-left: -20px; */
        margin: auto;
        margin-bottom: 10px;
	    transition: all 1s;
    }
    .thecrime [type="radio"]:checked ~ div span {
	    font-weight: 900;
	    background-color: #6d7879;
    }
    .thecrime [type="radio"]:checked ~ div.inside {
	    background-color: #3BBC4C;
    }
    .thecrime{
        text-align: center;
    }
   .thecrime label {
       text-align: center;
       line-height: 25px;
   }
	.thecrime label:hover {
		color: #fff;
	}
    .thecrime label:hover div {
	    background-color: #6d7879;
    }
</style>