<?php 
  
  // Inherit invoices controller
	AngieApplication::useController('invoices', INVOICING_MODULE);
	
  /**
   * Main recurring invoices controller
   *
   * @package activeCollab.modules.invoicing
   * @subpackage controllers
   */
  class RecurringInvoiceController extends InvoicesController {

    /**
     * Selected invoice
     *
     * @var RecurringProfile
     */
    protected $active_recurring_profile;
    
    /**
     * State controller delegate
     *
     * @var StateController
     */
    protected $state_delegate;

	  /**
	   * Access log controller delegate
	   *
	   * @var AccessLogController
	   */
	  protected $access_logs_delegate;

	  /**
	   * History of changes controller delegate
	   *
	   * @var HistoryOfChangesController
	   */
	  protected $history_of_changes_delegate;
    
    /**
     * Construct recurring invoices controller
     * 
     * @param Request $request
     * @param string $context
     */
    function __construct(Request $request, $context = null) {
      parent::__construct($request, $context);
      
       if($this->getControllerName() == 'recurring_invoice') {
      	 $this->state_delegate = $this->__delegate('state',ENVIRONMENT_FRAMEWORK_INJECT_INTO,'recurring_profile');
       } // if

		   if(AngieApplication::isModuleLoaded('footprints')) {
			   $this->access_logs_delegate = $this->__delegate('access_logs', FOOTPRINTS_MODULE, 'recurring_profile');
			   $this->history_of_changes_delegate = $this->__delegate('history_of_changes', FOOTPRINTS_MODULE, 'recurring_profile');
		   } // if
      
    }//__construct

    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      $this->wireframe->actions->clear();
      
      $this->wireframe->tabs->setCurrentTab('recurring_profiles');
     
      $recurring_profile_id = $this->request->getId('recurring_profile_id');
      if($recurring_profile_id) {
        $this->active_recurring_profile = RecurringProfiles::findById($recurring_profile_id);
      } // if

      if($this->active_recurring_profile instanceof RecurringProfile) {

        if ($this->active_recurring_profile->getState() == STATE_ARCHIVED) {
          $this->wireframe->breadcrumbs->add('archive', lang('Archive'), Router::assemble('recurring_profiles_archive'));
        } // if

        $this->wireframe->breadcrumbs->add('recuring_profile', $this->active_recurring_profile->getName(), $this->active_recurring_profile->getViewUrl());
      } else {
        $this->active_recurring_profile = new RecurringProfile();
      } // if
     
      $this->wireframe->breadcrumbs->add('recuring_profile', lang('Recurring profiles'), $this->active_recurring_profile->getMainPageUrl());
	  
      if ($this->request->isWebBrowser() && in_array($this->request->getAction(), array('index', 'view')) && RecurringProfiles::canAdd($this->logged_user)) {
        $this->wireframe->actions->add('new_recurring_profile', lang('New Recurring Profile'), $this->active_recurring_profile->getAddUrl(), array(
          'onclick' => new FlyoutFormCallback('recurring_profile_created'),
          'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
        ));
      } // if

      
      if($this->state_delegate instanceof StateController) {
        $this->state_delegate->__setProperties(array(
          'active_object' => &$this->active_recurring_profile
        ));
      } // if

	    if ($this->access_logs_delegate instanceof AccessLogsController) {
		    $this->access_logs_delegate->__setProperties(array(
			    'active_object' => &$this->active_recurring_profile
		    ));
	    } // if

	    if ($this->history_of_changes_delegate instanceof HistoryOfChangesController) {
		    $this->history_of_changes_delegate->__setProperties(array(
			    'active_object' => &$this->active_recurring_profile
		    ));
	    } // if
      
      $this->response->assign(array(
        'active_recurring_profile' => $this->active_recurring_profile, 
      	'js_company_details_url' => Router::assemble('people_company_details'),
        'js_move_icon_url' => AngieApplication::getImageUrl('layout/bits/handle-move.png', ENVIRONMENT_FRAMEWORK), 
        'skipped_profiles' => RecurringProfiles::findSkipped(),
        'today' => new DateTimeValue()
      ));
    } // __construct

    /**
     * Show recurring profile dashboard
     */
    function index() {

      // Regular web browser request
    	if($this->request->isWebBrowser()) {
    		$this->wireframe->list_mode->enable();
    		
        $this->response->assign(array(
          'recurring_profiles' => RecurringProfiles::findForObjectsList(),
          'companies_map' => Companies::getIdNameMap($this->logged_user->visibleCompanyIds()),
          'print_url' => Router::assemble('recurring_profiles', array('print' => 1)),
          'in_archive' => false
        ));
        
      // Phone request
      } elseif($this->request->isPhone()) {
      	$this->response->assign('formatted_recurring_profiles', RecurringProfiles::findForPhoneList(STATE_VISIBLE));
      	
      // Tablet device
    	} elseif($this->request->isTablet()) {
    		throw new NotImplementedError(__METHOD__);
        
      // Print interface
      } elseif ($this->request->isPrintCall()) {
        $group_by = strtolower($this->request->get('group_by', null));
        
        // find invoices
        $recurring_profiles = RecurringProfiles::findForPrint($group_by, STATE_VISIBLE);
        
        $page_title = lang("Recurring Profiles");
        // maps
        if ($group_by == 'company_id') {
      	  $map = Companies::getIdNameMap();
      	  
      	  if(empty($map)) {
      	    $map = array();
      	  } // if
      	  
      	  $map[0] = lang('Unknown Client');
      	  $getter = 'getCompanyId';
      	  $page_title.= ' ' . lang('Grouped by Client'); 
      	}//if

        $this->smarty->assignByRef('recurring_profiles', $recurring_profiles);
        $this->smarty->assignByRef('map', $map);
        $this->response->assign(array(
          'page_title' => $page_title,
        	'getter' => $getter
        ));
      }//if
    } // index

    /**
     * Show completed tasks (mobile devices only)
     */
    function archive() {
      if ($this->request->isWebBrowser()) {

        $this->wireframe->list_mode->enable();
        $this->response->assign(array(
          'recurring_profiles' => RecurringProfiles::findForObjectsList(STATE_ARCHIVED),
          'companies_map' => Companies::getIdNameMap($this->logged_user->visibleCompanyIds()),
          'print_url' => Router::assemble('recurring_profiles_archive', array('print' => 1)),
          'in_archive' => true
        ));

        // Printer
      } else if ($this->request->isPrintCall()) {
        $group_by = strtolower($this->request->get('group_by', null));

        // find invoices
        $recurring_profiles = RecurringProfiles::findForPrint($group_by, STATE_ARCHIVED);

        $page_title = lang("Recurring Profiles Archive");
        // maps
        if ($group_by == 'company_id') {
          $map = Companies::getIdNameMap();

          if(empty($map)) {
            $map = array();
          } // if

          $map[0] = lang('Unknown Client');
          $getter = 'getCompanyId';
          $page_title.= ' ' . lang('Grouped by Client');
        }//if

        $this->smarty->assignByRef('recurring_profiles', $recurring_profiles);
        $this->smarty->assignByRef('map', $map);
        $this->response->assign(array(
          'page_title' => $page_title,
          'getter' => $getter
        ));
      } else {
        $this->response->badRequest();
      } // if

    } // archive

    /**
     * Manualy trigger this recurring profile
     * 
     */
    function trigger() {
      if($this->request->isAsyncCall()) {
        if(!$this->active_recurring_profile instanceof RecurringProfile) {
          $this->response->notFound();
        }//if
        if(!$this->active_recurring_profile->isSkippedToTrigger()) {
          $this->response->forbidden();
        }//if
        
        RecurringInvoice::createInvoice($this->active_recurring_profile);
        $this->response->respondWithData($this->active_recurring_profile, array('as' => 'recurring_profile','detailed' => true));
       
      } else {
        $this->response->badRequest();
      }//if
    }//trigger
    
    /**
     * Duplicate this recurring profile
     * 
     */
    function duplicate() {
      if($this->request->isAsyncCall() || $this->request->isApiCall()) {
        
        if(!RecurringProfiles::canAdd($this->logged_user)) {
          $this->response->forbidden();
        }//if
        
        if(!$this->active_recurring_profile instanceof RecurringProfile) {
          $this->response->notFound();
        }//if
        
          $recurring_profile_data = array(
            'name' => $this->active_recurring_profile->getName(),
            'company_id' => $this->active_recurring_profile->getCompanyId(),
            'company_address' => $this->active_recurring_profile->getCompanyAddress(),
            'currency_id' => $this->active_recurring_profile->getCurrencyId(),
          	'language_id' => $this->active_recurring_profile->getLanguageId(),
          	'note' => $this->active_recurring_profile->getNote(),
          	'private_note' => $this->active_recurring_profile->getPrivateNote(),
          	'frequency' => $this->active_recurring_profile->getFrequency(),
          	'occurrences' => $this->active_recurring_profile->getOccurrences(),
          	'auto_issue' => $this->active_recurring_profile->getAutoIssue(),
          	'allow_payments' => $this->active_recurring_profile->getAllowPayments(),
            'project_id' => $this->active_recurring_profile->getProjectId(),
            'recipient_id' => $this->active_recurring_profile->getRecipientId(),
            'invoice_due_after' => $this->active_recurring_profile->getInvoiceDueAfter(),
            'purchase_order_number' => $this->active_recurring_profile->getPurchaseOrderNumber()

          );
          
	        if(is_foreachable($this->active_recurring_profile->getItems())) {
	          $recurring_profile_data['items'] = array();
	          foreach($this->active_recurring_profile->getItems() as $item) {
	            $recurring_profile_data['items'][] = array(
	              'description'         => $item->getDescription(),
	              'unit_cost'           => $item->getUnitCost(),
	              'quantity'            => $item->getQuantity(),
	              'first_tax_rate_id'   => $item->getFirstTaxRateId(),
	              'second_tax_rate_id'  => $item->getSecondTaxRateId(),
	              'total'               => $item->getTotal(),
	              'subtotal'            => $item->getSubtotal()
	            );
	          } // foreach
	        } // if


        $this->response->assign('recurring_profile_data', $recurring_profile_data);
        $this->response->assign('is_duplicat', true);
        $this->response->assign(RecurringProfiles::getSettingsForInvoiceForm($this->active_recurring_profile));
      } else {
        $this->response->badRequest();
      }//if
    }//duplicate
    
    
    /**
     * Mass edit
     */
    function mass_edit() {
    	if ($this->getControllerName() == 'recurring_invoice') {
    		$this->mass_edit_objects = RecurringProfiles::findByIds($this->request->post('selected_item_ids'), STATE_ARCHIVED, $this->logged_user->getMinVisibility());
    	} // if
    	parent::mass_edit();
    } // mass_edit
    
    /**
     * Show recurring profile details
     */
    function view() {
      if($this->active_recurring_profile->isNew()) {
        $this->response->notFound();
      } // if
      
      if(!RecurringProfile::canView($this->logged_user)) {
        $this->response->forbidden();
      } // if

      // Mobile interface
      if($this->request->isMobileDevice()) {
        $this->response->assign('invoice_template', new InvoiceTemplate());

      // Other interfaces
      } else {
        $this->wireframe->setPageObject($this->active_recurring_profile, $this->logged_user);
        if($this->request->isSingleCall() || $this->request->isQuickViewCall()) {
          $this->response->assign(array(
            'invoice_template' => new InvoiceTemplate()
          ));
          $this->render();
        } else {
          $this->__forward('index', 'index');
        } // if
      } // if
    } // view
    
    /**
     * Add recurring profile
     */
    function add() {
      if($this->request->isAsyncCall() || $this->request->isApiCall()) {
        if(!RecurringProfiles::canAdd($this->logged_user)) {
          $this->response->forbidden();
        }//if
        
        $recurring_profile_data = $this->request->post('recurring_profile');
        $recurring_profile_items_data = $this->request->post('invoice');
        
        $default_currency = Currencies::getDefault();
        if(!is_array($recurring_profile_data)) {
          $recurring_profile_data['currency_id'] = $default_currency instanceof Currency ? $default_currency->getId() : null;
          $recurring_profile_data['occurrences'] = 1;
          $recurring_profile_data['second_tax_is_compound'] = $this->active_recurring_profile->getSecondTaxIsCompound();
        }//if

        $this->response->assign('recurring_profile_data', $recurring_profile_data);
        $this->response->assign(RecurringProfiles::getSettingsForInvoiceForm($this->active_recurring_profile));
        
        if($this->request->isSubmitted()) {
          try {
            DB::beginWork('Creating new recurring profile @ ' . __CLASS__);
            
            $this->active_recurring_profile = new RecurringProfile();
            
            if(!$recurring_profile_data['language_id']) {
              $recurring_profile_data['language_id'] = ConfigOptions::getValue('language');
            }//if

            $this->active_recurring_profile->setAttributes($recurring_profile_data);
            $this->active_recurring_profile->setRecipient($recurring_profile_data['recipient_id']);
            $this->active_recurring_profile->setState(STATE_VISIBLE);
           
            $start_on = $recurring_profile_data['start_on'];
            $frequency = $recurring_profile_data['frequency'];
            $this->active_recurring_profile->save();
            
            $this->active_recurring_profile->setNextTriggerOnDate($frequency,$start_on);

            if ($this->active_recurring_profile->getSecondTaxIsEnabled()) {
              $this->active_recurring_profile->setSecondTaxIsCompound(array_var($recurring_profile_data, 'second_tax_is_compound', false));
            } // if

            $this->active_recurring_profile->setItems($recurring_profile_items_data['items']);
            
            $this->active_recurring_profile->save();

            
            //if start date is today, make invoice
            $start = new DateValue($start_on);
            if($start->isToday()) {
              RecurringInvoice::createInvoice($this->active_recurring_profile);
            }//if
            
            DB::commit('New recurring profile added @ ' . __CLASS__);
            $this->response->respondWithData($this->active_recurring_profile, array(
              'as' => 'recurring_profile', 
              'detailed' => true,
            ));
          } catch(Error $e) {
             DB::rollback('Failed to add new recurring profile @ ' . __CLASS__);
             $this->response->exception($e);
          } //try
        } // if
      } else {
        $this->response->badRequest();
      } // if
    }//add
    
    /**
     * Edit recurring profile
     */
    function edit() {
      if($this->request->isAsyncCall() || $this->request->isApiCall()) {
        if(!RecurringProfile::canEdit($this->logged_user)) {
          $this->response->forbidden();
        }//if
        
        $recurring_profile_data = $this->request->post('recurring_profile');
        $recurring_profile_items_data = $this->request->post('invoice');
        
        if(!is_array($recurring_profile_data)) {
          $recurring_profile_data = array(
            'name' => $this->active_recurring_profile->getName(),
            'company_id' => $this->active_recurring_profile->getCompanyId(),
            'company_address' => $this->active_recurring_profile->getCompanyAddress(),
            'currency_id' => $this->active_recurring_profile->getCurrencyId(),
          	'language_id' => $this->active_recurring_profile->getLanguageId(),
          	'note' => $this->active_recurring_profile->getNote(),
          	'private_note' => $this->active_recurring_profile->getPrivateNote(),
          	'start_on' => $this->active_recurring_profile->getStartOn(),
          	'frequency' => $this->active_recurring_profile->getFrequency(),
          	'occurrences' => $this->active_recurring_profile->getOccurrences(),
          	'auto_issue' => $this->active_recurring_profile->getAutoIssue(),
          	'allow_payments' => $this->active_recurring_profile->getAllowPayments(),
            'project_id' => $this->active_recurring_profile->getProjectId(),
            'recipient_id' => $this->active_recurring_profile->getRecipientId(),
            'invoice_due_after' => $this->active_recurring_profile->getInvoiceDueAfter(),
            'second_tax_is_compound' => $this->active_recurring_profile->getSecondTaxIsCompound(),
            'purchase_order_number' => $this->active_recurring_profile->getPurchaseOrderNumber()
          );
          
	        if(is_foreachable($this->active_recurring_profile->getItems())) {
	          $recurring_profile_data['items'] = array();
	          foreach($this->active_recurring_profile->getItems() as $item) {
	            $recurring_profile_data['items'][] = array(
                'id'                  => $item->getId(),
	              'description'         => $item->getDescription(),
	              'unit_cost'           => $item->getUnitCost(),
	              'quantity'            => $item->getQuantity(),
	              'first_tax_rate_id'   => $item->getFirstTaxRateId(),
	              'second_tax_rate_id'  => $item->getSecondTaxRateId(),
                'subtotal'            => $item->getSubtotal(),
	              'total'               => $item->getTotal()
	            );
	          } // foreach
	        } // if	        
        }//if
        
        $this->response->assign('recurring_profile_data', $recurring_profile_data);
        $this->response->assign(RecurringProfiles::getSettingsForInvoiceForm($this->active_recurring_profile));
        
        if($this->request->isSubmitted()) {
          try {
            if(!$this->active_recurring_profile instanceof RecurringProfile) {
              $this->response->badRequest();
            }//if
            
            DB::beginWork('Editing recurring profile @ ' . __CLASS__);
            
            $this->active_recurring_profile->setAttributes($recurring_profile_data);
            $this->active_recurring_profile->setRecipient($recurring_profile_data['recipient_id']);
           
            $start_on = $recurring_profile_data['start_on'];
            $frequency = $recurring_profile_data['frequency'];
            if($start_on && $frequency) {
              $this->active_recurring_profile->setNextTriggerOnDate($frequency,$start_on);
            }//if

            if ($this->active_recurring_profile->getSecondTaxIsEnabled()) {
              $this->active_recurring_profile->setSecondTaxIsCompound(array_var($recurring_profile_data, 'second_tax_is_compound', false));
            } // if

            $this->active_recurring_profile->setItems($recurring_profile_items_data['items'], true);
            $this->active_recurring_profile->save();
            
            //if start date is today, make invoice
            if($start_on) {
              $start = new DateValue($start_on);
              if($start->isToday()) {
                RecurringInvoice::createInvoice($this->active_recurring_profile);
              }//if
            }//if
            
            DB::commit('Recurring profile edited @ ' . __CLASS__);
            $this->response->respondWithData($this->active_recurring_profile, array(
              'as' => 'recurring_profile', 
              'detailed' => true,
            ));
          } catch (Exception $e) {
            DB::rollback('Failed to edit recurring profile @ ' . __CLASS__);
            $this->response->exception($e);
          }//try  
        }//if
      } else {
        $this->response->badRequest();
      }//if
    }//edit
    
    /**
     * Delete recuring profile
     */
    function delete() {
      if($this->request->isAsyncCall() || $this->request->isApiCall()) {
        if(!$this->active_recurring_profile instanceof RecurringProfile) {
          $this->response->badRequest();
        }//if
        try {
          DB::beginWork('Deleting recurring profile @ ' .__CLASS__);
          $this->active_recurring_profile->delete();
          
          DB::commit('Recurring profile deleted @ ' . __CLASS__);
        } catch (Error $e) {
          DB::rollback('Failed to delete recurring profile @ ' . __CLASS__);
          $this->response->exception($e);
        }//try
      } else {
        $this->response->badRequest();
      }//if
      die();
    }//delete
    
    
  }