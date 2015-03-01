<?php
return array(
	'paymentid' => "INTEGER UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT",
	'contactid' => "INTEGER UNSIGNED NOT NULL",
	'created' => "DATETIME NOT NULL",
	'confirmed' => "DATETIME",
	'deleted' => "DATETIME",
	'paid' => "DATETIME",
	'pending' => "BOOLEAN NOT NULL DEFAULT FALSE",
	'claimed' => "BOOLEAN NOT NULL DEFAULT FALSE",
	'title' => "VARCHAR(63) NOT NULL",
	'code' => "VARCHAR(63) NOT NULL",
	'amount' => "INTEGER UNSIGNED NOT NULL",
	'price' => "DECIMAL(7,2)",
	'vat' => "DECIMAL(4,2)",
	'discount' => "DECIMAL(4,2)",
);
