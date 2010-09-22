<?php

abstract class eVias_Service_Abstract
{
    protected $_ormClass  = null;

    protected $_dbConfig  = array( // default init.
        'host'      => 'localhost',
        'dbname'    => 'evias',
        'username'  => 'dev',
        'password'  => 'opendev'
    );

    /*
     * PUBLIC API
     */

    public function initMe() {
        self::_initDb();
    }

    /*
     * PROTECTED API
     */

	protected function _initDb() {
        eVias_ArrayObject_Db::setDefaultAdapter(
            new Zend_Db_Adapter_Pdo_Pgsql($this->_dbConfig));
	}

}
