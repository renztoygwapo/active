<?php

  /**
   * Display CAPTCHA
   * 
   * @package angie.frameworks.environment
   * @subpackage proxies
   */
  abstract class FwCaptchaProxy extends ProxyRequestHandler {
  
    /**
     * Print captch
     */
    function execute() {
      require_once ANGIE_PATH . '/functions/files.php';
      require_once ANGIE_PATH . '/classes/captcha/Captcha.class.php';
    
      $captcha = new Captcha(200,30);
      $captcha->Create();
    } // execute
    
  }