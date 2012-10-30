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
 */
  public $donationType = "one-time";
/**
 * The environment to use for paypal (ie. sandbox or live)
 *
 * @var string
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
  public function __construct($donation, $donationType, $paypalEnvironment = "sandbox")
  {
    $this->paypalEnvironment = strtolower($paypalEnvironment);
    $this->paypal = new Paypal($this->paypalEnvironment);
    $this->setDonation($donation);
    $this->setDonationType($donationType);
  }
  
/**
 * Set the donation amount
 *
 * @param string $donation the donation amount 
 * @return string
 * @access public
 * @author Johnathan Pulos
 */
  public function setDonation($donation)
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
 * @access public
 * @author Johnathan Pulos
 */
  public function setDonationType($donationType)
  {
    if(!in_array(strtolower($donationType), array("one-time", "monthly")))
    {
      trigger_error("Donation type must be one-time or monthly!", E_USER_WARNING);
    }
    $this->donationType = strtolower($donationType);
    return $this->donationType;
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
    $requestParams = array( 'RETURNURL'   => "$this->domain/confirm_donation.php",
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
    $donationTypeText = ($this->donationType == "monthly") ? "Recurring Monthly" : "One Time";
    $item = array(  'L_PAYMENTREQUEST_0_NAME0'  => "Donation",
                    'L_PAYMENTREQUEST_0_DESC0'  => "Annual Fund Drive: $donationTypeText Gift",
                    'L_PAYMENTREQUEST_0_AMT0'   => $this->donation,
                    'L_PAYMENTREQUEST_0_QTY0'   => "1",
                    'PAYMENTREQUEST_0_CUSTOM'   => $this->donationType
    );
    if($this->donationType == 'monthly') {
     $item['L_BILLINGTYPE0'] = 'RecurringPayments';
     $item['L_BILLINGAGREEMENTDESCRIPTION0'] = "Annual Fund Drive: $donationTypeText Gift";
    }
/**
 * Send the request to Paypal
 *
 * @author Johnathan Pulos
 */
    $response = $this->paypal->request('SetExpressCheckout',$requestParams + $orderParams + $item);
    if("SUCCESS" == strtoupper($response["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($response["ACK"])) {
    	return urldecode($response["TOKEN"]);
    } else{
      $error = "There was a problem with the request.";
      if (isset($response["L_SHORTMESSAGE0"])) {
        $error = $response["L_SHORTMESSAGE0"];
      }
      if (isset($response["L_LONGMESSAGE0"])) {
        $error .= $response["L_LONGMESSAGE0"];
      }
      trigger_error($error, E_USER_ERROR);
    }
  }
}

?>