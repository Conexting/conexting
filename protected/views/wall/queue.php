<h2><?php t('Message approval queue'); ?></h2>
<?php
$this->widget('ChatView',array(
	'options'=>array(
		'url'=>strtr($this->createUrl('wall/chat',array('queue'=>true,'question'=>'all','cmd'=>'commandPlaceholder')),array('commandPlaceholder'=>'{cmd}')),
		'isAdmin'=>$this->isWallAdmin(),
		'isQueue'=>true,
		'threaded'=>false,
		'messageCount'=>100
	),
	'showSearch'=>true,
	'showMsg'=>false,
	'showImages'=>true,
));
?>