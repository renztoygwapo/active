<?php

  /**
   * Init JSON library
   * 
   * @package angie.library.json
   */
  
  AngieApplication::setForAutoload(array(
  	'IJSON' => ANGIE_PATH . '/classes/json/IJSON.class.php',
    
  	'JSON' => ANGIE_PATH . '/classes/json/JSON.class.php',
  	'JSONDecodeError' => ANGIE_PATH . '/classes/json/errors/JSONDecodeError.class.php',
  
    'IJavaScriptCallback' => ANGIE_PATH . '/classes/json/IJavaScriptCallback.class.php',
    'JavaScriptCallback' => ANGIE_PATH . '/classes/json/JavaScriptCallback.class.php',
  ));