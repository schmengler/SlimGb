<?php
abstract class SlimGb_EntryDecorator extends AbstractDecorator implements SlimGb_Entry
{
	/**
	 * 
	 */
	public function getAuthor() {
		return parent::getAuthor();
	}

	/**
	 * 
	 */
	public function getMessage() {
		return parent::getMessage();
	}


	/**
	 * 
	 */
	public function getTime() {
		return parent::getTime();
	}

	/**
	 * @param unknown_type $author
	 */
	public function setAuthor($author) {
		return parent::setAuthor($author);
	}

	/**
	 * @param unknown_type $message
	 */
	public function setMessage($message) {
		return parent::setMessage($message);
	}

	/**
	 * @param SlimGb_DateTime $time
	 */
	public function setTime(SlimGb_DateTime $time) {
		return parent::setTime($time);
	}
	/**
	 * 
	 */
	public function getProperties() {
		return parent::getProperties() + get_object_vars($this);
	}


	
}