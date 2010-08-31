<?php
interface SlimGb_Service_EntryFactory
{
	/**
	 * @return SlimGb_Entry
	 */
	public function makeEntry();
	
	/**
	 * @param string $className Class that inherits from SlimGb_EntryDecorator
	 */
	public function addDecorator($className);
}