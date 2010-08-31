<?php
abstract class SlimGb_Controller
{
	
	private $includeActions = array();
	private $postActions = array();

	/**
	 * @var SlimGb_Service_Config Application config
	 */
	protected $config;

	/**
	 * Service Container
	 * 
	 * needed for EventDispatcher, DataProvider etc.
	 * 
	 * @var sfServiceContainer
	 */
	protected $serviceContainer;

	/**
	 * @var SlimGb_Service_DataProvider
	 */
	protected $dataProvider;
	/**
	 * @var SlimGb_Service_EntryValidator
	 */
	protected $entryValidator;
	/**
	 * @var SlimGb_Service_View
	 */
	protected $view;

	/**
	 * @var sfEventDispatcher
	 */
	protected $eventDispatcher;
	
	
	/**
	 * @param sfServiceContainer $sc
	 * @param SlimGb_Service_Config $config
	 */
	public function __construct(sfServiceContainer $sc)
	{
		$this->serviceContainer = $sc;
		$this->config = $this->serviceContainer->{'config.app'};
		$this->dataProvider = $this->serviceContainer->dataProvider;
		$this->entryValidator = $this->serviceContainer->entryValidator;
		$this->view = $this->serviceContainer->view;
		$this->eventDispatcher = $this->serviceContainer->eventDispatcher;

		$this->registerActions();
		$this->connectActions();
	}

	/**
	 * template method: register actions on construct
	 */
	abstract protected function registerActions(); 

	/**
	 * registers a method as callback for shortcut actions. Prefix for shortcut
	 * will be 'include'.
	 * 
	 * Shortcut actions are meant to be used as widgets from outside the
	 * application. See registerPostAction for POST actions.
	 * 
	 * Example:
	 * <code>
	 * // in Controller:
	 * $this->registerAction('showForm', 'form');
	 * // outside the application:
	 * echo $gb->include_form();
	 * </code>
	 * 
	 * @param string $methodName
	 * @param string $shortcutName
	 */
	protected function registerIncludeAction($methodName, $shortcutName)
	{
		$this->includeActions[$shortcutName] = array($this, $methodName);
	}
	protected function registerPostAction($methodName, $actionName)
	{
		$this->postActions[$actionName] = array($this, $methodName);
	}

	private function connectActions()
	{
		$this->eventDispatcher->connect('app.shortcut_method', array($this, 'callActionFromShortcut'));
	}
	
	public function callActionFromShortcut(sfEvent $callEvent)
	{
		if ($callEvent['prefix'] != 'include') {
			return false;
		}
		if (!array_key_exists($callEvent['method'], $this->includeActions)) {
			return false;
		}
		$callEvent->setReturnValue(
			call_user_func_array(
				$this->includeActions[$callEvent['method']],
				$callEvent['arguments']
			)
		);
		return true;
	}
	
	public function callPostActions()
	{
		if (!array_key_exists('SlimGb_action', $_POST)) {
			return false;
		}
		foreach($this->postActions as $action => $method)
		{
			if ($_POST['SlimGb_action'] === $action) {
				return call_user_func($method);
			}
		}
		return false;
	}

	/*
	 * Getters, Setters
	 */
	
	public function getDataProvider() {
		return $this->dataProvider;
	}

	public function getView() {
		return $this->view;
	}

	public function setDataProvider(SlimGb_Service_DataProvider $dataProvider) {
		$this->dataProvider = $dataProvider;
		return $this;
	}

	public function setView(SlimGb_Service_View $view) {
		$this->view = $view;
		return $this;
	}

}