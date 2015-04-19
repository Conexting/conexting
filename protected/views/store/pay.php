<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'type'=>'horizontal',
));
?>
<div class="well <?php echo $payment->confirmed && $payment->paid ? 'span7' : 'row-fluid'; ?>">
	<div class="span6">
		<h2><?php t('Purchase details'); ?></h2>
<?php
$attributes = array(
	'title',
);
if( $payment->Wall ) {
	$startDate = null;
	if( $payment->Wall->premium ) {
		// Extending purchased Premium-wall extension starts when current purchase expires
		$startDate = $payment->Wall->expires;
	}
	$attributes[] = 'Wall.displayTitle';
	$attributes[] = array(
		'name'=>'Wall.expires',
		'value'=>Wall::intervalDate($option['length'],$startDate)->format('j.n.Y H:i')
	);
	$attributes[] = array(
		'name'=>'Wall.dies',
		'value'=>Wall::intervalDate($option['removedAfter'],$startDate)->format('j.n.Y H:i')
	);
} else if( $payment->License ) {
	$attributes[] = array(
		'name'=>'License.expires',
		'value'=>Wall::intervalDate($option['length'])->format('j.n.Y H:i')
	);
}
$attributes[] = 'priceDisplay';
$this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$payment,
	'attributes'=>$attributes,
));
?>
		<div class="">
		<?php
		if( !$payment->confirmed || !$payment->paid ) {
			if( !$payment->confirmed ) {
				$confirmText = g('Confirm and proceed to payment');
			} else {
				$confirmText = g('Proceed to payment');
			}
			$this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'submit','htmlOptions'=>array('name'=>'pay'),'type'=>'primary','label'=>$confirmText,'icon'=>'ok'));
			echo ' ';
			$this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'submit','htmlOptions'=>array('name'=>'cancel'),'label'=>g('Cancel'),'icon'=>'remove'));
		} else {
			$this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'link','htmlOptions'=>array('name'=>'pay'),'type'=>'primary','label'=>g('Back to my payment history'),'icon'=>'arrow-left','url'=>$this->createUrl('store/payments')));
		}
		?>
		</div>
	</div>
	
<?php if( !$payment->confirmed || !$payment->paid ) { ?>
	<div class="span6">
	<h3><?php t('Contact information'); ?></h3>
<?php
$this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$payment->Contact,
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
			'value'=>$payment->Contact->countryName
		)),
));
?>
	</div>
<?php } ?>
	
</div>
<?php $this->endWidget(); ?>
