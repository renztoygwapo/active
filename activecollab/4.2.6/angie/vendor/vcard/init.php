<?php

  /**
   * IMC library initialization file
   *
   * @var angie.vendor.vcard
   */

  define('VCARD_FOR_ANGIE_PATH', ANGIE_PATH . '/vendor/vcard');
  
  // vCard export directory path
  define('VCARD_EXPORT_DIR_PATH', WORK_PATH . '/contacts');
  
  AngieApplication::setForAutoload(array(
  	'vCardForAngie' => VCARD_FOR_ANGIE_PATH . '/vCardForAngie.class.php',
	  'File_IMC' => VCARD_FOR_ANGIE_PATH . '/IMC/IMC.php', 
	  'File_IMC_Build' => VCARD_FOR_ANGIE_PATH . '/IMC/IMC/Build.php', 
	  'File_IMC_Exception' => VCARD_FOR_ANGIE_PATH . '/IMC/IMC/Exception.php', 
	  'File_IMC_Parse' => VCARD_FOR_ANGIE_PATH . '/IMC/IMC/Parse.php', 
	  'File_IMC_Build_Vcard' => VCARD_FOR_ANGIE_PATH . '/IMC/IMC/Build/Vcard.php', 
	  'File_IMC_Parse_Vcard' => VCARD_FOR_ANGIE_PATH . '/IMC/IMC/Parse/Vcard.php', 
	  'File_IMC_Parse_Vcalendar' => VCARD_FOR_ANGIE_PATH . '/IMC/IMC/Parse/Vcalendar.php',
	  'File_IMC_Parse_Vcalendar_Event' => VCARD_FOR_ANGIE_PATH . '/IMC/IMC/Parse/Vcalendar/Event.php',
	  'File_IMC_Parse_Vcalendar_Events' => VCARD_FOR_ANGIE_PATH . '/IMC/IMC/Parse/Vcalendar/Events.php',
	));