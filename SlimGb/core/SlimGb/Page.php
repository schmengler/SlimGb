<?php
class SlimGb_Page
{
	const LINK = 0;
	const ACTIVE = 1;
	const DOTS = 2;

	public $type;
	public $number;
	public $link;
	public $from;
	public $to;
	
	public function __construct($type, $number = null, $from = null, $to = null, $link = null)
	{
		$this->type = $type;
		$this->number = $number;
		$this->link = $link;
		$this->from = $from;
		$this->to = $to;
	}
}