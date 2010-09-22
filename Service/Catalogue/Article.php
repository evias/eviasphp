<?php

class eVias_Service_Catalogue_Article
    extends eVias_Service_Catalogue
{
    public function __construct() {
        $this->_ormClass = 'eVias_Catalogue_Article';
        parent::__construct();
    }

    public function getAll() {
        return eVias_ArrayObject_Db::fetchAll($this->_ormClass);
    }
}
