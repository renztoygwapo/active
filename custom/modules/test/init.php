<?php

	const TEST_MODULE = 'test';
	const TEST_MODULE_PATH = __DIR__;
	

	AngieApplication::setForAutoload(array(
    	'TestHomeScreenWidget' => TEST_MODULE_PATH.'/models/homescreen_widgets/TestHomeScreenWidget.class.php'
  	));