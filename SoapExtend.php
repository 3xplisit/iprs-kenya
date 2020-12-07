<?php
error_reporting(0);
header('Content-type:application/json');
/*
------------------------------------------------------------------------------------/
Description: IPRS Web Service call. This we will use it to access
the various functions ascribed in the documentation for the 
I-ntegrated 
P-opulation and 
R-egistration 
S-ervice.
------------------------------------------------------------------------------------/
Author:     Jesse Kariuki Kamau
Test Date:  26-March-2018 10:45:09
Comments:   To God Be the Glory.
------------------------------------------------------------------------------------/

ChangeLog: 
[26-03-2018] : Tested the Endpoint by Passing requests to the Web Service.

------------------------------------------------------------------------------------/
Error's while using do_request by extending the SoapClient.
1. Declaration of MySoapClient::__doRequest($request, $location, $action, $version) 
should be compatible with 
SoapClient::__doRequest($request, $location, $action, $version, $one_way = 0)

Solution:
Add the Method One way = 0  to Turn off the error.

Errors:

Error 1 - General Error IPRS Cannot Validate Names.
Error 0 - Successful Response from IPRSError
Error 105 - Error in ID Details
Error 114 - Credentials Error on IPRS
-------------------------------------------------------------------------------------/
*/

const WS_USERNAME = ''; //obtain this from IPRS Once you have an account
const WS_PASSWORD = ''; //obtain this from IPRS once you have an account

class MySoapClient extends SoapClient {

    public function __construct(){

        $this->ws_location = 'http://10.1.1.7:9004/IPRSServerWCF'; //the default webservice address
        $this->ws_soapVers = '1.1';
    }
    
    
    public function __doRequest($request, $location, $action, $version, $one_way = 0) 

    { 

        $result = parent::__doRequest($request, $location, $action, $version, $one_way = 0);
        return $result; 

    }

//Function to pass the login credentials to the authorization server over at IPRS
  function Login($array =null ,$ws_action = null) { 
           
		(!empty($array     ? : die('Please Pass    ws_parrameters')));
		(!empty($ws_action ? : die('Please Specify ws_Action')));


        $request  = $array;
        $location = $this->ws_location;    //Set the endpoint for the Web Service
        $action   = $ws_action;           //Set the web service action here
        $version  = $this->ws_soapVers;  //Set the SOAP Version for Use 1.1 OR 1.2

        $result   = $this->__doRequest($request, $location, $action, $version, $one_way = 0);

        return $result;

   }

//Function to pass parameters to request ID Card Details from the webservice
   function GetDataByIdCard($array =null ,$ws_action = null) { 
           
        (!empty($array     ? : die('Please Pass    ws_parrameters')));
        (!empty($ws_action ? : die('Please Specify ws_Action')));

        $request  = $array;
        $location = $this->ws_location;   //Set the endpoint for the Web Service
        $action   = $ws_action;          //Set the web service action here
        $version  = $this->ws_soapVers; //Set the SOAP Version for Use 1.1 OR 1.2

        $result   = $this->__doRequest($request, $location, $action, $version, $one_way = 0);

        return $result;

   }


}