jQuery(document).ready( function($) {
	$('input[name="csw_category[]"]').change(function() {
		var url = $('input[name="csw_category[]"]:checked').map(function() {
			return $(this).val();
		}).get().join('+');//debugger;
		if (url != "") {
			window.location.href = customs.home_url + '/category/' + url;
		} else {
			window.location.href = customs.home_url;
		}		
	});
});