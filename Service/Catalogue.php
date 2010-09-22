<?php

class eVias_Service_Catalogue
	extends eVias_Service_Abstract
{

    static private $_catalogueList = array();

	public static function initServiceCatalogue() {
        // will init if needed
		parent::_initDb();
	}

    /*
     *
     * PUBLIC API
     *
     *
     */

    public function __construct() {
        $this->_dbConfig  = array(
            'host'      => 'localhost',
            'dbname'    => 'evias',
            'username'  => 'dev',
            'password'  => 'opendev'
        );

        if (empty($this->_ormClass)) // not yet defined from children
            $this->_ormClass = 'eVias_Catalogue';

        $this->initMe();
    }

    public function getAll() {
        // @todo collection
        return eVias_ArrayObject_Db::fetchAll($this->_ormClass);
    }

	/**
	 * @todo: move into Abstract
	 *		  define special-edit only in this service.
	 *		  this one is pretty generic and works fine
	 */
	public function editCatalogue($catObject, $newData=array()) {
		foreach ($newData as $key => $value) {
			if (! in_array($key, $catObject->fieldNames()))
				throw new eVias_Service_Catalogue_Exception('Trying to change key: ' . $key . ', which doesn\'t exist');

			if (! $this->_validateField($key, $value)) {
				throw new eVias_Service_Catalogue_Exception('Could not validate the field: ' . $key . ' with : ' .$value);
			}
			$catObject->$key = $value;

		}

		return $catObject;
	}
	/**
	 * @todo: Use validators
	 */
	protected function _validateField($key, $value) {
		if (empty($key))
			throw new eVias_Service_Catalogue_Exception('Validating an empty key.');

		switch($key) {
			case 'title':
			{
				if (empty($value))
					return false;
			}

			default:
		}

		return true;
	}
}
