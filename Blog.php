<?php

class eVias_Blog
	extends eVias_ArrayObject_Db
{
	protected $_tableName = 'evias_blog';

	protected $_pk = 'blog_id';

	protected $_fields = array(
		'libelle',
		'description',
		'date_creation',
		'date_updated'
	);

	public static function loadById($id) {
		$object = new self;

		return $object->_load($id);
	}
}
