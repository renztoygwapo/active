<?php

  /**
   * Hyperlight for Angie initialization file
   * 
   * @package angie.vendor.hyperlight
   */

  const LESS_FOR_ANGIE_PATH = __DIR__;
  
  if(class_exists('AngieApplication', false)) {
    AngieApplication::setForAutoload(array(
      'LessForAngie' => LESS_FOR_ANGIE_PATH . '/LessForAngie.class.php',
    	'lessc' => LESS_FOR_ANGIE_PATH . '/lessc/lessc.inc.php',
    ));
  } else {
    require_once LESS_FOR_ANGIE_PATH . '/lessc/lessc.inc.php';
    require_once LESS_FOR_ANGIE_PATH . '/LessForAngie.class.php';
  } // if