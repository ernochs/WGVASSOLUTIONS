<?php

namespace App\Http\Controllers\USSD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class UssdController extends Controller
{
    public function handleUssd(Request $request)
    {
        $data = $request->getContent();
        Log::info('Received USSD request: ' . $data);

        $xml = simplexml_load_string($data);

        $msisdn = $xml->msisdn;
        $sessionid = $xml->sessionid;
        $type = $xml->type;
        $msg = $xml->msg;

        // Determine the cost and ref dynamically
        $cost = $this->determineCost($msisdn, $type);
        $ref = $this->generateRef();

        // Process the request and generate the response
        $responseXml = $this->generateResponseXml($type, $msg, $cost, $ref);

        return Response::make($responseXml, 200, ['Content-Type' => 'application/xml']);
    }

    public function handleUssdCid(Request $request)
    {
        $data = $request->getContent();
        Log::info('Received USSD-CID request: ' . $data);

        $xml = simplexml_load_string($data);

        $msisdn = $xml->msisdn;
        $sessionid = $xml->sessionid;
        $type = $xml->type;
        $msg = $xml->msg;
        $position = $xml->position;
        $vlr = $xml->vlr;

        // Process the request and generate the response
        $responseXml = $this->generateResponseXml($type, $msg, 10, 'REF123');

        return Response::make($responseXml, 200, ['Content-Type' => 'application/xml']);
    }
    
    private function determineCost($msisdn, $type)
    {
        // Example logic to determine cost
        // You can replace this with your own business logic or database queries
        return 10; // Placeholder value
    }

    private function generateRef()
    {
        // Example logic to generate a unique reference
        // You can replace this with your own reference generation logic
        return uniqid('REF');
    }
    
    private function generateResponseXml($type, $msg, $cost, $ref)
    {
        // Create a new SimpleXMLElement object
        $response = new \SimpleXMLElement('<ussd/>');

        // Add type element
        $response->addChild('type', $this->xmlEncode($type));

        // Add msg element
        $response->addChild('msg', $this->xmlEncode($msg));

        // Add premium element with cost and ref as children
        $premium = $response->addChild('premium');
        $premium->addChild('cost', $this->xmlEncode($cost));
        $premium->addChild('ref', $this->xmlEncode($ref));

        // Return the XML as a string
        return $response->asXML();
    }

    // Helper method to XML encode values
    private function xmlEncode($string)
    {
        $encoded = htmlspecialchars($string, ENT_XML1, 'UTF-8');
        return $encoded;
    }
}
