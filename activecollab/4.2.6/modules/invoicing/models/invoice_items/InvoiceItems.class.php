<?php
  /**
   * InvoiceItems class
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class InvoiceItems extends InvoiceObjectItems {

    /**
     * Delete all items for a invoice
     *
     * @param Invoice $invoice
     * @return null
     */
    static function deleteByParent(Invoice $invoice) {
      DB::beginWork('Deleting invoice related records by parent');
      // delete related invoice records
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'invoice_related_records WHERE invoice_id = ?', $invoice->getId());
      // perform parent deletion
      parent::deleteByParent($invoice);
      DB::commit('Deleting invoice related records by parent');
    } // deleteByParent

    /**
     * Delete all items for by an invoice and ids
     *
     * @param Invoice $invoice
     * @param array $ids
     *
     * @return null
     */
    static function deleteRelatedRecordsByParentAndIds(Invoice $invoice, $ids) {
      DB::beginWork('Deleting invoice related records by parent and ids and set their status to billable');

      // update related time records, and set their status to billable
        $records = DB::execute('SELECT parent_id, parent_type FROM ' . TABLE_PREFIX . 'invoice_related_records WHERE invoice_id = ? AND item_id IN (?)', $invoice->getId(), $ids);
        if (is_foreachable($records)) {
          foreach ($records as $record) {
            $obj_id = $record['parent_id'];
            $obj = new $record['parent_type']($obj_id);
            $obj->setBillableStatus(BILLABLE_STATUS_BILLABLE);
            $obj->save();
          } //foreach
        } //if
        // delete related time records
        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'invoice_related_records WHERE invoice_id = ? AND item_id IN (?)', $invoice->getId(), $ids);

        DB::commit('Deleting invoice related records by parent and ids and set their status to billable end');
    } // deleteByParentAndIds
  }