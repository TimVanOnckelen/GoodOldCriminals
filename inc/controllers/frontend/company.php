<?php


namespace Xe_GOC\Inc\Controllers\Frontend;


use Xe_GOC\Inc\Lib\PageHandling;
use Xe_GOC\Inc\Lib\TemplateEngine;
use Xe_GOC\Inc\Models\Frontend\BackpackTransaction;
use Xe_GOC\Inc\Models\Frontend\BankTransaction;
use Xe_GOC\Inc\Models\Frontend\CompanyProduct;
use Xe_GOC\Inc\Models\Frontend\CriminalUser;

class Company {

    private $buy_hook = "gox_company_buy";
    private $company;

    public function __construct() {

        // Load the bank shortcode
        add_shortcode('goc_company',array($this,'loadCompany'));

        // Buy an item
        // Load the action
        add_action("admin_post_{$this->buy_hook}",array($this,"handleTransaction"));
    }

    /**
     * @param $args
     *
     * @return string
     */
    public function loadCompany($args){

        if(!isset($args["id"])){
            $m = __("Deze fabriek bestaat niet.");
            PageHandling::showMessage($m);
            exit;
        }

        // Load the template
        $temp = new TemplateEngine();

        // Setup crime user
        $c = new CriminalUser();
        // Setup company
        $company = new \Xe_GOC\Inc\Models\Frontend\Company($args['id']);
        $company->setLocation($c->getLocation());

        $temp->company = $company;
        $temp->hook = $this->buy_hook;

        // Display the template
        return $temp->display('frontend/company/main.php');

    }

    public function handleTransaction(){

        $m = "";
        $c = new CriminalUser();

        if(is_numeric($_POST["company_id"])) {

            // Setup company
            $company = new \Xe_GOC\Inc\Models\Frontend\Company($_POST["company_id"]);
            $company->setLocation($c->getLocation());

            if ($company->exists() == true) {


                $total_price = 0;
                $company_products = $company->getProducts();

                // Loop over products
                foreach ($_POST["products"] as $product => $amount){

                    if($amount >! 0){
                        continue;
                    }

                    // If product stock is not valid, set message
                    if($amount > $company->getProductStock($product) && isset($_POST["buy"])){
                        unset($_POST["products"][$product]);
                        $m = __("Sommige producten die je wil kopen zijn niet in stock. Probeer het opnieuw","xe_goc");
                    }

                    if(isset($company_products[$product]) && $amount > 0){
                        // Add to total price
                        $total_price += $company_products[$product]->getValue() * $amount;

                    }

                }

                // Do a buy
                if(isset($_POST["buy"]) && $m == ""){
                    // Try to do a transaction for the bank
                    $b = new BankTransaction(get_current_user_id(),false,$total_price,"buy","cash");
                    if($b->doTransaction() === true){

                        // Loop again over products
                        foreach ($_POST["products"] as $product => $amount) {

                            if($amount >! 0){
                                continue;
                            }

                            // Get product stock
                            $stock = $company->getProductStock($product);
                            // Add the items to the backpack
                            $backpackt = new BackpackTransaction( $company->getId(),$product, get_current_user_id(),$amount, "add" );
                            $backpackt->doTransaction();

                            $new_stock = $stock - $amount;
                            // Update company stock;
                            $company->updateProductStock($product,$new_stock);
                        }

                        $m =  __("Aankoop geslaagd.","xe_goc");

                    }else{ // Not enough cash
                        $m = __("Je hebt niet genoeg cash om deze item(s) te kopen","xe_goc");
                    }

                }

                // Do a sell
                if(isset($_POST["sell"])){

                    // Loop again over products
                    foreach ($_POST["products"] as $product => $amount) {

                        if($amount <= 0){
                            continue;
                        }

                        // Add the items to the backpack
                        $backpackt = new BackpackTransaction( $company->getId(),$product, get_current_user_id(),$amount, "delete" );
                        // Do the transaction
                        if($backpackt->doTransaction() == false){
                            // If some product is not owned, show that its not selled.
                            $m = __("Niet alle producten zijn verkocht. Je kan geen producten verkopen die je niet bezit.", "xe_goc");
                            break;
                        }else{
                            // Do a bank transaction for the given products
                            $total_price = $company_products[$product]->getValue() * $amount;
                            $bt = new BankTransaction(get_current_user_id(),false,$total_price,"sell","cash");
                            $bt->doTransaction();

                            // Update the stock
                            $stock = $company->getProductStock($product);
                            $new_stock = $stock + $amount;
                            // Update company stock;
                            $company->updateProductStock($product,$new_stock);
                        }

                    }

                    // Transaction done succesfully
                    if($m == ""){
                        $m = __("Producten succesvol verkocht","xe_goc");
                    }


                }

            }else{
                $m = __("Dit bedrijf bestaat niet.","xe_goc").' ';
            }

        }else{
            $m = __("Dit bedrijf bestaat niet.","xe_goc").' ';
        }

        // Forward back to page
        PageHandling::forwardBack($m);

    }

}