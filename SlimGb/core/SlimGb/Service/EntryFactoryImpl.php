<?php
class SlimGb_Service_EntryFactoryImpl implements SlimGb_Service_EntryFactory
{
	private $decorators = array();
	
	/**
	 * @param string $className
	 */
	public function addDecorator($className) {
		if (! $className instanceof SlimGb_EntryDecorator) {
			throw new InvalidArgumentException("addDecorator(): $className does not inherit from SlimGb_EntryDecorator");
		}
		$this->decorators[] = $className;
	}

	/**
	 * @return SlimGb_Entry
	 */
	public function makeEntry($id = null) {
		$entry = new SlimGb_EntryBasic($id);
		foreach($this->decorators as $decorator) {
			$entry = new $decorator($entry);
		}
		return $entry;
	}

	
}