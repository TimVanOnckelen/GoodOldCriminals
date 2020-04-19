<?php


namespace Xe_GOC\Inc\Models\Frontend;


class BackpackTransaction {

	private $transactionId;
	private $company;
	private $product;
	private $user;
	private $amount;
	private $type;
	private $result;
	private $table_name = "goc_backpack";

	/**
	 * BackpackTransaction constructor.
	 *
	 * @param $company
	 * @param $product
	 * @param $user
	 * @param int $amount
	 * @param string $type
	 */
	public function __construct($company,$product,$user,$amount = 0,$type= "add") {
		$this->company = $company;
		$this->product = $product;
		$this->user = $user;
		$this->amount = $amount;
		$this->type = $type;
	}

	/**
	 * @return false|int
	 */
	public function doTransaction(){

		switch ($this->type){
			case "add";
				return $this->addToBackpack();
			break;

			case "update";
				return $this->updateBackpack();
			break;

			case "delete";
				return $this->deleteFromBackpack();
			break;
		}

	}

	/**
	 * ADD TO THE backpack
	 * @return false|int
	 */
	private function addToBackpack(){

		global $wpdb;

		// Already exists
		if($this->doesUserAlreadyOwnProduct() == true){
			return $this->updateBackpack();
		}

		return $wpdb->insert($wpdb->prefix.$this->table_name,array("company_id" => $this->company,"product_id" => $this->product, "user_id" => $this->user, "amount" =>$this->amount));

	}

	/**
	 * Update the backpack
	 * @return false|int
	 */
	private function updateBackpack(){

		global $wpdb;

		$update = "UPDATE {$wpdb->prefix}{$this->table_name} SET amount = amount+{$this->amount} WHERE product_id='{$this->product}' AND user_id = '{$this->user}'";

		return $wpdb->query($update);

	}

	/**
	 * Delete from the backpack
	 * @return false|int
	 */
	private function deleteFromBackpack(){

		global $wpdb;

		// cannot update, because user does not own this
		if($this->userHasEnoughOfProduct() < $this->amount){
		    return null;
        }

		$update = "UPDATE {$wpdb->prefix}{$this->table_name} SET amount = amount-{$this->amount} WHERE product_id='{$this->product}' AND user_id = '{$this->user}'";

		return $wpdb->query($update);

	}

	/**
	 * Check if the user already owns the current product
	 * @return bool
	 */
	private function doesUserAlreadyOwnProduct(){
		global $wpdb;

		$select = "SELECT * FROM {$wpdb->prefix}{$this->table_name} WHERE product_id = {$this->product} AND user_id = {$this->user} LIMIT 1";
		$result = $wpdb->get_results($select);
		$this->result = $result;

		if($result != null){
			return true;
		};

		// Noting found
		return false;

	}

    /**
     * Check if current user has enough of product to delte
     * @return int|null
     */
	private function userHasEnoughOfProduct(){

	    if($this->doesUserAlreadyOwnProduct() == true){
	        return $this->result[0]->amount;
        }

        return null;

    }




}