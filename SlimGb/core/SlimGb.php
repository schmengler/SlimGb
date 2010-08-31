<?php
final class SlimGb
{
	private $isDebug, $isAdmin;
	private $postHandled = false;

	private $serviceContainer;
	/**
	 * @var SlimGb_Service_Config
	 */
	private $config;
	/**
	 * @var sfEventDispatcher
	 */
	private $eventDispatcher;
	/**
	 * @var SlimGb_Service_PluginManager
	 */
	private $pluginManager;
	/**
	 * @var SlimGb_Controller
	 */
	private $controller;

	/**
	 * @param boolean $isDebug Aktiviert Debug-Modus, schaltet v.a. das Cachen
	 * des ServiceContainers aus. Sobald Config-Service registriert, kann der
	 * Modus auch über SlimGb.yaml aktiviert werden.
	 * @param boolean $isAdmin
	 */
	public function __construct($isDebug = false, $isAdmin = false)
	{
		$this->isAdmin = $isAdmin;
		$this->isDebug = $isDebug;
		
		// Service Container
		$scFactory = new SlimGb_ServiceContainerFactory(SLIMGB_BASEPATH . '/runtime', $this->isDebug);
		$this->serviceContainer = $scFactory->makeServiceContainer(SLIMGB_BASEPATH . '/conf/Services.yaml');

		// Services
		$this->config = $this->serviceContainer->{'config.app'};
		$this->isDebug = $this->isDebug || $this->config['debug'];

		$this->eventDispatcher = $this->serviceContainer->eventDispatcher;
		$this->pluginManager = $this->serviceContainer->pluginManager;
		
		// Plugins
		//TODO: Plugins laden
		//$this->pluginManager->load();

	}
	
	public function initGuestbook()
	{
		$this->controller = new SlimGb_GuestbookController($this->serviceContainer);
		$this->handlePost();
	}
	
	public function initAdmin()
	{
		$this->controller = new SlimGb_AdminController($this->serviceContainer);
		$this->handlePost();
	}
	
	public function iniInstall()
	{
		$this->controller = new SlimGb_InstallController($this->serviceContainer);
		$this->handlePost();
	}
	
	private function handlePost()
	{
		if ($this->postHandled) {
			return;
		}
		// Plugin actions
		$event = new sfEvent($this, 'app.handle_post', array('controller' => $this->controller));
		$this->eventDispatcher->notify($event);
		// Controller actions
		$this->controller->callPostActions();
		$this->postHandled = true;
	}

	/**
	 * Shortcut methods may be registered by Plugins and Controllers via the
	 * app.shortcut_method event. They must start with a prefix plus underscore,
	 * i.E. css_style() from the css plugin.
	 * 
	 * @param string $method
	 * @param array $arguments
	 */
	public function __call($method, $arguments)
	{
		list($prefix, $method) = explode('_', $method, 2);
		$event = new sfEvent($this, 'app.shortcut_method', array('prefix' => $prefix, 'method' => $method, 'arguments' => $arguments));
		$this->eventDispatcher->notifyUntil($event);
		if (!$event->isProcessed())
		{
			throw new BadMethodCallException(sprintf('Call to undefined method %s::%s.', get_class($this), $method));
		}
		return $event->getReturnValue();
	}
	
	public function registerShortcutMethod($prefix, $method)
	{
		$this->eventDispatcher->connect('app.shortcut_method', array($this, 'addBarMethodToFoo'));
	}
}