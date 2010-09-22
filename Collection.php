<?php

class eVias_Collection
{
	protected $_entries = array();
	protected $_index = 0;

	public function __construct($initData = array()) {
		if (!empty($initData) && is_array($initData)) {
			$this->_entries = $initData;
		}
	}

	public function add($entry) {
		$this->_entries[] = $entry;
	}

	public function get($idx = null) {
		$i = ! is_null($idx) ? $idx : $this->_index;

		if (isset($this->_entries[$i])) {

			if (is_null($idx)) {
				// having read one more, we need to increment
				// the internal index pointer

				$this->_index++;
			}

			return $this->_entries[$i];
		}
		else {
			return false;
		}
	}

    public function set ($idx, $val) {
        $this->_entries[$idx] = $val;
    }

    public function rewind () {
        $this->_index = 0;
    }
}
