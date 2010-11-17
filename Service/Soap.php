<?php

abstract class eVias_Service_Soap
    extends SoapClient
{

    /** @var SoapServer */
    private $_server = null;

    /** @var array */
    protected $_methods = array();

    /** @var string */
    protected $_className = null;

/** public : **/

    public function __construct($wsdl, $options)
    {
        parent::__construct($wsdl, $options);

        $this->_server = new SoapServer($wsdl, $options);

        $this->_server->setClass($this->_className);
    }

    /**
     * __doRequest inherited from SoapClient class
     *
     * @param $request  SOAP request XML
     * @param $location location of the server (optional)
     * @param $action   SOAP action to perform (optional)
     * @param $version  SOAP version           (optional)
     *
     * @return string   content of the output buffer
     */
    public function __doRequest($request, $location, $action, $version)
    {
        /*
        $domDoc = new DOMDocument('1.0', 'UTF-8');
        $domDoc->preserveWhiteSpace = false;
        try {
            //loads the SOAP request to the Document
            $domDoc->loadXML($request);

        } catch (DOMException $e) {
            die('Parse error in the request.');
        }

        $soapHeader = $domDoc->createElementNS('http://schemas.xmlsoap.org/soap/envelope/', 'SOAP-ENV:Header');

        // insert the SOAP header element at the very begin of the document
        $domDoc->documentElement->insertBefore($soapHeader, $domDoc->documentElement->firstChild);

        // rewrite request in order to be SOAP 1.0 standardized
        $soapRequest = $domDoc->saveXML();
        return parent::__doRequest($soapRequest, $location, $action, $version);
        */

        ob_start();

        $this->_server->handle($request);
        $response = ob_get_contents();
        ob_end_clean();

        return $response;
    }

}

