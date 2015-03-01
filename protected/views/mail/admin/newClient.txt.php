<?php
t('You have been granted client access to Conexting web service.');
echo ' ';
t('Please use the following account name and password to log in.');
echo PHP_EOL.PHP_EOL;
echo g('Account name').': '.$client->name.PHP_EOL;
echo g('Account email').': '.$client->email.PHP_EOL;
echo g('Password').': '.$password.PHP_EOL;
echo g('Login address').': '.$this->createAbsoluteUrl('client/login'),$this->createAbsoluteUrl('client/login').PHP_EOL;
?>
