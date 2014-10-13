<?php 

	/**
	 * Sample invoice for PDF preview
	 * 
	 * @package activeCollab.modules.invoicing
	 * @subpackage models
	 */
	class SampleInvoice implements IInvoice, IDescribe, IPayments {
		
  	/**
  	 * Return invoice ID
  	 * 
  	 * @return integer
  	 */
		function getId() {
			return 1;
		} // getId
		
		/**
		 * Return invoice number
		 * 
		 * @return integer
		 */
		function getNumber() {
			$today = new DateValue();
			return  '1/' . $today->getYear();
		} // getNumber

		/**
		 * Return invoice name
		 * 
		 * @param boolean $short
		 * @return string
		 */
		function getName($short=false) {
      if ($short) {
        return $this->getNumber();
      } else {
        return lang(':invoice_show_as #:invoice_num', array(
          'invoice_show_as' => Invoices::printInvoiceAs(),
          'invoice_num' => $this->getNumber()
        ));
      } // if
		} // getId
		
		/**
		 * Is invoice issued
		 * 
		 * @return boolean
		 */
		function isIssued() {
			return true;
		} // isIssued
      
		/**
		 * Is invoice paid
		 * 
		 * @return boolean
		 */		
		function isPaid() {
			return false;
		} // isPaid
      
		/**
		 * Is invoice sent
		 * 
		 * @return boolean
		 */
    function isSent() {
      return false;
    } // isSent
      
		/**
		 * Is invoice issued
		 * 
		 * @return boolean
		 */
		function isWon() {
			return false;
		} // isWon
		
		/**
		 * Is invoice draft
		 * 
		 * @return boolean
		 */
		function isDraft() {
			return false;
		} // isDraft
		
		/**
		 * Is invoice canceled
		 * 
		 * @return boolean
		 */
		function isCanceled() {
			return false;
		} // isCanceled

		/**
		 * Return date when invoice is created
		 * 
		 * @return DateValue
		 */
		function getCreatedOn() {
			return new DateValue();
		} // getCreatedOn
		
		/**
		 * Return date when invoice is due
		 * 
		 * @return DateValue
		 */
		function getDueOn() {
			return new DateValue('+1 week');
		} // getDueOn
		
		/**
		 * Return date when invoice is issued
		 * 
		 * @return DateValue
		 */
		function getIssuedOn() {
			return new DateValue();
		} // getIssuedOn
		
		/**
		 * Get company name
		 * 
		 * @return string
		 */
		function getCompanyName() {
			return 'Sample Company Inc.';
		} // getCompanyName
		
		/**
		 * Return company address
		 * 
		 * @return string
		 */
		function getCompanyAddress() {
			return "Magic Lane 45\n24325 Illusion District\nUtopia";
		} // getCompanyAddress
		
		/**
		 * Get invoice items
		 * 
		 * @return array
		 */
		function getItems() {
			$items = array();
			
			// find the first tax rate available
			$tax_rate = TaxRates::findOneBySql('SELECT * FROM ' . TABLE_PREFIX . 'tax_rates');
			
			$first_item = new InvoiceItem();
			$first_item->setDescription('Magic Wand');
			$first_item->setQuantity('4');
			$first_item->setUnitCost('20');
			if($tax_rate instanceof TaxRate) {
			  $first_item->setFirstTaxRateId($tax_rate->getId());
			}//if
      $first_item->recalculate();
			$items[] = $first_item;
			
			$second_item = new InvoiceItem();
			$second_item->setDescription('Box of chocolates');
			$second_item->setQuantity('5');
			$second_item->setUnitCost('10');
      $second_item->recalculate();
			$items[] = $second_item;
			
			$third_item = new InvoiceItem();
			$third_item->setDescription('White Doves');
			$third_item->setQuantity('2');
			$third_item->setUnitCost('100');
      $third_item->recalculate();
			$items[] = $third_item;
			
			$fourth_item = new InvoiceItem();
      $fourth_item->setDescription('Invisible Cloak');
      $fourth_item->setQuantity('1');
      $fourth_item->setUnitCost('1000');
      $fourth_item->recalculate();
			$items[] = $fourth_item;
			
			return $items;
		} // getItems
		
    /**
     * Return invoice total
     *
     * @param boolean $cache
     * @return float
     */
    function getSubTotal() {
    	$total = 0;
    	
    	$items = $this->getItems();
    	foreach ($items as $item) {
    		$total += $item->getQuantity() * $item->getUnitCost();
    	} // foreach
    	
    	return $total;
    } // getTotal

    /**
     * Return calculated tax
     *
     * @param boolean $cache
     * @return float
     */
    function getTax() {
			$tax_total = 0;
    	
    	$items = $this->getItems();
    	foreach ($items as $item) {
    		$tax_total += $item->getFirstTax() + $item->getSecondTax();
    	} // foreach
    	
    	return $tax_total;
    } // getTax

    /**
     * Returned taxed total
     *
     * @param boolean $cache
     * @return float
     */
    function getTotal() {
    	return $this->getSubTotal() + $this->getTax();
    } // getTotal

    /**
     * Get paid amount
     *
     * @return int
     */
    function getPaidAmount() {
      return 0;
    } // getPaidAmount

    /**
     * Get Balance Due
     *
     * @return flaot
     */
    function getBalanceDue() {
      return $this->getTotal();
    } // getBalanceDue

    /**
     * Require Rounding
     *
     * @param boolean
     */
    function requireRounding() {
      return false;
    } // requireRounding

    /**
     * Returns language of invoice
     * 
     * @param void
     * @return null
     */
    function getLanguage() {
    	return null;
    } // getLanguage
    
    /**
     * Get invoice note
     * 
     * @param void
     * @return string
     */
    function getNote() {
			$return = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed aliquet ornare est vel ullamcorper. Donec eu leo sed mauris dictum venenatis non in est. Nullam eu feugiat nunc. Nunc pulvinar nibh vitae nisi venenatis vel bibendum libero aliquam.\n\n";
			$return.= "Fusce et turpis aliquam risus tristique porttitor. Nunc in quam at lorem euismod euismod eu ac enim. Fusce venenatis, justo nec pharetra tempor.\n\n";
			return $return;
    } // getNote

    /**
     * Get Currency
     *
     * @return Currency
     */
    function getCurrency() {
      return Currencies::getDefault();
    } // getCurrency
    
    /**
     * Get currency code
     * 
     * @return string
     */
    function getCurrencyCode() {
    	return 'USD';
    } // getCurrencyCode
    
    /**
     * Get Currencu name
     * 
     * @return string
     */
    function getCurrencyName() {
    	return 'United States Dollar';
    } // getCurerncyName

    /**
     * Check if second tax is enabled
     *
     * @return bool
     */
    function getSecondTaxIsEnabled () {
      return false;
    } // getSecondTaxIsEnabled

    /**
     * Is sample invoice overdue
     *
     * @return bool
     */
    function isOverdue() {
      return false;
    } // isOverdue

    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------

    /**
     * Payments helper
     *
     * @var ISampleInvoicePaymentsImplementation
     */
    private $payments = false;

    /**
     * Return payments helper instance
     *
     * @return ISampleInvoicePaymentsImplementation
     */
    function payments() {
      if($this->payments === false) {
        require_once INVOICING_MODULE_PATH . '/models/ISampleInvoicePaymentsImplementation.class.php';

        $this->payments = new ISampleInvoicePaymentsImplementation($this);
      } // if
      return $this->payments;
    } // payments

    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
      $result = array();
    
      $result['currency'] = array(
      	'id' => NULL,
      	'code' => $this->getCurrencyCode(),
      	'name' => $this->getCurrencyName()
      );
      
      $result['urls'] = null;
      
      $result['status_conditions']['is_draft'] = $this->isDraft();
      $result['status_conditions']['is_issued'] = $this->isIssued();
      $result['status_conditions']['is_paid'] = $this->isPaid();
      $result['status_conditions']['is_canceled'] = $this->isCanceled();
      
      $result['name'] = array(
      	'short' => $this->getName(true),
      	'long'	=> $this->getName()
      );
      
      $result['created_on'] = $this->getCreatedOn();
      $result['issued_on'] = $this->getIssuedOn();
      $result['due_on'] = $this->getDueOn();
      $result['paid_on'] = null;
      $result['canceled_on'] = null;
            
      $result['project'] = null;
      $result['client'] = array(
      	'id' => 0,
      	'name' => $this->getCompanyName(),
      	'address' => $this->getCompanyAddress(),
      	'permalink'	=> null
      );
      
      $result['note'] = nl2br($this->getNote());
      
      $result['subtotal'] = $this->getSubTotal();
      $result['tax'] = $this->getTax();
      $result['total'] = $this->getTotal();
      
      $result['items'] = array();
      
      $items = $this->getItems();
      if (is_foreachable($items)) {
      	foreach ($items as $item) {
      		$result['items'][] = $item->describe($user, false, $for_interface);
      	} // foreach
      } // if
      
      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      throw new NotImplementedError(__METHOD__);
    } // describeForApi
	}