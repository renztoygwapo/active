<?php

  /**
   * Main upgrade file
   */

  if(DIRECTORY_SEPARATOR == '\\') {
    define('PUBLIC_PATH', realpath(str_replace('\\', '/', dirname(__FILE__)) . '/..'));
  } else {
    define('PUBLIC_PATH', realpath(dirname(__FILE__) . '/..'));
  } // if
  
  define('CONFIG_PATH', realpath(PUBLIC_PATH . '/../config'));
  define('IGNORE_MAINTENANCE_MESSAGE', true); // Make sure that we ignore MAINTENANCE_MESSAGE option from config/config.php
  
  // Bootstrap and handle HTTP request
  if(is_file(CONFIG_PATH . '/config.php')) {
    require_once CONFIG_PATH . '/config.php';
    require_once ANGIE_PATH . '/init.php';
    
    AngieApplication::bootstrapForUpgrade();
    
    if(isset($_POST['submitted']) && $_POST['submitted'] == 'submitted') {
      if(isset($_POST['upgrade_step']) && is_array($_POST['upgrade_step'])) {
        AngieApplicationUpgrader::executeAction($_POST['upgrade_step']);
      } elseif(isset($_POST['list_steps']) && $_POST['list_steps']) {
        AngieApplicationUpgrader::listActions();
      } elseif(isset($_POST['upgrader_section']) && $_POST['upgrader_section']) {
        AngieApplicationUpgrader::executeSection($_POST['upgrader_section'], $_POST);
      } else {
        die('Invalid request');
      } // if
    } else {
      AngieApplicationUpgrader::render();
    } // if
  } else {
    die('activeCollab not installed');
  } // if