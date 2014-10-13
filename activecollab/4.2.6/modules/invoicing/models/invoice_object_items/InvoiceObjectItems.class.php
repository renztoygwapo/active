<?php

  /**
   * InvoiceObjectItems class
   *
   * @package ActiveCollab.modules.invoicing
   * @subpackage models
   */
  class InvoiceObjectItems extends BaseInvoiceObjectItems {

    /**
     * Return items by invoice_object
     *
     * @param InvoiceObject $invoice_object
     * @return array
     */
    static function findByParent(InvoiceObject $invoice_object) {
      return InvoiceObjectItems::find(array(
        'conditions' => array('parent_id = ? AND parent_type = ?', $invoice_object->getId(), $invoice_object->getType()),
        'order' => 'position'
      ));
    } // findByParent

    /**
     * Return number of items that user $rate tax rate
     *
     * @param TaxRate $tax_rate
     * @return integer
     */
    static function countByTaxRate($tax_rate) {
      return InvoiceObjectItems::count(array('first_tax_rate_id = ? || second_tax_rate_id = ?', $tax_rate->getId(), $tax_rate->getId()));
    } // countByTaxRate

    /**
     * Delete all items for a invoice object
     *
     * @param InvoiceObject $invoice_object
     * @return null
     */
    static function deleteByParent(InvoiceObject $invoice_object) {
      return InvoiceObjectItems::delete(array('parent_id = ? AND parent_type = ?', $invoice_object->getId(), $invoice_object->getType()));
    } // deleteByInvoice

    /**
     * Delete all items for by an invoice object and ids
     *
     * @param InvoiceObject $invoice_object
     * @param array $ids
     *
     * @return null
     */
    static function deleteByParentAndIds($invoice_object, $ids) {
      if($invoice_object instanceof Invoice) {
        InvoiceItems::deleteRelatedRecordsByParentAndIds($invoice_object, $ids);
      } //if
      return InvoiceObjectItems::delete(array('parent_id = ? AND parent_type = ? AND id IN (?)', $invoice_object->getId(), $invoice_object->getType(), $ids));
    } // deleteByParentAndIds
  
  }