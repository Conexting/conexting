<?php
return array(
	'pollvoteid' => "INTEGER UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
	'pollid' => "INTEGER UNSIGNED NOT NULL",
	'choice' => "INTEGER UNSIGNED NOT NULL",
	'senderhash' => "CHAR(40) NOT NULL",
);
