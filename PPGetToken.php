<?php
/**
 * This makes a request to Paypal to setup a new donation
 *
 * @author Johnathan Pulos
 */
require_once("vendor/PaypalProcessing.php");
$donation = $_POST["d-amount"];
$donationType = $_POST["gift-type"];
if(($donation != "") && ($donationType != ""))
{
  $paypalProcessing = new PaypalProcessing($donation, $donationType);
  $paypalProcessing->paypalEnvironment = "sandbox";
  $token = $paypalProcessing->setupCheckout();
  /**
   * Set the url for paypal redirect
   * sandbox = https://www.sandbox.paypal.com
   * paypal = https://www.paypal.com
   *
   * @author Johnathan Pulos
   */
  $paypalUrl = 'https://www.sandbox.paypal.com';
  header("Location: $paypalUrl/webscr&cmd=_express-checkout&token=$token");
  exit;
} else{
  header("Location: http://sgccfund.local/?donation=error");
	exit;
}
?>