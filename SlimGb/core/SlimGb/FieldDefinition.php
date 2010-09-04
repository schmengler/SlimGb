<?php
class SlimGb_FieldDefinition
{
	private $name;
	private $type;
	private $options;
	
	/**
	 * @param string $name
	 * @param string $type allowed values: integer, double, boolean, string, DateTime, Enum
	 * @param array $options may have the following keys: size, enum, default
	 */
	public function __construct($name, $type, array $options = array())
	{
		$this->name = $name;
		$this->type = $type;
		$this->options = $options;
	}
	
	//TODO: getters etc.
}