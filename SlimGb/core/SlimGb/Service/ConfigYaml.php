<?php
class SlimGb_Service_ConfigYaml implements SlimGb_Service_Config
{
	/**
	 * @var string
	 */
	private $source;
	/**
	 * @var array
	 */
	private $configArray;
	
	/**
	 * @param string $source
	 */
	public function __construct($source)
	{
		$this->source = $source;
		if (!$this->readCompiledFile()) {
			$this->refresh();
		}
	}
	
	private function getCompiledFile()
	{
		return SLIMGB_BASEPATH . '/runtime/' . basename($this->source, '.yaml') . '.compiled.php';
	}
	
	private function readCompiledFile()
	{
		$compiled = $this->getCompiledFile();
		if (!file_exists($compiled)) {
			return false;
		} 
		$this->configArray = include($compiled);
		return true;
	} 
	
	private function saveCompiledFile()
	{
		$filename = $this->getCompiledFile();
		if(!is_dir(dirname($filename))) {
			mkdir(dirname($filename), 0777, true);
		}
		file_put_contents($filename, '<?php return ' . var_export($this->configArray, true) . ';');
	}
	
	private function readYamlFile()
	{
		$this->configArray = sfYaml::load($this->source);
	}

	private function saveYamlFile()
	{
		file_put_contents($this->source, sfYaml::dump($this->configArray));
	}
		
/*	public function get($item = null)
	{
		$nodes = explode('.', $item);
		$result = $this->configArray;
		foreach($nodes as $subnode)
		{
			$result = $result[$subnode];
		}
		return $result;
	}
*/
	public function refresh()
	{
		$this->readYamlFile();
		$this->saveCompiledFile();
	}

	public function save()
	{
		$this->saveYamlFile();
		$this->saveCompiledFile();
	}
	
	/*
	 * direct access via ArrayAccess
	 */

	/**
	 * @param scalar $offset
	 */
	public function offsetExists($offset) {
		return array_key_exists($offset, $this->configArray);
	}

	/**
	 * @param scalar $offset
	 */
	public function offsetGet($offset) {
		return $this->configArray[$offset];
	}

	/**
	 * @param scalar $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value) {
		$this->configArray[$offset] = $value;
	}

	/**
	 * @param scalar $offset
	 */
	public function offsetUnset($offset) {
		unset($this->configArray[$offset]);
	}


}