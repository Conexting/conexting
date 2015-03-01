<?php
return array(
	'contactid' => "INTEGER UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
	'clientid' => "INTEGER UNSIGNED NOT NULL",
	'forname' => "VARCHAR(127) NOT NULL",
	'surname' => "VARCHAR(127) NOT NULL",
	'street' => "VARCHAR(255)",
	'zipcode' => "VARCHAR(32)",
	'zip' => "VARCHAR(32)",
	'country' => "CHAR(2) NOT NULL DEFAULT 'fi'", // Country code (ISO-3166)
	'phone' => "VARCHAR(32)",
	'mobile' => "VARCHAR(32)",
	'organization' => "VARCHAR(64)",
	'created' => "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP",
	'deleted' => "DATETIME",
	'modified' => "DATETIME"
);
