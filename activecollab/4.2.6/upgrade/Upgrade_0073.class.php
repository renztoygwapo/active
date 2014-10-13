<?php

  /**
   * Update activeCollab 3.3.9 to activeCollab 3.3.10
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0073 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.3.9';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.3.10';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'recalculateInvoices' => 'Recalculate invoices balance due and paid amount',
      );
    } // getActions

    /**
     * Recalculate Invoices balance due
     *
     * @return boolean
     */
    function recalculateInvoices() {
      if($this->isModuleInstalled('invoicing')) {
        try {
          $invoice_objects_table = TABLE_PREFIX . 'invoice_objects';
          $payments_table = TABLE_PREFIX . 'payments';
          $invoices = DB::execute("SELECT id, total FROM $invoice_objects_table WHERE type = ?", 'Invoice');
          if(is_foreachable($invoices)) {
            foreach($invoices as $invoice) {
              $paid_amount = DB::executeFirstCell("SELECT SUM(amount) FROM $payments_table WHERE parent_type = ? AND parent_id = ? AND status = ?", 'Invoice', $invoice['id'], 'Paid');
              $paid_amount = $paid_amount ? $paid_amount : 0;

              if(function_exists('bcsub')) {
                $balance_due = bcsub($invoice['total'],$paid_amount,3);
              } else {
                $balance_due = $invoice['total'] - $paid_amount;
              }//if
              DB::execute("UPDATE $invoice_objects_table SET balance_due = ?, paid_amount = ? WHERE id = ?", $balance_due, $paid_amount, $invoice['id']);
            } //foreach

          } //if

        } catch (Exception $e) {
          return $e->getMessage();
        } // try
      } // if

      return true;
    } // recalculateInvoiceBalanceDue

  }