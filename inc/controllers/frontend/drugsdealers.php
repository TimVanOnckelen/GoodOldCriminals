<?php


namespace Xe_GOC\Inc\Controllers\Frontend;


use Xe_GOC\Inc\Lib\PageHandling;
use Xe_GOC\Inc\Lib\TemplateEngine;
use Xe_GOC\Inc\Models\Frontend\Backpack;
use Xe_GOC\Inc\Models\Frontend\BackpackTransaction;
use Xe_GOC\Inc\Models\Frontend\BankTransaction;
use Xe_GOC\Inc\Models\Frontend\CriminalUser;
use Xe_GOC\Inc\Models\Frontend\DealersGoOut;
use Xe_GOC\Inc\Models\Frontend\xpTransaction;
use Xe_GOC\Inc\Models\Rank;

class Drugsdealers {

	private $hook = "goc_drugsdealers_action";

	function __construct() {

		// Load the bank shortcode
		add_shortcode('goc_drugsdealers',array($this,'loadDrugsdealers'));

		// Load the action
		add_action("admin_post_{$this->hook}",array($this,"doDrugsdealers"));

		add_shortcode('goc_drugsdealers_time',array($this,'loadTime'));

	}

	/**
	 * @return string
	 */
	public function loadDrugsdealers(){

		$c = new CriminalUser();

		if(Rank::checkRank($c->getXp(),2) !== true){
			PageHandling::showMessage(vsprintf(__("Je kan drugs laten verkopen door dealers vanaf dat je %s bent. Nog even hard doorranken!","xe_goc"),array(Rank::checkRank($c->getXp(),2))));
			return;
		}

		$dg = new DealersGoOut($c->getId(),$c->getLocation());

		// Dealers are not going out yet
		if($dg->getStatus() == false) {
			// Load the template
			$temp          = new TemplateEngine();
			$temp->hook    = $this->hook;
			$temp->location = new \Xe_GOC\Inc\Models\Frontend\location($c->getLocation());
			$temp->dg      = $dg;
			$temp->company = new \Xe_GOC\Inc\Models\Frontend\Company( 3784 ); // Set company to drug lab :)
			// Display the template
			return $temp->display( 'frontend/dealers/main.php' );
		}else{

			if($dg->currentProcess() == false){
			    // Dealers are not back yet
				PageHandling::timerMessage($dg->getTime(),__("Je dealers zijn pas terug binnen %s."));
			}else{

				// Do some transactions
				// Update dealers
				$dealers = $dg->getAvailableDealers() + $dg->getAmountOfDealers();
				$dg->updateAvailableDealers( $dealers );

				// Do a bank transaction
				$b = new BankTransaction($c->getId(),false,$dg->getProfit(),"sell","cash");
				$b->doTransaction();

				// Add the drugs again
				$bp = new BackpackTransaction(0,$dg->getProductId(),$c->getId(),$dg->getProductNotSold(),"update");
				$bp->doTransaction();

				// Add xp
				$xp = rand(500,1000);
				$xp_t = new xpTransaction($xp,"add","drugdealers");
				$xp_t->doTransaction();

				// Dealing is done, show the result. :)
				$temp = new TemplateEngine();
				$temp->dg = $dg;
				return $temp->display( 'frontend/dealers/result.php' );
			}

		}

	}



	public function doDrugsdealers(){

		$m = "";

		if(!isset($_POST["product_id"]) OR !is_numeric($_POST["product_id"])){
			$m = __("Product bestaat niet.","xe_goc");
		}elseif(!isset($_POST["amount_product"]) OR !is_numeric($_POST["amount_product"])){
			$m = __("Je met een aantal opgeven.","xe_goc");
		}elseif(!isset($_POST["dealers"]) OR !is_numeric($_POST["dealers"]) OR $_POST["dealers"] <= 0){
			$m = __("Geen dealers opgegeven.","xe_goc");
		}else{

			$c = new CriminalUser();
			// Start a dealers object
			$dg = new DealersGoOut($c->getId(),$c->getLocation(),$_POST["product_id"],$_POST["dealers"],$_POST["amount_product"]);

			// Check available dealers
			if($dg->getAvailableDealers() >= $_POST["dealers"]){
				// Get the user backpack
				$bp = new Backpack($c->getId());
				// Has user enough of product?
				if($bp->amountOfProduct($_POST["product_id"]) >= $_POST["amount_product"]){

					$max_amount = $_POST["dealers"] * $dg->getMaxProductPerDealer();

					if($_POST["amount_product"] <= $max_amount) { // Cannot take more than dealers can carry

						// Total price of the dealers
						$price_of_dealers = $_POST["dealers"] * $dg->getDealerRate();
						// New back transaction
						$bt = new BankTransaction( $c->getId(), false, $price_of_dealers, "buy", "cash" );
						if ( $bt->doTransaction() === true ) {
							// Payment done, start sending dealers
							$dg->startDealing();
							// Update dealers
							$dealers = $dg->getAvailableDealers() - $_POST["dealers"];
							// Remove product from backp
							$bp_t = new BackpackTransaction( 0, $_POST["product_id"], $c->getId(), $_POST["amount_product"], "delete" );
							$bp_t->doTransaction();
							// Upgrade the stock of dealers
							$dg->updateAvailableDealers( $dealers );
							$m = __( "Je dealers zijn op pad gestuurd. Binnen een uur zijn ze terug met de omzet", "xe_goc" );
						} else { // Not enough cash
							$m = __( "Je hebt niet genoeg cash om zoveel dealers op pad te sturen.", "xe_goc" );
						}

					}else{
						$m = vsprintf(__("Elke dealer kan slechts %s stuk meenemen.","xe_goc"),array($dg->getMaxProductPerDealer()));
					}

				}else{ // Not enough product
					$m = __("Je hebt niet genoeg van het gekozen product om deze te verkopen door dealers.","xe_goc");
				}

			}else{
				$m = __("Zoveel dealers zijn er niet beschikbaar","xe_goc");
			}

		}

		// Forward back to page
		PageHandling::forwardBack($m);
	}

	/**
	 * Load the time for drug dealing
	 */
	public function loadTime(){
		$c = new CriminalUser();
		$dg = new DealersGoOut($c->getId(),$c->getLocation());

		if($dg->getStatus() === false){
			echo __("Klaar","xe_goc");
		}else{
			$t = new TemplateEngine();
			$t->time = $dg->getTime();
			echo $t->display('frontend/crime/timer.php');
		}
	}

}