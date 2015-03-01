<?php
return array(
	'licenseid' => "INTEGER UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
	'clientid' => "INTEGER UNSIGNED NOT NULL",
	'expires' => "DATETIME NOT NULL",
	'wallremovedafter' => "VARCHAR(32) NOT NULL",
);
