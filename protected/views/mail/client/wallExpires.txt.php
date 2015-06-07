<?php
t('Your Conexting wall {name} is expiring today at {time}.',array(
	'{name}'=>'"'.$wall->name.'"',
	'{time}'=>$wall->getExpirationTime('H:i')
));
echo ' ';
t('No new messages or votes can be received after the wall has expired.');
echo ' ';
t('The contents of the wall will be online until {date}.',array('{date}'=>$wall->dyingDate));
echo PHP_EOL.PHP_EOL;

if( $wall->premium ) {
	$action = g('extend your Premium wall');
} else {
	$action = g('upgrade your wall to Premium');
}
t('If you would like to continue using the wall, you can {action}.',array(
	'{action}'=>$action
));

if( !$wall->premium ) {
	echo ' ';
	t('You can extend your Free wall without upgrading to Premium after it has been expired.');
}
echo ' ';
t('Otherwise you can ignore this email.');
echo PHP_EOL.PHP_EOL;
t('Login to Conexting: {url}',array(
	'{url}'=>Yii::app()->createAbsoluteUrl('client/index',array('utm_medium'=>'email','utm_source'=>$utm_source,'utm_campaign'=>$utm_campaign,'utm_content'=>'LogInToAccaunt'))
));
