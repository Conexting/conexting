<h1><?php t('All walls'); ?></h1>
<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'type'=>'striped bordered condensed',
	'dataProvider'=>$data,
	'ajaxUpdate'=>false,
	'columns'=>array(
		array(
			'name'=>'name',
			'value'=>function($data){
				$str = CHtml::link($data->name,$data->url);
				if( $data->premium ) {
					$str .= '<a class="badge badge-premium pull-right" data-toggle="modal" href="#premiumInfo'
						.'" title="'.g('This is a Premium wall.').' '.g('All Premium features are available.').'" rel="tooltip">'
						.g('Premium').' <i class="fa fa-check-square-o"></i></a>';
				} else if( $data->hasPremiumFeatures ) {
					$str .= '<a class="badge badge-premiumfeatures pull-right" data-toggle="modal" href="#premiumInfo'
						.'" title="'.g('This wall uses Premium features.').' '.g('You can select a suitable Premium product when publishing this wall.').'" rel="tooltip">'
						.g('Premium').' <i class="fa fa-credit-card"></i></a>';
				}
				if( !$data->isPublished ) {
					$str .= ' <i class="fa fa-eye-slash" title="'.g('Wall is not published').'"></i>';
				} else if( $data->hidden ) {
					$str .= ' <i class="fa fa-eye-slash" title="'.g('Hidden').'"></i>';
				}
				return $str;
			},
			'type'=>'raw',
			'cssClassExpression'=>'$data->deleted ? "deleted" : ""',
		),
		array(
			'name'=>'created',
			'value'=>'$data->creationDate',
		),
		array(
			'name'=>'expires',
			'value'=>function($data){
				$str = '';
				if( !is_null($data->expires) ) {
					$str .= $data->expirationDate.' ';
				}
				if( $data->isExpired ) {
					$str .= ' <span class="label label-warning">'.g('Expired').'</span>';
				}
				return $str;
			},
			'type'=>'raw'
		),
		array(
			'name'=>'dies',
			'value'=>function($data){
				$str = $data->dyingDate;
				if( is_null($data->deleted) && $data->removedInDays <= Yii::app()->params['cnxConfig']['notifyBeforeWallDiesDays'] ) {
					$str .= ' <span class="label label-important">'.g('In {n} day|In {n} days',$data->removedInDays).'</span>';
				}
				return $str;
			},
			'type'=>'raw'
		),
		array(
			'header'=>'',
			'value'=>function($data){
				if( !$data->isPublished ) {
					$linkText = '<i class="fa fa-comments-o"></i> '.g('Publish wall');
				} else if( !$data->isExpired && !$data->premium ) {
					$linkText = '<i class="fa fa-cloud-upload"></i> '.g('Upgrade to Premium');
				} else {
					$linkText = '<i class="fa fa-plus-square"></i> '.g('Extend wall use');
				}
				return CHtml::link($linkText,Yii::app()->createUrl('client/wallPublish',array('search'=>$data->name)));
			},
			'type'=>'raw'
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{update} {view} {delete}{undelete}',
			'updateButtonUrl'=>'Yii::app()->createUrl("client/wallSettings",array("search"=>$data->name,"from"=>"walls"))',
			'viewButtonUrl'=>'Yii::app()->createUrl("client/wallTheme",array("search"=>$data->name,"from"=>"walls"))',
			'deleteButtonUrl'=>'Yii::app()->createUrl("client/wallDelete",array("search"=>$data->name,"from"=>"walls"))',
			'buttons'=>array(
				'update'=>array(
					'label'=>g('Settings'),
					'visible'=>'$data->deleted==null'
				),
				'view'=>array(
					'label'=>g('Theme'),
					'visible'=>'$data->deleted==null'
				),
				'delete'=>array(
					'visible'=>'$data->deleted==null'
				),
				'undelete'=>array(
					'visible'=>'$data->deleted!=null',
					'label'=>g('Undelete'),
					'url'=>'Yii::app()->createUrl("client/wallUndelete",array("search"=>$data->name,"id"=>$data->wallid,"from"=>"walls"))',
					'icon'=>'refresh'
				),
			),
			'updateButtonIcon'=>'edit',
			'deleteConfirmation'=>false,
		),
	),
)); ?>

<p>
<?php if( $showDeleted ) {
	echo CHtml::link(g('Hide deleted walls'),$this->createUrl('',array('showDeleted'=>null)));
} else {
	echo CHtml::link(g('Show deleted walls'),$this->createUrl('',array('showDeleted'=>true)));
}
?>
</p>