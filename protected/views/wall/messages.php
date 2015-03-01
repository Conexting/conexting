<h2><?php echo $title ?></h2>
<?php
$this->widget('ChatView',array(
	'options'=>array(
		'url'=>strtr(
			$this->createUrl('wall/chat',array(
				'question'=>'all',
				'cmd'=>'commandPlaceholder',
				'removed'=>$showRemoved
			)),array('commandPlaceholder'=>'{cmd}')),
		'isAdmin'=>$this->isWallAdmin(),
		'messageCount'=>100
	),
	'showSearch'=>true,
	'showMsg'=>false,
	'showImages'=>false,
));
?>