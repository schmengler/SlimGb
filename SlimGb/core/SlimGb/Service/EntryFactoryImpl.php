<?php
class SlimGb_Service_EntryFactoryImpl implements SlimGb_Service_EntryFactory
{
	private $decorators = array();

	/**
	 * @var sfEventDispatcher
	 */
	private $eventDispatcher;
	
	public function __construct(sfEventDispatcher $ed)
	{
		$this->eventDispatcher = $ed;
		$event = new sfEvent($this, 'entry_factory.instantiate');
		$this->eventDispatcher->notify($event);
	}
	/**
	 * @param string $className
	 */
	public function addDecorator($className) {
		if (!is_subclass_of($className, 'SlimGb_EntryDecorator')) {
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