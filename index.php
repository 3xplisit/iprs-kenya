<?php
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
Email:      jessy3g@gmail.com
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

IPRS Errors:

Error 1 - General Error IPRS Cannot Validate Names.
Error 0 - Successful Response from IPRSError
Error 105 - Error in ID Details
Error 114 - Credentials Error on IPRS
-------------------------------------------------------------------------------------/
*/

include_once('SoapExtend.php');

header('Content-type:application/json');

$data = file_get_contents("php://input");

//capture request from IPRS 
file_put_contents('request.log',$data,FILE_APPEND|LOCK_EX);

(!empty($_POST['idNumber'] ? : die  (json_encode(['responseCode'=>'100','responseDesc'=>'Invalid Request for id Number']))));

$idnumber = $_POST['idNumber'];

try{

$soap_params = array('exception'=>1,'cache_wsdl'=>WSDL_CACHE_NONE,'trace'=>1);

//Generate Request payload to be passed to the IPRS Endpoint
$request = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
   <soapenv:Header/>
   <soapenv:Body>
      <tem:GetDataByIdCard> 
         <tem:log>'.WS_USERNAME.'</tem:log>
         <tem:pass>'.WS_PASSWORD.'</tem:pass>
         <tem:id_number>'.$idnumber.'</tem:id_number>
         <tem:serial_number></tem:serial_number>
      </tem:GetDataByIdCard>
   </soapenv:Body>
</soapenv:Envelope>';

//Create an instance of the SoapClient class to pass the Request 
$soapClient      = new MySoapClient("http://10.1.1.5:9004/IPRSServerwcf?wsdl", $soap_params);
$PostTransaction = $soapClient->GetDataByIdCard($request,'http://tempuri.org/IServiceIPRS/GetDataByIdCard');

//Capture the response from the request on an XML for parsing..
file_put_contents('IPRS_Response.xml', $PostTransaction, FILE_APPEND);

//parse using Simple XML Functions
$data = simplexml_load_string($PostTransaction);

$ns                = $data->getNamespaces(true);          
$soap              = $data->children($ns['s']);
$response_Body     = $soap->children($ns['']);

$error = $response_Body->LoginResponse->LoginResult;

if($error=='false')
{

  echo(json_encode(['responseCode'=>'100','responseDesc'=>'The Information provided could not be validated']));


}else if($error <> 'false')

{

  $element = simplexml_load_string($PostTransaction);

    $element->registerXPathNamespace('a', 'http://schemas.datacontract.org/2004/07/IPRSManager');
    $element->registerXPathNamespace('s', 'http://schemas.xmlsoap.org/soap/envelope/');
    $element->registerXPathNamespace('i', 'http://www.w3.org/2001/XMLSchema-instance');
    $errorcode = $element->xpath('//a:ErrorCode');
    $error_res = $element->xpath('//a:ErrorMessage');

    switch($errorcode['0'])
    {

      //Incase there is no error Found then just send the results.
      case(''):


        $firstname   = $element->xpath('//a:First_Name');
        $other_Name  = $element->xpath('//a:Other_Name');
        $surname     = $element->xpath('//a:Surname');
        $gender      = $element->xpath('//a:Gender');
        $birthdate   = $element->xpath('//a:Date_of_Birth');
        $deathdate   = $element->xpath('//a:Date_of_Death');

        $result      = array('responseCode'=>'200','responseDesc'=>array('First_Name'=>$firstname['0'],'Other_Name'=>$other_Name['0'],'Surname'=>$surname['0'],'gender'=>$gender['0'],'dateofBirth'=>$birthdate['0'],'dateofDeath'=>$deathdate['0']));

        
      echo(json_encode($result));

      file_put_contents(date('Y-m-d').'_IPRS_Response.log', '['.date('Y-m-d H:i:s').']'.' '.$_SERVER['REMOTE_ADDR'].' | '.$firstname['0'].' '.$other_Name['0'].' '.$surname['0'].PHP_EOL, FILE_APPEND);
      break;

      //We now have an error the id details are not found.
      case('ISB-105'):

      echo(json_encode(array('responseCode'=>'105','responseDesc'=>$error_res)));
      break;

      //We now have an error related to the credentials.
      case('ISB-114'):

      $cred = array('responseCode'=>'114','responseDesc'=>$error_res['0']);

      echo (json_encode($cred));

      break;
    
      default:

      $validate = array('responseCode'=>'100','responseDesc'=>$error_res['0']);

      echo (json_encode($validate));

      break; 

    }
  

  }
   
}catch(SoapFault $fault){

  echo '<pre>'.PHP_EOL.
  'Fault String '.($fault->faultstring);
}
 
 


?>
