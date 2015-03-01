<?php
return array(
	'messageid' => "INTEGER UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
	'wallid' => "INTEGER UNSIGNED NOT NULL",
	'timestamp' => "DATETIME NOT NULL",
	'tweetid' => "BIGINT UNSIGNED",
	'smsid' => "INTEGER UNSIGNED",
	'deleted' => "DATETIME",
	'replyto' => "INTEGER UNSIGNED",
	'username' => "VARCHAR(127)",
	'text' => "TEXT NOT NULL"
);
