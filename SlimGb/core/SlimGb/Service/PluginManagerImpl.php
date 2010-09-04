<?php
class SlimGb_Service_PluginManagerImpl implements SlimGb_Service_PluginManager
{
	/**
	 * @var sfServiceContainer
	 */
	private $serviceContainer;

	/**
	 * General plugin configuration
	 * 
	 * @var SlimGb_Service_Config
	 */
	private $config;
	/**
	 * @var sfEventDispatcher
	 */
	private $eventDispatcher;

	private $plugins = array();
	
	public function __construct(SlimGb_Service_Config $config, sfEventDispatcher $ed)
	{
		$this->config = $config;
		$this->eventDispatcher = $ed;
	}

	public function setServiceContainer(sfServiceContainer $sc)
	{
		$this->serviceContainer = $sc;
		return $this;
	}
	
	public function loadPlugins()
	{
		//snippet:
		foreach($this->config['active_plugins'] as $pluginName) {
			$this->plugins[] = $this->makePlugin($pluginName);
		}
	}
	
	public function activatePlugin($pluginName)
	{
		// DataProvider direkt aus servicecontainer, dann muss er nicht von beginn an initialisiert sein!
		// Grund: Plugins können DataSource noch manipulieren (z.B. Plugin zur Benutzung bestehender PDO Verbindung)
		$dataProvider = $this->serviceContainer->dataProvider; 
		//TODO: update plugin-config
		$plugin = $this->makePlugin($pluginName);
		$plugin->install();
	}
	
	public function deactivatePlugin($pluginName)
	{
		//TODO: deactivatePlugin()
	}
	// snippet:
	private function makePlugin($pluginName)
	{
		//todo: exists?
		$pluginClass = 'SlimGb_Plugin_' . ucfirst($pluginName);
		$pluginConfig = $this->getPluginConfig($pluginName);
		return $this->connectEvents(new $pluginClass($pluginConfig));
	}
	/**
	 * @param string $pluginName
	 * @return SlimGb_Service_Config
	 */
	private function getPluginConfig($pluginName)
	{
		$this->serviceContainer->setParameter('config.plugin.source', SLIMGB_BASEPATH . '/plugins/' . $pluginName . '/plugin.yaml' );
		$pluginConfig = $this->serviceContainer->getService('config.plugin');
		if (is_array($this->config['plugin_configuration'][$pluginName])) {
			foreach ($this->config['plugin_configuration'][$pluginName] as $key => $value) {
				$pluginConfig['configuration'] = $pluginConfig['configuration'] + array($key => $value);
			}
		}
		return $pluginConfig;
	}
	
	/**
	 * @param SlimGb_Plugin $plugin
	 * @return SlimGb_Plugin for fluent interface in makePlugin()
	 */
	private function connectEvents(SlimGb_Plugin $plugin)
	{
		foreach($plugin->getEventhandlers() as $event => $handler) {
			$this->eventDispatcher->connect($event, $handler);
		}
		return $plugin;
	}

}