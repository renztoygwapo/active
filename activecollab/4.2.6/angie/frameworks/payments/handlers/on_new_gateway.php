<?php

	/**
   * Payment gateways on_new_gateway handler
   *
   * @package activeCollab.modules.payments
   * @subpackage handlers
   */
  
  /**
   * Handler
   *
   * @param NamedList $defined_gateways
   * @param User $logged_user
   */
  function payments_handle_on_new_gateway(&$defined_gateways,IUser &$logged_user) {
    if($logged_user->isAdministrator()) {
      
      $defined_gateways[] = new PaypalDirectGateway();
      $defined_gateways[] = new PaypalExpressCheckoutGateway();
      $defined_gateways[] = new AuthorizeAimGateway();
      $defined_gateways[] = new StripeGateway();
      $defined_gateways[] = new BrainTreeGateway();

    }//if
    
  }