<?php

abstract class eVias_Service_Soap_Abstract
{
    protected $_methods     = array();
    protected $_className   = null;

    public function getMethods()
        { return $this->_methods; }

    public function getClass()
        { return $this->_className; }

}

