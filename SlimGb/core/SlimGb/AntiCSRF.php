<?php

/**
* Anti Cross Site Request Forgery class.
*
*/
class SlimGb_AntiCSRF {
	const ERROR_NO_REQUEST_TOKEN = 1;
	const ERROR_TOKEN_NOT_FOUND = 2;
	const ERROR_TOKEN_TIMED_OUT = 4;
	/**
	 * Index of $_SESSION where tokens are stored
	 * 
	 * @var string
	 */
	private $sessionIndex = 'SlimGb_Tokens';
	
	/**
	 * Unique identifier for the form to be protected
	 * 
	 * @var string
	 */
	private $formId;

	/**
	 * Token timeout period, in minutes
	 *
	 * @var integer
	 */
	private $timeout;

	/**
	 * Generated new token after setToken()
	 *
	 * @var string
	 */
	private $token = null;

	/**
	 * Error code of check() call
	 *
	 * @var integer
	 */
	private $error = 0;

	/**
	 * @param string $formId Unique identifier for form to be protected.
	 * @param integer $timeout Token timeout period, in minutes. Default 15 minutes.
	 */
	public function __construct($formId, $timeout=15) {
		$this->formId = $formId;
		$this->timeout = $timeout;
		if(!isset($_SESSION)) {
			//may be too late here thus session_start() is called first in SlimGb.inc.php
			session_start();
		}
	}

	/**
	 * Generates token.
	 * 
	 * @return string
	 */
	private function makeToken() {
		return md5(uniqid(mt_rand(), true));
	}

	/**
	 * Destroys a single token.
	 *
	 * @param string $token the token to destroy.
	 */
	public function deleteToken($token) {
		unset($_SESSION[$this->sessionIndex][$this->formId][$token]);
	}

	/**
	 * Destroys all tokens for the current form except the new token (if set).
	 */
	public function deleteTokens() {
		if (!isset($_SESSION[$this->sessionIndex][$this->formId]) || !is_array($_SESSION[$this->sessionIndex][$this->formId])) {
			return;
		}
		$toDelete = array_keys($_SESSION[$this->sessionIndex][$this->formId]);
		unset($toDelete[array_search($this->token, $toDelete)]);
		foreach($toDelete as $d) {
			unset($_SESSION[$this->sessionIndex][$this->formId][$d]);
		}
	}

	/**
	 * Saves a new token to session
	 */
	private function setToken() {
		$this->token = $this->makeToken();
		$_SESSION[$this->sessionIndex][$this->formId][$this->token] = time();
	}

	/**
	 * Generates, saves and returns new token
	 * 
	 * @return string
	 */
	public function getNewToken()
	{
		$this->setToken();
		return $this->token;
	}
	
	/**
	 * Returns previously generated token
	 * 
	 * @return string
	 */
	public function getToken()
	{
		return $this->token;
	}

	/**
	 * Checks if the request have the right token set; after that, destroy the old tokens.
	 * Returns true if the request is ok, otherwise returns false.
	 * 
	 * @param string $token the posted token
	 * @return boolean
	 */
	function check($token) {
		// Check if the token has been sent.
		if ($token===null) {
			$this->error = self::ERROR_NO_REQUEST_TOKEN;
			return false;
		}
		// Check if the token exists
		if (!isset($_SESSION[$this->sessionIndex][$this->formId][$token])) {
			$this->error = self::ERROR_TOKEN_NOT_FOUND;
			return false;
		}
		// Check if the token did not timeout
		$age = time() - (int)$_SESSION[$this->sessionIndex][$this->formId][$token];
		if($age > $this->timeout * 60) {
			$this->error = self::ERROR_TOKEN_TIMED_OUT;
			$this->deleteToken($token);
			return false;
		}
		$this->error = 0;
		$this->deleteToken($token);
		return true;
	}

	/**
	 * Returns error code of last check() call.
	 */
	function getError() {
		return $this->error;
	}
 }
 ?>