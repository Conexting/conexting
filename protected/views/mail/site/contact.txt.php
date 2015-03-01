<?php
t('New contact from Conexting online form');
echo PHP_EOL.PHP_EOL;
echo PHP_EOL.PHP_EOL;
echo g('Name').': '.$contact->name.PHP_EOL;
echo g('Email').': '.$contact->email.PHP_EOL;
echo PHP_EOL.PHP_EOL;
echo $contact->message;
?>
