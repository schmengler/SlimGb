<?php
class SlimGb_Plugin
{

	/**
	 * Plugin definition and configuration from plugin.yaml
	 * 
	 * @var SlimGb_Service_Config
	 */
	protected $config;

	/**
	 * @var string
	 */
	protected $name;
	/**
	 * @var string
	 */
	protected $version;

	final public function __construct(SlimGb_Service_Config $config)
	{
		$this->config = $config;
		$this->version = $config['version'];
	}
	
	final public function install()
	{
		//TODO: install()
	}
	
	/**
	 * on_Entry_Validate => entry.validate
	 * on_App_HandlePost => app.handle_post
	 * 
	 * @return callback[]
	 */
	final public function getEventHandlers()
	{
		$eventHandlers = array();
		foreach(get_class_methods(get_class($this)) as $method) {
			if ($method[0] != 'o'  || $method[1] != 'n' || $method[2] != '_') {
				continue;
			}
			$_parts = explode('_', $method, 2);
			if (count($_parts) < 2) {
				continue;
			}
			$_subject = self::uncamelize($_parts[1]);
			$_event = self::uncamelize($_parts[2]);
			$eventHandlers[$_subject . '.' . $_event] = array($this, $method);
		}
		return $eventHandlers;
	}
	private static function uncamelize($string)
	{
		$string[0] = strtolower($string[0]);
		return preg_replace_callback('/([A-Z])/', create_function('$s','return \'_\'. strtolower($s[0]);'), $string);
	}
	
	
	final public function on_View_Instantiate(sfEvent $event)
	{
		if (!is_array($this->config['output_filters'])) {
			return;
		}
		foreach($this->config['output_filters'] as $filter) {
			$this->connectFilterToView($filter, $event->getSubject());
		}
	}
	
	private function connectFilterToView($filter, SlimGb_Service_View $view)
	{
		if (!is_array($filter)) {
			throw new SlimGb_ConfigException('output_filters', $filter, $this->config->getSource(), 'Must be an array!');
		}
		if (!class_exists($filter['class']) || !in_array('Zend_Filter', class_implements($filter['class']))) {
			throw new SlimGb_ConfigException('output_filters.class', $filter['class'], $this->config->getSource(), 'Must be existing class that implements Zend_Filter!');
		}
		if (!in_array($filter['position'], array('append', 'prepend'))) {
			throw new SlimGb_ConfigException('output_filters.position', $filter['position'], $this->config->getSource(), 'Must be append or prepend!');
		}
		$filter = new $filter['class']();
		$constraints = new OutputFilterWrapperConstraints($filter['constraints']);
		$filterWrapper = new OutputFilterWrapper($filter, $constraints);
		if ($filter['position'] == 'append') {
			$view->appendOutputFilterWrapper($filterWrapper);
		} else {
			$view->prependOutputFilterWrapper($filterWrapper);
		}
	}
	
	final public function on_EntryFactory_Instantiate(sfEvent $event)
	{
		if (!is_array($this->config['entry_decorators'])) {
			return;
		}
		foreach($this->config['entry_decorators'] as $decorator) {
			$this->addEntryDecorator($decorator, $event->getSubject());
		}
	}

	private function addEntryDecorator($decorator, SlimGb_Service_EntryFactory $factory) {
		if (!class_exists($decorator['class']) || is_subclass_of($decorator['class'], 'SlimGb_EntryDecorator')) {
			throw new SlimGb_ConfigException('entry_decorators.class', $decorator['class'], $this->config->getSource(), 'Must be existing class that extends SlimGb_EntryDecorator!');
		}
		$factory->addDecorator($decorator['class']);
	}
	
	
	// helper methods:
	
	protected function addEntryProperty(SlimGb_FieldDefinition $definition)
	{
		//TODO: addEntryProperty()
	}
	
}