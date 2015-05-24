<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'type'=>'horizontal',
));
?>
<div class="well">
	<div class="row-fluid">
	<div class="span6">
		<h2><?php t('Redeem voucher'); ?></h2>
<?php
$attributes = array(
	'Voucher.name',
	'Voucher.code',
);
$startDate = null;
if( $wall->premium ) {
	// Extending purchased Premium-wall extension starts when current purchase expires
	$startDate = $wall->expires;
}
$attributes[] = 'displayTitle';
$attributes[] = array(
	'name'=>'expires',
	'value'=>Wall::intervalDate($voucher->walllength,$startDate)->format('j.n.Y H:i')
);
$attributes[] = array(
	'name'=>'dies',
	'value'=>Wall::intervalDate($voucher->wallremovedafter,$startDate)->format('j.n.Y H:i')
);
$this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$wall,
	'attributes'=>$attributes,
));
?>
		<div class="">
		<?php
		$this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'submit','htmlOptions'=>array('name'=>'redeem'),'type'=>'primary','label'=>g('Confirm'),'icon'=>'ok'));
		echo ' ';
		$this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'submit','htmlOptions'=>array('name'=>'cancel'),'label'=>g('Cancel'),'icon'=>'remove'));
		?>
		</div>
	</div>
	<div class="span6">
	<h3><?php t('Contact information'); ?></h3>
<?php
$this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$contact,
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
			'value'=>$contact->countryName
		)),
));
?>
	</div>
</div>
</div>
<?php $this->endWidget(); ?>
