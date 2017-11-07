<?php

define('APPLICATION_PATH', dirname(__FILE__)."/../");

define('CACHE_PATH', APPLICATION_PATH.'cache' . DIRECTORY_SEPARATOR);
$application = new \Yaf\Application( APPLICATION_PATH . "/conf/application.ini");

$application->bootstrap()->run();
?>
