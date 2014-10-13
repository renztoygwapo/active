<?php

  /**
   * Handle on_extra_stats event
   *
   * @package activeCollab.modules.invoicing
   * @subpackage handlers
   */

  /**
   * Populate extra stats
   *
   * @param array $stats
   */
  function invoicing_handle_on_extra_stats(&$stats) {
    $invoice_objects_table = TABLE_PREFIX . 'invoice_objects';

    $stats['gateways'] = PaymentGateways::count(array('is_enabled > 0'));
    $stats['invoices'] = '0,';
    $stats['quotes'] = '0,';
    $stats['rinvoices'] = '0,';

    $counts = DB::execute("SELECT type, COUNT(id) AS 'record_count' FROM $invoice_objects_table GROUP BY type");
    if($counts) {
      foreach($counts as $count) {
        if($count['type'] == 'Invoice') {
          $key = 'invoices';
        } elseif($count['type'] == 'Quote') {
          $key = 'quotes';
        } elseif($count['type'] == 'RecurringProfile') {
          $key = 'rinvoices';
        } else {
          $key = null;
        } // if

        if($key) {
          $stats[$key] = (integer) $count['record_count'] . ',' . DB::executeFirstCell("SELECT MAX(DATE(created_on)) FROM $invoice_objects_table WHERE type = ?", $count['type']);
        } // if
      } // foreach
    } // if
  } // invoicing_handle_on_extra_stats