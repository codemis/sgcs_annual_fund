<?php
/**
 * This makes a request to Paypal to setup a new donation
 *
 * @author Johnathan Pulos
 */
require_once('vendor/paypal.php');
$website = 'http://sgccfund.local';
/**
 * Strip dollar signs
 *
 * @author Johnathan Pulos
 */
$donation = preg_replace('/[^0-9.]*/','', $_POST['d-amount']);
$giftType = $_POST['gift-type'];
$giftTypeText = ($giftType == 'monthly') ? 'Monthly' : 'One Time';
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
   'RETURNURL' => "$website/PPCompleteDonation.php",
   'CANCELURL' => "$website/?donation=cancelled",
   'LOGOIMG'   =>  "$website/images/logo.png",
   'LANDINGPAGE' => "Billing",
   'NOSHIPPING' => 1
);
/**
 * Setup the order parameters
 *
 * @author Johnathan Pulos
 */
$orderParams = array(
   'PAYMENTREQUEST_0_AMT' => $donation,
   'PAYMENTREQUEST_0_SHIPPINGAMT' => '0',
   'PAYMENTREQUEST_0_CURRENCYCODE' => 'USD',
   'PAYMENTREQUEST_0_ITEMAMT' => $donation
);
$item = array(
   'L_PAYMENTREQUEST_0_NAME0' => 'Donation',
   'L_PAYMENTREQUEST_0_DESC0' => "Annual Fund Drive: $giftTypeText Gift",
   'L_PAYMENTREQUEST_0_AMT0' => $donation,
   'L_PAYMENTREQUEST_0_QTY0' => '1',
   'PAYMENTREQUEST_0_CUSTOM'  => "$giftType"
);
if($giftType == 'monthly') {
 $item['L_BILLINGTYPE0'] = 'RecurringPayments';
 $item['L_BILLINGAGREEMENTDESCRIPTION0'] = "Annual Fund Drive: $giftTypeText Gift";
}
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