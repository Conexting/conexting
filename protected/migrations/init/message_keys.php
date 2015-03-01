<?php
return array(
	'wallid' => array('wall','wallid','CASCADE','CASCADE'),
	'replyto' => array('message','messageid','SET NULL','CASCADE'),
	'smsid' => array('sms','smsid','SET NULL','CASCADE'),
	'tweetid' => array('tweet','tweetid','SET NULL','CASCADE'),
);
