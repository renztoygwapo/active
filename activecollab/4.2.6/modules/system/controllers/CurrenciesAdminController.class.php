<?php

  // Build on top of framework level controller
  AngieApplication::useController('fw_currencies_admin', GLOBALIZATION_FRAMEWORK);
  
  /**
   * Application level currencies administration controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class CurrenciesAdminController extends FwCurrenciesAdminController {
    
  }