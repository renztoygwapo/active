<?php

  /**
   * Stash for Angie initialization file
   * 
   * @package angie.vendor.stash
   */

  const STASH_FOR_ANGIE_PATH = __DIR__;

  AngieApplication::registerAutoloader(function($class) {
    if(str_starts_with($class, 'Stash')) {
      $file = __DIR__ . '/' . strtr($class, '\\', '/') . '.php';
      if (file_exists($file)) {
        require $file;

        return true;
      } // if
    } // if
  });