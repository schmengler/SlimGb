<?php
interface SlimGb_Entry
{
	/**
	 * @return array All properties as array, in persistable format
	 */
	public function getProperties();
	
	/**
	 * @return mixed Identification of existing entry, determined by DataSource
	 */
	public function getId();
	
	public function getAuthor();
	
	public function getMessage();
	
	public function getTime();

	public function setAuthor($author);
	
	public function setMessage($message);
	
	public function setTime(SlimGb_DateTime $time);
}