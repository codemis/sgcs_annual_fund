<?php
/**
 * This makes a request to Paypal to setup a new donation
 *
 * @author Johnathan Pulos
 */
require_once("vendor/PaypalProcessing.php");
/**
 * set the checkout environment (ie. sandbox or live)
 *
 * @author Johnathan Pulos
 */
$environment = "sandbox";
$donation = $_POST["d-amount"];
$donationType = $_POST["gift-type"];
if(($donation != "") && ($donationType != ""))
{
  $paypalProcessing = new PaypalProcessing($environment);
  $donationTypeText = ($donationType == "monthly") ? "Recurring Monthly" : "One Time";
  $paypalProcessing->newDonation($donation, $donationType, "Annual Fund Drive: $donationTypeText Gift");
  $token = $paypalProcessing->setupCheckout();
  $paypalUrl = ($environment == 'sandbox') ? "https://www.sandbox.paypal.com" : "https://www.paypal.com";
  header("Location: $paypalUrl/webscr&cmd=_express-checkout&token=$token");
  exit;
} else{
  header("Location: http://".$_SERVER['SERVER_NAME']."/?donation=error");
	exit;
}
?>