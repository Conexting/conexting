<!DOCTYPE html><html>
	<head>
		<title><?php echo $subject; ?></title>
		<style type="text/css">
			@media all and (max-width: 480px) {
				table.container {
					width: auto !important;
				}
			}
		</style>
	</head>
	<body style="margin: 0; padding: 0; background-color: #FFFFFF;">
		<table class="container" style="cell-spacing: 0; border-collapse: collapse; border-spacing: 0;" width="480px">
			<tr height="15px">
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td width="5px"> </td>
				<td width="470px">
					<h1 style="font-size: 1.1em;"><?php echo $subject; ?></h1>
					<?php echo $content; ?>
				</td>
				<td width="5px"> </td>
			</tr>
			<tr height="25px">
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td><?php echo CHtml::link(CHtml::image($this->imageFileAbsolute('conexting_small'),'Conexting',array('style'=>'border: 0;')),Yii::app()->createAbsoluteUrl('site/index',array('utm_medium'=>'email','utm_source'=>$utm_source,'utm_campaign'=>$utm_campaign,'utm_content'=>'bottomLogo'))); ?></td>
				<td></td>
			</tr>
			<tr height="15px">
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</table>
	</body>
</html>