<?php
/**
 * This checks the user info, and sees if we need to setup recurring payments
 *
 * @author Johnathan Pulos
 */
require_once('vendor/paypal.php');
 $website = 'http://sgccfund.local';
/**
 * Check that the token exists
 *
 * @author Johnathan Pulos
 */
if( isset($_GET['token']) && !empty($_GET['token']) ) {
  $token = $_GET['token'];
   /**
    * Get checkout details, including buyer information.
    * We can save it for future reference or cross-check with the data we have
    *
    * @author Johnathan Pulos
    */
   $paypal = new Paypal();
   $checkoutDetails = $paypal->request('GetExpressCheckoutDetails', array('TOKEN' => $token));
   $donation = $checkoutDetails['AMT'];
   $donationType = $checkoutDetails['CUSTOM'];
   $donationDesc = $checkoutDetails['L_DESC0'];

   // Complete the checkout transaction
   $requestParams = array(
       'TOKEN' => $token,
       'PAYMENTACTION' => 'Sale',
       'PAYERID' => $_GET['PayerID'],
       'PAYMENTREQUEST_0_AMT' => $donation, // Same amount as in the original request
       'PAYMENTREQUEST_0_CURRENCYCODE' => 'USD' // Same currency as the original request
   );

   $response = $paypal->request('DoExpressCheckoutPayment',$requestParams);
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
        if($donationType == 'monthly') {
          $todayDate = date("Y-m-d");
          $dateOneMonthAdded = strtotime(date("Y-m-d", strtotime($todayDate)) . "+1 month");
          $startDate = gmdate('Y-m-d H:i:s', $dateOneMonthAdded);
          /**
           * Setup the recurring payments
           *
           * @author Johnathan Pulos
           */
           $requestParams = array(
                'TOKEN' => $token,
                'PROFILESTARTDATE' => $startDate,
                'PAYERID' => $_GET['PayerID'],
                'DESC' => $donationDesc, // Same amount as in the original request
                'AMT' => $donation,
                'BILLINGPERIOD' => 'Month',
                'BILLINGFREQUENCY' => '12',
                'CURRENCYCODE' => 'USD' // Same currency as the original request
            );
            $response = $paypal->request('CreateRecurringPaymentsProfile',$requestParams);
            if(is_array($response) && $response['ACK'] == 'Success') {
              header("Location:  $website/?donation=complete");
            	exit;
            }else{
              header("Location:  $website/?donation=error");
              exit;
            }
        } else{
          header("Location:  $website/?donation=complete");
        	exit;
        }
   } else{
     header("Location:  $website/?donation=error");
   	 exit;
   }
}
?>