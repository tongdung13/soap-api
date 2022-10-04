<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

class AuthController extends Controller
{
    public  function Login()
    {
        $payload = '';
        try {
            $username =  "Sandy";
            $password =  "tyb$708";
            $this->request = $payload;
            $this->openSoapEnvelope   =   '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">';
            $this->closeSoapEnvelope   = '</soap:Envelope>';
            $soapBody =   '<soap:Body> <Login xmlns="https://uat.Internal.com/sandy"> ' . $username . '</UserName><Password>' . $password . '</Password></Login></soap:Body>';
            $xmlRequest =   $this->generateSoapRequest($soapBody);
            //
            $client = new Client();
            $options = [
                'body'    => $xmlRequest,
                'headers' => [
                    "Content-Type" => "text/xml",
                    "Accept" => "*/*",
                    "Accept-Encoding" => "gzip, deflate,br",
                    "Host" => "uat.Intenal.com",
                    "SOAPAction" => "https://uat.Internal.com/Login"
                ]
            ];
            $res = $client->request(
                'POST',
                'https://uat.Internal.com/IndexLog.asmx',
                $options
            );
            $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $res->getBody()->getContents());
            $xml = new SimpleXMLElement($response);
            $body = $xml->xpath('//soapBody')[0];
            $array = json_decode(json_encode((array)$body), TRUE);
            dd($array["LoginResponse"]["LoginResult"]);
            return ["data" => $res->getBody(), "ResponseCode" => 200];
        } catch (\Exception  $e) {
            Log::error("Login Error", [$e->getMessage()]);
            return ["error" => "Login Failed " . $e->getMessage(), "ResponseCode" => 500];
        } catch (GuzzleException $e) {
            Log::error("Guzzle Error", [$e->getMessage()]);
            return ["error" => "Login Failed " . $e->getMessage(), "ResponseCode" => 500];
        }
    }
    public function generateSoapRequest($soapBody)
    {
        return $this->openSoapEnvelope . $soapBody . $this->closeSoapEnvelope;
    }
}
