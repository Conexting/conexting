<?php
return array(
	'userid' => "INTEGER UNSIGNED NOT NULL PRIMARY KEY",
	'screen_name' => "VARCHAR(127) NOT NULL",
	'oauth_token'=>'VARCHAR(63) NOT NULL',
	'oauth_token_secret'=>'VARCHAR(63) NOT NULL',
	'limit_refreshed'=>"DATETIME",
	'limit_search'=>"INTEGER UNSIGNED",
	'limit_search_remaining'=>"INTEGER UNSIGNED",
	'limit_reset'=>"INTEGER UNSIGNED",
);
