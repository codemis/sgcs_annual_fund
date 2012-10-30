<?php
require_once('PaypalSettings.inc.php');
class Paypal {
/**
* Last error message(s)
* @var array
*/
  protected $_errors = array();

/**
* API Credentials
* Use the correct credentials for the environment in use (Live / Sandbox)
* @var array
*/
  protected $_credentials = array();

/**
* API endpoint
* Live- https://api-3t.paypal.com/nvp
* Sandbox- https://api-3t.sandbox.paypal.com/nvp
* @var string
*/
  protected $_endPoint = "https://api-3t.sandbox.paypal.com/nvp";

/**
* API Version
* @var string
*/
  protected $_version = '74.0';

  public function __construct($environment = "sandbox")
  {
    $paypalSettings = new PaypalSettings();
    $this->_credentials = $paypalSettings->paypalSettings[strtolower($environment)];
    $this->_endPoint = (strtolower($environment) == "sandbox") ? "https://api-3t.sandbox.paypal.com/nvp" : "https://api-3t.paypal.com/nvp";
  }

/**
* Make API request
*
* @param string $method string API method to request
* @param array $params Additional request parameters
* @return array / boolean Response array / boolean false on failure
*/
  public function request($method,$params = array())
  {
    $this->_errors = array();
    if( empty($method) )
    {
      $this->_errors = array('API method is missing');
      return false;
    }
    $requestParams = array( 'METHOD'  =>  $method,
                            'VERSION' =>  $this->_version
    ) + $this->_credentials;
    $request = http_build_query($requestParams + $params);
    $curlOptions = array( CURLOPT_URL             =>  $this->_endPoint,
                          CURLOPT_VERBOSE         =>  1,
                          CURLOPT_RETURNTRANSFER  =>  1,
                          CURLOPT_SSL_VERIFYPEER  =>  true,
                          CURLOPT_SSL_VERIFYHOST  =>  2,
                          CURLOPT_CAINFO          =>  dirname(__FILE__) . '/api_cert_chain.crt', //CA cert file
                          CURLOPT_POST            =>  1,
                          CURLOPT_POSTFIELDS      =>  $request
    );
    $ch = curl_init();
    curl_setopt_array($ch,$curlOptions);
/**
 * Sending our request- $response will hold the API response
 */
    $response = curl_exec($ch);
/**
 * Checking for cURL errors
 *
 * @author Johnathan Pulos
 */
    if (curl_errno($ch))
    {
      $this->_errors = curl_error($ch);

      curl_close($ch);
      return false;
    }else
    {
      curl_close($ch);
      $responseArray = array();
      parse_str($response,$responseArray);
      return $responseArray;
    }
  }
}
?>