<?php

  // Extend company profile
  AngieApplication::useController('companies', SYSTEM_MODULE);

  /**
   * Company invoices controller implementation
   *
   * @package activeCollab.modules.invoicing
   * @subpackage controllers
   */
  class CompanyInvoicesController extends CompaniesController {
    
    /**
     * Selected invoice
     *
     * @var Invoice
     */
    protected $active_invoice;

    /**
     * Payments controller
     *
     * @var PaymentsController
     */
    protected $payments_delegate;

    /**
     * Cached array of statuses
     *
     * @var array
     */
    protected $status_map;

    /**
     * Construct invoices controller
     *
     * @param Request $request
     * @param string $context
     */
    function __construct(Request $request, $context = null) {
      parent::__construct($request, $context);

      if($this->getControllerName() == 'company_invoices') {
        $this->payments_delegate = $this->__delegate('payments', PAYMENTS_FRAMEWORK_INJECT_INTO, 'invoice');
      } // if

      $this->response->assign(array(
        'allow_payment' => boolval(ConfigOptions::getValue('allow_payments')),
        'allow_payments_for_invoice' => boolval(ConfigOptions::getValue('allow_payments_for_invoice')),
      ));
    } // __construct

    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if(Invoices::canAccessCompanyInvoices($this->logged_user, $this->active_company)) {
        $this->wireframe->actions->clear();
        $this->wireframe->breadcrumbs->add('company_invoices', lang('Invoices'), Router::assemble('people_company_invoices', array('company_id' => $this->active_company->getId())));
        $this->wireframe->setCurrentMenuItem('invoicing');

        $this->wireframe->tabs->clear();
        EventsManager::trigger('on_client_invoices_tabs', array(&$this->wireframe->tabs, &$this->logged_user));
        $this->wireframe->tabs->setCurrentTab('company_invoices');
        
        $invoice_id = $this->request->getId('invoice_id');
        if($invoice_id) {
          $this->active_invoice = Invoices::findById($invoice_id);
        } // if
        
        if($this->active_invoice instanceof Invoice) {
          if($this->active_invoice->getCompanyId() != $this->active_company->getId()) {
            $this->response->operationFailed();
          } // if
          
          $this->wireframe->breadcrumbs->add('company_invoice', $this->active_invoice->getName(), $this->active_invoice->getCompanyViewUrl());
        } else {
          $this->active_invoice = new Invoice();
        } // if

        $this->status_map = Invoices::getStatusMap();

        if($this->payments_delegate instanceof PaymentsController) {
          $this->payments_delegate->__setProperties(array(
            'active_object' => &$this->active_invoice,
          ));
        }

        $this->response->assign('active_invoice', $this->active_invoice);
      } else {
        $this->response->forbidden();
      } // if
    } // __construct
    
    /**
     * Show company invoices
     */
    function index() {
    	$this->wireframe->actions->clear();
    	// Regular web browser request
      if($this->request->isWebBrowser()) {
        if ($this->request->get('for_company_profile')) {
          $this->response->assign('invoices', Invoices::findByCompany($this->active_company, $this->logged_user));
          $this->renderView('index_company_profile', 'company_invoices', INVOICING_MODULE);
        } else {
          $this->wireframe->list_mode->enable();

          $invoices = Invoices::findForObjectsList($this->logged_user, $this->active_company);

          $invoice_dates_map = Invoices::getIssuedAndDueDatesMap($invoices);

          $this->response->assign(array(
            'invoices' => $invoices,
            'invoice_dates_map' => $invoice_dates_map,
            'status_map' => $this->status_map
          ));
        } // if
        
      // Request made by phone device
      } elseif($this->request->isPhone()) {
      	$this->response->assign('formatted_invoices', Invoices::findForPhoneList($this->logged_user, $this->active_company));
      	
      } elseif($this->request->isPrintCall()) {
        
        $group_by = strtolower($this->request->get('group_by', null));
        $filter_by = $this->request->get('filter_by', null);
        
        // page title
        $filter_by_completion = array_var($filter_by, 'status', null); 
        if ($filter_by_completion === '1') {
					$page_title = lang('Issued Invoices');
        } else if ($filter_by_completion === '2') {
					$page_title = lang('Paid Invoices');
        } else if ($filter_by_completion === '3') {
					$page_title = lang('Canceled Invoices');
        } else {
        	$page_title = lang('All Invoices');
        } // if

        // maps
        $map = array();
        
        switch ($group_by) {
         case 'status':
            $map = $this->status_map;
            $map[0] = lang('Draft');
            
          	$getter = 'getStatus';
          	$page_title.= ' ' . lang('Grouped by Status');
            break;
         case 'issued_on_month':
            $map = Invoices::mapIssuedOnMonth();
            $map[0] = lang('Draft');
            
            $getter = 'getIssuedOnMonth';
          	$page_title.= ' ' . lang('Grouped by Issued On Month');
            break;
         case 'due_on_month':
            $map = Invoices::mapDueOnMonth();
            $map[0] = lang('Draft');
            
            $getter = 'getDueOnMonth';
          	$page_title.= ' ' . lang('Grouped by Due On Month');
            break;
        }//switch
        
        // find invoices
        $invoices = Invoices::findForPrint($this->logged_user, $this->active_company, $group_by, $filter_by);

        //use thisa to sort objects by map array
        $print_list = group_by_mapped($map,$invoices,$getter);
                  
        $this->smarty->assignByRef('invoices', $print_list);
        $this->smarty->assignByRef('map', $map);
        $this->response->assign(array(
          'page_title' => $page_title,
        ));
      } else {
        throw new NotImplementedError(__METHOD__);
      } // if
    } // index
    
    /**
     * Company payments
     */ 
    function payments() {
      $this->response->assign('payments', Payments::findByCompany($this->active_company));
    } // payments
    
    /**
     * Show invoice details
     */
    function view() {
      if (!$this->active_invoice->isLoaded()) {
				$this->response->notFound();
      } // if

      if (!$this->active_invoice->canView($this->logged_user)) {
        $this->response->forbidden();
      } // if

      // Web browser request
      if($this->request->isWebBrowser()) {
      	$this->wireframe->setPageObject($this->active_invoice, $this->logged_user);

        if ($this->request->isSingleCall()) {
          $this->response->assign(array(
            'invoice_template' => new InvoiceTemplate()
          ));
          $this->smarty->fetch(get_view_path('view', 'invoices', INVOICING_MODULE));
        	$this->render();
        } else {
        	$this->__forward('index', 'index');
        } // if
        
      // Interface for phone devices is implemented
      } elseif(!$this->request->isPhone()) {
        throw new NotImplementedError(__METHOD__);
      } // if
    } // view
    
    /**
     * Render invoice PDF
     */
    function pdf() {
      if($this->active_invoice->isLoaded() || $this->active_invoice->getStatus() == INVOICE_STATUS_DRAFT) {
				require_once INVOICING_MODULE_PATH . '/models/InvoicePDFGenerator.class.php';
				InvoicePDFGenerator::download($this->active_invoice, lang('#:invoice_id.pdf', array('invoice_id' => $this->active_invoice->getName())));
        die();
      } else {
        $this->response->notFound();
      } // if
    } // pdf
    
  }