<h1><?php echo $this->wall->title; ?></h1>
<h2><?php echo CHtml::encode($question->question) ?></h2>
<?php
if( $this->wall->enablesms && !$this->wall->isExpired ) {
	echo CHtml::tag('p',array(),g('Answer by SMS to <b class="example">{number}</b> using format <b class="example">{prefix} <i>Your answer...</i></b> (notice spaces!)',array(
		'{number}'=>$question->Wall->smsCurrentNumber,
		'{prefix}'=>trim($question->Wall->sms.' '.$question->smsPrefix)
	)));
}
if( $this->wall->enabletwitter && !$this->wall->isExpired ) {
	echo CHtml::tag('p',array(),g('Answer by Twitter using format <b class="example">{hashtag} {prefix} <i>Your answer...</i></b>',array(
		'{hashtag}'=>$question->Wall->twitter,
		'{prefix}'=>$question->keyword
	)));
}
?>
<?php
$this->widget('ChatView',array(
	'prependMsg'=>$question->keyword,
	'showMsg'=>false,
	'options'=>array(
		'url'=>strtr($this->createUrl('wall/chat',array('question'=>$question->primaryKey,'cmd'=>'commandPlaceholder')),array('commandPlaceholder'=>'{cmd}')),
		'isAdmin'=>false,
		'refreshRate'=>$this->wall->premium ? 10 : 20,
		'allowReply'=>false,
		'threaded'=>(bool)$this->wall->threaded,
		'showUserImages'=>(bool)$this->wall->themeModel->showUserImages,
		'showTimestamps'=>(bool)$this->wall->themeModel->showTimestamps,
	)
));
?>
