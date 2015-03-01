<?php
return CMap::mergeArray(
	require('common.php'),
	array(
		'theme'=>'basic',
		'preload'=>array(
			'less'
		),
		'components'=>array(
			'user'=>array(
				'class'=>'WebUser',
				'allowAutoLogin'=>true,
				'autoRenewCookie'=>true,
				'loginUrl'=>array('client/login')
			),
			'session'=>array(
				'cookieParams'=>array(
					'path'=>'/'
				),
				'class'=>'CDbHttpSession',
				'sessionTableName'=>'{{session}}',
				'connectionID'=>'db',
				'timeout'=>60*60*2, // 2 hours
			),
			'errorHandler'=>array(
				'errorAction'=>'site/error',
			),
			'bootstrap'=>array(
				'class'=>'bootstrap.components.Bootstrap',
			),
			'less'=>array(
				'class'=>'ext.less.YiiLess',
				'paths'=>array(
					'protected/extensions/bootstrap/assets/css/bootstrap' => array(
						'file' => 'protected/extensions/bootstrap/assets/less/bootstrap.less',
						'modified' => filemtime('protected/extensions/bootstrap/assets/less/variables.less'),
					),
					'protected/extensions/bootstrap/assets/css/bootstrap-responsive' => array(
						'file' => 'protected/extensions/bootstrap/assets/less/responsive.less',
						'modified' => filemtime('protected/extensions/bootstrap/assets/less/variables.less'),
					),
				),
			),
			'twitter'=>array(
				'class'=>'Twitter',
				'appid'=>$cnxConfig['twitter']['messageApp']['id'],
				'appidWithUser'=>$cnxConfig['twitter']['loginApp']['id'],
			),
			'widgetFactory'=>array(
				'widgets'=>array(
					'ChatView'=>array(
						'options'=>array(
							'analytics'=>new CJavaScriptExpression("ga")
						),
					),
					'PollView'=>array(
						'options'=>array(
							'analytics'=>new CJavaScriptExpression('ga')
						),
					),
				),
			),
		),
	)
);
