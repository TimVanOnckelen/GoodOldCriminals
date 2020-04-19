<p>
    <select class="select2 input is-large">
    </select>
</p>
<?php
if(is_array($this->conversations) && count($this->conversations)>0){
	foreach ($this->conversations as $c){


		$conv_with = $c->sender;
		if($conv_with == get_current_user_id()){ // If sender is current user, get receiver as partner
			$conv_with = $c->receiver;
		}

		$conv_id = $conv_with;

		$cu = new \Xe_GOC\Inc\Models\Frontend\CriminalUser($conv_with);
		$conv_with = $cu->getName();
		$profile_pic = $cu->getProfilePic();


		?>
        <a href="?action=view&id=<?php echo \Xe_GOC\Inc\Lib\security::encrypt($conv_id); ?>" class="goc_message">
            <div class="columns">
                <div class="column is-one-fifth" style="text-align: center"><img src="<?php echo $profile_pic; ?>" width="50x;"/></div>
                <div class="column"><?php echo $conv_with?></div>
                <div class="column is-one-fifth">
					<?php if($c->amount_unread > 0){ ?>
                        <span class="inbox_alert"><?php echo $c->amount_unread; ?></span>
					<? } ?>
                </div>
            </div>
        </a>

		<?php

	}
}
?>
<style>
    .goc_message {
        display: block;
        height: 80px;
        border: 1px solid #6d7879;
        margin-bottom: 5px;
        padding: 10px;
    }
    .goc_message .inbox_alert{
        background-color: #3BBC4C;
        color: #fff;
        border-radius: 10px;
        padding: 5px;
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script type="text/javascript">
    (function($) {
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: 'Zoek een gebruiker om een gesprek te starten...'
            });
        });
    })( jQuery );
</script>
