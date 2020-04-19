<?php


namespace Xe_GOC\Inc\Lib;


class security {

	static private $key = "+Qc6bpbrvdFPbX@7T_77AxC!JFT-94qkNe7Ec8eGA$*zKK+z+xWtL3$2!$";
	static private $vi = "CcSt0V/teW/RNxoA";
	/**
	 * Encrypt
	 * @param $message
	 *
	 * @return string
	 * @throws \Exception
	 */
	static function encrypt($message){

		$message = openssl_encrypt($message, "AES-128-CTR",
			self::$key, 0, self::$vi);
		return $message;
	}

	/**
	 * Decrypt message
	 * @param $message
	 *
	 * @return false|string
	 */
	static function decrypt($message){

		$message = openssl_decrypt($message, "AES-128-CTR",
			self::$key, 0, self::$vi);
		return  $message;
	}
}