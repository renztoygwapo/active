<?php

  /**
   * Public interface file
   * 
   * @package activeCollab
   */
  
  if(DIRECTORY_SEPARATOR == '\\') {
    define('PUBLIC_PATH', str_replace('\\', '/', dirname(__FILE__)));
  } else {
    define('PUBLIC_PATH', dirname(__FILE__));
  } // if
  
  define('CONFIG_PATH', realpath(PUBLIC_PATH . '/../config'));
  
  // Bootstrap and handle HTTP request
  if(is_file(CONFIG_PATH . '/config.php')) {
    require_once CONFIG_PATH . '/config.php';
    require_once ANGIE_PATH . '/init.php';
    
    AngieApplication::bootstrapForHttpRequest();
    AngieApplication::handleHttpRequest();
    
  // Prepare and execute installer
  } else {
    require_once CONFIG_PATH . '/config.empty.php';
    require_once ANGIE_PATH . '/init.php';
    
    AngieApplication::bootstrapForHttpRequest();
    
    if(isset($_POST['submitted']) && $_POST['submitted'] == 'submitted' && $_POST['installer_section']) {
      AngieApplicationInstaller::executeSection($_POST['installer_section'], $_POST);
    } else {
      AngieApplicationInstaller::render();
    } // if
  } // if
  
