<?php

  /**
   * Update activeCollab 3.3.14 to activeCollab 3.3.15
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0078 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.3.14';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.3.15';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'updateInvoiceItemTemplateDescriptionField' => 'Updating invoice item template description field',
        'recalculateCertainInvoiceObjects' => 'Recalculating certain invoice objects',
      );
    } // getActions

    /**
     * Update invoice item template description field
     *
     * @return bool|string
     */
    function updateInvoiceItemTemplateDescriptionField() {
      if (!$this->isModuleInstalled('invoicing')) {
        return true;
      } // if
      
      try {
        DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'invoice_item_templates CHANGE description description TEXT');
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // updateInvoiceItemTemplateDescriptionField

    /**
     * taxes ID/rate map
     *
     * @var array
     */
    private $taxes = false;

    /**
     * List of invoices ids who have second tax enabled
     *
     * @var array
     */
    private $invoices_with_second_tax = array();

    /**
     * List of invoices ids who have second compound tax
     *
     * @var array
     */
    private $invoices_with_compound_second_tax = array();

    /**
     * Recalculate Item
     *
     * @param integer $parent_id
     * @param float $item_quantity
     * @param float $item_unit_cost
     * @param integer $first_tax_rate_id
     * @param integer $second_tax_rate_id
     * @return array
     */
    function recalculateItem($parent_id, $item_quantity, $item_unit_cost, $first_tax_rate_id = null, $second_tax_rate_id = null) {
      // item subtotal
      $item_subtotal = $item_quantity * $item_unit_cost;

      // item tax
      $item_first_tax = 0;
      if ($first_tax_rate_id && array_key_exists($first_tax_rate_id, $this->taxes)) {
        $item_first_tax = round($item_subtotal * $this->taxes[$first_tax_rate_id], 2);
      } // if

      $item_second_tax = 0;
      if ($second_tax_rate_id && array_key_exists($second_tax_rate_id, $this->taxes) && in_array($parent_id, $this->invoices_with_second_tax)) {
        if (in_array($parent_id, $this->invoices_with_compound_second_tax)) {
          // perform compound second tax calculation
          $item_second_tax = round(($item_subtotal + $item_first_tax) * $this->taxes[$second_tax_rate_id], 2);
        } else {
          // perform non compound second tax calculation
          $item_second_tax = round($item_subtotal * $this->taxes[$second_tax_rate_id], 2);
        } // if
      } // if

      // item total
      $item_total = $item_subtotal + round($item_first_tax, 2) + round($item_second_tax, 2);

      // return the values
      return array($item_subtotal . '', $item_first_tax . '', $item_second_tax . '', $item_total . '');
    } // recalculateItem

    /**
     * Recalculate Invoice Objects
     *
     * @return bool|string
     */
    function recalculateCertainInvoiceObjects() {
      if (!$this->isModuleInstalled('invoicing')) {
        return true;
      } // if

      try {
        DB::beginWork('Recalculating deviated invoices');

        $invoice_objects_table = TABLE_PREFIX . 'invoice_objects';
        $invoice_object_items_table = TABLE_PREFIX . 'invoice_object_items';
        $currencies_table = TABLE_PREFIX . 'currencies';

        // get affected currencies
        $affected_currencies = DB::executeFirstColumn('SELECT id FROM ' . $currencies_table . ' WHERE decimal_spaces = ?', 2);
        if (!is_foreachable($affected_currencies)) {
          return true;
        } // if

        // find invoice ids which we need to update
        $invoice_ids = DB::executeFirstColumn('SELECT id FROM ' . $invoice_objects_table . ' WHERE balance_due < ? AND balance_due != ? AND balance_due > ? AND currency_id IN (?)', 1, 0, -5, $affected_currencies);
        if (!is_foreachable($invoice_ids)) {
          return true;
        } // if

        // find invoices with second tax enabled
        $this->invoices_with_second_tax = DB::executeFirstColumn('SELECT id FROM ' . $invoice_objects_table . ' WHERE id IN (?) AND second_tax_is_enabled = ?', $invoice_ids, 1);
        // find invoices with second compound tax
        $this->invoices_with_compound_second_tax = DB::executeFirstColumn('SELECT id FROM ' . $invoice_objects_table . ' WHERE id IN (?) AND second_tax_is_compound = ?', $this->invoices_with_second_tax, 1);

        // find invoice items to recalculate
        $invoice_items = DB::execute('SELECT id, parent_id, quantity, unit_cost, first_tax_rate_id, second_tax_rate_id, subtotal, first_tax, second_tax, total FROM ' . $invoice_object_items_table . ' WHERE parent_id IN (?)', $invoice_ids);
        if (!is_foreachable($invoice_items)) {
          return true;
        } // if

        // load taxes
        $taxes = DB::execute("SELECT id, percentage FROM " . TABLE_PREFIX . "tax_rates");
        $this->taxes = array();
        if (is_foreachable($taxes)) {
          foreach ($taxes as $tax) {
            $this->taxes[$tax['id']] = $tax['percentage'] / 100;
          } // foreach
        } // if

        $updated_invoice_ids = array();
        foreach ($invoice_items as $invoice_item) {
          // loaded values
          $item_id = $invoice_item['id'];
          $parent_id = $invoice_item['parent_id'];
          $loaded_subtotal = $invoice_item['subtotal'];
          $loaded_first_tax = $invoice_item['first_tax'];
          $loaded_second_tax = $invoice_item['second_tax'];
          $loaded_total = $invoice_item['total'];

          list ($recalculated_subtotal, $recalculated_first_tax, $recalculated_second_tax, $recalculated_total) = $this->recalculateItem($parent_id, $invoice_item['quantity'], $invoice_item['unit_cost'], $invoice_item['first_tax_rate_id'], $invoice_item['second_tax_rate_id']);
          // check if maybe have deviation from rounded value
          if ($loaded_subtotal != $recalculated_subtotal || $loaded_first_tax != $recalculated_first_tax || $loaded_total != $recalculated_total) {
            // update items with deviation
            DB::execute('UPDATE ' . $invoice_object_items_table . ' SET subtotal = ?, first_tax = ?, second_tax = ?, total = ? WHERE id = ?', $recalculated_subtotal, $recalculated_first_tax, $recalculated_second_tax, $recalculated_total, $item_id);
            if (!in_array($parent_id, $updated_invoice_ids)) {
              $updated_invoice_ids[] = $parent_id;
            } // if
          } // if
        } // foreach

        $invoices = empty($updated_invoice_ids) ? null : DB::execute('SELECT id, subtotal, tax, total, paid_amount, balance_due FROM ' . $invoice_objects_table . ' WHERE id IN (?)', $updated_invoice_ids);

        if (is_foreachable($invoices)) {
          foreach ($invoices as $invoice) {
            $loaded_invoice_id = $invoice['id'];
            $loaded_invoice_subtotal = $invoice['subtotal'];
            $loaded_invoice_tax = $invoice['tax'];
            $loaded_invoice_total = $invoice['total'];
            $loaded_invoice_paid_amount = $invoice['paid_amount'];
            $loaded_invoice_balance_due = $invoice['balance_due'];

            $recalculated = DB::executeFirstRow('SELECT SUM(subtotal) AS recalculated_invoice_subtotal, SUM(first_tax) AS recalculated_invoice_first_tax, SUM(second_tax) AS recalculated_invoice_second_tax, SUM(total) AS recalculated_invoice_total FROM ' . $invoice_object_items_table . ' WHERE parent_id = ?', $loaded_invoice_id);
            $recalculated_invoice_subtotal = $recalculated['recalculated_invoice_subtotal'];
            $recalculated_invoice_tax = $recalculated['recalculated_invoice_first_tax'] + $recalculated['recalculated_invoice_second_tax'];
            $recalculated_invoice_total = $recalculated['recalculated_invoice_total'];
            $recalculated_invoice_balance_due = $recalculated_invoice_total - $loaded_invoice_paid_amount;

            // see deviation between loaded and recalculated and if it is in boundaries of acceptable, update the invoice
            $deviation_delta = $recalculated_invoice_total - $loaded_invoice_total;
            if ($deviation_delta < 1 && $deviation_delta > -1) {
              DB::execute('UPDATE ' . $invoice_objects_table . ' SET subtotal = ?, tax = ?, total = ?, balance_due = ? WHERE id = ?', $recalculated_invoice_subtotal, $recalculated_invoice_tax, $recalculated_invoice_total, $recalculated_invoice_balance_due ,$loaded_invoice_id);
            } // if
          } // foreach
        } // if

        DB::commit('Deviated invoices fixed');
      } catch(Exception $e) {
        DB::rollback('Failed to fix deviated invoices');
        return $e->getMessage();
      } // try

      return true;
    } // recalculateCertainInvoiceObjects

  } // Upgrade_0078
