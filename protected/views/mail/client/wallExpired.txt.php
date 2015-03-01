<?php
t('Your Conexting wall {name} is expiring today and will not receive any new messages afterwards.',array('{name}'=>'"'.$wall->name.'"'));
echo PHP_EOL.PHP_EOL;
t('The contents of the wall will still be online for three months.');
echo ' ';
t('If you would like to continue using the wall, you can extend the wall use using the following url: {url}',array('{url}'=>Yii::app()->createAbsoluteUrl('client/extendWall',array('search'=>$wall->name,'urllogin'=>$wall->Client->getLoginUrlHash(),'utm_medium'=>'email','utm_source'=>$utm_source,'utm_campaign'=>$utm_campaign,'utm_content'=>'ExtendWallUse'))));
echo PHP_EOL;
t('Otherwise you can ignore this email.');