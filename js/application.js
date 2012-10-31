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
	var donation = $.url().param('donation');
	if(donation == 'complete') {
		window.setTimeout(function(){
			topBar('Thank You!  Your donation has been processed.  Please take a moment a share this website with your friends.', false);
		}, 1500);
	}else if(donation == 'error') {
		window.setTimeout(function(){
			topBar('Sorry,  your order has had an error.  Please contact us below, or try again later.', true);
		}, 1500);
	}
});
/**
 * add a topBar message
 * @param String message teh message to display
 * @return void 
 */
function topBar(message, isError) {
	var divClass = (isError === true) ? 'topbar error' : 'topbar';
  $("<div />",{'class': divClass, text: message}).hide().prependTo("body").slideDown('fast').delay(6000).slideUp(function() {$(this).remove();});
};