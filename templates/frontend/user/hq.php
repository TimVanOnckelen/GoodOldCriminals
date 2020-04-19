<div class="columns is-multiline">
	<div class="column"  style="text-align: center">

        <table class="table is-bordered is-striped" >
            <tbody>
            <tr>
                <td style="position: relative;"><h3><?php echo $this->user->getName(); ?></h3>
                <?php echo \Xe_GOC\Inc\Models\Rank::getRank($this->user->getXp()); ?>
                    <div style="position: absolute; right: 10px; top:10px;">
                        <a href="#" id="trigger-edit-profile-pic"><button><i class="far fa-paper-plane"></i> <?php _e("Profielfoto aanpassen","xe_goc") ?></button></a>
                        <a href="#" id="trigger-edit-profile-text"><button><i class="far fa-meh-blank"></i> <?php _e("Profieltekst aanpassen","xe_goc") ?></button></a>
                    </div>

                    <div id="edit-profile-text" style="display: none">
                        <form action="<?php echo(admin_url('admin-post.php')); ?>" method="POST">
                            <textarea maxlength="1000" name="profile-text" id="profile-text"><?php echo $this->user->getUserMeta("goc_profile_text",true); ?></textarea>
                            <input type="hidden" name="action" value="<?php echo $this->hook_save_text; ?>" />
                            <input type="hidden" name="_wp_http_referer" value="<?php echo(urlencode($_SERVER['REQUEST_URI'])); ?>">
                            <input type="submit" value="Opslaan">
                        </form>
                    </div>
                    <p id="profile-text">
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
                <td>Attack</td>
                <td><?php echo \Xe_GOC\inc\lib\Formats::cf($this->user->getAttack()); ?></td>
            </tr>
            <tr>
                <td>Defence</td>
                <td><?php echo \Xe_GOC\inc\lib\Formats::cf($this->user->getDefence()); ?></td>
            </tr>
            </tbody>
            <tr>
                <td>Totaal vermogen</td>
                <td>&euro; <?php echo \Xe_GOC\inc\lib\Formats::cf($this->user->getCash()+$this->user->getBank()); ?></td>
            </tr>
            <tr>
                <td>Cash</td>
                <td>&euro; <?php echo \Xe_GOC\inc\lib\Formats::cf($this->user->getCash()); ?></td>
            </tr>
            <tr>
                <td>Bank</td>
                <td>&euro; <?php echo \Xe_GOC\inc\lib\Formats::cf($this->user->getBank()); ?></td>
            </tr>
            <tr>
                <td>Ervaring</td>
                <td><?php echo $this->user->getXp(); ?> xp</td>
            </tr>
            <tr>
                <td>Rangvordering</td>
                <td><span class="rank_vordering" style="width: <?php echo \Xe_GOC\Inc\Models\Rank::progressToRank($this->user->getXp()); ?>%;"><?php echo \Xe_GOC\Inc\Models\Rank::progressToRank($this->user->getXp()); ?> %</span></td>
            </tr>
            <tr>
                <td>Misdaad pogingen</td>
                <td><?php echo $this->user->getUserMeta('25_goc_crime_data',true)["trys"]; ?></td>
            </tr>
            <tr>
                <td>Moeilijke misdaad pogingen</td>
                <td><?php echo $this->user->getUserMeta('26_goc_crime_data',true)["trys"]; ?></td>
            </tr>
            <tr>
                <td>Auto steel pogingen</td>
                <td><?php echo $this->user->getUserMeta('27_goc_crime_data',true)["trys"]; ?></td>
            </tr>
            </tbody>
        </table>

	</div>
</div>
<div>
    <h3>Aanval geschiedenis</h3>
    <p>Een overzicht van de laatste 20 criminele die jouw aanvielen.</p>
    <table class="table is-striped is-bordered">
        <thead>
        <tr>
            <th>Tijdstip</th>
            <th>Aanvaller</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $logs = $this->user->getAttackLogs();

            if(is_array($logs) && count($logs) > 0){
                foreach ($logs as $l){
                    $attacker = new \Xe_GOC\Inc\Models\Frontend\CriminalUser($l->attacker);
                    ?>
                <tr>
                    <td><?php echo $l->time; ?></td>
                    <td><a href="profiel/?id=<?php echo \Xe_GOC\Inc\Lib\security::encrypt($attacker->getId()); ?>" style="color:#3BBC4C;"><?php echo $attacker->getName(); ?></a></td>
                </tr>
        <?
                }
            }
        ?>
        </tbody>
    </table>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.20.0/ui/trumbowyg.min.css" integrity="sha256-B6yHPOeGR8Rklb92mcZU698ZT4LZUw/hTpD/U87aBPc=" crossorigin="anonymous" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.20.0/trumbowyg.min.js" integrity="sha256-oFd4Jr73mXNrGLxprpchHuhdcfcO+jCXc2kCzMTyh6A=" crossorigin="anonymous"></script>
<style>
    .rank_vordering {
        display: block;
        margin: 0px;
        background-color: #3BBC4C;
        width: 100%;
        text-align: center;
    }
</style>
<script>
    (function($) {
    $('#profile-text').trumbowyg({
        btns: [
            ['viewHTML'],
            ['undo', 'redo'], // Only supported in Blink browsers
            ['formatting'],
            ['strong', 'em', 'del'],
            ['superscript', 'subscript'],
            ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
            ['unorderedList', 'orderedList'],
            ['horizontalRule'],
            ['removeformat']],
            autogrow: true
    });

    $("#trigger-edit-profile-text").on("click",function(){
       $("#edit-profile-text").show();
       $(this+",#profile-text").hide();
    });

    })( jQuery );
</script>