<?php

class eVias_View 
	extends Zend_View
{

	const NOTICE_GOOD = 1;
	const NOTICE_WARN = 2;
	const NOTICE_ERROR= 3;
	const NOTICE_USER = 4;

	// file with tmx translations definitions
	// @var string
	static public $translation_file = null;

	// @var Zend_Translate_Adapter_Tmx
	protected $_translateAdapter = null;

	// @var array
	protected $_translateOpts = array(
		'disableNotices'	=> true,
		'logMessage'		=> 'Message [%message%] not translated for locale: %locale.',
	);

	public function init() {
		$this->setEncoding('UTF-8');

		$this->addHelperPath(dirname(__FILE__) . '/View/Helper', 
							 'eVias_View_Helper');

		$this->_initTranslator();
	}

	public function toUtf8($string) {
		$encoding = mb_detect_encoding($string, array('UTF-8', 'ISO-8859-15'));
		if ($encoding != 'UTF-8') {
			$string = mb_convert_encoding($string, 'UTF-8', $encoding);
		}
		return ($string);
	}

	public function text($string) {
		$encoding = mb_detect_encoding($string, array('UTF-8', 'ISO-8859-15'));
		if ($encoding != 'UTF-8') {
			$string = mb_convert_encoding($string, 'UTF-8', $encoding);
		}
		echo ($string);
	}

	public function notice($string, $code = self::NOTICE_LOW) {
		echo 'NOTICE [' . $code . '] : ' . $string;
	}

	public function __($string) {
		return $this->_translateAdapter->_($string);
	}

	protected function _initTranslator($lang='fr') {
		if (! isset($this->_translateAdapter)) {
			if (empty(self::$translation_file)) {
				self::$translation_file = APPLICATION_PATH . '/configs/translations.tmx';
			}
			$this->_translateAdapter = new Zend_Translate('tmx',self::$translation_file, $lang);
		}
	}
}
