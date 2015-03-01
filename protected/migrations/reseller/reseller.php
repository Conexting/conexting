<?php
return array(
	'resellerid' => "INTEGER UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
	'name' => "VARCHAR(127) NOT NULL",
	'email' => "VARCHAR(255) NOT NULL",
	'created' => "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP",
	'deleted' => "DATETIME",
	'password' => "CHAR(40)",
	'INDEX (name)',
	'INDEX (email)'
);
