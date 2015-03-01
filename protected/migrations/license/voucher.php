<?php
return array(
	'voucherid' => "INTEGER UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
	'name' => "VARCHAR(128) NOT NULL",
	'code' => "VARCHAR(128) NOT NULL",
	'walllength' => "VARCHAR(32) NOT NULL",
	'wallremovedafter' => "VARCHAR(32) NOT NULL",
	'wallsmscredit' => "INTEGER UNSIGNED NOT NULL DEFAULT 0",
	'expires' => "DATETIME NOT NULL",
	'count' => "INTEGER NOT NULL DEFAULT 1",
	'active' => "BOOLEAN NOT NULL DEFAULT TRUE",
);
