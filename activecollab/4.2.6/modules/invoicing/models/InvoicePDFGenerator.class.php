<?php 

require_once INVOICING_MODULE_PATH . '/models/InvoiceTCPDF.class.php';

/**
 * Invoice PDF Generator
 * 
 * @author godza
 * @package activeCollab.modules.invoicing
 * @subpackage invoicing
 */
class InvoicePDFGenerator {
	
	/**
	 * saves the invoice
	 * 
	 * @param IInvoice $invoice
	 * @param string $filename
	 */
	static function save($invoice, $filename) {
		$generator = new InvoiceTCPDF($invoice);
		$generator->generate();
		$generator->Output($filename, 'F');
	} // inline

	/**
	 * Downloads the invoice
	 * 
	 * @param IInvoice $invoice
	 * @param string $filename
	 */
	static function download($invoice, $filename = null) {
		$generator = new InvoiceTCPDF($invoice);
		$generator->generate();
		$generator->Output($filename, 'D');
	} // inline
	
	/**
	 * Displays the invoice inline
	 * 
	 * @param IInvoice $invoice
	 * @param string $filename
	 */
	static function inline($invoice, $filename = null) {
		$generator = new InvoiceTCPDF($invoice);
		$generator->generate();
		$generator->Output($filename, 'I');
	} // inline
	
} // invoicePDFGenerator