<?php

class eVias_Service_Catalogue_Category
    extends eVias_Service_Catalogue
{
    protected $_formFields = array(
        'title'         => array(
            'type' => 'text',
            'config' => array(
                'label' => 'Titre',)
        ),
        'description'   => array(
            'type' => 'text',
            'config' => array(
                'label' => 'Description',)
        ),
    );

    static public function getFields() {
        $object = new eVias_Catalogue_Category;
        $fields = $object->fieldNames();
        unset($object);
        return $fields;
    }

    public function __construct() {
        $this->_ormClass = 'eVias_Catalogue_Category';
        parent::__construct();
    }

    public function getAll($orderedChildren = false, $orderBy = 'category_id', $sort = 'DESC') {
        if (! $orderedChildren)
            return eVias_ArrayObject_Db::fetchAll($this->_ormClass);

        $output = array();
        $all = eVias_ArrayObject_Db::fetchAll($this->_ormClass, $orderBy, $sort);
        foreach ($all as $category) {
            $workingCategory = $category;
            $isParent = false;
            $children = array();
            if (empty($output[$category->category_id]))
                $output[$category->category_id] = $workingCategory;

            if (empty($workingCategory->parent_category_id))
                $isParent = true;

            if ($isParent)
                foreach (call_user_func($this->_ormClass . '::hasChildrenCategories', $category->category_id, true) as $childCateg)
                    $output[$childCateg->category_id] = $childCateg;
        }

        return $output;
    }

    public function getById($id) {
        return call_user_func("{$this->_ormClass}::loadById", (int)$id);
    }

    public function formFields(array $userParams) {
        $xHtml = '';
        $fieldConf = array();
        foreach ($this->_formFields as $fieldName => $field) {
            $fieldVal = empty($userParams[$fieldName]) ? '' : $userParams[$fieldName];
            $fieldConf = empty($field['config']) ? array() : $field['config'];
            $needsLabel = empty($fieldConf['label']) ? false : true;
            switch ($field['type']) {
                case 'text':
                    if ($needsLabel)
                        $xHtml .= '<label for="'.$fieldName.'">'.$fieldConf['label'].'</label>';
                    $xHtml .= '<input type="text" name="'.$fieldName.'" value="'.$fieldVal.'" />';
                    break;

                default:
                    $xHtml .= '';
                    break;
            }
        }

        return $xHtml;
    }

    public function save(array $userParams) {
        $object = new $this->_ormClass;
        $object->bind($userParams);

        try {
            $object->save();

            return true;
        }
        catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
