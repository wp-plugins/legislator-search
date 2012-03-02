$(document).ready(function() {
	$('#legislator_search_zip').click(function() {
		if($(this).val() == 'Zip Code')
		{
			$(this).val('');
		}			
	});
	$('#legislator_search_zip').blur(function() {
		if($(this).val() == '')
		{
			$(this).val('Zip Code');
		}			
	});
	$('#legislator_search_form').submit(function() {
		$('#legislator_search_zip').addClass('legislator_search_zip_pending');
		$('#legislator_search_response').hide();			
		$.ajax({
			url: legislator_search.ajaxurl+"?action=legislator_search_ajax&widget_id="+$('#legislator_search_widget_id').val(),
			data: "zip="+$('#legislator_search_zip').val(),
			success: function(data, textStatus) {
				$('#legislator_search_response').val('');
				$('#legislator_search_response').html(data);
				$('#legislator_search_response').slideDown();
				$('#legislator_search_zip').removeClass('legislator_search_zip_pending');
			},
			dataType: "html"
		});
		return false;
	});		
});