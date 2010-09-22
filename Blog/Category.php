<?php

class eVias_Blog_Category
	extends eVias_ArrayObject_Db
{
	protected $_tableName = 'evias_blog_category';

	protected $_pk = 'category_id';

	protected $_fields = array(
		'libelle',
		'description',
		'blog_id',
		'date_creation',
		'date_updated'
	);

	public static function loadById($id) {
		$object = new self;

		return $object->_load($id);
	}
}
