<?php

class eVias_Controller_Action extends Zend_Controller_Action
{
    public function init() {
		// Add modules' directory to inject delcared modules
		$this->getFrontController()->addModuleDirectory(APPLICATION_PATH . '/modules');

		// initialize authentication
	}

	public function _initAuth() {

	}
}
