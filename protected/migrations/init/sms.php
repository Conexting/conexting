<?php
return array(
	'smsid' => "INTEGER UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
	'operator' => "VARCHAR(127)",
	'source' => "VARCHAR(63)",
	'destination' => "VARCHAR(63)",
	'keyword' => "VARCHAR(63)",
	'header' => "VARCHAR(255)",
	'text' => "TEXT",
	'binary' => "TEXT"
);
