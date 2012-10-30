<?php
/**
 * This checks the user info, and sees if we need to setup recurring payments
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
/**
 * Check that the token exists
 *
 * @author Johnathan Pulos
 */
if(isset($_GET['token']) && !empty($_GET['token']))
{
  $token = $_GET['token'];
  $payerId = $_GET['PayerID'];
  $paypalProcessing = new PaypalProcessing($environment);
  $details = $paypalProcessing->getCheckoutDetails($token);
  $paypalProcessing->newDonation($details['AMT'], $details['CUSTOM'], $details['L_DESC0']);
  $checkoutResponse = $paypalProcessing->completeCheckout($token, $payerId);
  header("Location:  http://sgccfund.local/?donation=complete");
  exit;
} else
{
  header("Location:  http://sgccfund.local/?donation=error");
  exit;
}
?>