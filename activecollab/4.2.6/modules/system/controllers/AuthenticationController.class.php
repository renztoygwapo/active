<?php

  // Build on top of framework authentication controller
  AngieApplication::useController('fw_authentication', AUTHENTICATION_FRAMEWORK);

  /**
   * Authentication controller
   * 
   * This controller will handle user login, logout, lost password and similar 
   * actions. It does not require login!
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class AuthenticationController extends FwAuthenticationController {
    
  }