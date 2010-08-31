<?php
class SlimGb_ServiceContainerFactory
{
	private $appDir;
	private $isDebug;
	private $projectName = 'SlimGb';

	public function __construct($tempDir, $isDebug)
	{
		$this->tempDir = $tempDir;
		$this->isDebug = $isDebug;
	}

	public function makeServiceContainer($source)
	{
		$name = 'Project'.md5($this->projectName . $this->isDebug).'ServiceContainer';
		$file = $this->tempDir . '/' . $name . '.php';
		 
		if (!$this->isDebug && file_exists($file))
		{
		  require_once $file;
		  $sc = new $name();
		}
		else
		{
		  // build the service container dynamically
		  $sc = new sfServiceContainerBuilder();
		  $loader = new sfServiceContainerLoaderFileYaml($sc);
		  $loader->load($source);
		 
		  if (!$this->isDebug)
		  {
		    $dumper = new sfServiceContainerDumperPhp($sc);
		 
		    file_put_contents($file, $dumper->dump(array('class' => $name)));
		  }
		}
		return $sc;
	}
}