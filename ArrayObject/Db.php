<?php
/**
 * @package eVias_Core
 * @author   g.saive
 */

abstract class eVias_ArrayObject_Db
	extends eVias_ArrayObject_Abstract
{
 	/**
     * table name
     *
     * @var string
     */
    protected $_tableName = null;


	/**
     * Primary key
     *
     * @var mixed
     */
    protected $_pk;

	/**
     * Sequence
     *
     * @var unknown_type
     */
    protected $_sequence;

	/**
     * Modifiable fields
     *
     * @var array
     */
    protected $_fields = array();

	/**
     * Database adapter
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     * Default database adapter to use
     *
     * @var $_default_db Zend_Db_Adapter_Abstract
     */
    static protected $_DEFAULT_DB = null;

    /**
     * Sets the default database adapter to use
     *
     * @param Zend_Db_Adapter_Abstract $db The database adapter
     *
     * @return void
     */
    public static function setDefaultAdapter(Zend_Db_Adapter_Abstract $db)
    {
        self::$_DEFAULT_DB = $db;
    }

    /**
     * Returns default database adapter
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public static function getDefaultAdapter()
    {
        return self::$_DEFAULT_DB;
    }

    /**
     * Sets database adapter to use
     *
     * @param Zend_Db_Adapter_Abstract $db The database adapter
     *
     * @return eVias_ArrayObject_Db
     */
    public function setAdapter(Zend_Db_Adapter_Abstract $db)
    {
        $this->_db = $db;
        return $this;
    }

    /**
     * Returns defined database adapter
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function getAdapter()
    {
        if ( ! isset($this->_db) ) {
            $this->_db = self::$_DEFAULT_DB;
        }
        return $this->_db;
	}

	public function primaryKey() {
		return $this->_pk;
	}

	public static function fetchAll($className, $orderBy = '', $sort = '') {
		if (is_null(self::getDefaultAdapter())) {
			return false;
		}

        if (! class_exists($className)) {
            return false;
        }

		$object		= new $className;
		$arrayOut	= array();
        if (empty($orderBy) && in_array('date_creation', $object->fieldNames())) {
            $orderBy = ' date_creation ';
            $sort    = ' ASC ';
        }

        $orderBy = ' ORDER BY ' . $orderBy . ' ' . $sort;

		$query		= "
			SELECT
				" . implode(', ', $object->fieldNames()) . "
			FROM
				" . $object->tableName() . "
			WHERE TRUE
            $orderBy
		";

		$result = $object->getAdapter()->fetchAll($query);

		if (! $result) {
			throw new eVias_Catalogue_Exception('No ' . $className . ' entries found.');
		}

		foreach ($result as $index => $row) {
			$tmpObject = new $className;
			$tmpObject->bind($row);
			$arrayOut[] = $tmpObject;
			unset($tmpObject);
		}

		return $arrayOut;
	}

    /**
     * Return the primary keys values
     *
     * @return array
     */
    public function getPrimaryKey()
    {
        if (is_array($this->_pk)) {
            $pk_values = array();
            if (isset($this->_pk)) {
                foreach ($this->_pk as $pkey) {
                    $pk_values[$pkey] = $this->{$pkey};
                }
            }

            return $pk_values;
        } else {
            return $this->{$this->_pk};
        }
    }

    public function issetPrimaryKey()
    {
        return ! empty($this->{$this->_pk});
    }

    /**
     * Return the last sequence id
     *
     * @return mixed
     */
    public function getLastSequenceId()
    {
        $db = $this->getAdapter();
        try {
            $lastSequenceId = $db->lastSequenceId($this->_sequence);
        }
        catch (Exception $e) {
            $sql = 'SELECT last_value FROM ' . $this->_sequence . ';';
            $lastSequenceId = $db->fetchOne($sql);
        }
        return $lastSequenceId;
    }

    /**
     * Load object from database
     *
     * @param mixed $id The value of the primary key
     *    if array, then format:
     * ['pk_name1' => 'pk_val1', 'pk_name2' => 'pk_val2']
     *
     * @return eVias_ArrayObject_Abstract
     */
    protected function _load($id)
    {
        if ( is_array($this->_pk) ) {
            if ( ! is_array($id) ) {
                throw new eVias_ArrayObject_Exception( 'There is more than one primary key for ' . $this->_tableName . ', the data you wan\'t the object to be loaded with should be an array.');
            }

            if ( count($this->_pk) != count($id) ) {
                throw new eVias_ArrayObject_Exception( 'The count of primary keys should be equal to the count of data rows.' );
            }

            $whereTab = array();
            foreach ($this->_pk as $key) {
                $whereTab[] = $key . ' = :' . $key;
                $params[$key] = $id[$key];
            }

            $where = implode( ' and ', $whereTab );
        }
        else {
            $where = $this->_pk . ' = :id';
            $params = array('id' => $id);
        }

        return $this->_fetch($where, $params);
    }

    /**
     * Load object according to a where condition
     *
     * @param string $where  the select conditions with parameters
     * @param array  $params the parameters
     *
     * @return eVias_ArrayObject_Abstract
     */
    protected function _fetch($where, array $params = array())
    {
        $fields = implode(', ', array_merge((array) $this->_pk, $this->_fields));

        $sql = 'SELECT ' . $fields . ' FROM ' . $this->_tableName . ' WHERE ' . $where . ';';

        $row = $this->getAdapter()->fetchRow($sql, $params);
        if ( empty($row) ) {
            $formattedParams = str_replace(array("\n", "\r"), '', var_export($params, true));
            throw new eVias_ArrayObject_Exception($this->_tableName . ' (' . $where . ' : ' . $formattedParams . ')  does not exist');
        }

        $this->reset();
        $this->bind($row);

        return $this;
    }

    public function save() {
        $this->_save();

        return $this;
    }

    public function toArray() {
        $keys = $this->fieldNames();
        $params = array();
        foreach ($keys as $key) {
            $params[$key] = $this->$key;
        }
        return $params;
    }

    /**
     * Save data into database
     *
     * @return E_ArrayObject_Abstract
     */
    protected function _save()
    {
        $values = $this->getPrimaryKey();

        if (! isset($values) || empty($values)) {
            $this->_insert();
        }
        else {
            $this->_update();
        }

        return $this;
    }

    /**
     * Insert data into the database
     *
     * @return E_ArrayObject_Abstract
     */
    protected function _insert()
    {
        $this->_preInsert();

        $values = $this->asArray();

        $this->getAdapter()->insert($this->_tableName, $values);

        if ( ! empty($this->_sequence) ) {
            parent::__set($this->_pk, $this->getLastSequenceId());
        }

        return $this;
    }

    /**
     * Update data into the database
     *
     * @return E_ArrayObject_Abstract
     */
    protected function _update()
    {
        if ( empty($this->{$this->_pk})) {
            throw new eVias_ArrayObject_Exception('Could not update, primary key is not set');
        }

        $this->_preUpdate();

        $db = $this->getAdapter();

        $values = $this->asArray();

        $db->update($this->_tableName, $values, $this->_getWhereClause());

        return $this;
    }

    /**
     * Delete data from the database
     *
     * @return eVias_ArrayObject_Abstract
     */
    protected function _delete($where)
    {
        $db = $this->getAdapter();

        $db->delete($this->_tableName, $where);

        return $this;
    }

    /**
     * Pre insert logic
     *
     * @return void
     */
    protected function _preInsert()
    {
        $this->_setDateCreation()
            ->_setDateUpdate();
    }

    /**
     * Pre update logic
     *
     * @return void
     */
    protected function _preUpdate()
    {
        $this->_setDateUpdate();
    }

    /**
     * Set the creation date
     *
     * @return eVias_ArrayObject_Abstract
     */
    protected function _setDateCreation()
    {
        if ( in_array('date_creation', $this->_fields) && empty($this->date_creation) ) {
            $this->date_creation = date('Y-m-d H:i:s');
        }
        return $this;
    }

    /**
     * Set the update date
     *
     * @return eVias_ArrayObject_Abstract
     */
    protected function _setDateUpdate()
    {
        if ( in_array('date_update', $this->_fields) ) {
            $this->date_update = date('Y-m-d H:i:s');
        }
        return $this;
    }

    /**
     * Returns a where clause where the parameters are already parsed.
     *
     * @return string
     */
    protected function _getWhereClause( ) {
        $where = '';
        if (is_array($this->_pk)) {
            $values = $this->getPrimaryKey();

            $sqlTab = $keysTab = $whereTab = array();

            foreach ($this->_pk as $key) {
                $sqlTab[]   = $key . ' = ?';
                $keysTab[]  = $key;
            }

            for ($i = 0; $i < count($sqlTab); $i++) {
                $whereTab[] = $this->_db->quoteInto( $sqlTab[$i], $values[$keysTab[$i]] );
            }

            $where = implode( ' and ', $whereTab );
        }
        else {
            $where = $this->_db->quoteInto($this->_pk . ' = ?', $this->getPrimaryKey());
        }

        return $where;
    }

    /**
     * Convert a boolean into its string representation
     *
     * @param  $bool boolean
     * @return string
     */
    public function booleanToString($bool)
    {
        return $bool ? 't' : 'f';
    }

    /**
     * Convert a string value into its boolean represantation
     *
     * @param  $bool boolean
     * @return string
     */
    public function stringToBoolean($str)
    {
        return ($str == 'true' || $str == 'TRUE' || $str == 't' || $str == 'T');
	}

	public function fieldNames() {
		return array_merge((array)$this->_pk, $this->_fields);
	}

	public function tableName() {
		if (empty($this->_tableName)) {
			throw new eVias_ArrayObject_Exception('Table name for object is not set.');
		}

		return $this->_tableName;
	}

}
