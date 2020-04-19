<?php


namespace Xe_GOC\Inc\Models\Frontend;


class xpTransaction {

	private $user = 0;
	private $amount = 0;
	private $type = "add";
	private $origin;
	private $table = "goc_xp";
	private $transactionId = 0;

	public function __construct($amount,$type,$origin,$user_id=0) {

		if($user_id > 0){
			$this->user = $user_id;
		}else{
			$this->user = get_current_user_id();
		}

		$this->amount = $amount;
		$this->type = $type;
		$this->origin = $origin;

	}

	public function setTransactionId($id){
		$this->transactionId = $id;
	}

	public function doTransaction(){

		switch ($this->type){

			case "add";
				return $this->add();
			break;

			case "delete";
				return $this->delete();
			break;

		}

	}

	/**
	 * Add a transaction
	 * @return array|object|null
	 */
	private function add(){

		global $wpdb;

		// Get the defence from the db
		$select = "INSERT INTO {$wpdb->prefix}{$this->table} (user_id,amount,origin) VALUES ('{$this->user}','{$this->amount}','{$this->origin}')";

		// Get the result
		return $wpdb->get_results($select);

	}

	/**
	 * Delete the xp based on id of transaction
	 * @return bool|false|int
	 */
	private function delete(){

		global $wpdb;

		if($this->transactionId > 0) {
			// Get the defence from the db
			return $wpdb->delete( $wpdb->prefix . $this->table, array( "id" => $this->transactionId ) );
		}
		return false;

	}
}