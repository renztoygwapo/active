<?php

  /**
   * Interface that all invoice instances need to implement
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  interface IInvoice {
		
		/**
		 * Return invoice number
		 * 
		 * @param void
		 * @return integer
		 */
		function getNumber();

		/**
		 * Return invoice name
		 * 
		 * @param boolean $short
		 * @return string
		 */
		function getName($short=false);

		/**
		 * Return date when invoice is due
		 * 
		 * @param void
		 * @return DateValue
		 */
		function getDueOn();
		
		/**
		 * Return date when invoice is issued
		 * 
		 * @param void
		 * @return DateValue
		 */
		function getIssuedOn();
		
		/**
		 * Get company name
		 * 
		 * @param void
		 * @return null
		 */
		function getCompanyName();
		
		/**
		 * Return company address
		 * 
		 * @param void
		 * @return null
		 */
		function getCompanyAddress();
		
		/**
		 * Get invoice items
		 * 
		 * @param void
		 * @return array
		 */
		function getItems();
		
    /**
     * Return invoice total
     *
     * @return float
     */
    function getSubTotal();

    /**
     * Return calculated tax
     *
     * @return float
     */
    function getTax();

    /**
     * Returned taxed total
     *
     * @return float
     */
    function getTotal();
    
		/**
		 * Is invoice issued
		 * 
		 * @param void
		 * @return boolean
		 */
		function isIssued();
      
		/**
		 * Is invoice paid
     *
		 * @return boolean
		 */		
		function isPaid();

  }