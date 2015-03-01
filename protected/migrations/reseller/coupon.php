<?php
return array(
	'couponid' => "INTEGER UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
	'resellerid' => "INTEGER UNSIGNED",
	'created' => "DATETIME NOT NULL",
	'deleted' => "DATETIME",
	'code' => "VARCHAR(16) NOT NULL",
	'active' => "BOOLEAN NOT NULL DEFAULT TRUE",
	'discount' => "DECIMAL(4,2)",
);
