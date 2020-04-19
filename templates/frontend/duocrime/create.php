<div class="columns">
    <div class="column is-one-third">
        <p>Kies een beschermingsbudget voor de opstart van de missie. Het opstarten van een missie kost je &euro; <?php echo $this->min_budget; ?> aan missiebudget. Je kan dit verhogen om de slaagkansen te verhogen.</p>
        <p>Jij krijgt als organisator de kans om het percentage van de buitverderling te bepalen.</p>
        <p>De missie start zodra een partner intekent op jou missie.</p>
    </div>
    <div class="column">
        <h3>Missie aanmaken.</h3>
        <form action="<?php echo(admin_url('admin-post.php')); ?>" method="post">
            <input type="hidden" name="_wp_http_referer" value="<?php echo(urlencode($_SERVER['REQUEST_URI'])); ?>">
            <label for="mission_budget">Missie budget</label>
            <div class="control has-icons-left">
                <span class="icon is-left">
                    &euro;
                </span>
                <input class="input" type="number" name="mission_budget" value="<?php echo $this->min_budget; ?>" min="<?php echo $this->min_budget; ?>" />
            </div>
            <label for="percentage_partner">Percentage partner</label>
            <div class="control has-icons-right">
                <input class="input" type="number" name="percentage_partner" min="0" max="100" value="50" />
                    <span class="icon is-right">
                     %
                </span>
            </div>
            <div class="control">
                <br />
                <input type="hidden" name="action" value="<?php echo($this->hook); ?>">
                <input type="submit" value="<?php echo __('Doe missie voorstel','xe_goc'); ?>" />
            </div>
        </form>
    </div>
</div>