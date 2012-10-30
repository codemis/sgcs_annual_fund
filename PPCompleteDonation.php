<?php
/**
 * This checks the user info, and sees if we need to setup recurring payments
 *
 * @author Johnathan Pulos
 */
require_once('vendor/paypal.php');
/**
 * Check that the token exists
 *
 * @author Johnathan Pulos
 */
if( isset($_GET['token']) && !empty($_GET['token']) ) {
  $donation = '500';
  $donationType = 'one-time';
   /**
    * Get checkout details, including buyer information.
    * We can save it for future reference or cross-check with the data we have
    *
    * @author Johnathan Pulos
    */
   $paypal = new Paypal();
   $checkoutDetails = $paypal -> request('GetExpressCheckoutDetails', array('TOKEN' => $_GET['token']));

   // Complete the checkout transaction
   $requestParams = array(
       'TOKEN' => $_GET['token'],
       'PAYMENTACTION' => 'Sale',
       'PAYERID' => $_GET['PayerID'],
       'PAYMENTREQUEST_0_AMT' => $donation, // Same amount as in the original request
       'PAYMENTREQUEST_0_CURRENCYCODE' => 'USD' // Same currency as the original request
   );

   $response = $paypal -> request('DoExpressCheckoutPayment',$requestParams);
   if( is_array($response) && $response['ACK'] == 'Success') {
       /**
        * Get the transactionId
        *
        * @author Johnathan Pulos
        */
       $transactionId = $response['PAYMENTINFO_0_TRANSACTIONID'];
       /**
        * Write data to a CSV file
        *
        * @author Johnathan Pulos
        */
        $fp = fopen(dirname(dirname(__FILE__))."/donations.csv", 'a');
        fputcsv($fp, array($transactionId, $_GET['PayerID'], $donation, $donationType));
        fclose($fp);
        header("Location: http://sgccfund.local/?donation=complete");
      	exit;
   } else{
     header("Location: http://sgccfund.local/?donation=error");
   	 exit;
   }
}
?>