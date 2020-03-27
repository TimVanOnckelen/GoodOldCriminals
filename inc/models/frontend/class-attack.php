<?php
/**
 * Created by PhpStorm.
 * User: Tim
 * Date: 24/07/2018
 * Time: 20:31
 */

namespace Xe_GOC\Inc\Models\Frontend;
use Xe_GOC\Inc\Models\frontend\BankTransaction;
use Xe_GOC\Inc\Models\Frontend\CriminalUser;

class Attack {

	// Attacker data
	private $attacker;
	private $att_multi;
	public $attacker_power;

	// Defender data
	private $defender;
	private $def_multi;
	public $defender_power;
	// Luck factors
	private $luck;
	private $min;

	// Results
	public $winner;
	public $loser;
	public $amount_won;
	public $transaction_result;
	public $defender_got_away = false;

	/**
	 * Max percentage of cash to win
	 * @var int
	 */
	private $maxPercentageWin = 30;
	/**
	 * Time between attack on same user
	 * @var int
	 */
	private $age_between_attacks = 120;
	/**
	 * Max attack a day
	 * @var int
	 */
	private $max_attacks_a_day = 10;
	/**
	 * DB table attack logs
	 * @var string
	 */
	private $db_table_attack_log = 'goc_attack_log';
	/**
	 * Result after attack
	 * @var string
	 */
	public $result = 'Something got wrong, you where prob to slow.';

	/**
	 * Attack constructor.
	 *
	 * @param $defender
	 */
	public function __construct($defender) {

		// Set the defender
		$this->defender = $defender;

	}

	/**
	 * DO the attack
	 * @return bool
	 */
	public function doAttack(){

			// set the defender and attacker
			$this->defender = new CriminalUser($this->defender);
			$this->attacker = new CriminalUser();

			// Cannot attack yourself
			if($this->defender->getId() == $this->attacker->getId()) {
				$this->result = __( 'You cannot attack yourself', 'xe-goc' );
				return false;
			}

			// If user does not exsits, stop attack
			if($this->defender->userExsists() === false) {
				$this->result = __( 'The user you want to attack does not exsist.', 'xe-goc' );
				return false;
			}

			// Check if the user can attack
			if(!$this->canAttack() === true){
				return false;
			}

			// Calculate the attack
			$this->calculateAttack();

			// Calculate profit
			$this->calculateMoneyWin();

			// Do the transaction
			$this->transaction_result = $this->doWinTransaction();

			// Log the attack
			$this->addAttackLog();
			return true;

	}

	/**
	 * Calculate the attack
	 */
	private function calculateAttack(){

		// multipliers
		$this->def_multi = rand(1,1.4);
		$this->att_multi = rand(1,1.3);

		// Extra luck for defender?
		$get_away = round(rand(0,14));

		// Calculate the power
		$this->attacker_power = $this->attacker->getAttack() * $this->att_multi;
		$this->defender_power = $this->defender->getDefence() * $this->def_multi;

		if($this->defender_power >= $this->attacker_power){
			// Defender is stronger

			// Defender is winner
			$this->winner = $this->defender;
			$this->loser = $this->attacker;
		}else{

			// Attacker is stronger
			// Attacker is winner
			$this->winner = $this->attacker;
			$this->loser = $this->defender;

			// Defender got lucky, and got away early
			if($get_away > 12){
				$this->defender_got_away = true;
			}

		}

	}

	/**
	 * Calculate the Win
	 */
	private function calculateMoneyWin(){

		// Defender got away, so no need to calculate the money
		if($this->defender_got_away === true)
			return false;

		// Attacker luck
		$this->luck = rand(1,5);
		$this->min = rand(50,100);
		// Get the cash of the loser
		$loser_money = $this->loser->getCash();
		// Calculate to won money
		$amount_to_win = ($loser_money/100)*$this->maxPercentageWin;
		$extra_money = ($amount_to_win/$this->min)*$this->luck;
		// Set the total amount robbed
		$this->amount_won = round($amount_to_win + $extra_money);

		// Never negative
		if($this->amount_won <= 0)
			$this->amount_won = 0;

	}

	/**
	 * Do the transaction
	 * @return string
	 */
	private function doWinTransaction(){

		// Defender got away, so no need to calculate the money
		if($this->defender_got_away === true)
			return false;

		// Do the tranaction
		$t = new BankTransaction($this->loser->getId(),$this->winner->getId(),$this->amount_won,'usertouser','cash');
		return $t->doTransaction();

	}

	/**
	 * Check if the user can attack the user
	 * @return bool
	 */
	private function canAttack(){

		// Check max attacks a day
		if($this->maxAttackADay() !== true){
			return false;
		}

		// Check last attack
		if($this->timeCheck() !== true){
			return false;
		}

		return true;

	}

	/*
	 * Check the time of the last attack
	 */
	private function timeCheck(){

		global $wpdb;

		// Get the defence from the db
		$select = "SELECT time FROM {$wpdb->prefix}{$this->db_table_attack_log} WHERE attacker='{$this->attacker->getId()}' AND defender='{$this->defender->getId()}' AND time >= CURDATE() AND time < CURDATE() + INTERVAL 1 DAY ORDER BY time DESC LIMIT 1";
		// Get the result
		$result = $wpdb->get_row($select);

		// No time yet, so user can attack
		if(!isset($result->time))
			return true;

		$result->time = strtotime($result->time);
		// Current time
		$c_time = current_time('timestamp');

		// The time the next attack is allowed
		$allowed_time = $result->time + $this->age_between_attacks;
		$timeleft = $allowed_time - $c_time;

		// User has to wait to attack again
		if($c_time < $allowed_time){
			$this->result = sprintf(__('You need to wait another %d seconds to attack %s again.'),$timeleft,$this->defender->getUserinfo()->user_login);
			return false;
		}

		// It seems to work, so user can attack
		return true;


	}

	/**
	 * Check if the user already has attack the max amount of times
	 * @return bool
	 */
	private function maxAttackADay(){

		global $wpdb;

		$select = "SELECT COUNT(*) FROM {$wpdb->prefix}{$this->db_table_attack_log} WHERE attacker='{$this->attacker->getId()}' AND time >= CURDATE() AND time < CURDATE() + INTERVAL 1 DAY ORDER BY time";
		$count = $wpdb->get_var($select);

		// check if user has already attacked more than the allowed attacks a day
		if($count >= $this->max_attacks_a_day){
			$this->result = sprintf(__('You already attacked %s times today. Wait untill tommorow to attack again.'),$this->max_attacks_a_day);
			return false;
		}

		return true;

	}

	/**
	 * Log the attack
	 */
	private function addAttackLog(){

		global $wpdb;

		$table = $wpdb->prefix.$this->db_table_attack_log;

		// insert new attack log
		$wpdb->insert($table,array(
			'attacker' => $this->attacker->getId(),
			'defender' => $this->defender->getId()
		) );

	}
}