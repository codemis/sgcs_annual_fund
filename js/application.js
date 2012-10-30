jQuery(document).ready(function($) {
	/**
	 * Submit the form 
	 */
	$('a.form-process-button').click(function() {
		var link = $(this);
		if(link.hasClass('disabled')) {
			return false;
		}
		if($('input#d-amount').length > 0) {
			var donateAmount = $('input#d-amount').val();
			if((donateAmount == '') || (donateAmount == 'e.g. 10, 20, 100.. ')) {
				$('div#donate-error-msg').html('<p>Please provide a donation amount.</p>');
				$('input#d-amount').addClass('error');
				return false;
			}
		}
		link.addClass('disabled');
		link.children('span:first').text('Processing....');
		link.parent('form').submit();
		return false;
	});
});
