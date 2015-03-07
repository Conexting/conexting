<?php
return CMap::mergeArray(
	require('common.php'),
	array(
		'components'=>array(
			'request'=>array(
				'hostInfo' => 'http://conexting.com',
				'baseUrl' => '',
				'scriptUrl' => '',
			),
		),
		'import'=>array(
			'application.commands.shell*',
		),
		'commandMap'=>array(
			'migrate'=>array(
				'class'=>'system.cli.commands.MigrateCommand',
				'migrationPath'=>'application.migrations',
				'migrationTable'=>'{{tbl_migration}}',
				'connectionID'=>'db',
				'templateFile'=>'application.migrations.template',
			)
		),
		'params'=>array(
		),
	),
	$cnxConfig['yiiconfig']['console']
);
