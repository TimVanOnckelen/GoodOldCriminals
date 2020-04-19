<div class="columns">
    <div class="column is-one-third">
        <p>Ga samen met een andere crimineel op duo missie. Je kan zelf een missie starten of meedoen aan een missie van een andere crimineel.</p>
        <p><b>De organisator</b> voorziet het missiebudget. Hoe hoger het budget, hoe meer slaagkansen.</p>
        <p><b>De partner</b> voorziet een wagen. Van zodra er twee criminele deelnemen start de missie.</p>
        <p>De organisator is verantwoordelijk voor de correcte uitbetaling van de partner na afloop van de missie.</p>
        <a href="?action=create"><button>Organiseer een missie</button></a>
    </div>
    <div class="column">
        <h3>Beschikbare missies</h3>
        <table class="table">
            <thead>
                <tr><th>Organisator</th><th>Locatie</th><th>Missie budget</th><th>Acties</th></tr>
            </thead>
            <tbody>
            <?php
            if(count($this->missions) > 0){
                foreach ($this->missions as $m){
                    $c = new \Xe_GOC\Inc\Models\Frontend\CriminalUser($m->owner);
                    $l = new \Xe_GOC\inc\models\frontend\location($m->location);
                    ?>
            <tr>
                <td><?php echo $c->getName(); ?></td>
                <td><?php echo $l->getName(); ?></td>
                <td><?php echo $m->missionBudget; ?></td>
                <td><a href="?action=join&id=<?php echo $m->id; ?>" style="color:#3BBC4C;">Join</a></td>
            </tr>
            <?php
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>