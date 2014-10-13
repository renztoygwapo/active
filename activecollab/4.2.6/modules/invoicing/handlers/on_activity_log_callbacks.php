<?php

  /**
   * on_activity_log_callbacks event handler
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage helpers
   */

  /**
   * Handle on_activity_log_callbacks event
   * 
   * @param array $callbacks
   */
  function invoicing_handle_on_activity_log_callbacks(&$callbacks) {
    $callbacks['invoice/issued'] = new InvoiceIssuedActivityLogCallback();
    $callbacks['invoice/paid'] = new InvoicePaidActivityLogCallback();
    $callbacks['invoice/canceled'] = new InvoiceCanceledActivityLogCallback();
    $callbacks['invoice/new_payment'] = new InvoiceNewPaymentActivityLogCallback();
  } // invoicing_handle_on_activity_log_callbacks