<?php if (!defined(SLIMGB_RUNNING)) {
	header('Status: 403 Forbidden');
	exit;
}?>
parameters:
  dataSource.dsn:       'mysql:dbname=testdb;host=127.0.0.1'
  dataSource.user:      dbuser
  dataSource.password:  dbpass
  #
  # do not change anything from here
  #
  dataSource.class:     SlimGb_Service_DataSourcePDO
  
services:
  dataSource:
    class:     %dataSource.class%
    arguments: [ %dataSource.dsn%, %dataSource.user%, %dataSource.password%, [ 1002 : "SET NAMES 'UTF8'" ]