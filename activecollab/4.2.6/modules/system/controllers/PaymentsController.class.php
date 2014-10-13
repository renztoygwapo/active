<?php

  // Build on top of framework controller
  AngieApplication::useController('fw_payments', PAYMENTS_FRAMEWORK);
  
  /**
   * Payments  controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class PaymentsController extends FwPaymentsController {

    
  }