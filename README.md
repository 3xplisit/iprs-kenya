# IPRS - Kenya API SDK (PHP)
This Project offers sample codes to be used when querying the IPRS API Service 
you need to be onboarded on their VPN Service for you to use the API


1. You will need the Username and Password obtained from the API Administrators
2. You will need knowledge on SoapUI usage for generating the XML Requests to Submit to the API Endpoint
# Generating the Request payload to IPRS VPN WebService
Constant WS_USERNAME contains the Web Service Username while 
WS_PASSWORD contains the password

Variable $idnumber contains the ID Number to be passed to the Request
```
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
```
Sample XML Returned from the Web Service call
This is how
```<xml>
  <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
  <s:Body>
    <GetDataByIdCardResponse xmlns="http://tempuri.org/">
    <GetDataByIdCardResult xmlns:a="http://schemas.datacontract.org/2004/07/IPRSManager" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
      <a:ErrorCode/>
      <a:ErrorMessage/>
      <a:ErrorOcurred>false</a:ErrorOcurred>
      <a:Citizenship i:nil="true"/>
      <a:Clan i:nil="true"/>
      <a:Date_of_Birth>00/00/0000 12:00:00 AM</a:Date_of_Birth>
      <a:Date_of_Death i:nil="true"/>
      <a:Ethnic_Group i:nil="true"/>
      <a:Family i:nil="true"/>
      <a:Fingerprint i:nil="true"/>
      <a:First_Name>JOHN</a:First_Name>
      <a:Gender>M</a:Gender>
      <a:ID_Number i:nil="true"/>
      <a:Occupation i:nil="true"/>
      <a:Other_Name>DOE</a:Other_Name>
      <a:Photo i:nil="true"/>
      <a:Pin i:nil="true"/>
      <a:Place_of_Birth i:nil="true"/>
      <a:Place_of_Death i:nil="true"/>
      <a:Place_of_Live i:nil="true"/>
      <a:Signature i:nil="true"/>
      <a:Surname>YOUNG</a:Surname>
      <a:Date_of_Issue i:nil="true"/>
      <a:RegOffice i:nil="true"/>
      <a:Serial_Number>000000000</a:Serial_Number>
    </GetDataByIdCardResult>
  </GetDataByIdCardResponse>
</s:Body>
</s:Envelope>

</xml>
```
With this information at hand, you can now parse the details using PHP SimpleXML Function
<code>
  
    $element = simplexml_load_string($PostTransaction);
    $element->registerXPathNamespace('a', 'http://schemas.datacontract.org/2004/07/IPRSManager');
    $element->registerXPathNamespace('s', 'http://schemas.xmlsoap.org/soap/envelope/');
    $element->registerXPathNamespace('i', 'http://www.w3.org/2001/XMLSchema-instance');
    $errorcode = $element->xpath('//a:ErrorCode');
    $error_res = $element->xpath('//a:ErrorMessage');
    
     $firstname   = $element->xpath('//a:First_Name');
     $other_Name  = $element->xpath('//a:Other_Name');
       $surname     = $element->xpath('//a:Surname');
       $gender      = $element->xpath('//a:Gender');
       $birthdate   = $element->xpath('//a:Date_of_Birth');
       $deathdate   = $element->xpath('//a:Date_of_Death');
</code>

