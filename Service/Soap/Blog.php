<?php

require_once ('eVias/Service/Soap.php');

class eVias_Service_Soap_Blog
    extends eVias_Service_Soap
{
    protected $_methods = array(
        'getArticles',
        'getCategories',
    );

    protected $_className = 'eVias_Service_Soap_Blog';

/** public : **/

    public function getArticles()
    {
        return 'articles';
    }

    public function getCategories()
    {
        return 'categories';
    }

}

