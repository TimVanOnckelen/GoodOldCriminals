<?php
/**
 * Created by PhpStorm.
 * User: Tim
 * Date: 7/07/2018
 * Time: 19:20
 */

namespace Xe_GOC\Inc\Models\frontend;


class BankTransaction {

	private $transaction_id;
	private $amount = 0;
	private $action = "buy";
	private $sender = 0;
	private $receiver = 0;
	private $type = "cash";
	private $db_table_bank = "goc_bank_transactions";

	public function __construct($sender,$receiver=false,$amount,$action=false,$type="cash") {

		// Load the sender as a crime user
		$this->sender = new CriminalUser($sender);
		// Load the receiver as a crime user
		$this->receiver = new CriminalUser($receiver);
		$this->amount = $amount;
		$this->action = $action;
		$this->type = $type;

	}

	/**
	 * Do a transaction
	 * @return string
	 */
	public function doTransaction(){

		switch ($this->action){
			case "buy";

			return $this->buy();

			break;

			case "sell";

			return $this->sell();

			break;

			case "transfer";

			return $this->transfer();

			break;

			case "usertouser";

			return $this->userToUser();

			break;

			default;
			// Nothing?
			return __('Something went wrong with the transaction. Try again. #1','xe_goc');

			break;
		}

	}

	/**
	 * A Buy transaction
	 * @return bool|string|void
	 */
	private function buy(){

		if($this->amount > 0){

			// User has enough money to do transaction
			if($this->canUserDoIt()){

				// Do the transation
				$this->sender->setMoneyAccount($this->amount,$this->type,"remove");
				$this->logTransaction();

				return true;

			}

			// Not enough cash
			return __('Your '.$this->type.' balance is to low','xe_goc');

		}

		return __('Something went wrong with the transaction. Try again. #2','xe_goc');

	}

	/**
	 * A Sell transaction
	 * @return string
	 */
	private function sell(){

		// Check if amount if bigger than zero
		if($this->amount > 0){

				// Do the transation
				$this->sender->setMoneyAccount($this->amount,$this->type,"add");
				$this->logTransaction();

				return __('Transaction succesfull','xe_goc');

		}

		return __('Something went wrong with the transaction. Try again.','xe_goc');

	}

	/**
	 * Transfer Money
	 * @return string
	 */
	private function transfer(){

		if($this->amount > 0){

			if($this->canUserDoIt()){

				// Where to transfer to
				$transfer_to = "bank";

				if($this->type == "bank"){
					$transfer_to = "cash";
				}
				// Remove from first account
				$this->sender->setMoneyAccount($this->amount,$this->type,"remove");
				// Add to next account
				$this->sender->setMoneyAccount($this->amount,$transfer_to,"add");

				return __('Transfer succesfull!','xe_goc');

			}

			// Not enough cash
			return __('Your '.$this->type.' balance is to low','xe_goc');

		}

		return __('Something went wrong with the transaction. Try again.','xe_goc');

	}

	/**
	 * Transfer from user to user
	 * @return string|void
	 */
	private function userToUser(){

		if($this->amount > 0){

			if($this->canUserDoIt()){

				// Remove from first account
				$this->sender->setMoneyAccount($this->amount,$this->type,"remove");
				// Add to next account
				$this->receiver->setMoneyAccount($this->amount,$this->type,"add");

				return __('Transfer succesfull!','xe_goc');

			}

			// Not enough cash
			return __('Your '.$this->type.' balance is to low','xe_goc');

		}

		return __('Something went wrong with the transaction. Try again.','xe_goc');

	}

	/**
	 * Check if the user has enough funds
	 * @return bool
	 */
	private function canUserDoIt(){

		// Get the user amount
		$amount = $this->getAmountFromUserType();

		// Check if user has enough funds
		if($amount >= $this->amount){
			return true;
		}

		return false;

	}

	/**
	 * Get the right amount of the sender by the given type
	 * @return int
	 */
	private function getAmountFromUserType(){

		$sender_u = $this->sender;

		switch ($this->type){

			case "cash";

				// get the cash of the user
				return $sender_u->getCash();

			break;

			case "bank";

				// Get the bank from the user
				return $sender_u->getBank();

			break;

		}

	}

	private function logTransaction(){
		// Silence is golden
	}
}