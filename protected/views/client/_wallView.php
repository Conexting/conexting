<?php
if( $data->premium ) {
	$premiumBadge = '<a class="badge badge-premium pull-right" data-toggle="modal" href="#premiumInfo'
		.'" title="'.g('This is a Premium wall.').' '.g('All Premium features are available.').'" rel="tooltip">'
		.g('Premium').' <i class="fa fa-check-square-o"></i></a>';
} else if( $data->hasPremiumFeatures ) {
	$premiumBadge = '<a class="badge badge-premiumfeatures pull-right" data-toggle="modal" href="#premiumInfo'
		.'" title="'.g('This wall uses Premium features.').' '.g('You can select a suitable Premium product when publishing this wall.').'" rel="tooltip">'
		.g('Premium').' <i class="fa fa-credit-card"></i></a>';
} else {
	$premiumBadge = '';
}

if( $data->published && $data->hidden ) {
	$status = 'wallHidden';
	$statusText = g('The wall is published but currently hidden');
	$statusIcon = 'fa fa-eye-slash fa-3x';
	$statusAction = 'Show';
	$statusActionLabel = '<i class="fa fa-eye"></i> '.g('Show');
	$statusActionType = 'primary';
} else if( $data->published ) {
	$status = 'wallPublished';
	$statusText = g('The wall is published');
	$statusIcon = 'fa fa-comments-o fa-3x';
	$statusAction = 'Hide';
	$statusActionLabel = '<i class="fa fa-eye-slash"></i> '.g('Hide');
	$statusActionType = 'warning';
} else {
	$status = 'wallUnpublished';
	$statusText = g('Not yet published');
	$statusIcon = 'fa fa-pencil-square-o fa-3x';
	$statusAction = 'Publish';
	$statusActionLabel = '<i class="fa fa-comments-o"></i> '.g('Publish');
	$statusActionType = 'primary';
}
?>

<div class="wallView panel panel-default <?php echo $status; ?>">
	<h2>
		<?php echo $premiumBadge; ?>
		<?php echo CHtml::encode($data->displayTitle); ?>
		<?php echo CHtml::link($data->url,$data->url); ?>
	</h2>
	<div class="wallInfo">
		<div class="row-fluid">
			<div class="span3 status text-center">
				<p>
					
					<i class="<?php echo $statusIcon; ?>"></i><br />
					<?php echo $statusText; ?>
				</p>
			</div>
			<div class="span3">
				<dl>
					<dt><?php t('Created'); ?>:</dt><dd><?php echo $data->creationTime; ?></dd>
					<?php if( $data->expires ) { ?>
						<dt><?php t('Expires'); ?>:</dt><dd><?php echo $data->expirationTime; ?></dd>
					<?php } else if( $data->dies ) { ?>
						<dt><?php t('Wall removed'); ?>:</dt><dd><?php echo $data->dyingDate; ?></dd>
					<?php } ?>
				</dl>
			</div>
			<div class="span3">
				<dl>
					<?php if( $data->enablesms ) { ?>
						<dt><?php t('SMS'); ?>:</dt><dd><?php echo $data->sms; ?></dd>
						<dd><?php t('{n} pcs left',$data->smscredit); ?></dd>
					<?php } ?>
					<?php if( $data->enabletwitter ) { ?>
						<dt><?php t('Twitter'); ?>:</dt><dd><?php echo $data->twitter; ?></dd>
					<?php } ?>
				</dl>
			</div>
			<div class="span3">
				<dl>
					<?php if( $data->isPublished ) { ?>
						<dt><?php t('Content'); ?>:</dt>
						<dd><?php echo g('{n} message|{n} messages',count($data->Messages)); ?></dd>
						<dd><?php echo g('{n} question|{n} questions',count($data->Questions)); ?></dd>
						<dd><?php echo g('{n} poll|{n} polls',count($data->Polls)); ?></dd>
					<?php } ?>
				</dl>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span3">
				<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'link','block'=>true,'type'=>$statusActionType,'encodeLabel'=>false,'label'=>$statusActionLabel,'url'=>$this->createUrl('client/wall'.$statusAction,array('search'=>$data->name)))); ?>
			</div>
			<div class="span9">
				<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'link','type'=>'default','encodeLabel'=>false,'label'=>'<i class="fa fa-pencil"></i> '.g('Settings'),'url'=>$this->createUrl('client/wallSettings',array('search'=>$data->name)))); ?>
				<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'link','type'=>'default','encodeLabel'=>false,'label'=>'<i class="fa fa-eye"></i> '.g('Theme'),'url'=>$this->createUrl('client/wallTheme',array('search'=>$data->name)))); ?>
				<?php if( !$data->premium && $data->isPublished ) {
					$this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'link','type'=>'primary','encodeLabel'=>false,'label'=>'<i class="fa fa-cloud-upload"></i> '.g('Upgrade'),'url'=>$this->createUrl('client/wallUpgrade',array('search'=>$data->name))));
				} ?>
				<div class="pull-right">
					<?php $this->widget('bootstrap.widgets.TbButton',array('buttonType'=>'link','type'=>'danger','encodeLabel'=>false,'label'=>'<i class="fa fa-trash"></i> '.g('Delete'),'url'=>$this->createUrl('client/wallDelete',array('search'=>$data->name)))); ?>
				</div>
			</div>
		</div>
	</div>
</div>