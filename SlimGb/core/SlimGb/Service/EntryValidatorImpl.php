<?php
class SlimGb_Service_EntryValidatorImpl implements SlimGb_Service_EntryValidator
{
	/**
	 * @var array [ key : [message1 , ...] ]
	 */
	private $result;
	
	/**
	 * @var SlimGb_Service_Config
	 */
	private $config;
	
	/**
	 * @var sfEventDispatcher
	 */
	private $eventDispatcher;
	
	public function __construct(sfEventDispatcher $ed, SlimGb_Service_Config $config)
	{
		$this->eventDispatcher = $ed;
		$this->config = $config;
	}
	
	public function validate(SlimGb_Entry $entry)
	{
		$this->result = array();
		// filter + validate author
		$entry->setAuthor($this->filterAuthor($entry->getAuthor()));
		$this->validateAuthor($entry->getAuthor());
		// filter + validate message
		$entry->setMessage($this->filterMessage($entry->getMessage()));
		$this->validateMessage($entry->getMessage());
		// allow plugins to filter + validate anything
		$event = new sfEvent($entry, 'entry.validate');
		$this->eventDispatcher->notify($event);
		// combine results
		$this->result = array_merge_recursive($this->result, (array)$event->getReturnValue());
		return $this->result;
	}
	
	private function filterAuthor($author)
	{
		return trim($author);
	}
	private function validateAuthor($author)
	{
		if (empty($author)) {
			$this->result['author'][] = 'Name must not be empty.';
		}
		if (mb_strlen($author) > $this->config['entries']['author_max_length']) {
			$this->result['author'][] = sprintf(
				'Name must not be longer than %d characters. Actual length is %d.',
				$this->config['entries']['author_max_length'], mb_strlen($author)
			);
		}
	}
	
	private function filterMessage($message)
	{
		return trim($message);
	}
	private function validateMessage($message)
	{
		if (empty($message)) {
			$this->result['message'][] = 'Message must not be empty.';
		}
		if (mb_strlen($message) > $this->config['entries']['max_length']) {
			$this->result['message'][] = sprintf(
				'Message must not be longer than %d characters. Actual length is %d.',
				$this->config['entries']['max_length'], mb_strlen($message)
			);
		}
	}
}