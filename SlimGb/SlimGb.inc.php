<?php
/**
 * @var boolean grants access to source files
 */
define('SLIMGB_RUNNING', true);

/**
 * Containing: core, datasources, plugins, runtime
 * 
 * @var string base path
 */
define('SLIMGB_BASEPATH', dirname(__FILE__));

set_include_path(
	get_include_path() .
	PATH_SEPARATOR . SLIMGB_BASEPATH . '/lib/SGH-lib' .
	PATH_SEPARATOR . SLIMGB_BASEPATH . '/lib/symfony-components'
);

require_once 'sfYaml.php';
require_once 'sfEventDispatcher.php';
require_once 'sfServiceContainerAutoloader.php';
require_once 'OutputFilter/outputfilter.lib.php';
require_once 'OutputFilter/Filters/HtmlEntitiesFilter.php';
require_once 'OutputFilter/Filters/Nl2brFilter.php';
require_once 'OutputFilter/Filters/FilterChain.php';

sfServiceContainerAutoloader::register();
spl_autoload_register('SlimGb_autoload');

/**
 * All classes of the SlimGb package get autoloaded here.
 * 
 * @param $className
 */
function SlimGb_autoload($className)
{
	$fileName = SLIMGB_BASEPATH . '/core/' . str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
	if (file_exists($fileName)) {
		require $fileName;
	}
}