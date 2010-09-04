<?php
interface SlimGb_Service_PluginManager
{
	/*
	 * injects the service container
	 */
	public function setServiceContainer(sfServiceContainer $sc);
	
	/**
	 * loads active plugins
	 */
	public function loadPlugins();
	
	/**
	 * @param string $pluginName
	 */
	public function activatePlugin($pluginName);
	
	/**
	 * @param string $pluginName
	 */
	public function deactivatePlugin($pluginName);
}