<?php
return array(
	'tweetid' => "BIGINT UNSIGNED NOT NULL",
	'user_id' => "INTEGER UNSIGNED",
	'user_screen' => "VARCHAR(127)",
	'user_name' => "VARCHAR(255)",
	'user_image' => "VARCHAR(255)",
	'replyto' => "BIGINT UNSIGNED",
	'retweetof' => "BIGINT UNSIGNED",
	'UNIQUE KEY (tweetid)',
);
