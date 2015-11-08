<?php
Yii::app()->clientScript->registerScriptFile('//cdnjs.cloudflare.com/ajax/libs/jquery-scrollTo/1.4.14/jquery.scrollTo.min.js');
$this->jsFile('visualize');
$this->cssFile('visualize');
?>
<h1><?php echo $this->wall->title; ?></h1>
<?php
if( $this->wall->enablesms && !$this->wall->isExpired && is_null($this->wall->smsDefaultQuestion) ) {
	echo CHtml::tag('p',array(),g('Send a message by SMS to <b class="example">{number}</b> using format <b class="example">{prefix} <i>Your message...</i></b> (notice spaces!)',array(
		'{number}'=>$this->wall->smsCurrentNumber,
		'{prefix}'=>trim($this->wall->sms)
	)));
}
if( $this->wall->enabletwitter && !$this->wall->isExpired ) {
	echo CHtml::tag('p',array(),g('Send a message by Twitter using <b>{hashtag} <i>Your message...</i></b>',array(
		'{hashtag}'=>$this->wall->twitter
	)));
}
?>
<?php
$this->widget('ChatView',array(
	'options'=>array(
		'url'=>strtr($this->createUrl('wall/chat',array('cmd'=>'commandPlaceholder')),array('commandPlaceholder'=>'{cmd}')),
		'isAdmin'=>false,
		'refreshRate'=>$this->wall->premium ? 10 : 20,
		'allowReply'=>false,
		'threaded'=>false,
		'highlightNewAfter'=>false,
		'showUserImages'=>(bool)$this->wall->themeModel->showUserImages,
		'showTimestamps'=>(bool)$this->wall->themeModel->showTimestamps,
	),
	'showMsg'=>false,
	'showPinnedBox'=>true,
));
?>
