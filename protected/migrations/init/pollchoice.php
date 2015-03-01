<?php
return array(
	'pollid' => "INTEGER UNSIGNED NOT NULL",
	'choice' => "INTEGER UNSIGNED NOT NULL",
	'votes' => "INTEGER UNSIGNED NOT NULL DEFAULT 0",
	'text' => "VARCHAR(255) NOT NULL",
	'PRIMARY KEY (pollid,choice)',
);
