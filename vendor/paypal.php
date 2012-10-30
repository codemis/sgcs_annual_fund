<?php
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
   protected $_credentials = array(
      'USER' =>'sell_1351543482_biz_api1.jpulos.com',
      'PWD' =>'1351543502',
      'SIGNATURE' =>'AvSdIN.gXmvOAU8jEcDbDhXzfpMIADAnle7PC8chPZ6aCWlYgDiSgEoT',
   );

   /**
    * API endpoint
    * Live- https://api-3t.paypal.com/nvp
    * Sandbox- https://api-3t.sandbox.paypal.com/nvp
    * @var string
    */
   protected $_endPoint = 'https://api-3t.sandbox.paypal.com/nvp';

   /**
    * API Version
    * @var string
    */
   protected $_version = '74.0';

   /**
    * Make API request
    *
    * @param string $method string API method to request
    * @param array $params Additional request parameters
    * @return array / boolean Response array / boolean false on failure
    */
   public function request($method,$params = array()) {
      $this->_errors = array();
      if( empty($method) ) { //Check if API method is not empty
         $this->_errors = array('API method is missing');
         return false;
      }
      
      //Our request parameters
      $requestParams = array(
         'METHOD' =>$method,
         'VERSION' =>$this->_version
      ) + $this->_credentials;

      //Building our NVP string
      $request = http_build_query($requestParams + $params);

      //cURL settings
      $curlOptions = array (
         CURLOPT_URL =>$this->_endPoint,
         CURLOPT_VERBOSE =>1,
         CURLOPT_RETURNTRANSFER =>1,
         CURLOPT_SSL_VERIFYPEER => true,
         CURLOPT_SSL_VERIFYHOST => 2,
         CURLOPT_CAINFO => dirname(__FILE__) . '/api_cert_chain.crt', //CA cert file
         CURLOPT_POST =>1,
         CURLOPT_POSTFIELDS =>$request
      );

      $ch = curl_init();
      curl_setopt_array($ch,$curlOptions);

      //Sending our request- $response will hold the API response
      $response = curl_exec($ch);
      
      //Checking for cURL errors
      if (curl_errno($ch)) {
         $this->_errors = curl_error($ch);
         
         curl_close($ch);
         return false;
         //Handle errors
      } else  {
         curl_close($ch);
         $responseArray = array();
         parse_str($response,$responseArray); // Break the NVP string to an array
         return $responseArray;
      }
   }
}
?>