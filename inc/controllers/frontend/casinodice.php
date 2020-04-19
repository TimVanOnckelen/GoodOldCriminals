<?php


namespace Xe_GOC\inc\Controllers\Frontend;


use Xe_GOC\Inc\Lib\PageHandling;
use Xe_GOC\Inc\Lib\TemplateEngine;
use Xe_GOC\Inc\Models\Frontend\BankTransaction;

class casinoDice {

	public function __construct() {

		// Load the bank shortcode
		add_shortcode('goc_casino_dice',array($this,'loadDice'));

		add_action( 'rest_api_init', function () {
			register_rest_route( 'goc/v1', 'casino/dice', array(
				'methods' => 'POST',
				'callback' => array($this,'generateDiceNumbers'),
				'permission_callback' => function () {
					return is_user_logged_in();
				}
			) );
		} );

	}

	public function loadDice(){

		// Load the template
		$temp = new TemplateEngine();
		// Display the template
		return $temp->display('frontend/casino/dice.php');

	}

	public function generateDiceNumbers($request){

		$data = array();
		$message = 'Error';

		if(is_numeric($request["amount"]) && is_numeric($request["dice"])) {

			if($request["dice"] > 1 && $request["dice"] < 13) {

				// check if user has enough money :)
				$t = new BankTransaction(get_current_user_id(), false, $request["amount"], "buy", "cash");
				$transaction = $t->doTransaction();

				if($transaction === true) { // Transactions is done :)
					$data["dice1"]  = round( rand( 1, 6 ) );
					$data["dice2"]  = round( rand( 1, 6 ) );
					$total = $data["dice1"] + $data["dice2"];
					$data["status"] = true;
					$data["total"] = $total;
					$data["requestedDice"] = $request["dice"];

					if(ceil($total) == intval($request["dice"])){ // Check if total dices is same than user input

						$amount_won = $request["amount"] * 2;
						$nt = new BankTransaction(get_current_user_id(),false,$amount_won,"sell","cash");
						$nt->doTransaction();

						$message = __( "Gewonnen! Je krijgt &euro;", "xe_goc" ).' '.$amount_won;

					}else{
						$message = __("Je hebt niets gewonnen, probeer het opnieuw.","xe_goc");
					}


				}else{
					$message = $transaction;
				}

			}else{
				$message = __("Geen geldige invoer. Probeer opnieuw.","xe_goc");
			}

		}else{
			$message = __("Geen geldige invoer. Probeer opnieuw.","xe_goc");
		}

		$data["message"] = PageHandling::returnMessage($message);
		$response = new \WP_REST_Response( $data );

// Add a custom status code
		$response->set_status( 201 );

		return $response;
	}


}