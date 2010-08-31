<?php
/**
 * Views display 'pages' with given data and may filter that data with OutputFilter
 * 
 * @author fs
 *
 */
interface SlimGb_Service_View
{
	
	/**
	 * @param OutputFilterWrapperInterface $fw
	 */
	public function appendOutputFilterWrapper(OutputFilterWrapperInterface $fw);
	
	/**
	 * @param OutputFilterWrapperInterface $fw
	 */
	public function prependOutputFilterWrapper(OutputFilterWrapperInterface $fw);
	
	/**
	 * @param scalar $var
	 */
	public function __get($var);
	
	/**
	 * @param scalar $var
	 * @param mixed $value
	 */
	public function __set($var, $value);
	
	/**
	 * @param string $page
	 */
	public function render($page);
}