<?php
$cnxConfig = require(dirname(__FILE__).'/config.php');
$config = dirname(__FILE__).'/protected/config/site.php';
$yii = $cnxConfig['yiiPath'].'/yii.php';

require('protected/globals.php');
require_once($yii);
Yii::createWebApplication($config)->run();
