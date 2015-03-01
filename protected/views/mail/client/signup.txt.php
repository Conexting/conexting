<?php
t('Welcome to Conexting!');
echo PHP_EOL.PHP_EOL;
t('You have successfully created your Conexting account.');
echo ' ';
t('Please use the following account name and password to log in.');
echo PHP_EOL.PHP_EOL;
echo g('Account name').': '.$client->name.PHP_EOL;
echo g('Account email').': '.$client->email.PHP_EOL;
echo g('Password').': '.$password.PHP_EOL;
echo g('Log in to your account').': '.$this->createAbsoluteUrl('client/login').PHP_EOL;
echo PHP_EOL.PHP_EOL;
t('You can try Conexting for free by creating your Free wall.');
?>
