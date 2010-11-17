<?php

require_once ('/srv/srv_eviasdev/www/eviasweb/public/addon_methods.php');
require_once ('eVias/Test.php');
require_once ('eVias/Service/Soap/Blog.php');

class soapMethods
    extends eVias_Test
{
    public function testSoapServer()
    {
        $soapService = new eVias_Service_Soap_Blog(get_wsdl('http://web.evias.be/?wsdl=blog'), array());

        $this->assertEquals('articles', $soapService->getArticles());
        $this->assertEquals('categories', $soapService->getCategories());
    }
}

