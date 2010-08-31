<?php
interface SlimGb_Service_Config extends ArrayAccess
{
	/**
	 * @param string $source
	 */
	public function __construct($source);
	
	/**
	 * Gets a configuration item
	 * 
	 * @param string $item i.e. 'db.host'
	 */
	/* public function get($item = null); */
	
	/**
	 * reread configuration (for caching/compiling mechanisms)
	 */
	public function refresh();
	
	/**
	 * save changed configuration
	 */
	public function save();
}