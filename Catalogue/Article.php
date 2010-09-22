<?php

class eVias_Catalogue_Article
	extends eVias_ArrayObject_Db
{

	/**
	 * _tableName : database table name
	 * @var string protected
	 */
	protected $_tableName = 'evias_catalogue_article';

	/**
	 * _pk : primary key field name
	 * @var string protected
	 */
	protected $_pk = 'article_id';

	/**
	 * _fields : list of table's field, pk excluded
	 * @var array of string protected
	 */
	protected $_fields = array(
		'title',
		'description',
		'date_creation',
		'date_updated',
	);

    static public function loadById($id) {
        try {
            $object = $this->_load($id);

            return $object;
        }
        catch (eVias_ArrayObject_Exception $e) {
            return false; // article does not exist
        }
    }

}
