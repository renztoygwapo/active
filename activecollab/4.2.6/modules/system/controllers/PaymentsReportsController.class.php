<?php

  // Build on top of framework level controller
  AngieApplication::useController('fw_payments_reports', PAYMENTS_FRAMEWORK);
  
  /**
   * Payment reports controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class PaymentsReportsController extends FwPaymentsReportsController {
    
  }