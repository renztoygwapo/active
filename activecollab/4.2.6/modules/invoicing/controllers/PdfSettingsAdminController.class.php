<?php

  // We need admin controller
  AngieApplication::useController('admin');

  /**
   * PDF settings controller
   *
   * @package activeCollab.modules.invoicing
   * @subpackage controllers
   */
  class PdfSettingsAdminController extends AdminController {

  	/**
  	 * Active template
  	 * 
  	 * @var InvoiceTemplate
  	 */
  	protected $active_template;
  	
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      $this->active_template = new InvoiceTemplate();
      $this->wireframe->breadcrumbs->add('invoice_pdf_settings', lang('Invoice Designer'), Router::assemble('admin_invoicing_pdf'));
    } // __construct

    /**
     * Show invoicing settings panel
     */
    function index() {
    	// we need sample invoice
    	require_once INVOICING_MODULE_PATH . '/models/invoices/SampleInvoice.class.php';
    	
      $this->smarty->assign(array(
        'active_template' => $this->active_template->describe($this->logged_user),
      	'sample_invoice' => new SampleInvoice(),
      	'sample_url' => Router::assemble('admin_invoicing_pdf_sample')
      ));
    } // index
    
    /**
     * Invoice paper settings
     */
    function paper() {
    	if (!($this->request->isAsyncCall() || $this->request->isSubmitted())) {
    		$this->response->badRequest();
    	} // if
    	
    	$template_data = $this->request->post('template', array(
    		'paper_size' => $this->active_template->getPaperSize(),
    	));
    	    	
    	if ($this->request->isSubmitted()) {
    		$file_uploaded = false;
    		try {
					$this->active_template->setPaperSize(array_var($template_data, 'paper_size'));
					
					// if background image is uploaded, attach it to the template
					if (isset($_FILES['background_image']) && isset($_FILES['background_image']['tmp_name']) && is_file($_FILES['background_image']['tmp_name'])) {
						$file_uploaded = true;
						$this->active_template->useBackgroundImage($_FILES['background_image']['tmp_name']);
					} // if
					
					$this->active_template->save();
															
    			// serve data in way compatible with jquery form plugin
    			if ($file_uploaded) {
    				die(JSON::encode($this->active_template->describe($this->logged_user)));
    			} else {
    				$this->response->respondWithData($this->active_template);
    			} // if
    		} catch (Exception $e) {
    			if ($file_uploaded) {
	    			die(JSON::encode(array(
	    				'ajax_error' => true,
	    				'ajax_message' => $e->getMessage()
	    			)));
    			} else {
    				$this->response->exception($e);
    			} // if
    		} // try
    	} // if
    	
    	$this->smarty->assign(array(
    		'form_url' => Router::assemble('admin_invoicing_pdf_paper', array('async' => 1)),
    		'template_data'	=> $template_data,
    		'remove_background_image_url' => $this->active_template->hasBackgroundImage() ? Router::assemble('admin_invoicing_pdf_paper_remove_background') : false
    	));
    } // paper
    
    /**
     * Remove background image
     */
    function remove_background() {
    	if (!$this->request->isSubmitted()) {
    		$this->response->badRequest();
    	} // if
    	
    	try {
    		$this->active_template->removeBackgroundImage();    		
    		$this->response->respondWithData($this->active_template);
    	} catch (Exception $e) {
    		$this->response->respondWithData($e);
    	} // try  	
    } // remove_background
    
    /**
     * Header settings
     */
    function header() {
    	if (!($this->request->isAsyncCall() || $this->request->isSubmitted())) {
    		$this->response->badRequest();
    	} // if
    	    	    	
    	$template_data = $this->request->post('template', array(
    		'header_layout'         => $this->active_template->getHeaderLayout(),
				'print_logo'            => $this->active_template->getPrintLogo(),
				'print_company_details' => $this->active_template->getPrintCompanyDetails(),
				'company_name'          => $this->active_template->getCompanyName(),
				'company_details'       => $this->active_template->getCompanyDetails(),
				'header_font'           => $this->active_template->getHeaderFont(),
				'header_text_color'     => $this->active_template->getHeaderTextColor(),
    		'print_header_border'   => $this->active_template->getPrintHeaderBorder(),
    		'header_border_color'   => $this->active_template->getHeaderBorderColor()
    	));
    	
    	if ($this->request->isSubmitted()) {
    		$file_uploaded = false;
    		try {
    			$this->active_template->setHeaderLayout(array_var($template_data, 'header_layout'));
    			$this->active_template->setPrintLogo((boolean) array_var($template_data, 'print_logo', false));
    			$this->active_template->setPrintCompanyDetails((boolean) array_var($template_data, 'print_company_details', false));
    			$this->active_template->setCompanyName(array_var($template_data, 'company_name'));
    			$this->active_template->setCompanyDetails(array_var($template_data, 'company_details'));
    			$this->active_template->setHeaderFont(array_var($template_data, 'header_font'));
    			$this->active_template->setHeaderTextColor(array_var($template_data, 'header_text_color'));
    			$this->active_template->setPrintHeaderBorder((boolean) array_var($template_data, 'print_header_border'));
    			$this->active_template->setHeaderBorderColor(array_var($template_data, 'header_border_color'));
    			
					// if background image is uploaded, attach it to the template
					if (isset($_FILES['company_logo']) && isset($_FILES['company_logo']['tmp_name']) && is_file($_FILES['company_logo']['tmp_name'])) {
						$file_uploaded = true;
						$this->active_template->useLogoImage($_FILES['company_logo']['tmp_name']);
					} // if
    			
    			$this->active_template->save();
    			
    			// serve data in way compatible with jquery form plugin
    			if ($file_uploaded) {
    				die(JSON::encode($this->active_template->describe($this->logged_user)));
    			} else {
    				$this->response->respondWithData($this->active_template);
    			} // if
    		} catch (Exception $e) {
    			if ($file_uploaded) {
	    			die(JSON::encode(array(
	    				'ajax_error' => true,
	    				'ajax_message' => $e->getMessage()
	    			)));
    			} else {
    				$this->response->exception($e);
    			} // if
    		} // try
    	} // if
    	
    	$this->smarty->assign(array(
				'form_url' => Router::assemble('admin_invoicing_pdf_header', array('async' => 1)),
    		'template_data'	=> $template_data,
    	));    	
    } // header
    
    /**
     * Body settings
     */
    function body() {
    	if (!($this->request->isAsyncCall() || $this->request->isSubmitted())) {
    		$this->response->badRequest();
    	} // if
    	
			$template_data = $this->request->post('template', array(
				'body_layout'                   => $this->active_template->getBodyLayout(),
				'client_details_font'           => $this->active_template->getClientDetailsFont(),
				'client_details_text_color'     => $this->active_template->getClientDetailsTextColor(),
				'invoice_details_font'          => $this->active_template->getInvoiceDetailsFont(),
				'invoice_details_text_color'    => $this->active_template->getInvoiceDetailsTextColor(),
				'items_font'                    => $this->active_template->getItemsFont(),
				'items_text_color'              => $this->active_template->getItemsTextColor(),
				'print_table_border'            => $this->active_template->getPrintTableBorder(),
				'table_border_color'            => $this->active_template->getTableBorderColor(),
				'print_items_border'            => $this->active_template->getPrintItemsBorder(),
				'items_border_color'            => $this->active_template->getItemsBorderColor(),
				'note_font'                     => $this->active_template->getNoteFont(),
				'note_text_color'               => $this->active_template->getNoteTextColor(),
        'display_item_order'            => $this->active_template->getDisplayItemOrder(),
        'display_quantity'              => $this->active_template->getDisplayQuantity(),
        'display_unit_cost'             => $this->active_template->getDisplayUnitCost(),
        'display_subtotal'              => $this->active_template->getDisplaySubtotal(),
        'display_tax_rate'              => $this->active_template->getDisplayTaxRate(),
        'display_tax_amount'            => $this->active_template->getDisplayTaxAmount(),
        'display_total'                 => $this->active_template->getDisplayTotal(),
        'summarize_tax'                 => $this->active_template->getSummarizeTax(),
        'hide_tax_subtotal'             => $this->active_template->getHideTaxSubtotal(),
        'show_amount_paid_balance_due'  => $this->active_template->getShowAmountPaidBalanceDue()
			));
			
			if ($this->request->isSubmitted()) {
				try {
					$this->active_template->setBodyLayout(array_var($template_data, 'body_layout'));
					$this->active_template->setClientDetailsFont(array_var($template_data, 'client_details_font'));
					$this->active_template->setClientDetailsTextColor(array_var($template_data, 'client_details_text_color'));
					$this->active_template->setInvoiceDetailsFont(array_var($template_data, 'invoice_details_font'));
					$this->active_template->setInvoiceDetailsTextColor(array_var($template_data, 'invoice_details_text_color'));
					$this->active_template->setItemsFont(array_var($template_data, 'items_font'));
					$this->active_template->setItemsTextColor(array_var($template_data, 'items_text_color'));
					$this->active_template->setPrintTableBorder((boolean) array_var($template_data, 'print_table_border', false));
					$this->active_template->setTableBorderColor(array_var($template_data, 'table_border_color'));
					$this->active_template->setPrintItemsBorder((boolean) array_var($template_data, 'print_items_border', false));
					$this->active_template->setItemsBorderColor(array_var($template_data, 'items_border_color'));
					$this->active_template->setNoteFont(array_var($template_data, 'note_font'));
					$this->active_template->setNoteTextColor(array_var($template_data, 'note_text_color'));
          $this->active_template->setDisplayItemOrder(array_var($template_data, 'display_item_order'));
          $this->active_template->setDisplayQuantity(array_var($template_data, 'display_quantity'));
          $this->active_template->setDisplayUnitCost(array_var($template_data, 'display_unit_cost'));
          $this->active_template->setDisplaySubtotal(array_var($template_data, 'display_subtotal'));
          $this->active_template->setDisplayTaxRate(array_var($template_data, 'display_tax_rate'));
          $this->active_template->setDisplayTaxAmount(array_var($template_data, 'display_tax_amount'));
          $this->active_template->setDisplayTotal(array_var($template_data, 'display_total'));
          $this->active_template->setSummarizeTax(array_var($template_data, 'summarize_tax'));
          $this->active_template->setHideTaxSubtotal(array_var($template_data, 'hide_tax_subtotal'));
          $this->active_template->setShowAmountPaidBalanceDue(array_var($template_data, 'show_amount_paid_balance_due'));
    			$this->active_template->save();
					
					$this->response->respondWithData($this->active_template);
				} catch (Exception $e) {
					$this->response->exception($e);
				} // try
			} // if
			
			$this->smarty->assign(array(
				'form_url' => Router::assemble('admin_invoicing_pdf_body', array('async' => 1)),
				'template_data' => $template_data
			));
    } // body
    
    /**
     * Footer settings
     */
    function footer() {
    	if (!($this->request->isAsyncCall() || $this->request->isSubmitted())) {
    		$this->response->badRequest();
    	} // if
    	
    	$template_data = $this->request->post('template', array(
        'print_footer'          => $this->active_template->getPrintFooter(),
    		'footer_layout'         => $this->active_template->getFooterLayout(),
				'footer_font'           => $this->active_template->getFooterFont(),
				'footer_text_color'     => $this->active_template->getFooterTextColor(),
    		'print_footer_border'   => $this->active_template->getPrintFooterBorder(),
    		'footer_border_color'   => $this->active_template->getFooterBorderColor()
    	));
    	
			if ($this->request->isSubmitted()) {
				try {
          $this->active_template->setPrintFooter((boolean) array_var($template_data, 'print_footer'));
					$this->active_template->setFooterLayout(array_var($template_data, 'footer_layout'));
					$this->active_template->setFooterFont(array_var($template_data, 'footer_font'));
					$this->active_template->setFooterTextColor(array_var($template_data, 'footer_text_color'));
					$this->active_template->setPrintFooterBorder((boolean) array_var($template_data, 'print_footer_border'));
					$this->active_template->setFooterBorderColor(array_var($template_data, 'footer_border_color'));
    			$this->active_template->save();
					
					$this->response->respondWithData($this->active_template);
				} catch (Exception $e) {
					$this->response->exception($e);
				} // try
			} // if
    	
			$this->smarty->assign(array(
				'form_url' => Router::assemble('admin_invoicing_pdf_footer', array('async' => 1)),
				'template_data' => $template_data
			));
    } // footer
    
    /**
     * Renders the sample invoice
     */
    function sample() {
    	// we need sample invoice
    	require_once INVOICING_MODULE_PATH . '/models/invoices/SampleInvoice.class.php';
    	require_once INVOICING_MODULE_PATH . '/models/InvoicePDFGenerator.class.php';
			
    	// generate it and download it please
    	InvoicePDFGenerator::download(new SampleInvoice(), 'sample-invoice.pdf');
      die();
    } // sample
    
  }