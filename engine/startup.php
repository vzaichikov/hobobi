<?php

require_once(dirname(__FILE__) . '/../vendor/autoload.php');
require_once(dirname(__FILE__) . '/../engine/hoboBi.php');

$config = '';
if (!empty($argv[1])){
	$config = trim($argv[1]);
}

$hoboBi = new \hobotix\HoboBI($config);