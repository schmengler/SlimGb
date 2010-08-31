<?php
class SlimGb_GuestbookController extends SlimGb_Controller
{
	/**
	 * Runtime cache for already generated output
	 * 
	 * @var array
	 * @see SlimGb_GuestbookController::showPagination();
	 */
	private $cache = array();
	
	/**
	 * Pagination object
	 * 
	 * @var SlimGb_Pagination
	 */
	private $pagination;
	
	public function __construct(sfServiceContainer $sc)
	{
		parent::__construct($sc);
	}
	/**
	 * register actions on construct
	 */
	protected function registerActions()
	{
		$this->registerIncludeAction('showMessages', 'messages');
		$this->registerIncludeAction('showForm', 'form');
		$this->registerIncludeAction('showPagination', 'pagination');
		$this->registerIncludeAction('showEntries', 'entries');
		$this->registerPostAction('addEntry', 'add');
	}

	/**
	 * include action 'entries'
	 */
	public function showEntries()
	{
		$pagination = $this->getPagination();
		$entries = $this->dataProvider->fetchEntries($pagination->getCurrentPage()->from - 1, $this->config['entries']['per_page']);

		$this->view->entries = $entries;
		$this->view->timeFormat = $this->config['locale']['time_format'];
		return $this->view->render('entries');
	}

	/**
	 * include action 'form'
	 */
	public function showForm()
	{
		if (!isset($this->view->entry)) {
			$this->view->entry = $this->dataProvider->makeEntry();
		}
		if (!isset($this->view->validationErrors)) {
			$this->view->validationErrors = array();
		}
		$this->view->entryConfig = $this->config['entries'];

		$antiCSRF = new SlimGb_AntiCSRF('add');
		$this->view->csrfToken = $antiCSRF->getNewToken();
		return $this->view->render('form');
	}

	/**
	 * include action 'messages'
	 */
	public function showMessages()
	{
		if (!isset($this->view->message)) {
			$this->view->message = '';
		}
		return $this->view->render('messages');
	}
	/**
	 * include action 'pagination'
	 */
	public function showPagination()
	{
		// usually include_pagination() is called twice => cache that
		if (isset($this->cache['pagination'])) {
			return $this->cache['pagination'];
		}
		$this->view->pagination = $this->getPagination();
		$this->cache['pagination'] = $this->view->render('pagination');
		return $this->cache['pagination'];
	}

	/**
	 * POST Action 'add'
	 */
	public function addEntry()
	{
		// spam check:
		if ($this->detectSpam()) {
			return;
		}
		
		// populate object:
		$entry = $this->dataProvider->makeEntry(array(
			'author' => filter_input(INPUT_POST, 'SlimGb_author'),
			'message' => filter_input(INPUT_POST, 'SlimGb_message'),
			'time' => 'now'
		));

		// csrf check:
		if ($this->detectCSRF('add')) {
			$this->view->entry = $entry;
			return;
		}
		
		// validation:
		$validation = $this->entryValidator->validate($entry);
		if (!empty($validation)) {
			$this->view->validationErrors = $validation;
			$this->view->entry = $entry;
			return;
		}
		
		// OK
		$this->dataProvider->persistEntry($entry);
		$this->view->message = 'Entry added.';
	}
	
	/**
	 * @return SlimGb_Pagination Pagination dependent on GET parameters (SlimGb_page / SlimGb_offset)
	 */
	private function getPagination()
	{
		if ($this->pagination !== null) {
			return $this->pagination;
		}
		if (isset($_GET['SlimGb_page'])) {
			$offset = $this->config['entries']['per_page'] * (intval($_GET['SlimGb_page']) - 1);
		} else {
			$offset = isset($_GET['SlimGb_offset']) ? intval($_GET['SlimGb_offset']) : 0;
		}
		$limit = $this->config['entries']['per_page'];
		$total = $this->dataProvider->countEntries();
		
		return $this->pagination = new SlimGb_Pagination($this->config, $total, $offset);
	}
	/**
	 * @return boolean true if POST is recognized as spam
	 */
	private function detectSpam()
	{
		// SlimGb_foo: Honeypot input field
		if (!empty($_POST['SlimGb_foo'])) {
			$this->view->message = 'This looks like spam. Please don\'t.';
			$this->eventDispatcher->notify(new sfEvent($this, 'controller.handle_spam', array('POST' => $_POST, 'detected' => 'honeypot')));
			return true;
		}
		$event = new sfEvent($this, 'controller.detect_spam', array('POST' => $_POST, 'dataProvider' => $this->dataProvider));
		$this->eventDispatcher->notifyUntil($event);
		if ($event->getReturnValue() !== null) {
			$this->view->message = 'This looks like spam. Please don\'t.';
			$this->eventDispatcher->notify(new sfEvent($this, 'controller.handle_spam', array('POST' => $_POST, 'detected' => $event->getReturnValue())));
			return true;
		}
		return false;
	}
	/**
	 * @return boolean true if POST does not have a valid csrf token
	 */
	private function detectCSRF()
	{
		$antiCSRF = new SlimGb_AntiCSRF('add');
		if ($antiCSRF->check(filter_input(INPUT_POST, 'SlimGb_csrfToken'))) {
			return false;
		}
		switch($antiCSRF->getError()) {
			case SlimGb_AntiCSRF::ERROR_NO_REQUEST_TOKEN:
				$this->view->message = 'Invalid Request. Please submit again.';
				break;
			case SlimGb_AntiCSRF::ERROR_TOKEN_NOT_FOUND:
				$this->view->message = 'Invalid Session. Please submit again.';
				break;
			case SlimGb_AntiCSRF::ERROR_TOKEN_TIMED_OUT:
				$this->view->message = 'Session timed out. Please submit again.';
				break;
		}
		return true;
	}
}