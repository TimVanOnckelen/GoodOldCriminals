<?php
/**
 * Created by PhpStorm.
 * User: Tim Van Onckelen
 * Date: 7/04/2020
 * Time: 21:05
 */

namespace Xe_GOC\Inc\Controllers\Frontend;


use Xe_GOC\Inc\Lib\PageHandling;
use Xe_GOC\Inc\Lib\TemplateEngine;
use Xe_GOC\Inc\Models\Frontend\BankTransaction;
use Xe_GOC\Inc\Models\Frontend\CarInGarage;
use Xe_GOC\Inc\Models\Frontend\CriminalUser;
use Xe_GOC\Inc\Models\Frontend\DealersGoOut;
use Xe_GOC\Inc\Models\Frontend\Message;
use Xe_GOC\Inc\Models\Frontend\xpTransaction;
use Xe_GOC\Inc\Models\Rank;

class DuoCrime
{

    public $create_key = "goc_create_duo_crime";
    public $mission_key = "goc_duo_crime";
    public $join_key = "goc_duo_crime";
    public $table_duocrime = XE_GOC_TABLE_DUOCRIME;

    public function __construct()
    {
        add_shortcode("goc_duocrime",array($this,"showTemplate"));
        // Run the goc duo crime on cron run
        // add_action("goc_cron_run_duo_crime",array($this,"doCrime"));

	    add_action("admin_post_{$this->create_key}",array($this,"createNewCrime"));
	    add_action("admin_post_{$this->join_key}",array($this,"joinCrime"));

	    // Crons every 5 minutes
	    add_action("goc_5min_crons",array($this,"doCrime"));
    }

    /**
     * @return false|int
     */
    public function showTemplate(){

        $c = new CriminalUser();
        $currentMission = get_user_meta($c->getId(),$this->mission_key,true);

        if(Rank::checkRank($c->getXp(),4) !== true){
			PageHandling::showMessage(vsprintf(__("Je kan pas meedoen met een duo crime vanaf dat je %s bent. Nog even hard doorranken!","xe_goc"),array(Rank::checkRank($c->getXp(),4))));
        	return;
        }

        // IS current user on a mission?
        if(isset($currentMission) && $currentMission > 0 && isset($_GET["action"]) != 'cancel'){
            // user is on a mission
	        $mission = new \Xe_GOC\Inc\Models\Frontend\DuoCrime($currentMission);

	        if($mission->getStatus() == false){ // Mission started, but no partner yet

	        	$t = new TemplateEngine();
	        	$t->message = vsprintf(__("Je hebt een missie gestart. Wacht tot een partner deelneemt aan de missie. Wil je de missie annuleren? Klik dan %s."),array('<a href="?action=cancel">'.__('hier').'</a>'));
	        	echo $t->display("frontend/messages/info.php");

	        }else { // Mission is busy
		        // Load the mission time
		        $this->loadTime( $mission->getMissionTime() );
		        return;
	        }
        }else{
            if(isset($_GET["action"])){
                // Not on a mission. :)
                switch($_GET["action"]){
                    // Join
                    case 'join':
						$this->templateJoin();
                        break;
                    case 'create':
						$this->templateCreate();
                        break;

	                case 'cancel':
	                	$this->cancelEvent($currentMission);
	                	break;
                    // No action
                    default:
                        $this->templateOverview();
                        break;
                }
            }else{
                // Normal template
                $this->templateOverview();
            }
        }

    }

    /**
     * @return array|null|object
     */
    public function getMissions(){
        global $wpdb;
        $db_table = "goc_duocrime";

        $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}{$db_table} WHERE status = false ",OBJECT);
        return $results;
    }

	/**
	 * Template main
	 */
    public function templateOverview(){
        // Maybe result?
        // Results in inbox?
        $t = new TemplateEngine();
        $t->missions = $this->getMissions();
        echo $t->display("frontend/duocrime/main.php");
    }

	/**
	 * Template main
	 */
	public function templateJoin(){
		// Maybe result?
		// Results in inbox?

		if(!is_numeric($_GET["id"]) OR $_GET["id"] <= 0){
			echo PageHandling::returnMessage(__("Je kan deze missie niet joinen.","xe_goc"));
			return;
		}

		$mission = new \Xe_GOC\Inc\Models\Frontend\DuoCrime($_GET["id"]);

		if($mission->getOwner() > 0 && $mission->getStatus() == false && $mission->getOwner() != get_current_user_id()){
			$t = new TemplateEngine();
			$t->mission = $mission;
			$t->owner = new \Xe_GOC\Inc\Models\Frontend\CriminalUser($mission->getOwner());
			$t->location = new \Xe_GOC\Inc\Models\Frontend\location($mission->getLocation());
			$t->hook = $this->join_key;
			$t->cars = CarInGarage::getCarsFromUsers(get_current_user_id());
			echo $t->display("frontend/duocrime/join.php");
		}else{
			echo PageHandling::returnMessage(__("Je kan deze missie niet joinen.","xe_goc"));
		}

	}

	/**
	 * Template create
	 */
    public function templateCreate(){
	    $t = new TemplateEngine();
	    $t->hook = $this->create_key;
	    $t->min_budget = \Xe_GOC\Inc\Models\Frontend\DuoCrime::$min_budget;
	    echo $t->display("frontend/duocrime/create.php");
    }

    public function createNewCrime(){

    	$current_crime = get_user_meta(get_current_user_id(),$this->mission_key,true);

    	// Already on a mission
    	if($current_crime > 0){
    		PageHandling::forwardBack(__("Je bent al op missie. Je kan geen nieuwe missie starten.","xe_goc"));
    		return;
	    }

    	// Mission budget not enough
    	if(!isset($_POST["mission_budget"]) OR $_POST["mission_budget"] < \Xe_GOC\Inc\Models\Frontend\DuoCrime::$min_budget){
		    PageHandling::forwardBack(__("Je moet moet minsten &euro; missie budget gebruiken.","xe_goc"));
		    return;
	    }

	    // Mission percentage not enough
	    if(!isset($_POST["percentage_partner"]) OR ($_POST["percentage_partner"] > 100 && $_POST["percentage_partner"] < 0)){
		    PageHandling::forwardBack(__("Je moet een percentage geven aan je partner.","xe_goc"));
		    return;
	    }

	    // Do bank transaction
	    $bt = new BankTransaction(get_current_user_id(),false,$_POST["mission_budget"],"buy","cash");

	    // Check banktransaction
	    if($bt->doTransaction() !== true){
		    PageHandling::forwardBack(__("Je hebt niet genoeg cash om de missie te starten.","xe_goc"));
		    return;
	    }

	    // Setup mission
	    $c = new CriminalUser();
	    $mission = new \Xe_GOC\Inc\Models\Frontend\DuoCrime();
	    $mission->setLocation($c->getLocation());
	    $mission->setOwner($c->getId());
	    $mission->setMissionBudget($_POST["mission_budget"]);
	    $mission->setPercentage($_POST["percentage_partner"]);
	    $mission_id = $mission->saveCrime();

	    // Set user in mission
	    update_user_meta($c->getId(),$this->mission_key,$mission_id);
	    PageHandling::forwardBack(__("De missie is gestart.","xe_goc"));

    }

    // Run the duo crime
    public function doCrime(){

	    global $wpdb;

	    $results = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}{$this->table_duocrime} WHERE end_date < NOW() AND status = 1 AND payout = 0",OBJECT);

	    if(is_array($results) && count($results) > 0){
	    	foreach ($results as $m_id){

	    		// Do the crime :)
	    		$m = new \Xe_GOC\Inc\Models\Frontend\DuoCrime($m_id->id);
	    		$m->doCrime();

	    		// Set mission profit to owner of the mission
			    $bt = new BankTransaction(null,$m->getOwner(),$m->getProfit(),"sell","cash");
			    $bt->doTransaction();

			    // Update xp
			    $xp_t = new xpTransaction($m->getExp(),"add","duocrime",$m->getOwner());
			    $xp_t->doTransaction();
			    $xp_t_partner = new xpTransaction($m->getExp(),"add","duocrime",$m->getPartner());
			    $xp_t_partner->doTransaction();

			    // Delete mission from the owner & partner
			    delete_user_meta($m->getOwner(),$this->mission_key);
			    delete_user_meta($m->getPartner(),$this->mission_key);


			    // Payout done, now send message to user :)

			    // Send message to the owner with the result
			    $message_owner = new Message();
			    $message_owner->setReceiver($m->getOwner());
			    $message_owner->setSender(0); // 0 is system message
			    $t = new TemplateEngine();
			    $t->mission = $m;
			    $t->other_user = new CriminalUser($m->getPartner());
			    $message = $t->display("frontend/messages/mission/owner_mission_report.php");
			    $message_owner->setMessage($message);
			    $message_owner->saveMessage();

			    // Send message to partner
			    $message_partner = new Message();
			    $message_partner->setReceiver($m->getPartner());
			    $message_partner->setSender(0); // 0 is system message
			    $t = new TemplateEngine();
			    $t->mission = $m;
			    $t->other_user = new CriminalUser($m->getOwner());
			    $message = $t->display("frontend/messages/mission/partner_mission_report.php");
			    $message_partner->setMessage($message);
			    $message_partner->saveMessage();


		    }
	    }

    }

    public function cancelEvent($currentMission){

    	$mission = new \Xe_GOC\Inc\Models\Frontend\DuoCrime($currentMission);

    	if($mission->getOwner() == get_current_user_id() && $currentMission > 0) {
		    $mission->cancel();
		    // Update mission key
		    delete_user_meta( get_current_user_id(), $this->mission_key );

		    $bt = new BankTransaction(get_current_user_id(),false,$mission->getMissionBudget(),"sell","cash");
		    $bt->doTransaction();

		    PageHandling::forwardBack(__("De missie is geannuleerd. Het missiebudget is teruggestort naar je cash.","xe_goc"));
	    }else{
    		PageHandling::forwardBack(__("Je bent geen eigenaar van deze missie. Je kan deze niet annuleren"));
	    }

    }

    public function joinCrime(){

    	if(!isset($_POST["id"]) OR $_POST["id"] <= 0){
		    PageHandling::forwardBack(__("Join niet mogelijk. Probeer het opnieuw"));
	    }

    	// Set mission
    	$mission = new \Xe_GOC\Inc\Models\Frontend\DuoCrime($_POST["id"]);

    	// Cannot join mission without owner
    	if(!$mission->getOwner() > 0 OR $mission->getOwner() == get_current_user_id()){
		    PageHandling::forwardBack(__("Joinen niet mogelijk. Probeer het opnieuw"));
	    }

    	// Mission already started
    	if($mission->getStatus() == true){
		    PageHandling::forwardBack(__("De missie is al gestart. Je kan niet meer joinnen. Iemand was je voor."));
	    }

    	$garage = new CarInGarage();
    	$garage->setTransactionId($_POST["car"]);
    	$garage->setOwnerId(get_current_user_id());
    	$c = new CriminalUser();

    	// User wrong location
    	if($c->getLocation() != $mission->getLocation()){
		    PageHandling::forwardBack(__("Je bevindt je niet op de juiste locatie om de missie te starten."));
	    }

    	// Car not owned
    	if(!$garage->isCarOfOwner() === true){
		    PageHandling::forwardBack(__("De auto die je hebt geselecteerd is niet van jou."));
	    }

    	// Car wrong location
    	if($garage->getLocation() != $mission->getLocation()){
		    PageHandling::forwardBack(__("De auto bevind zich niet op de juiste locatie."));

	    }

    	// Start the mission
    	$mission->setPartner(get_current_user_id());
    	$mission->setCar($_POST["car"]);
    	$mission->saveCrime();

    	// Car needs to be disabled

	    // Set user in a mission
	    update_user_meta($c->getId(),$this->mission_key,$_POST["id"]);

	    PageHandling::forwardBack(__("De missie is gestart."));

    }


	public function loadTime($time){

		// Dealers are not back yet
		PageHandling::timerMessage($time,__("Je ben op missie. Deze zal eindigen binnen %s."));

	}
}