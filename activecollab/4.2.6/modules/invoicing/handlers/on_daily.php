<?php

  /**
   * Invoicing handle daily tasks
   *
   * @package activeCollab.modules.invoicing
   * @subpackage handlers
   */
  
  /**
   * Do daily taks
   */
  function invoicing_handle_on_daily() {
    require_once INVOICING_MODULE_PATH . '/models/RecurringInvoice.class.php';
    RecurringInvoice::createFromRecurringProfile();

    // Send invoice overdue reminders
    require_once INVOICING_MODULE_PATH . '/models/InvoiceOverdueReminders.class.php';
    InvoiceOverdueReminders::send();
  } // system_handle_on_daily