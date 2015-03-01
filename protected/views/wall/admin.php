<h3><?php t('Wall views'); ?></h3>
<ul>
	<li><?php echo CHtml::link(g('Screen view'),$this->createUrl($this->id.'/view')); ?> - <?php t('Large screen optimized view to show messages and votes. You can use browser\'s full screen option (keyboard shortcut F11) and zoom as necessary.'); ?></li>
</ul>

<?php if( $this->wall->premoderated ) { ?>
<h3><?php t('Moderating'); ?></h3>
<ul>
	<li><?php echo CHtml::link(g('Message approval queue'),$this->createUrl($this->id.'/queue')); ?> - <?php t('Messages awaiting approval'); ?></li>
</ul>
<?php } ?>

<h3><?php t('Message reports'); ?></h3>
<ul>
	<li><?php echo CHtml::link(g('All messages'),$this->createUrl($this->id.'/messages')); ?> - <?php t('View and search all messages (inc. answers to all questions)'); ?></li>
	<li><?php echo CHtml::link(g('Removed messages'),$this->createUrl($this->id.'/removedMessages')); ?> - <?php t('View, search and restore removed messages'); ?></li>
	<li><?php echo CHtml::link(g('Report'),$this->createUrl($this->id.'/report')); ?> - <?php t('Combined social wall report'); ?></li>
</ul>

<h3><?php t('Questions'); ?></h3>
<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'type'=>'striped bordered condensed',
	'dataProvider'=>$questions,
	'summaryText'=>'',
	'columns'=>array(
		array(
			'name'=>'position',
			'header'=>'#',
			'value'=>$this->wall->enablesms ? 'CHtml::encode($data->position).($data->smsdefault ? " ".CHtml::tag("i",array("class"=>"icon-envelope","title"=>g("SMS default"))) : "")' : 'CHtml::encode($data->position)',
			'type'=>'raw'
		),
		array('name'=>'keyword'),
		array('name'=>'title'),
		array('name'=>'question'),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>$this->wall->isExpired ? '{delete}' : '{update} {delete}',
			//'viewButtonUrl'=>'$this->grid->controller->createUrl("wall/questionView",array("id"=>$data->primaryKey,"phsh"=>$this->grid->controller->phsh()))',
			'updateButtonUrl'=>'$this->grid->controller->createUrl("wall/adminQuestion",array("id"=>$data->primaryKey))',
			'deleteButtonUrl'=>'$this->grid->controller->createUrl("wall/adminQuestionDelete",array("id"=>$data->primaryKey,"delete"=>"delete"))',
			'buttons'=>array(
				'update'=>array('label'=>g('Settings')),
			),
			'updateButtonIcon'=>'edit',
		),
	),
)); ?>
<?php if( !$this->wall->isExpired )  {
	$this->widget('bootstrap.widgets.TbButton', array(
	'label'=>g('Create new question'),
		'url'=>$this->createUrl($this->id.'/adminQuestion')
	));
} ?>

<h3><?php t('Polls'); ?></h3>
<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'type'=>'striped bordered condensed',
	'dataProvider'=>$polls,
	'summaryText'=>'',
	'columns'=>array(
		array(
			'name'=>'position',
			'header'=>'#',
			'value'=>$this->wall->enablesms ? 'CHtml::encode($data->position).($data->smsdefault ? " ".CHtml::tag("i",array("class"=>"icon-envelope","title"=>g("SMS default"))) : "")' : 'CHtml::encode($data->position)',
			'type'=>'raw'
		),
		array('name'=>'title'),
		array('name'=>'question'),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{update} {delete}',
			//'viewButtonUrl'=>'$this->grid->controller->createUrl("wall/pollView",array("id"=>$data->primaryKey,"phsh"=>$this->grid->controller->phsh()))',
			'updateButtonUrl'=>'$this->grid->controller->createUrl("wall/adminPoll",array("id"=>$data->primaryKey))',
			'deleteButtonUrl'=>'$this->grid->controller->createUrl("wall/adminPollDelete",array("id"=>$data->primaryKey,"delete"=>"delete"))',
			'buttons'=>array(
				'update'=>array('label'=>g('Settings')),
			),
			'updateButtonIcon'=>'edit',
		),
	),
)); ?>
<?php if( !$this->wall->isExpired )  {
	$this->widget('bootstrap.widgets.TbButton', array(
		'label'=>g('Create new poll'),
		'url'=>$this->createUrl($this->id.'/adminPoll')
	));
} ?>

<h3><?php t('Embed'); ?></h3>
<?php
$wallEmbedUrl = $this->createAbsoluteUrl($this->id.'/index',array('wall'=>$this->wall->name,'pshs'=>$this->phsh(),'embed'=>$this->ehsh()));
echo '<p>'.g('Use these links to embed your wall to your own web site.').'</p>';
echo '<ul>';
echo '<li>'.CHtml::link(g('Conversation'),$this->createUrl($this->id.'/index',array('wall'=>$this->wall->name,'pshs'=>$this->phsh(),'embed'=>$this->ehsh()))).'</li>';
foreach( $questions->data as $question ) {
	echo '<li>'.g('Question').': '.CHtml::link(CHtml::encode($question->title),$this->createUrl($this->id.'/question',array('search'=>$question->keyword,'pshs'=>$this->phsh(),'embed'=>$this->ehsh()))).'</li>';
}
foreach( $polls->data as $poll ) {
	echo '<li>'.g('Poll').': '.CHtml::link(CHtml::encode($poll->title),$this->createUrl($this->id.'/poll',array('search'=>$poll->keyword,'pshs'=>$this->phsh(),'embed'=>$this->ehsh()))).'</li>';
}
echo '</ul>';
echo '<p>'.g('Example code for embedding conversation').':</p>';
echo '<pre>';
echo CHtml::encode('<iframe src="'.$wallEmbedUrl.'" style="border: 0; width: 100%; height: 100%;" />');
echo '</pre>';
?>

<hr />
<p><?php echo CHtml::link(g('Exit wall admin tools.'),$this->createUrl($this->id.'/adminLogout')); ?></p>