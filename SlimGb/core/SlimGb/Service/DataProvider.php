<?php
interface SlimGb_Service_DataProvider
{
	public function persistEntry(SlimGb_Entry $entry);
	
	public function fetchEntries($offset, $limit);
	
	public function countEntries();
	
	public function makeEntry();
}