<p>
	<label for="<?php echo $widget->get_field_id('title') ?>"><?php _e('Widget Title') ?>:</label>
	<br />
	<input class="widefat" id="<?php echo $widget->get_field_id('title') ?>" name="<?php echo $widget->get_field_name('title') ?>" value="<?php echo $title ?>" />		
</p>
<p>
	<label for="<?php echo $widget->get_field_id('api_key') ?>"><?php _e('Sunlight Labs API Key') ?>:</label>
	<br />
	<input class="widefat" id="<?php echo $widget->get_field_id('api_key') ?>" name="<?php echo $widget->get_field_name('api_key') ?>" value="<?php echo $api_key ?>" />		
</p>