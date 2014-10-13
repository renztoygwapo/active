<?php

  /**
   * Init invoicing module
   *
   * @package activeCollab.modules.invoicing
   */

  const INVOICING_MODULE = 'invoicing';
  const INVOICING_MODULE_PATH = __DIR__;

  const INVOICE_STATUS_DRAFT = 0;
  const INVOICE_STATUS_ISSUED = 1;
  const INVOICE_STATUS_PAID = 2;
  const INVOICE_STATUS_CANCELED = 3;
  
  const QUOTE_STATUS_DRAFT = 0;
  const QUOTE_STATUS_SENT = 1;
  const QUOTE_STATUS_WON = 2;
  const QUOTE_STATUS_LOST = 3;
  
  define('INVOICES_WORK_PATH', WORK_PATH . '/invoices');
  
  const INVOICE_NUMBER_COUNTER_TOTAL = ':invoice_in_total';
  const INVOICE_NUMBER_COUNTER_YEAR = ':invoice_in_year';
  const INVOICE_NUMBER_COUNTER_MONTH = ':invoice_in_month';
  
  const INVOICE_VARIABLE_CURRENT_YEAR = ':current_year';
  const INVOICE_VARIABLE_CURRENT_MONTH = ':current_month';
  const INVOICE_VARIABLE_CURRENT_MONTH_SHORT = ':current_short_month';
  const INVOICE_VARIABLE_CURRENT_MONTH_LONG = ':current_long_month';

  AngieApplication::useModel(array(
    'invoice_objects',
    'invoice_object_items',
    'invoices',
    'invoice_items',
    'invoice_payments',
    'tax_rates',
    'invoice_item_templates',
    'invoice_note_templates',
    'quotes',
    'quote_items',
    'recurring_profiles',
    'recurring_profile_items',
  ), INVOICING_MODULE);
  
  require INVOICING_MODULE_PATH . '/functions.php';
  
  AngieApplication::setForAutoload(array(
    'IInvoice' => INVOICING_MODULE_PATH . '/models/IInvoice.class.php',
    'InvoiceTemplate' => INVOICING_MODULE_PATH . '/models/InvoiceTemplate.class.php',
    'IQuoteCommentsImplementation' => INVOICING_MODULE_PATH . '/models/IQuoteCommentsImplementation.class.php',
    'IQuoteAttachmentsImplementation' => INVOICING_MODULE_PATH . '/models/IQuoteAttachmentsImplementation.class.php',
    'IQuoteSubscriptionsImplementation' => INVOICING_MODULE_PATH . '/models/IQuoteSubscriptionsImplementation.class.php',
    'IInvoicePaymentsImplementation' => INVOICING_MODULE_PATH . '/models/IInvoicePaymentsImplementation.class.php',
    'RecurringInvoice' => INVOICING_MODULE_PATH . '/models/RecurringInvoice.class.php',
    'QuoteComment' => INVOICING_MODULE_PATH . '/models/QuoteComment.class.php',
    'QuoteAttachment' => INVOICING_MODULE_PATH . '/models/QuoteAttachment.class.php',
    'InvoicePDFGenerator' => INVOICING_MODULE_PATH . '/models/InvoicePDFGenerator.class.php',

    // State
    'IInvoiceObjectStateImplementation' => INVOICING_MODULE_PATH . '/models/IInvoiceObjectStateImplementation.class.php',
    'IInvoiceStateImplementation' => INVOICING_MODULE_PATH . '/models/IInvoiceStateImplementation.class.php',
    'IQuoteStateImplementation' => INVOICING_MODULE_PATH . '/models/IQuoteStateImplementation.class.php',
    'IRecurringProfileStateImplementation' => INVOICING_MODULE_PATH . '/models/IRecurringProfileStateImplementation.class.php',

    // Invoice Based On
    'IInvoiceBasedOnImplementation' => INVOICING_MODULE_PATH . '/models/invoice_based_on/IInvoiceBasedOnImplementation.class.php',
    'IInvoiceBasedOnQuoteImplementation' => INVOICING_MODULE_PATH . '/models/invoice_based_on/IInvoiceBasedOnQuoteImplementation.class.php',

    'IInvoiceBasedOnTrackedDataImplementation' => INVOICING_MODULE_PATH . '/models/invoice_based_on/tracked_data/IInvoiceBasedOnTrackedDataImplementation.class.php',
    'IInvoiceBasedOnMilestoneImplementation' => INVOICING_MODULE_PATH . '/models/invoice_based_on/tracked_data/IInvoiceBasedOnMilestoneImplementation.class.php',
    'IInvoiceBasedOnTaskImplementation' => INVOICING_MODULE_PATH . '/models/invoice_based_on/tracked_data/IInvoiceBasedOnTaskImplementation.class.php',

    'IInvoiceBasedOnTrackingReportResultImplementation' => INVOICING_MODULE_PATH . '/models/invoice_based_on/tracked_data/report_results/IInvoiceBasedOnTrackingReportResultImplementation.class.php',
    'IInvoiceBasedOnTrackingReportImplementation' => INVOICING_MODULE_PATH . '/models/invoice_based_on/tracked_data/report_results/IInvoiceBasedOnTrackingReportImplementation.class.php',
    'IInvoiceBasedOnProjectImplementation' => INVOICING_MODULE_PATH . '/models/invoice_based_on/tracked_data/report_results/IInvoiceBasedOnProjectImplementation.class.php',

    // Invoice Activity Log
    'IInvoiceActivityLogsImplementation' => INVOICING_MODULE_PATH . '/models/IInvoiceActivityLogsImplementation.class.php',
    'IInvoiceInspectorImplementation' => INVOICING_MODULE_PATH . '/models/IInvoiceInspectorImplementation.class.php',
   
    'InvoiceIssuedActivityLogCallback' => INVOICING_MODULE_PATH . '/models/javascript_callbacks/InvoiceIssuedActivityLogCallback.class.php', 
    'InvoicePaidActivityLogCallback' => INVOICING_MODULE_PATH . '/models/javascript_callbacks/InvoicePaidActivityLogCallback.class.php', 
    'InvoiceCanceledActivityLogCallback' => INVOICING_MODULE_PATH . '/models/javascript_callbacks/InvoiceCanceledActivityLogCallback.class.php', 
    'InvoiceNewPaymentActivityLogCallback' => INVOICING_MODULE_PATH . '/models/javascript_callbacks/InvoiceNewPaymentActivityLogCallback.class.php',

    'InvoicesFilter' => INVOICING_MODULE_PATH . '/models/invoicing_filters/InvoicesFilter.class.php',
    'DetailedInvoicesFilter' => INVOICING_MODULE_PATH . '/models/invoicing_filters/DetailedInvoicesFilter.class.php',
    'SummarizedInvoicesFilter' => INVOICING_MODULE_PATH . '/models/invoicing_filters/SummarizedInvoicesFilter.class.php',

    // Notifications
    'QuoteNotification' => INVOICING_MODULE_PATH . '/notifications/QuoteNotification.class.php',
    'InvoiceStatusChangedNotification' => INVOICING_MODULE_PATH . '/notifications/InvoiceStatusChangedNotification.class.php',
    'InvoiceIssuedNotification' => INVOICING_MODULE_PATH . '/notifications/InvoiceIssuedNotification.class.php',
    'InvoicePaidNotification' => INVOICING_MODULE_PATH . '/notifications/InvoicePaidNotification.class.php',
    'InvoiceCanceledNotification' => INVOICING_MODULE_PATH . '/notifications/InvoiceCanceledNotification.class.php',
    'QuoteSentNotification' => INVOICING_MODULE_PATH . '/notifications/QuoteSentNotification.class.php',
    'QuoteUpdatedNotification' => INVOICING_MODULE_PATH . '/notifications/QuoteUpdatedNotification.class.php',
    'RecurringProfileNotification' => INVOICING_MODULE_PATH . '/notifications/RecurringProfileNotification.class.php',
    'InvoiceGeneratedViaRecurringProfileNotification' => INVOICING_MODULE_PATH . '/notifications/InvoiceGeneratedViaRecurringProfileNotification.class.php',
    'DraftInvoiceCreatedViaRecurringProfileNotification' => INVOICING_MODULE_PATH . '/notifications/DraftInvoiceCreatedViaRecurringProfileNotification.class.php',
    'RecurringProfileArchivedNotification' => INVOICING_MODULE_PATH . '/notifications/RecurringProfileArchivedNotification.class.php',
    'InvoiceReminderNotification' => INVOICING_MODULE_PATH . '/notifications/InvoiceReminderNotification.class.php'
  ));
  
  DataObjectPool::registerTypeLoader('Invoice', function($ids) {
    return Invoices::findByIds($ids);
  });
  
  DataObjectPool::registerTypeLoader('Quote', function($ids) {
    return Quotes::findByIds($ids);
  });

  DataObjectPool::registerTypeLoader('RecurringProfile', function($ids) {
    return RecurringProfiles::findByIds($ids);
  });

  DataObjectPool::registerTypeLoader('TaxRate', function($ids) {
    return TaxRates::findByIds($ids);
  });
