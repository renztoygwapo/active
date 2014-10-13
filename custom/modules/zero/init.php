<?php

  /**
   * My Reports module initialization file
   */
  
  const MY_REPORTS_MODULE = 'zero';
  const MY_REPORTS_MODULE_PATH = __DIR__;
  
  AngieApplication::setForAutoload(array(
    'ZeroHomeScreenWidget' => MY_REPORTS_MODULE_PATH.'/models/homescreen_widgets/ZeroHomeScreenWidget.class.php'
  ));