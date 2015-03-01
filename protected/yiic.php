<?php
$cnxConfig = require(dirname(__FILE__).'/../config.php');
$config = dirname(__FILE__).'/config/console.php';
$yiic = $cnxConfig['yiiPath'].'/yiic.php';

require('globals.php');
require_once($yiic);
