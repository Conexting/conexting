<?php
$cnxConfig = require(dirname(__FILE__).'/../../config.php');
$cnxParams = require(dirname(__FILE__).'/../../params.php');

Yii::setPathOfAlias('bootstrap',dirname(__FILE__).'/../extensions/bootstrap');

return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>$cnxConfig['name'],
	'language'=>$cnxConfig['language'],
	'preload'=>array(
		'log'
	),
	'import'=>array(
		'application.components.*',
		'application.models.*',
		'application.forms.*',
		'application.validators.*',
		'ext.mail.YiiMailMessage',
		'application.widgets.*',
		'zii.widgets.jui.*',
	),
	'modules'=>array(
	),
	'components'=>array(
		'urlManager'=>array(
			'class'=>'UrlManager',
			'urlFormat'=>'path',
			'showScriptName' => false,
			'rules'=>array(
				'<language:('.implode('|',array_keys($cnxConfig['languages'])).')>' => 'site/index',
				'' => 'site/index',

				'<language:('.implode('|',array_keys($cnxConfig['languages'])).')>/<controller:('.implode('|',$cnxConfig['controllers']).')>'=>'<controller>/index',
				'<language:('.implode('|',array_keys($cnxConfig['languages'])).')>/<controller:('.implode('|',$cnxConfig['controllers']).')>/<action:\w+>/<search:[\w\-\+\._]+>/<id:\d+(-\d+)?>'=>'<controller>/<action>',
				'<language:('.implode('|',array_keys($cnxConfig['languages'])).')>/<controller:('.implode('|',$cnxConfig['controllers']).')>/<action:\w+>/<id:\d+(-\d+)?>'=>'<controller>/<action>',
				'<language:('.implode('|',array_keys($cnxConfig['languages'])).')>/<controller:('.implode('|',$cnxConfig['controllers']).')>/<action:\w+>/<search:[\w\-\+\._]+>'=>'<controller>/<action>',
				'<language:('.implode('|',array_keys($cnxConfig['languages'])).')>/<controller:('.implode('|',$cnxConfig['controllers']).')>/<action:\w+>'=>'<controller>/<action>',

				'<controller:('.implode('|',$cnxConfig['controllers']).')>'=>'<controller>/index',
				'<controller:('.implode('|',$cnxConfig['controllers']).')>/<action:\w+>/<search:[\w\-\+\._]+>/<id:\d+(-\d+)?>'=>'<controller>/<action>',
				'<controller:('.implode('|',$cnxConfig['controllers']).')>/<action:\w+>/<id:\d+(-\d+)?>'=>'<controller>/<action>',
				'<controller:('.implode('|',$cnxConfig['controllers']).')>/<action:\w+>/<search:[\w\-\+\._]+>'=>'<controller>/<action>',
				'<controller:('.implode('|',$cnxConfig['controllers']).')>/<action:\w+>'=>'<controller>/<action>',
				
				'<wall:[\w\-\+\._]+>'=>'wall/index',
				'<wall:[\w\-\+\._]+>/<action:\w+>/<id:\d+(-\d+)?>'=>'wall/<action>',
				'<wall:[\w\-\+\._]+>/<action:\w+>/<search:[\w\-\+\._ ]+>'=>'wall/<action>',
				'<wall:[\w\-\+\._]+>/<action:\w+>'=>'wall/<action>',
			),
		),
		'db'=>$cnxConfig['db'],
		'mail' => array(
			'class' => 'ext.mail.YiiMail',
			'transportType' => 'smtp',
			'transportOptions' => $cnxConfig['mail'],
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'trace',
				),
			),
		),
	),
	'params'=>CMap::mergeArray($cnxParams,array(
		'cnxConfig'=>$cnxConfig,
		'fromEmail'=>$cnxConfig['fromEmail'],
		'languages'=>$cnxConfig['languages'],
		'illegalWallNames'=>$cnxConfig['illegalWallNames']+$cnxConfig['controllers'],
		'store'=>$cnxConfig['store'],
	)),
);
