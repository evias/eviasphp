<?php

class eVias_Catalogue_Category
	extends eVias_ArrayObject_Db
{
	protected $_tableName = 'evias_catalogue_category';

	protected $_pk = 'category_id';

	protected $_fields = array(
		'parent_category_id', // recursive foreign key
		'title',
		'description',
        'date_creation',
        'date_updated'
	);

	public static function loadById($id) {
        try {
            $object = new self;
		    $object->_load((int)$id);

            return $object;
        }
        catch (eVias_ArrayObject_Exception $e) {
            return false;
        }
	}

    // @return bool if ! $returnThem
    // @array of children if $returnThem
    public static function hasChildrenCategories($category_id, $returnThem = false) {
        $object = new self;
        $fields = implode(',', $object->fieldNames());
        $tableName = $object->tableName();
        $query = "
            SELECT
                $fields
            FROM
                $tableName
            WHERE
               parent_category_id = :id
        ";

        $stmt = self::getDefaultAdapter()->query($query, array('id' => $category_id));

        if (! empty($row) && ! $returnThem)
            return true; // has children boolean
        elseif (empty($row) && ! $returnThem)
            return false; // hasn't got any children boolean

        // return them
        $children = array();
        while ($row = $stmt->fetch()) {
            $object = new self;
            $object->bind($row);
            $children['category_id'] = $object;
            unset($object);
        }

        return $children;
    }
}
