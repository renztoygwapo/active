<?php

  /**
   * Simple HDML DOM for Angie initialization file
   * 
   * @package angie.vendor.simplehtmldom
   */

  define('SIMPLEHTMLDOM_FOR_ANGIE_PATH', ANGIE_PATH . '/vendor/simplehtmldom');
  
  AngieApplication::setForAutoload(array(
    'SimpleHTMLDomForAngie' => SIMPLEHTMLDOM_FOR_ANGIE_PATH . '/SimpleHTMLDomForAngie.php',
    'simple_html_dom' => SIMPLEHTMLDOM_FOR_ANGIE_PATH . '/simplehtmldom/simple_html_dom.php',
  ));