<?php
return array(
	'pollid' => "INTEGER UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
	'wallid' => "INTEGER UNSIGNED NOT NULL",
	'position' => "INTEGER UNSIGNED DEFAULT 1", // Hidden poll if position=NULL
	'title' => "VARCHAR(255) NOT NULL",
	'question' => "VARCHAR(255) NOT NULL",
	'deleted' => "DATETIME",
);
