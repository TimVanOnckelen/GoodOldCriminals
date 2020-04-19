<div class="columns">
    <div class="column is-one-fifth">
        <table class="table is-striped">
            <tbody>
            <tr>
                <td><i class="fas fa-money-bill-wave"></i> Cash</td>
                <td>&euro; <?php echo $this->user->getCash(); ?> </td>
            </tr>
            <tr>
                <td><i class="fas fa-piggy-bank"></i> Bank</td>
                <td>&euro; <?php echo $this->user->getBank(); ?> </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="column">
        <form action="<?php echo(admin_url('admin-post.php')); ?>" method="POST">
            <input type="hidden" name="action" value="<?php echo($this->hook_bankTransfer); ?>">
            <input type="hidden" name="_wp_http_referer" value="<?php echo(urlencode($_SERVER['REQUEST_URI'])); ?>">
            <h2><?php echo __('Geld overzetten','xe_goc');?></h2>
            <p>Hier kan je geld uitwisselen tussen je bank & je cash. Als je cash wil storten op de bank, betaal je hiervoor 2% transactiekosten.<br />
                Geld afhalen is gratis.</p>
            <p>
                <label for="bank_amount">Bedrag</label>
                <input type="number" value="0" name="bank_amount" />
            </p>
            <input type="submit" value="<?php echo __('Bank naar cash','xe_goc'); ?>" name="banktocash" />
            <input type="submit" value="<?php echo __('Cash naar bank - 2% transactiekost','xe_goc'); ?>" name="cashtobank" />
        </form>
    </div>
</div>
<div class="columns">
    <div class="column is-one-fifth">
        <table class="table is-striped">
            <tbody>
            <tr>
                <td><b><?php _e("Jouw id","xe_goc"); ?></b></td>
            </tr>
            <tr>
                <td><?php echo $this->user->getBankId(); ?> </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="column">
        <form action="<?php echo(admin_url('admin-post.php')); ?>" method="POST">
            <input type="hidden" name="action" value="<?php echo($this->hook_bankTransfer); ?>">
            <input type="hidden" name="_wp_http_referer" value="<?php echo(urlencode($_SERVER['REQUEST_URI'])); ?>">
            <h2><?php echo __('Storting','xe_goc');?></h2>
            <p>Wil je geld storten op de rekening van een andere crimineel? Vraag zijn bankrekening id en stort geld van jouw bankrekening naar de rekening van de andere crimineel.<br />
            Je betaalt hier 3% transactiekosten op.</p>
            <p>
                <label for="bank_id">Rekening id</label>
                <input type="text" placeholder="Type hier de rekeningnummer van de crimineel aan wie je wil storten." name="bank_id" />
            </p>
            <p>
                <label for="bank_amount">Bedrag</label>
                <input type="number" value="0" name="bank_amount" />
            </p>
            <input type="submit" value="<?php echo __('Storten','xe_goc'); ?>" name="transfer" />
        </form>
    </div>

</div>
