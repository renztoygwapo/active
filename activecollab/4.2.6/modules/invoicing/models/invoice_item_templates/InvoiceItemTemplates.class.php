<?php

  /**
   * InvoiceItemTemplates class
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class InvoiceItemTemplates extends BaseInvoiceItemTemplates {

    /**
     * Find by tax mode
     *
     * @param bool $two_taxes
     * @return InvoiceItemTemplate[]
     */
    static function findByTaxMode($two_taxes = true) {
      if ($two_taxes) {
        return InvoiceItemTemplates::find(array(
          'order' => 'description ASC'
        ));
      } else {
        return InvoiceItemTemplates::find(array(
          'conditions' => array('second_tax_rate_id < ?', 1),
          'order' => 'description ASC'
        ));
      }  // if
    } // find
  	
  	/**
  	 * Find templates for select
     *
     * @param boolean $two_taxes
  	 * @return array
  	 */
  	static function findForSelect($two_taxes = true) {
      $invoice_item_templates = self::findByTaxMode($two_taxes);
      $cleaned_item_templates = array();
      if(is_foreachable($invoice_item_templates)) {
        foreach ($invoice_item_templates as $invoice_item_template) {
        	$cleaned_item_templates[$invoice_item_template->getId()] = array(
        	 'description' => $invoice_item_template->getDescription(),
        	 'unit_cost' => $invoice_item_template->getUnitCost(),
        	 'quantity' => $invoice_item_template->getQuantity(),
        	 'first_tax_rate_id' => $invoice_item_template->getFirstTaxRateId(),
           'second_tax_rate_id' => $invoice_item_template->getSecondTaxRateId()
        	);
        } // foreach
      } // if
  		
      return $cleaned_item_templates;
  	} // findForSelect
  
  	/**
  	 * Return slice of invoice item template definitions based on given criteria
  	 * 
  	 * @param integer $num
  	 * @param array $exclude
  	 * @param integer $timestamp
  	 * @return DBResult
  	 */
  	static function getSlice($num = 10, $exclude = null, $timestamp = null) {
  		if($exclude) {
  			return InvoiceItemTemplates::find(array(
  			  'conditions' => array('id NOT IN (?)', $exclude), 
  			  'order' => 'description', 
  			  'limit' => $num,  
  			));
  		} else {
  			return InvoiceItemTemplates::find(array(
  			  'order' => 'description', 
  			  'limit' => $num,  
  			));
  		} // if
  	} // getSlice
  	
  }