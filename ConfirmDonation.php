<?php
/**
 * This checks the user info, and sees if we need to setup recurring payments
 *
 * @author Johnathan Pulos
 */
require_once("vendor/PaypalProcessing.php");
function logDonation($file, $data)
{
  $fp = fopen($file, 'a');
  fputcsv($fp, $data);
  fclose($fp);
}
/**
 * set the checkout environment (ie. sandbox or live)
 *
 * @author Johnathan Pulos
 */
$environment = "sandbox";
$todayDate = date("Y-m-d");
$paypalProcessing = new PaypalProcessing($environment);
/**
 * Check that the token exists
 *
 * @author Johnathan Pulos
 */
if(isset($_REQUEST['token']) && !empty($_REQUEST['token']))
{
  $token = $_REQUEST['token'];
  $payerId = $_REQUEST['PayerID'];
  $details = $paypalProcessing->getCheckoutDetails($token);
  $donation = $details['AMT'];
  $donationType = $details['CUSTOM'];
  $donationDesc = $details['L_DESC0'];
  $donationTypeText = ($donationType == "monthly") ? "Recurring Monthly" : "One Time";
  $oneMonthAway = $paypalProcessing->oneMonthFromNow();
} else
{
  header("Location:  http://sgccfund.local/?donation=error");
  exit;
}
if(isset($_POST['donation_confirmed']))
{
/**
 * Confirm the donation
 *
 * @author Johnathan Pulos
 */
  $paypalProcessing->newDonation($donation, $donationType, $donationDesc);
  $checkoutResponse = $paypalProcessing->completeCheckout($token, $payerId);
  $transactionId = $checkoutResponse['PAYMENTINFO_0_TRANSACTIONID'];
  logDonation(dirname(dirname(__FILE__))."/complete_donations.csv", array($todayDate, $payerId, $donationType, $donation, $transactionId));
  header("Location:  http://sgccfund.local/?donation=complete");
  exit;
} else
{
/**
 * Log the pending donation
 *
 * @author Johnathan Pulos
 */
  logDonation(dirname(dirname(__FILE__))."/pending_donations.csv", array($todayDate, $payerId, $donationType, $donation));
}
?>
<!DOCTYPE html>
<html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="description" content="corporate, business software, app" />
<meta name="keywords" content="corporate, software, app, business, marketing, landing page, web marketing, internate marketing, seo"/>
<meta name="author" content="Tansh" />
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;">
<link rel="icon" type="image/png" href="images/favicon.png"/>
<title>SGCS Annual Fund:: Enriching Hearts and Minds</title>

<!--google web font-->
<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,300italic' rel='stylesheet' type='text/css'>

<!--style sheets-->
<link rel="stylesheet" media="screen" href="css/style.css"/>
<link rel="stylesheet" media="screen" href="css/skeleton.css"/>
<link rel="stylesheet" media="screen" href="css/flexslider.css"/>
<link rel="stylesheet" media="screen" href="css/prettyPhoto.css"/>

<!--jquery libraries / others are at the bottom-->
<script src="js/jquery-1.7.1.min.js" type="text/javascript"></script>
<script src="js/modernizr.js" type="text/javascript"></script>
<script src="js/application.js" type="text/javascript"></script>
</head>
<body>

<!--wrapper starts-->
<div id="wrapper"> 
  <div class="row white donation-summary">
      <div class="container">
        <h3>Confirm Your Donation to SGCS Annual Fund</h3>
        <p>Thank you for your donation.  In order to complete your donation,  please review the donation information below, and click the <strong>"Complete Donation"</strong> Button.  Your donation will not be sent,  if you do not click the button.</p>
        <dl>
          <dt>Donation Amount:</dt>
            <dd><?php echo money_format('$%i', $donation); ?></dd>
          <dt>Type of Donation:</dt>
            <dd><?php echo $donationTypeText; ?></dd>
          <?php if($donationType == "monthly")
                {
          ?>
            <dt>Next Donation:</dt>
              <dd><?php echo date("F j, Y", strtotime($oneMonthAway)); ?></dd>
          <?php } ?>
        </dl>
        <form action="ConfirmDonation.php" method="post" accept-charset="utf-8">
          <input type="hidden" name="token" value="<?php echo $token; ?>">
          <input type="hidden" name="PayerID" value="<?php echo $payerId; ?>">
          <input type="hidden" name="donation_confirmed" value="true">
          <a href="" class="btn" id="donate-link"><span>Complete Donation</span></a>
        </form>
      </div>
      <div class="clear"></div>
    </div>
  <!--row starts-->
  <div class="row">
    <div class="container"> 
      
      <!--payment starts-->
      <div class="eight columns">
        <h4>Contact Us</h4>
        <p>San Gabriel Christian School<br>
        117 North Pine Street<br>
        San Gabriel, CA 91775<br>
        (626)287-0486<br>
        <a href="http://www.sgccandcs.org">www.sgccandcs.org</a></p>
      </div>
      <!--payment ends-->    
    </div>
    <div class="clear"></div>
  </div>
  <!--row ends--> 
  
  <!--copyright starts-->
  <div id="copyright">
    <div class="container">
      <div class="sixteen columns">
        <p>© Copyright 2012 San Gabriel Christian School Design By Younic</p>
        <ul>
          <li><a href="http://www.sgccandcs.org">Website</a></li>
          <li><a href="http://www.edline.net/pages/San_Gabriel_Christian_School">Edline</a></li>
        </ul>
      </div>
    </div>
    <div class="clear"></div>
  </div>
  <!--copyright ends-->
  
  <div class="clear"></div>
</div>
<!--wrapper ends--> 

<!--other jqueries required--> 
<script src="js/jquery.flexslider-min.js" type="text/javascript"></script> 
<script src="js/jquery.prettyPhoto.js" type="text/javascript"></script> 
<script src="js/jquery.validate.js" type="text/javascript"></script> 
<script src="js/jquery.form.js" type="text/javascript"></script> 
<script src="js/fitvids.js" type="text/javascript"></script> 
<script src="js/twitter.js" type="text/javascript"></script> 
<script src="js/custom.js" type="text/javascript"></script>
</body>
</html>