<?php


namespace Xe_GOC\Inc\Models\Frontend;


use mysql_xdevapi\Exception;
use Xe_GOC\Inc\Lib\security;

class Message {

	private $key;
	private $message;
	private $id;
	private $sender;
	private $receiver;
	private $nonce;
	private $date;
	private $table = "goc_messages";

	public function __construct($id = 0,$getFromDb=true) {

		$this->id = $id;

		// Get message from db
		if($this->id > 0 && $getFromDb == true){
			$this->messageFromDb();
		}

		$this->key = base64_decode(XE_GOC_MESS_KEY);

	}

	public function setMessage($m,$encrypt = true){
		$this->message = $m;
		if($encrypt == true) {
			$this->encrypt();
		}
	}

	public function setSender($s){
		$this->sender = $s;
	}

	public function setReceiver($r){
		$this->receiver = $r;
	}

	/**
	 * @param mixed $date
	 */
	public function setDate( $date ) {
		$this->date = $date;
	}

	public function getReceiver(){
		return $this->receiver;
	}

	public function getSender(){
		return $this->sender;
	}

	/**
	 * @param mixed $nonce
	 */
	public function setNonce( $nonce ) {
		$this->nonce = $nonce;
	}

	/**
	 * @return mixed
	 */
	public function getDate() {
		return $this->date;
	}

	public function getMessage(){
		$this->decrypt();
		return $this->message;
	}

	private function messageFromDb(){
		global $wpdb;

		$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}{$this->table} WHERE id='".$this->id."' LIMIT 1",OBJECT);

		if(is_array($results)){
			$this->message = $results[0]->message;
			$this->nonce = $results[0]->n;
			$this->receiver = $results[0]->receiver;
			$this->sender = $results[0]->sender;
			$this->date = $results[0]->date;
			$this->nonce = $results[0]->nonce;
		}

	}

	/**
	 * Save the new message
	 */
	public function saveMessage(){
		global $wpdb;

		$conversation_id =  security::encrypt($this->sender + $this->receiver);
		return $wpdb->insert($wpdb->prefix.$this->table,array("n" => $this->nonce, "conversation_id" => $conversation_id,"sender" => $this->sender,"receiver" => $this->receiver,"message" => $this->message));

	}

	/**
	 * Encrypt the message
	 * @throws \Exception
	 */
	private function encrypt(){

		$nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
		$this->message = sodium_crypto_secretbox($this->message, $nonce, $this->key);
		$this->message = base64_encode($this->message);
		$this->nonce = base64_encode($nonce);
	}

	/**
	 * Decypt the message
	 */
	private function decrypt(){

		$decoded = base64_decode($this->message);
		$nonce = base64_decode($this->nonce);

		$decrypted = sodium_crypto_secretbox_open($decoded, $nonce, $this->key);

		if($decrypted == false){
			$this->message = "Je kan dit bericht niet lezen.";
		}else{
			$this->message = $decrypted;
		}


	}


}