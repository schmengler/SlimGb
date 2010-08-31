<?php
class SlimGb_Service_DataProviderImpl implements SlimGb_Service_DataProvider
{

	/**
	 * @var SlimGb_Service_EntryFactory
	 */
	private $entryFactory;
	
	/**
	 * @var SlimGb_Service_DataSource
	 */
	private $dataSource;
	
	/**
	 * @var sfEventDispatcher
	 */
	private $eventDispatcher;
	
	public function __construct(SlimGb_Service_DataSource $dataSource, SlimGb_Service_EntryFactory $entryFactory, sfEventDispatcher $eventDispatcher)
	{
		$this->entryFactory = $entryFactory;
		$this->dataSource = $dataSource;
		$this->eventDispatcher = $eventDispatcher;
	}
	/**
	 * @param int $offset
	 * @param int $limit
	 */
	public function fetchEntries($offset, $limit) {
		$dataAll = $this->dataSource->fetch('SlimGb_entries', $offset, $limit);
		$result = array();
		foreach($dataAll as $id => $dataEntry) {
			$result[] = $this->makeEntry($dataEntry, $id);
		}
		return $result;
	}
	
	public function countEntries()
	{
		return $this->dataSource->count('SlimGb_entries');
	}

	/**
	 * creates Entry object from data array
	 */
	public function makeEntry(array $data = null, $id = null) {
		$entry = $this->entryFactory->makeEntry($id);
		if ($data === null) {
			return $entry;
		}
		$entry->setAuthor($data['author']);
		$entry->setMessage($data['message']);
		$entry->setTime(new SlimGb_DateTime($data['time']));
		$this->eventDispatcher->notify(new sfEvent($entry, 'entry.populate', array('data' => $data)));
		return $entry;
	}

	/**
	 * @param SlimGb_Entry $entry
	 */
	public function persistEntry(SlimGb_Entry $entry) {
		$this->dataSource->insert('SlimGb_entries', $entry->getProperties());
	}

}