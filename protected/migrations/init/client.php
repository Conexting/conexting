<?php
return array(
	'clientid' => "INTEGER UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
	'name' => "VARCHAR(127) NOT NULL",
	'email' => "VARCHAR(255) NOT NULL",
	'created' => "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP",
	'credits' => "INTEGER UNSIGNED NOT NULL DEFAULT 0",
	'deleted' => "DATETIME",
	'modified' => "DATETIME",
	'password' => "CHAR(40)",
	'INDEX (name)',
	'INDEX (email)'
);
