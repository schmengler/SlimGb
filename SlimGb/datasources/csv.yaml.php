<?php if (!defined('SLIMGB_RUNNING')) {
	header('Status: 403 Forbidden');
	exit;
}?>
parameters:
  #
  # do not change anything from here
  #
  dataSource.class:     SlimGb_Service_DataSourceCSV

services:
  dataSource:
    class:     %dataSource.class%
    arguments: [ @config.app ]