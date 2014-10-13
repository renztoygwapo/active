<?php

  /**
   * Elastica for Angie initialization file
   * 
   * @package angie.vendor.elastica
   */

  const ELASTICA_FOR_ANGIE_PATH = __DIR__;

  AngieApplication::registerAutoloader(function($class) {
    $class = trim($class, '\\');

    if(str_starts_with($class, 'Elastica')) {
      $path = str_replace(array('_', '\\'), array('/', '/'), $class) . '.php';

      if(file_exists(ELASTICA_FOR_ANGIE_PATH . "/{$path}")) {
        require_once(ELASTICA_FOR_ANGIE_PATH . "/{$path}");
      } // if
    } // if
  });