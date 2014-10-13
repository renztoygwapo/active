<?php

  /**
   * InvoiceNoteTemplates class
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class InvoiceNoteTemplates extends BaseInvoiceNoteTemplates {
  	
  	/**
  	 * Find notes for select
  	 * 
  	 * @return array
  	 */
  	static function findForSelect() {
  		$invoice_notes = InvoiceNoteTemplates::find(array(
        'order' => 'name ASC'
      ));

      $cleaned_notes = array();
      if ($invoice_notes) {
        foreach ($invoice_notes as $invoice_note) {
        	$cleaned_notes[$invoice_note->getId()] = $invoice_note->getContent();
        } // foreach
      } // if
      
      return $cleaned_notes;
  	} // getCleanedNotes
  	
  	/**
  	 * Return slice of invoice note  definitions based on given criteria
  	 * 
  	 * @param integer $num
  	 * @param array $exclude
  	 * @param integer $timestamp
  	 * @return DBResult
  	 */
  	function getSlice($num = 10, $exclude = null, $timestamp = null) {
  		if($exclude) {
  			return InvoiceNoteTemplates::find(array(
  			  'conditions' => array('id NOT IN (?)', $exclude), 
  			  'order' => 'name', 
  			  'limit' => $num,  
  			));
  		} else {
  			return InvoiceNoteTemplates::find(array(
  			  'order' => 'name', 
  			  'limit' => $num,  
  			));
  		} // if
  	} // getSlice

    /**
     * Get Default invoice note template
     *
     * @return InvoiceNoteTemplate
     */
    public static function getDefault() {
      return InvoiceNoteTemplates::find(array(
        'conditions' => array('is_default = ?', true),
        'one' => true
      ));
    } // getDefault
  }