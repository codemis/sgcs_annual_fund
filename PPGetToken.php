<?php
/**
 * This makes a request to Paypal to setup a new donation
 *
 * @author Johnathan Pulos
 */
require_once('vendor/paypal.php');
/**
 * Set the url for paypal redirect
 * sandbox = https://www.sandbox.paypal.com
 * paypal = https://www.paypal.com
 *
 * @author Johnathan Pulos
 */
$paypalUrl = 'https://www.sandbox.paypal.com';
/**
 * Set the urls for canceling and redirecting
 *
 * @author Johnathan Pulos
 */
$requestParams = array(
   'RETURNURL' => 'http://sgccfund.local/PPCompleteDonation.php',
   'CANCELURL' => 'http://sgccfund.local/?donation=cancelled'
);
/**
 * Setup the order parameters
 *
 * @author Johnathan Pulos
 */
$orderParams = array(
   'PAYMENTREQUEST_0_AMT' => '500',
   'PAYMENTREQUEST_0_SHIPPINGAMT' => '0',
   'PAYMENTREQUEST_0_CURRENCYCODE' => 'USD',
   'PAYMENTREQUEST_0_ITEMAMT' => '500'
);
$item = array(
   'L_PAYMENTREQUEST_0_NAME0' => 'Donation',
   'L_PAYMENTREQUEST_0_DESC0' => 'Annual Fund Drive',
   'L_PAYMENTREQUEST_0_AMT0' => '500',
   'L_PAYMENTREQUEST_0_QTY0' => '1'
);
/**
 * Process the order
 *
 * @author Johnathan Pulos
 */
$paypal = new Paypal();
$response = $paypal->request('SetExpressCheckout',$requestParams + $orderParams + $item);
if("SUCCESS" == strtoupper($response["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($response["ACK"])) {
	/**
	 * Redirect to paypal.com
	 *
	 * @author Johnathan Pulos
	 */
	$token = urldecode($response["TOKEN"]);
	header("Location: $paypalUrl/webscr&cmd=_express-checkout&token=$token");
	exit;
} else  {
	header("Location: http://sgccfund.local/?donation=error");
	exit;
}
?>