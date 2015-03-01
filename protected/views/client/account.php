<div class="row-fluid">
	<div class="span6">
		<h2><?php t('Account details'); ?></h2>
		<?php $this->widget('bootstrap.widgets.TbDetailView',array(
			'data'=>Yii::app()->user->client,
			'attributes'=>array('name','email'),
		)); ?>
		<p>
			<?php $this->widget('bootstrap.widgets.TbButton',array('label'=>g('View my payment history'),'url'=>$this->createUrl('store/payments'))); ?>
		</p>
	</div>

	<div class="span6">
	<h2><?php t('Contact information'); ?></h2>
<?php
if( Yii::app()->user->client->Contact ) {
	$this->widget('bootstrap.widgets.TbDetailView',array(
		'data'=>Yii::app()->user->client->Contact,
		'nullDisplay'=>'<span class="muted">-</span>',
		'attributes'=>array(
			'forname',
			'surname',
			'street',
			'zipcode',
			'zip',
			'phone',
			'mobile',
			'organization',
			array(
				'name'=>'country',
				'value'=>Yii::app()->params["countries"][Yii::app()->user->client->Contact->country]
			)),
	));
	echo '<p>'.CHtml::link(g('Change account details'),$this->createUrl('client/accountEdit')).'</p>';
} else {
	echo '<div class="alert">';
	echo '<strong>'.g('Notice!').'</strong> ';
	echo g('You have not provided your contact information.');
	echo ' '.g('Please <a href="{url}">complete your account details</a>.',array('{url}'=>$this->createUrl('client/accountEdit')));
	echo '</div>';
}
?>
	</div>
</div>