<?php
/**
 * Created by PhpStorm.
 * User: Tim
 * Date: 8/07/2018
 * Time: 21:36
 */

namespace Xe_GOC\Inc\Controllers\Frontend;

use Xe_GOC\Inc\Lib\security;
use Xe_GOC\Inc\Lib\TemplateEngine;
use Xe_GOC\Inc\Main;
use Xe_GOC\Inc\Models\Frontend\BankTransaction;
use Xe_GOC\Inc\Models\Frontend\CriminalUser;
use Xe_GOC\Inc\Lib\PageHandling;
use function is_numeric;

class Bank {

	private $hook_bankTransfer;

	public function __construct() {

		// Load the bank shortcode
		add_shortcode('goc_user_bank',array($this,'loadBankTemplate'));

		// Set the hook
		$this->hook_bankTransfer = main::$hook_bankTransfer;

		// Load the action
		add_action("admin_post_{$this->hook_bankTransfer}",array($this,"bankTransfer"));

	}

	/**
	 * Load the bank
	 */
	public function loadBankTemplate(){

		// Load the template
		$temp = new TemplateEngine();
		// Add parameters
		$temp->user = new CriminalUser();
		$temp->hook_bankTransfer = $this->hook_bankTransfer;

		// Display the template
		return $temp->display('frontend/bank/bank.php');

	}


	/**
	 * Do a bank transfer
	 */
	public function bankTransfer(){

		// Set the type
		$type = "cash";

		if(isset($_POST["banktocash"])){
			$type = "bank";
		}

		if(isset($_POST["transfer"])){
			$this->doTransferBetweenUsers( $_POST );
			return;
		}

		// Load the transfer
		$transfer = new BankTransaction(get_current_user_id(),false,$_POST["bank_amount"],'transfer',$type);
		// Do the transaction
		$m = $transfer->doTransaction();

		// Forward back to page
		PageHandling::forwardBack($m);
	}

	/**
	 * Do a transfer between users
	 * @param $data
	 */
	public function doTransferBetweenUsers($data){

		if( isset($data["bank_id"]) && is_numeric($data["bank_amount"])){
			$bank_id = explode ("__",security::decrypt($data["bank_id"]));
			$user_id = $bank_id[1];

			$c = new CriminalUser($user_id);

			// Do a transaction
			if($c->userExsists() == true && $user_id > 0){

				// The user exists
				$transfer = new BankTransaction(get_current_user(),$c->getId(),$data["bank_amount"],"usertouser","bank");
				$transfer->setTransactionCost(3);
				$m = $transfer->doTransaction();

			}else{
				$m = __("Er is geen bankrekening gevonden met deze id. De storting werd niet uitgevoerd.");
			}

			PageHandling::forwardBack($m);
		}

	}

}