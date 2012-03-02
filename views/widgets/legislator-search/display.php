<?php echo $before_widget ?>
<?php if($title): ?>
	<?php echo $before_title . $title . $after_title ?>
<?php endif ?>
<form id="legislator_search_form">
	<input type="hidden" id="legislator_search_widget_id" value="<?php echo $widget->id ?>" />
	<p>
		<input type="text" name="" value="Zip Code" id="legislator_search_zip" class="field" />
	</p>
</form>
<div id="legislator_search_response">	
</div>
<?php echo $after_widget ?>

