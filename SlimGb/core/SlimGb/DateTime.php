<?php
class SlimGb_DateTime extends DateTime
{
	public static $defaultFormat = 'c'; // ISO 8601
	
	public function __toString()
	{
		return $this->format(self::$defaultFormat);
	}
}