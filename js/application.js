jQuery(document).ready(function($) {
	/**
	 * Submit the form 
	 */
	$('#donate-link').click(function() {
		var donateAmount = $('input#d-amount').val();
		if((donateAmount == '') || (donateAmount == 'e.g. 10, 20, 100.. ')) {
			alert('Please specify the donation amount.');
			return false;
		} else{
			$(this).parent('form').submit();
			return false;
		}
	});
});
