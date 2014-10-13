<?php

  // Build on top of the framework controller
  AngieApplication::useController('fw_api_client_subscriptions', AUTHENTICATION_FRAMEWORK);

  /**
   * Application level API client subscriptions controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ApiClientSubscriptionsController extends FwApiClientSubscriptionsController {
  
  }