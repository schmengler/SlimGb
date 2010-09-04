<?php
/**
 * ConfigException: invalid configuration file
 * 
 * @author fs
 *
 */
class SlimGb_ConfigException extends SlimGb_Exception
{
	public function __construct($key, $value, $file, $message = '')
	{
		parent::__construct(sprintf('Invalid value for %s (%s) in %s. %s', $key, $value, $file, $message));
	}
}