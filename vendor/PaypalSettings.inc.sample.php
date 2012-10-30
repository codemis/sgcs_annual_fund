<?php
/**
 * Settings for your Paypal account,  Rename this file to "PaypalSettings.inc.php"
 *
 * @author Johnathan Pulos
 */
 class PaypalSettings
 {
   /**
    * Array of settings for accessing paypal
    *
    * @var array
    * @access public
    * @author Johnathan Pulos
    */
   public $paypalSettings = array("sandbox" => array(  "USER"      =>"",
                                                       "PWD"       =>"",
                                                       "SIGNATURE" =>""),
                                   "live"    => array( "USER"      =>"",
                                                       "PWD"       =>"",
                                                       "SIGNATURE" =>"")
   );
 }
?>