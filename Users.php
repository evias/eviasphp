<?php

class eVias_Users
	extends eVias_ArrayObject_Db
{
	protected $_tableName = 'evias_users';

	protected $_pk = 'user_id';

	protected $_fields = array(
		'access_name',
		'access_pass',
        'realname',
        'last_login',
		'date_creation',
		'date_updated'
	);

	public static function loadById($id) {
		$object = new self;

		return $object->_load($id);
	}

    public static function loadByLogin($login) {
        $object = new self;
        $fields = implode(', ', $object->fieldNames());
        $table  = $object->tableName();

        $query = "
            SELECT
                $fields
            FROM
                $table
            WHERE
                access_name = :login
            LIMIT 1
        ";

        $result = $object->getAdapter()->fetchRow($query, array('login'=>$login));

        if (! $result) {
            return false;
        }

        return $object->bind($result);
    }
}
