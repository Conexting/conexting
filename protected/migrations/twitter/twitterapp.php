<?php
return array(
	'appid' => "INTEGER UNSIGNED NOT NULL PRIMARY KEY",
	'name' => "VARCHAR(63) NOT NULL",
	'oauth_token'=>'VARCHAR(63) NOT NULL',
	'oauth_token_secret'=>'VARCHAR(63) NOT NULL',
	'oauth_consumer_key'=>'VARCHAR(63) NOT NULL',
	'oauth_consumer_secret'=>'VARCHAR(63) NOT NULL',
	'limit_refreshed'=>"DATETIME",
	'limit_search'=>"INTEGER UNSIGNED",
	'limit_search_remaining'=>"INTEGER UNSIGNED",
	'limit_reset'=>"INTEGER UNSIGNED",
);
