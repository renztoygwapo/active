<?php

  // Extend company profile
  AngieApplication::useController('companies', SYSTEM_MODULE);

  /**
   * Company quotes controller implementation
   *
   * @package activeCollab.modules.invoicing
   * @subpackage controllers
   */
  class CompanyQuotesController extends CompaniesController {
    
    /**
     * Selected quote
     *
     * @var Quote
     */
    protected $active_quote;

    /**
     * Mapped statuses for quotes
     *
     * @var array
     */
    protected $status_map;

    /**
		 * Construct company quotes controller
		 *
		 * @param Request $parent
		 * @param mixed $context
		 */
		function __construct($parent, $context = null) {
		  parent::__construct($parent, $context);
		} // __construct
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if(Invoices::canAccessCompanyInvoices($this->logged_user, $this->active_company)) {
        $this->wireframe->actions->clear();
        $this->wireframe->breadcrumbs->add('company_quotes', lang('Quotes'), Router::assemble('people_company_quotes', array('company_id' => $this->active_company->getId())));
        $this->wireframe->setCurrentMenuItem('invoicing');

        $this->wireframe->tabs->clear();
        EventsManager::trigger('on_client_invoices_tabs', array(&$this->wireframe->tabs, &$this->logged_user));
        $this->wireframe->tabs->setCurrentTab('company_quotes');
        
        $quote_id = $this->request->getId('quote_id');
        if($quote_id) {
          $this->active_quote = Quotes::findById($quote_id);
        } // if
        
        if($this->active_quote instanceof Quote) {
          if($this->active_quote->getCompanyId() != $this->active_company->getId()) {
            $this->response->operationFailed();
          } // if
          
          $this->wireframe->breadcrumbs->add('company_quote', $this->active_quote->getName(), $this->active_quote->getCompanyViewUrl());
        } else {
          $this->active_quote = new Quote();
        } // if

        $this->status_map = Quotes::getStatusMap();

        $this->response->assign(array(
          'active_quote'  => $this->active_quote,
          'status_map'    => $this->status_map
        ));
      } else {
        $this->response->notFound();
      } // if
    } // __construct
    
    /**
     * Show company quotes
     */
    function index() {
      if ($this->request->isWebBrowser()) {
        if ($this->request->get('for_company_profile')) {
          $this->response->assign('quotes', Quotes::findByCompany($this->active_company, $this->logged_user));
          $this->renderView('index_company_profile', 'company_quotes', INVOICING_MODULE);
        } else {
          $this->wireframe->list_mode->enable();
          $this->response->assign(array(
            'quotes' => Quotes::findForObjectsList($this->logged_user, $this->active_company),
            'status_map' => $this->status_map
          ));
        } // if
      } else {
        throw new NotImplementedError(__METHOD__);
      } // if
    } // index
    
    /**
     * Show quote details
     */
    function view() {
      if($this->active_quote->isLoaded()) {
		    if($this->active_quote->canView($this->logged_user)) {
		      if($this->request->isWebBrowser()) {
		        if($this->request->isSingleCall()) {
		          $this->wireframe->setPageObject($this->active_quote, $this->logged_user);
              $this->response->assign(array(
                'invoice_template' => new InvoiceTemplate()
              ));
              $this->smarty->fetch(get_view_path('view', 'quotes', INVOICING_MODULE));
		          $this->render();
		        } else {
		          $this->__forward('index', 'index');
		        } // if
		      } // if
		    } else {
		      $this->response->forbidden();
		    } // if
		  } else {
		    $this->response->notFound();
		  } // if
    } // view
    
    /**
     * Render Quote PDF
     */
    function pdf() {
      if($this->active_quote->isLoaded()) {
				require_once INVOICING_MODULE_PATH . '/models/InvoicePDFGenerator.class.php';
				InvoicePDFGenerator::download($this->active_quote, lang('#:quote_id.pdf', array('quote_id' => $this->active_quote->getName())));
        die();
      } else {
        $this->response->notFound();
      } // if
    } // pdf
    
  }