<?php
/**
* Handles the processing for Paypal specifically for this website
*/
require_once("Paypal.php");
class PaypalProcessing
{
/**
 * The domain name for the website
 *
 * @var string
 * @access public
 */
  public $domain = "http://sgccfund.local";
/**
 * The amount to be donated
 *
 * @var string
 * @access public
 */
  public $donation = "0.00";
/**
 * The type of donation (ie. one-time or monthly)
 *
 * @var string
 * @access public
 */
  public $donationType = "one-time";
/**
 * The description of the donation
 *
 * @var string
 * @access public
 */
  public $donationDesc = "";

/**
 * The environment to use for paypal (ie. sandbox or live)
 *
 * @var string
 * @access public
 */
  public $paypalEnvironment = "sandbox";
/**
 * The Paypal object
 *
 * @var object
 * @access private
 */
  private $paypal;
  
/**
 * The construct method is called when this class in instantiated
 *
 * @param string $donation the donation amount
 * @param string $donationType the type of donation
 * 
 * @access public
 * @author Johnathan Pulos
 */
  public function __construct($paypalEnvironment = "sandbox")
  {
    $this->paypalEnvironment = strtolower($paypalEnvironment);
    $this->paypal = new Paypal($this->paypalEnvironment);
  }
  
  /**
   * Create a new donation
   *
   * @param string $donation the donation amount
   * @param string $type the donation type (ie. one-time or monthly)
   * @return void
   * @access public
   * @author Johnathan Pulos
   */
  public function newDonation($donation, $type, $description = "")
  {
    $this->setDonation($donation);
    $this->setDonationType($type);
    $this->donationDesc = $description;
  }

/**
 * sets up the checkout with paypal,  you will need to redirect to Paypal if you receive a token
 *
 * @return string
 * @access public
 * @author Johnathan Pulos
 */
  public function setupCheckout()
  {
/**
 * Setup the request params
 *
 * @author Johnathan Pulos
 */
    $requestParams = array( 'RETURNURL'   => "$this->domain/ConfirmDonation.php",
                            'CANCELURL'   => "$this->domain/?donation=cancelled",
                            'LOGOIMG'     =>  "$this->domain/images/logo.png",
                            'LANDINGPAGE' => "Billing",
                            'NOSHIPPING'  => 1
    );
/**
 * Setup the order parameters
 *
 * @author Johnathan Pulos
 */
    $orderParams = array( 'PAYMENTREQUEST_0_AMT'          => $this->donation,
                          'PAYMENTREQUEST_0_SHIPPINGAMT'  => "0",
                          'PAYMENTREQUEST_0_CURRENCYCODE' => "USD",
                          'PAYMENTREQUEST_0_ITEMAMT'      => $this->donation
    );
/**
 * Setup the item details
 *
 * @author Johnathan Pulos
 */
    $item = array(  'L_PAYMENTREQUEST_0_NAME0'  => "Donation",
                    'L_PAYMENTREQUEST_0_DESC0'  => $this->donationDesc,
                    'L_PAYMENTREQUEST_0_AMT0'   => $this->donation,
                    'L_PAYMENTREQUEST_0_QTY0'   => "1",
                    'PAYMENTREQUEST_0_CUSTOM'   => $this->donationType
    );
    if($this->donationType == 'monthly') {
     $item['L_BILLINGTYPE0'] = 'RecurringPayments';
     $item['L_BILLINGAGREEMENTDESCRIPTION0'] = $this->donationDesc;
    }
/**
 * Send the request to Paypal
 *
 * @author Johnathan Pulos
 */
    $response = $this->paypal->request('SetExpressCheckout',$requestParams + $orderParams + $item);
    if($this->validResponse($response))
    {
      return urldecode($response["TOKEN"]);
    }
  }

/**
 * Complete the checkout system.  returns the response with the RECURRING_START_DATE if monthly.
 *
 * @param string $token the Paypal token
 * @param string $payerId the Paypal Payer ID
 * @return array
 * @access public
 * @author Johnathan Pulos
 */  
  public function completeCheckout($token, $payerId)
  {
    $requestParams = array( 'TOKEN'                         => $token,
                            'PAYMENTACTION'                 => 'Sale',
                            'PAYERID'                       => $payerId,
                            'PAYMENTREQUEST_0_AMT'          => $this->donation,
                            'PAYMENTREQUEST_0_CURRENCYCODE' => 'USD'
    );
    $response = $this->paypal->request('DoExpressCheckoutPayment',$requestParams);
    if($this->validResponse($response))
    {
      if($this->donationType == 'monthly')
      {
        $response['RECURRING_START_DATE'] = $this->createMonthlyRecurringProfile($token, $payerId, $response);
      }
      return $response;
    }
  }

/**
 * Creates the recurring profile for monthly donations.  Returns the starting date.
 *
 * @param string $token the Paypal token 
 * @param string $payerId the Paypal Payer ID
 * @return string
 * @access private
 * @author Johnathan Pulos
 */
  private function createMonthlyRecurringProfile($token, $payerId)
  {
    $startDate = $this->oneMonthFromNow();
/**
 * Setup the recurring payments
 *
 * @author Johnathan Pulos
 */
    $requestParams = array( 'TOKEN'             => $token,
                            'PROFILESTARTDATE'  => $startDate,
                            'PAYERID'           => $payerId,
                            'DESC'              => $this->donationDesc,
                            'AMT'               => $this->donation,
                            'BILLINGPERIOD'     => 'Month',
                            'BILLINGFREQUENCY'  => '12',
                            'CURRENCYCODE'      => 'USD'
    );
    $response = $this->paypal->request('CreateRecurringPaymentsProfile',$requestParams);
    if($this->validResponse($response))
    {
      return $startDate;
    }
  }
  
/**
 * Get the details of the checkout
 *
 * @param string $token the Paypal token
 * @return array
 * @access public
 * @author Johnathan Pulos
 */
  public function getCheckoutDetails($token)
  {
    $response = $this->paypal->request('GetExpressCheckoutDetails', array('TOKEN' => $token));
    if($this->validResponse($response))
    {
      return $response;
    }
  }
  
/**
 * Gets the date one month from now
 *
 * @return string
 * @access public
 * @author Johnathan Pulos
 */
  public function oneMonthFromNow()
  {
    $todayDate = date("Y-m-d");
    $dateOneMonthAdded = strtotime(date("Y-m-d", strtotime($todayDate)) . "+1 month");
    return gmdate('Y-m-d H:i:s', $dateOneMonthAdded);
  }
  
/**
 * Checks if the response is valid.  Returns true if valid, else it errors out
 *
 * @param string $response the response
 * @return boolean
 * @access private
 * @author Johnathan Pulos
 */
  private function validResponse($response)
  {
    $error = "There was a problem with the request.";
    if("SUCCESS" == strtoupper($response["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($response["ACK"])) {
    	return true;
    } else{
      if (isset($response["L_SHORTMESSAGE0"])) {
        $error = $response["L_SHORTMESSAGE0"];
      }
      if (isset($response["L_LONGMESSAGE0"])) {
        $error .= $response["L_LONGMESSAGE0"];
      }
      trigger_error($error, E_USER_ERROR);
    }
  }

/**
 * Set the donation amount
 *
 * @param string $donation the donation amount 
 * @return string
 * @access private
 * @author Johnathan Pulos
 */
  private function setDonation($donation)
  {
    $this->donation = preg_replace("/[^0-9.]*/","", $donation);
    if($this->donation == "")
    {
      trigger_error("Donation must be set to a valid value!", E_USER_WARNING);
    }
    return $this->donation;
  }

/**
 * Set the Donation Type (ie. one-time or monthly)
 *
 * @param string $donationType the type of donation
 * @return string
 * @access private
 * @author Johnathan Pulos
 */
  private function setDonationType($donationType)
  {
    if(!in_array(strtolower($donationType), array("one-time", "monthly")))
    {
      trigger_error("Donation type must be one-time or monthly!", E_USER_WARNING);
    }
    $this->donationType = strtolower($donationType);
    return $this->donationType;
  }

}

?>