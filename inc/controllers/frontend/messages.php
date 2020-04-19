<?php


namespace Xe_GOC\Inc\Controllers\Frontend;


use Xe_GOC\Inc\Lib\security;
use Xe_GOC\Inc\Lib\TemplateEngine;
use Xe_GOC\Inc\Models\Frontend\CriminalUser;
use Xe_GOC\Inc\Models\Frontend\Message;

class Messages {

	public $table = XE_GOC_MESSAGES_TABLE;

	public function __construct() {
		// Main view
		add_shortcode("goc_messages",array($this,"messagesTemplate"));

		// Add a message
		add_action( 'rest_api_init', function () {
			register_rest_route( 'goc/v1', 'messages/add', array(
				'methods' => 'POST',
				'callback' => array($this,'sendMessage'),
				'permission_callback' => function () {
					return is_user_logged_in();
				}
			) );
		} );

		// Get messages
		add_action( 'rest_api_init', function () {
			register_rest_route( 'goc/v1', 'messages/get', array(
				'methods' => 'GET',
				'callback' => array($this,'getMessages'),
				'permission_callback' => function () {
					return is_user_logged_in();
				}
			) );
		} );

		add_filter('wp_nav_menu_items', array($this,"addNotification"), 10, 2);

	}

	/**
	 * Get the message template
	 */
	public function messagesTemplate(){

		if(isset($_GET["action"])) {
			switch ( $_GET["action"] ) {

					case "view";
						$this->conversationTemplate();
					break;

				default;
					$this->inboxTemplate();
					break;
			}
		}else{
			$this->inboxTemplate();
		}

	}

	/**
	 * Inbox template
	 */
	public function inboxTemplate(){
		$t = new TemplateEngine();
		$t->conversations = $this->getInbox();
		echo $t->display('frontend/messages/inbox.php');
	}

	public function ConversationTemplate(){
		$t = new TemplateEngine();
		// Get messages
		$t->messages = $this->getConversation($_GET["id"]);
		// Set criminal user
		$t->currentUser = new CriminalUser();
		$t->otherUser = new CriminalUser(security::decrypt($_GET["id"]));

		echo $t->display('frontend/messages/conversation.php');
	}

	/**
	 * Get the inbox names from users who has started conversation
	 * @return array
	 */
	public function getInbox(){
		global $wpdb;

		$user = get_current_user_id();

		return $wpdb->get_results("SELECT conversation_id,receiver,sender,has_read,SUM(if(has_read = 0, 1, 0) AND if(receiver = {$user}, 1, 0)) AS 'amount_unread' FROM {$wpdb->prefix}{$this->table} WHERE (receiver = {$user} OR sender = {$user}) GROUP BY conversation_id ORDER BY 'date'",object);
	}

	public function getUnreadMessages(){

		global $wpdb;

		$user = get_current_user_id();

		return $wpdb->get_results("SELECT SUM(if(has_read = 0, 1, 0)) AS 'amount_unread' FROM {$wpdb->prefix}{$this->table} WHERE receiver = {$user}",object);

	}

	/**
	 * Get all messages from the conversation between current user and given user
	 * @param $other_user
	 *
	 * @return array
	 */
	public function getConversation($other_user,$timestamp = 0){

		global $wpdb;

		$messages        = array();


		$other_user = security::decrypt($other_user);

		if(!is_numeric($other_user)){
			return __("Gebruiker bestaat niet.");
		}


		// Check cookie timestamp
		if(isset($_COOKIE[$other_user."_conversation"])) {
			$last_timestamp = $_COOKIE[$other_user."_conversation"];
		}else{
			$last_timestamp = 0;
		}

		// Only give results if not new timestamp is after previous timestamp
		if($last_timestamp-1 < $timestamp OR $timestamp == 0) {


			// Get current user
			$user            = get_current_user_id();
			$mysql_timestamp = date( 'Y-m-d H:i:s', $timestamp); // Add 2 seconds to prevent save/get errors

			$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}{$this->table} WHERE  date > '{$mysql_timestamp}' AND ((receiver = {$user} OR sender = {$user}) AND (receiver = {$other_user} OR sender= {$other_user})) ORDER BY date ", object );
			$wpdb->query( "UPDATE {$wpdb->prefix}{$this->table} SET has_read = true WHERE (has_read = false AND date > '{$mysql_timestamp}') AND ((receiver = {$user}) AND (sender= {$other_user}))" );

			// Loop over all messages in conversation
			if ( is_array( $results ) && count( $results ) > 0 ) {

				$currentUser = new CriminalUser();
				$otherUser   = new CriminalUser( $other_user );

				// Set read of conversation to current time

				foreach ( $results as $r ) {
					$m = new Message( null, false );
					$m->setMessage( $r->message, false );
					$m->setSender( $r->sender );
					$m->setReceiver( $r->receiver );
					$m->setNonce( $r->n );
					$m->setDate( $r->date );
					// Add to messages array

					// Create json array for vue
					$item = array();

					if ( $m->getSender() == $currentUser->getId() ) {
						$sender = $currentUser->getName();
						$type   = "sender";
					} else {
						$sender = $otherUser->getName();
						$type   = "receiver";
					}

					// Setup item
					$item["type"]    = $type;
					$item["sender"]  = $sender;
					$item["message"] = $m->getMessage( false );
					$item["date"]    = $m->getDate();
					$item["id"] = $r->id;

					// Add to json array
					$messages[] = $item;

				}
			}

		}


		// create a json array for vue

		return json_encode($messages);

	}

	/**
	 * @param $request
	 *
	 * @return \WP_REST_Response
	 */
	public function getMessages($request){

		$data = array();
		$status_code = 201;
		$data["timestamp"] = current_time("timestamp");

		if(is_numeric($request["timestamp"]) && !is_numeric($request["user"])){
			// Return the new messages
			$data["messages"] = $this->getConversation($request["user"],$request["timestamp"]);
			// Save timestamp
			setcookie(security::decrypt($request["user"])."_conversation", $data["timestamp"], time()+2*60);

		}else{
			$status_code = 401;
		}

		$response = new \WP_REST_Response( $data );

		// Add a custom status code
		$response->set_status( $status_code );

		return $response;

	}

	/**
	 * @param $request
	 */
	public function sendMessage($request){

		$data = array();

		$user_id = security::decrypt($request["user"]);

		if(isset($user_id) && $user_id > 0){

			$other_u = new CriminalUser($user_id);

			if($other_u->userExsists() == true){
				// Send new message
				$m = new Message();
				$m->setDate(time());
				$m->setSender(get_current_user_id());
				$m->setReceiver($user_id);
				$m->setMessage($request["message"]);
				$m->saveMessage();
				$data["status"] = true;
			}else{

				$data["status"] = false;
				$data["message"] = __("Je kan geen berichten sturen naar deze gebruiker 1".$other_u->userExsists(),"xe_goc");
			}

		}else{
			$data["message"] = __("Je kan geen berichten sturen naar deze gebruiker 2 ","xe_goc");
			$data["status"] = false;
		}

		$response = new \WP_REST_Response( $data );

// Add a custom status code
		$response->set_status( 201 );

		return $response;

	}

	/**
	 * Add notification to messages
	 * @param $items
	 * @param $args
	 *
	 * @return array|mixed|string
	 */
	public function addNotification($items,$args){


		if($args->menu_id = "menu-1-c4ecd33") {
			$items = explode( "Berichten", $items );

			if ( isset( $items[1] ) ) {
				$items = $items[0] . 'Berichten (<span id="goc_unread_messages">'. $this->getUnreadMessages()[0]->amount_unread .'</span>) ' . $items[1];
			} else {
				$items = $items[0];
			}
		}

		return $items;
	}
}