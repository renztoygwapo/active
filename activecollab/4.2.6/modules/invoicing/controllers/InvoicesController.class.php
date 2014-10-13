<?php 

  // Build on top of backend controller
  AngieApplication::useController('backend', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Main invoices controller
   *
   * @package activeCollab.modules.invoicing
   * @subpackage controllers
   */
  class InvoicesController extends BackendController {

    /**
     * Selected invoice
     *
     * @var Invoice
     */
    protected $active_invoice;

    /**
     * State controller delegate
     *
     * @var StateController
     */
    protected $state_delegate;

	  /**
	   * Access log controller delegate
	   *
	   * @var AccessLogsController
	   */
	  protected $access_logs_delegate;

	  /**
	   * History of changes controller delegate
	   *
	   * @var HistoryOfChangesController
	   */
	  protected $history_of_changes_delegate;

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
      if($this->getControllerName() == 'invoices') {
        $this->state_delegate = $this->__delegate('state', ENVIRONMENT_FRAMEWORK_INJECT_INTO, 'invoice');

	      if(AngieApplication::isModuleLoaded('footprints')) {
		      $this->access_logs_delegate = $this->__delegate('access_logs', FOOTPRINTS_MODULE, 'invoice');
		      $this->history_of_changes_delegate = $this->__delegate('history_of_changes', FOOTPRINTS_MODULE, 'invoice');
	      } // if
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
      
      $this->wireframe->tabs->clear();
      $this->wireframe->tabs->add('invoices', lang('Invoices'), Router::assemble('invoices'), null, true);
      $this->wireframe->setCurrentMenuItem('invoicing');
      
      EventsManager::trigger('on_invoices_tabs', array(&$this->wireframe->tabs, &$this->logged_user));
      
      $invoice_id = $this->request->getId('invoice_id');
      if($invoice_id) {
        $this->active_invoice = Invoices::findById($invoice_id);
      } // if

      $this->wireframe->breadcrumbs->add('invoices', lang('Invoices'), Router::assemble('invoices'));
      if($this->active_invoice instanceof Invoice) {
        if ($this->active_invoice->getState() == STATE_ARCHIVED) {
          $this->wireframe->breadcrumbs->add('archive', lang('Archive'), Router::assemble('invoices_archive'));
        } // if

        $this->wireframe->breadcrumbs->add('invoice', $this->active_invoice->getName(), $this->active_invoice->getViewUrl());
      } else {
        $this->active_invoice = new Invoice();
      } // if

      if ($this->request->isWebBrowser() && (in_array($this->request->getAction(), array('index', 'view'))) && Invoices::canAdd($this->logged_user)) {
        $this->wireframe->actions->add('new_invoice', lang('New Invoice'), Router::assemble('invoices_add'), array(
        'onclick' => new FlyoutFormCallback('invoice_created'),
          'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
        ));
      } // if

      if($this->state_delegate instanceof StateController) {
        $this->state_delegate->__setProperties(array(
          'active_object' => &$this->active_invoice
        ));
      } // if

      $this->status_map = Invoices::getStatusMap();

      if($this->logged_user->isFinancialManager()) {
        $this->response->assign(array( 
          'active_invoice' => $this->active_invoice, 
          'drafts_count' => Invoices::countDrafts(),
        ));

	      if ($this->access_logs_delegate instanceof AccessLogsController) {
		      $this->access_logs_delegate->__setProperties(array(
			      'active_object' => &$this->active_invoice
		      ));
	      } // if

	      if ($this->history_of_changes_delegate instanceof HistoryOfChangesController) {
		      $this->history_of_changes_delegate->__setProperties(array(
			      'active_object' => &$this->active_invoice
		      ));
	      } // if
      } else {
        $this->response->forbidden();
      } // if
    } // __construct

    /**
     * Show invoicing dashboard
     */
    function index() {
    	
    	// API call
      if($this->request->isApiCall()) {
        $this->response->respondWithData(Invoices::findForApi($this->logged_user), array(
          'as' => 'invoices',
        ));

      // Regular request made by web browser
      } elseif($this->request->isWebBrowser()) {
      	$this->wireframe->list_mode->enable();

        $invoices = Invoices::findForObjectsList($this->logged_user, null, STATE_VISIBLE);

        $invoice_dates_map = Invoices::getIssuedAndDueDatesMap($invoices);

        $this->response->assign(array(
          'invoices' => $invoices,
          'companies_map' => Companies::getIdNameMap($this->logged_user->visibleCompanyIds()),
          'invoice_states_map' => $this->status_map,
          'invoice_dates_map' => $invoice_dates_map,
          'in_archive' => false,
          'print_url' => Router::assemble('invoices', array('print' => 1))
        ));
        
      // Phone request
      } elseif($this->request->isPhone()) {
      	$this->wireframe->actions->add('quotes', lang('Quotes'), Router::assemble('quotes'), array(
          'icon' => AngieApplication::getImageUrl('icons/navbar/quotes.png', INVOICING_MODULE, AngieApplication::getPreferedInterface())
        ));
        $this->wireframe->actions->add('recurring_profiles', lang('Recurring Profiles'), Router::assemble('recurring_profiles'), array(
          'icon' => AngieApplication::getImageUrl('icons/navbar/recurring.png', INVOICING_MODULE, AngieApplication::getPreferedInterface())
        ));
        
        $this->response->assign('formatted_invoices', Invoices::findForPhoneList($this->logged_user));
      	
      // Tablet device
    	} elseif($this->request->isTablet()) {
    		throw new NotImplementedError(__METHOD__);
        
      } elseif($this->request->isPrintCall()) {
        $group_by = strtolower($this->request->get('group_by', null));
        $filter_by = $this->request->get('filter_by', null);
        
        // page title
        $filter_by_completion = array_var($filter_by, 'status', null); 
        if ($filter_by_completion === '0') {
        	$page_title = lang('Drafts Invoices');
        } else if ($filter_by_completion === '1') {
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
          case 'client_id':
            $map = Companies::getIdNameMap();
            $map[0] = lang('Unknown Client');
            
          	$getter = 'getCompanyId';
          	$page_title.= ' ' . lang('Grouped by Client'); 
            break;
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
        } //switch
        
        // find invoices
        $invoices = Invoices::findForPrint($this->logged_user, null, $group_by, $filter_by);

        //use thisa to sort objects by map array
        $print_list = group_by_mapped($map,$invoices,$getter);
                  
        $this->smarty->assignByRef('invoices', $print_list);
        $this->smarty->assignByRef('map', $map);
        $this->response->assign(array(
          'group_by' => $group_by,
          'page_title' => $page_title,
        ));
      }//if
    } // index
    
    /**
     * Show invoicing archive
     */
    function archive() {
      if ($this->request->isWebBrowser()) {
        $this->wireframe->list_mode->enable();
        $this->wireframe->breadcrumbs->add('archive', lang('Archive'), Router::assemble('invoices_archive'));

        $invoices = Invoices::findForObjectsList($this->logged_user, null, STATE_ARCHIVED);
        $invoice_dates_map = Invoices::getIssuedAndDueDatesMap($invoices);

        $this->response->assign(array(
          'invoices' => $invoices,
          'companies_map' => Companies::getIdNameMap($this->logged_user->visibleCompanyIds()),
          'invoice_states_map' => $this->status_map,
          'invoice_dates_map' => $invoice_dates_map,
          'in_archive' => true,
          'print_url' => Router::assemble('invoices_archive', array('print' => 1))
        ));
      } else if ($this->request->isMobileDevice()) {

      } else if ($this->request->isPrintCall()) {
        $group_by = strtolower($this->request->get('group_by', null));
        $filter_by = $this->request->get('filter_by', null);

        // page title
        $filter_by_completion = array_var($filter_by, 'status', null);
        if ($filter_by_completion === '0') {
          $page_title = lang('Archived Drafts Invoices');
        } else if ($filter_by_completion === '1') {
          $page_title = lang('Archived Issued Invoices');
        } else if ($filter_by_completion === '2') {
          $page_title = lang('Archived Paid Invoices');
        } else if ($filter_by_completion === '3') {
          $page_title = lang('Archived Canceled Invoices');
        } else {
          $page_title = lang('All Archived Invoices');
        } // if

        // maps
        $map = array();

        switch ($group_by) {
          case 'client_id':
            $map = Companies::getIdNameMap();
            $map[0] = lang('Unknown Client');

            $getter = 'getCompanyId';
            $page_title.= ' ' . lang('Grouped by Client');
            break;
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
        } //switch

        // find invoices
        $invoices = Invoices::findForPrint($this->logged_user, null, $group_by, $filter_by, STATE_ARCHIVED);

        //use thisa to sort objects by map array
        $print_list = group_by_mapped($map,$invoices,$getter);

        $this->smarty->assignByRef('invoices', $print_list);
        $this->smarty->assignByRef('map', $map);
        $this->response->assign(array(
          'group_by' => $group_by,
          'page_title' => $page_title,
        ));
      } // if
    } // archive

    /**
     * Mark this invoice as paid - for credit invoices
     */
    function mark_as_paid() {
      if (!$this->active_invoice->isLoaded()) {
        $this->response->notFound();
      } // if

      if($this->active_invoice->isNew()) {
        $this->response->notFound();
      } // if

      if(!$this->active_invoice->canEdit($this->logged_user)) {
        $this->response->forbidden();
      } // if

      try{
        if($this->request->isAsyncCall()) {
          $this->active_invoice->setStatus(INVOICE_STATUS_PAID);
          $this->active_invoice->save();
          $this->response->respondWithData($this->active_invoice, array(
            'as' => 'invoice',
            'detailed' => true,
          ));
        } else {
          $this->response->badRequest();
        } //if
      } catch (Exception $e) {
        $this->response->exception($e);
      } //try
    }//mark_as_paid

    /**
     * Show invoice details
     */
    function view() {
      if($this->active_invoice->isAccessible()) {
        if($this->active_invoice->canView($this->logged_user)) {
          if ($this->request->isMobileDevice()) {
            $payment_options = array();

            if ($this->active_invoice->payments()->hasDefinedGateways() && $this->active_invoice->payments()->canMake($this->logged_user) || ($this->logged_user->isFinancialManager())) {
              $payment_options[] = "<a href='" . $this->active_invoice->payments()->getAddUrl()."' class='make_a_payment_btn'>" . lang('Make a Payment') . "</a>";
            } // if

            $this->response->assign(array(
              'payment_options' => $payment_options,
              'invoice_template' => new InvoiceTemplate()
            ));
          } else if ($this->request->isWebBrowser()) {
            $this->wireframe->setPageObject($this->active_invoice, $this->logged_user);

            if ($this->request->isSingleCall() || $this->request->isQuickViewCall()) {
              $this->response->assign(array(
                'invoice_template' => new InvoiceTemplate()
              ));

              // access log
              $this->active_invoice->accessLog()->log($this->logged_user);

              $this->render();
            } else {
              if ($this->active_invoice->getState() == STATE_ARCHIVED) {
                $this->__forward('archive', 'archive');
              } else {
                $this->__forward('index', 'index');
              } // if
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
     * Show PDF file
     */
    function pdf() {
      if($this->active_invoice->isLoaded()) {
        if($this->active_invoice->canView($this->logged_user)) {
          InvoicePDFGenerator::download($this->active_invoice, Invoices::getInvoicePdfName($this->active_invoice));
        	die();
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // pdf
    
    /**
     * Create a new invoice
     */
    function add() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted()) || true) {
        if(Invoices::canAdd($this->logged_user)) {
          $default_currency = Currencies::getDefault();
          
          if(!($default_currency instanceof Currency)) {
            $this->response->notFound();
          } // if

          $invoice_data = $this->request->post('invoice');
          
          if(!is_array($invoice_data)) {
            $duplicate_invoice_id = $this->request->getId('duplicate_invoice_id');
            
            // Duplicate an existing invoice
            if($duplicate_invoice_id) {
              $duplicate_invoice = Invoices::findById($duplicate_invoice_id);
              if($duplicate_invoice instanceof Invoice) {
                $invoice_data = array(
                  'company_id'              => $duplicate_invoice->getCompanyId(),
                  'company_address'         => $duplicate_invoice->getCompanyAddress(),
                  'private_note'            => $duplicate_invoice->getPrivateNote(),
                  'status'                  => INVOICE_STATUS_DRAFT,
                  'project_id'              => $duplicate_invoice->getProjectId(),
                  'note'                    => $duplicate_invoice->getNote(),
                  'currency_id'             => $duplicate_invoice->getCurrencyId(),
                  'payment_type'            => $duplicate_invoice->getAllowPayments(),
                  'second_tax_is_compound'  => $duplicate_invoice->getSecondTaxIsCompound(),
                  'language_id'             => $duplicate_invoice->getLanguageId()
                );
                
                if(is_foreachable($duplicate_invoice->getItems())) {
                  $invoice_data['items'] = array();
                  foreach($duplicate_invoice->getItems() as $item) {
                    $invoice_data['items'][] = array(
                      'description'         => $item->getDescription(),
                      'unit_cost'           => $item->getUnitCost(),
                      'quantity'            => $item->getQuantity(),
                      'first_tax_rate_id'   => $item->getFirstTaxRateId(),
                      'second_tax_rate_id'  => $item->getSecondTaxRateId(),
                      'total'               => $item->getTotal(),
                      'subtotal'            => $item->getSubtotal(),
                    );
                  } // foreach
                } // if
              } // if
            } // if
            
            // Blank invoice
            if(!is_array($invoice_data)) {
              $invoice_data = array(
                'due_on'                  => null,
                'currency_id'             => $default_currency->getId(),
                'time_record_ids'         => null,
                'payment_type'            => -1,
                'second_tax_is_compound'  => $this->active_invoice->getSecondTaxIsCompound()
              );
            } // if
          } // if

          $this->response->assign('invoice_data', $invoice_data);
          $this->response->assign(Invoices::getSettingsForInvoiceForm($this->active_invoice));
          
          if($this->request->isSubmitted()) {
          	try {
            	DB::beginWork('Creating a new invoice @ ' . __CLASS__);

              if (!is_foreachable($invoice_data['items'])) {
                throw new Error(lang('Invoice items data is not valid. All descriptions are required and there need to be at least one unit with cost set per item!'));
              } // if

            	$this->active_invoice->setAttributes($invoice_data);
    	        $this->active_invoice->setCreatedBy($this->logged_user);

              if ($this->active_invoice->getSecondTaxIsEnabled()) {
                $this->active_invoice->setSecondTaxIsCompound(array_var($invoice_data, 'second_tax_is_compound', false));
              } // if

              $this->active_invoice->setItems($invoice_data['items']);
              $this->active_invoice->setState(STATE_VISIBLE);
              $this->active_invoice->save();

    	        DB::commit('Invoice created @ ' . __CLASS__);
    	        
              $this->response->respondWithData($this->active_invoice, array(
              	'as' => 'invoice', 
                'detailed' => true,
              ));
    	        
          	} catch (Exception $e) {
          	  DB::rollback('Failed to create invoice @ ' . __CLASS__);
          		$this->response->exception($e);
          	} // try
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // add

    /**
     * Update existing invoice
     */
    function edit() {
      $this->wireframe->hidePrintButton();
      
      if($this->active_invoice->isNew()) {
        $this->response->notFound();
      } // if

      if(!$this->active_invoice->canEdit($this->logged_user)) {
        $this->response->forbidden();
      } // if

      $invoice_data = $this->request->post('invoice');
      if(!is_array($invoice_data)) {
        $invoice_data = array(
          'number'                  => $this->active_invoice->getNumber(),
          'due_on'                  => $this->active_invoice->getDueOn(),
          'issued_on'               => $this->active_invoice->getIssuedOn(),
          'currency_id'             => $this->active_invoice->getCurrencyId(),
          'purchase_order_number'   => $this->active_invoice->getPurchaseOrderNumber(),
          'private_note'            => $this->active_invoice->getPrivateNote(),
          'company_id'              => $this->active_invoice->getCompanyId(),
          'company_address'         => $this->active_invoice->getCompanyAddress(),
          'project_id'              => $this->active_invoice->getProjectId(),
          'note'                    => $this->active_invoice->getNote(),
          'language_id'             => $this->active_invoice->getLanguageId(),
          'payment_type'            => $this->active_invoice->getAllowPayments(),
          'second_tax_is_compound'  => $this->active_invoice->getSecondTaxIsCompound()
        );
        if(is_foreachable($this->active_invoice->getItems())) {
          $invoice_data['items'] = array();
          foreach($this->active_invoice->getItems() as $item) {
            $invoice_data['items'][] = array(
              'id'					        => $item->getId(),
              'description'         => $item->getDescription(),
              'unit_cost'           => $item->getUnitCost(),
              'quantity'            => $item->getQuantity(),
              'first_tax_rate_id'   => $item->getFirstTaxRateId(),
              'second_tax_rate_id'  => $item->getSecondTaxRateId(),
              'total'               => $item->getTotal(),
              'subtotal'            => $item->getSubtotal(),
              'time_record_ids'     => $item->getTimeRecordIds(),
            );
          } // foreach
        } // if
      } // if
    
      $this->response->assign('invoice_data', $invoice_data);
      $this->response->assign(Invoices::getSettingsForInvoiceForm($this->active_invoice));
      
      if ($this->request->isSubmitted()) {
      	if (!$this->request->isAsyncCall()) {
      		$this->response->badRequest();
      	} // if
      	
      	try {
          if (!is_foreachable($invoice_data['items'])) {
            throw new ValidationErrors(lang('At least one invoice item is required'));
          } // if

      		DB::beginWork('Editing Invoice');

      		$this->active_invoice->setAttributes($invoice_data);

          if ($this->active_invoice->getSecondTaxIsEnabled()) {
            $this->active_invoice->setSecondTaxIsCompound(array_var($invoice_data, 'second_tax_is_compound', false));
          } // if

          $this->active_invoice->setItems($invoice_data['items']);
      		
      		if ($this->active_invoice->isIssued()) {
      		  $issued_on = isset($invoice_data['issued_on']) ? new DateValue($invoice_data['issued_on']) : new DateValue();
      		  $this->active_invoice->setIssuedOn($issued_on);
      		} //if

      		
      		$this->active_invoice->save();
          
// TODO: resolve the issue when dialog has to be redirected to the notification page
//            $this->flash->success('":number" has been updated', array('number' => $this->active_invoice->getName()));
//            if ($this->active_invoice->isIssued()) {
//              $this->response->redirectTo('invoice_notify', array('invoice_id' => $this->active_invoice->getId()));
//            } else {
//              $this->response->redirectToUrl($this->active_invoice->getViewUrl());  
//            } // if

      		DB::commit('Invoice Edited');

					$this->response->respondWithData($this->active_invoice, array(
          	'as' => 'invoice', 
            'detailed' => true,
          ));
          
      	} catch (Exception $e) {
      		DB::rollback('Invoice Editing Failed');
      		$this->response->exception($e);
      	} // try
      	
      } // if
    } // edit
    
    /**
     * Issue invoice
     */
    function issue() {
      if($this->active_invoice->isLoaded()) {
        if($this->active_invoice->canIssue($this->logged_user)) {
          $company = $this->active_invoice->getCompany();
          
          if(!($company instanceof Company)) {
            $this->response->operationFailed();
          } // if
          
          $issue_data = $this->request->post('issue', array(
            'issued_on' => new DateValue(),
            'due_in_days' => ConfigOptions::getValue('invoicing_default_due'), // new DateValue('+15 days'),
          ));
          
          $this->response->assign('issue_data', $issue_data);
          
          if($this->request->isSubmitted()) {
          	try {
            	DB::beginWork('Issuing an invoice @ ' . __CLASS__);
            	
    	        $issued_on = isset($issue_data['issued_on']) ? new DateValue($issue_data['issued_on']) : new DateValue();
          
              $due_in_days = isset($issue_data['due_in_days']) ? $issue_data['due_in_days'] : (integer) ConfigOptions::getValue('invoicing_default_due');
              
              if(is_numeric($due_in_days)) {
                $due_in_days = (integer) $due_in_days;
              } // if
              
              if($due_in_days === 'selected') {
                if(isset($issue_data['due_in_days_selected_date']) && $issue_data['due_in_days_selected_date']) {
                  $due_on = new DateValue($issue_data['due_in_days_selected_date']);

                  if($due_on->getTimestamp() < $issued_on->getTimestamp()) {
                    $due_on = $issued_on;
                  } // if
                } else {
                  $due_on = $issued_on;
                } // if
              } elseif($due_in_days <= 0) {
                $due_on = $issued_on;
              } else {
                $due_on = $issued_on->advance($due_in_days * 86400, false);
              } // if
              
    	        $issued_to = isset($issue_data['send_emails']) && $issue_data['send_emails'] && isset($issue_data['user_id']) && $issue_data['user_id'] ? Users::findById($issue_data['user_id']) : null;
    	        
    	        $this->active_invoice->markAsIssued($this->logged_user, $issued_to, $issued_on, $due_on);
              
              DB::commit('Invoice issued @ ' . __CLASS__);
              
              if($issued_to instanceof User) {
                $recipients = array($issued_to);
                
                if($issued_to->getId() != $this->logged_user->getId()) {
                  $recipients[] = $this->logged_user;
                } // if

                AngieApplication::notifications()
                  ->notifyAbout('invoicing/invoice_issued', $this->active_invoice)
                  ->sendToUsers($recipients, true);
              } // if
              
              $this->response->respondWithData($this->active_invoice, array(
                'detailed' => true, 
              	'as' => 'invoice'
              ));
          	} catch (Exception $e) {
          	  DB::rollback('Failed to issue invoice @ ' . __CLASS__);
          		$this->response->exception($e);
          	} // try
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // issue
    
    /**
     * Page is displayed when issued invoice is edited
     */
    function notify() {
      if ($this->active_invoice->isLoaded()) {
        if ($this->active_invoice->canResendEmail($this->logged_user)) {
          $company = $this->active_invoice->getCompany();
          if(!($company instanceof Company)) {
            $this->response->operationFailed();
          } // if
          
          $issue_data = $this->request->post('issue', array(
            'issued_on' => $this->active_invoice->getIssuedOn(),
            'due_on' => $this->active_invoice->getDueOn(),
            'issued_to_id' => $this->active_invoice->getIssuedToId()
          ));
          
          $this->response->assign('issue_data', $issue_data);
          
          if ($this->request->isSubmitted()) {
            try {
              if($this->active_invoice->isIssued()) {
                $this->active_invoice->setDueOn($issue_data['due_on']);
              } // if
              
              $resend_to = isset($issue_data['user_id']) && $issue_data['user_id'] ? Users::findById($issue_data['user_id']) : null;
              
              if($issue_data['send_emails'] && $resend_to instanceof IUser) {
                $this->active_invoice->setIssuedTo($resend_to);
                
                $recipients = array($resend_to);

                AngieApplication::notifications()
                  ->notifyAbout('invoicing/invoice_reminder', $this->active_invoice)
                  ->sendToUsers($recipients, true);
              } // if
              
              $this->active_invoice->save();
              
              $this->response->respondWithData($this->active_invoice, array(
                'detailed' => true, 
              	'as' => 'invoice'
              ));
          	} catch (Exception $e) {
          	  DB::rollback('Failed to resend email @ ' . __CLASS__);
          	  $this->response->exception($e);
          	} // try
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // notify
    
    /**
     * Change invoice status to CANCELED
     */
    function cancel() {
      if($this->request->isAsyncCall()) {
        if($this->active_invoice->isLoaded()) {
          if($this->active_invoice->canCancel($this->logged_user)) {
            if($this->request->isSubmitted()) {
              try {
                $this->active_invoice->markAsCanceled($this->logged_user);
        
                $issued_to_user = $this->active_invoice->getIssuedTo();
                if ($issued_to_user instanceof User && Invoices::getNotifyClientAboutCanceledInvoice()) {
                  $notify_users = array($issued_to_user);
      	          if ($issued_to_user->getId() != $this->logged_user->getId()) {
      	            $notify_users[] = $this->logged_user;
      	          } // if

                  AngieApplication::notifications()
                    ->notifyAbout('invoicing/invoice_canceled', $this->active_invoice, $this->logged_user)
                    ->sendToUsers($notify_users);
                } // if
                
    						$this->response->respondWithData($this->active_invoice, array(
    	          	'as' => 'invoice', 
    	            'detailed' => true,
    	          ));
              } catch (Error $e) {
              	$this->response->exception($e);
              } // try
            } // if
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // cancel

    /**
     * Drop invoice
     */
    function delete() {
      if ($this->active_invoice->isLoaded()) {
        if ($this->active_invoice->canDelete($this->logged_user)) {
          if ($this->request->isSubmitted()) {        
            try {
              $this->active_invoice->releaseTimeRecords();
              $this->active_invoice->releaseExpenses();
            	$this->active_invoice->delete();
            	$this->response->respondWithData($this->active_invoice, array(
            	  'as' => 'invoice', 
            	));
            } catch (Exception $e) {
            	$this->response->exception($e);
            } // try
          } else {
            $this->response->badRequest();
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // delete
    
    /**
     * Show time attached to a particular invoice
     */
    function time() {
      if($this->active_invoice->isLoaded()) {
        if($this->active_invoice->canViewRelatedItems($this->logged_user)) {
          $this->wireframe->print->enable();
          
          $this->response->assign(array(
            'time_records' => $this->active_invoice->getTimeRecords(),
            'expenses' => $this->active_invoice->getExpenses(),
            'items_can_be_released' => $this->active_invoice->isDraft() || $this->active_invoice->isCanceled()
          ));
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // time
    
    /**
     * Release items related to this invoice
     */
    function items_release() {
      if($this->active_invoice->isLoaded()) {
        if($this->active_invoice->canEdit($this->logged_user) && ($this->active_invoice->isDraft() || $this->active_invoice->isCanceled())) {
          if($this->request->isSubmitted()) {
            try {
              $release_times = $this->request->post('release_times');
              $release_expenses = $this->request->post('release_expenses');
              
              if(is_foreachable($release_times)) {
                $this->active_invoice->releaseTimeRecordsByIds($release_times);
              }//if
              if(is_foreachable($release_expenses)) {
                $this->active_invoice->releaseExpensesByIds($release_expenses);
              }//if
              $this->response->respondWithData($this->active_invoice, array('as' => 'invoice'));
            } catch (Exception $e) {
              $this->response->exception($e);
            }//try
          } else {
            $this->response->badRequest();
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // time_release

    /**
     * Change language
     */
    function change_language() {
      if (!$this->request->isAsyncCall()) {
        $this->response->badRequest();
      } // if

      if ($this->active_invoice->isNew()) {
        $this->response->notFound();
      } // if

      if (!$this->active_invoice->canChangeLanguage($this->logged_user)) {
        $this->response->forbidden();
      } // if

      $invoice_data = $this->request->post('invoice');
      if(!is_array($invoice_data)) {
        $invoice_data = array(
          'language_id' => $this->active_invoice->getLanguageId(),
        );
      } // if

      $this->response->assign(array(
        'invoice_data' => $invoice_data
      ));

      if ($this->request->isSubmitted()) {
        try {
          $this->active_invoice->setLanguageId(array_var($invoice_data, 'language_id'));
          $this->active_invoice->save();

          $this->response->respondWithData($this->active_invoice, array(
            'as' => 'invoice',
          ));
        } catch (Exception $e) {
          $this->response->exception($e);
        } // try
      } // if
    } // change_language


    /**
     * Info about public payment url
     *
     */
    function public_payment_info() {
      if (!$this->request->isAsyncCall()) {
        $this->response->badRequest();
      } // if
      if ($this->active_invoice->isNew()) {
        $this->response->notFound();
      } // if

    } //public_payment_info
    
  }