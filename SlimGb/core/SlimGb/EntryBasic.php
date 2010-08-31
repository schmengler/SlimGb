<?php
class SlimGb_EntryBasic implements SlimGb_Entry
{
	private $_id, $author, $message, $time;
	
	public function __construct($id)
	{
		$this->_id = $id;
	}
	public function getId()
	{
		return $this->_id;
	}
	/**
	 * 
	 */
	public function getAuthor() {
		return $this->author;
	}

	/**
	 * 
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * 
	 */
	public function getProperties() {
		return array_diff_key(
			get_object_vars($this),
			array('_id' => null)
		);
	}

	/**
	 * 
	 */
	public function getTime($format = null) {
		if ($format !== null) {
			return $this->time->format($format);
		}
		return $this->time;
	}

	/**
	 * @param unknown_type $author
	 */
	public function setAuthor($author) {
		$this->author = $author;
		return $this;
	}

	/**
	 * @param unknown_type $message
	 */
	public function setMessage($message) {
		$this->message = $message;
		return $this;
	}

	/**
	 * @param SlimGb_DateTime $time
	 */
	public function setTime(SlimGb_DateTime $time) {
		$this->time = $time;
		return $this;
	}

	
}