<?php

  /**
   * Init controller classes and resources
   *
   * @package angie.library.controller
   */
  
  require_once ANGIE_PATH . '/classes/controller/Controller.class.php';
  
  AngieApplication::setForAutoload(array(
    'ControllerDnxError' => ANGIE_PATH.  '/classes/controller/errors/ControllerDnxError.class.php', 
    'ControllerActionDnxError' => ANGIE_PATH.  '/classes/controller/errors/ControllerActionDnxError.class.php',
    
    'FwRequest' => ANGIE_PATH.  '/classes/controller/request/FwRequest.class.php',
    
    'FwResponse' => ANGIE_PATH.  '/classes/controller/response/FwResponse.class.php',
    'BaseHttpResponse' => ANGIE_PATH.  '/classes/controller/response/BaseHttpResponse.class.php',
  	'ApiResponse' => ANGIE_PATH.  '/classes/controller/response/ApiResponse.class.php',
  ));