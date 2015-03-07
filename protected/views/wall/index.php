<?php
if( $this->wall->enablestream ) {
	echo '<div class="stream">';
	$this->widget('StreamView',array(
		'providerId'=>$this->wall->streamprovider,
		'streamId'=>$this->wall->streamid
	));
	echo '</div>';
}
?>
<h1><?php echo $this->wall->title; ?></h1>
<h2><?php echo $this->wall->themeModel->conversationTitle; ?></h2>
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
		'isAdmin'=>$this->isWallAdmin(),
		'refreshRate'=>$this->wall->premium ? 10 : 20,
		'threaded'=>(bool)$this->wall->threaded,
	),
	'showMsg'=>!$this->wall->isExpired
));
?>
