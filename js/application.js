jQuery(document).ready(function($) {
	/**
	 * Submit the form 
	 */
	$('a.form-process-button').click(function() {
		var link = $(this);
		if(link.hasClass('disabled')) {
			return false;
		}
		var donateAmount = $('input#d-amount').val();
		if((donateAmount == '') || (donateAmount == 'e.g. 10, 20, 100.. ')) {
			alert('Please specify the donation amount.');
			return false;
		} else{
			link.addClass('disabled');
			link.children('span:first').text('Processing....');
			link.parent('form').submit();
			return false;
		}
	});
});
