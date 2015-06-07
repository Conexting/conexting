<?php
t('Your Conexting wall {name} has been expired since {date} and will be removed in {days} days.',array('{name}'=>'"'.$wall->name.'"','{date}'=>$wall->expirationDate,'{days}'=>Yii::app()->params['cnxConfig']['notifyBeforeWallDiesDays']));
echo PHP_EOL.PHP_EOL;
t('If you would like to still use the wall, you can extend the wall use using the following url: {url}',array('{url}'=>Yii::app()->createAbsoluteUrl('client/wallPublish',array('search'=>$wall->name,'urllogin'=>$wall->Client->getLoginUrlHash(),'utm_medium'=>'email','utm_source'=>$utm_source,'utm_campaign'=>$utm_campaign,'utm_content'=>'ExtendWallUse'))));
echo PHP_EOL;
t('Otherwise you can ignore this email.');
