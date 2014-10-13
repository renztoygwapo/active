<?php

  // Use people controller
  AngieApplication::useController('people', SYSTEM_MODULE);

  /**
   * Company profile controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class CompaniesController extends PeopleController {
    
    /**
     * Name of the parent module
     *
     * @var mixed
     */
    protected $active_module = SYSTEM_MODULE;
    
    /**
     * Selected company
     *
     * @var Company
     */
    protected $active_company;
    
    /**
     * State controller delegate
     *
     * @var StateController
     */
    protected $state_delegate;
    
    /**
     * Avatar controller delegate
     *
     * @var CompanyAvatarController
     */
    protected $avatar_delegate;

	  /**
	   * History of changes controller delegate
	   *
	   * @var HistoryOfChangesController
	   */
	  protected $history_of_changes_delegate;

    /**
     * Actions available through API
     *
     * @var array
     */
    protected $api_actions = array('index', 'view', 'add', 'edit', 'delete');
    
    /**
     * Construct companies controller
     *
     * @param Request $parent
     * @param mixed $context
     */
    function __construct(Request $parent, $context = null) {
      parent::__construct($parent, $context);
      
      if($this->getControllerName() == 'companies') {
        $this->state_delegate = $this->__delegate('state', ENVIRONMENT_FRAMEWORK_INJECT_INTO, 'people_company');
        $this->avatar_delegate = $this->__delegate('company_avatar', AVATAR_FRAMEWORK_INJECT_INTO, 'people_company');

	      if(AngieApplication::isModuleLoaded('footprints')) {
		      $this->history_of_changes_delegate = $this->__delegate('history_of_changes', FOOTPRINTS_MODULE, 'people_company');
	      } // if
      } // if
    } // __construct
  
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      $company_id = $this->request->getId('company_id');
      if($company_id) {
        $this->active_company = Companies::findById($company_id);
      } // if
      
      if($this->active_company instanceof Company) {
        $this->wireframe->actions->clear();

        if (!$this->active_company->isAccessible()) {
          $this->response->notFound();
        } // if
        
        if(!$this->active_company->canView($this->logged_user)) {
          $this->response->forbidden();
        } // if

        /**
        if($this->active_company->getState() == STATE_ARCHIVED && ($this->logged_user->isPeopleManager() || $this->logged_user->isFinancialManager())) {
          $this->wireframe->breadcrumbs->add('companies_archive', lang('Archive'), Router::assemble('people_archive'));
        } // if
        */

        $this->wireframe->breadcrumbs->add('company', $this->active_company->getName(), $this->active_company->getViewUrl());
        
        $this->response->assign('company_tab', 'overview');
      } else {
        $this->active_company = new Company();
      } // if
      
      $this->response->assign('active_company', $this->active_company);
      
      if($this->getControllerName() == 'companies' && $this->state_delegate instanceof StateController) {
        $this->state_delegate->__setProperties(array(
          'active_object' => &$this->active_company,
        ));
      } // if
      
      if($this->getControllerName() == 'companies' && $this->avatar_delegate instanceof CompanyAvatarController) {
        $this->avatar_delegate->__setProperties(array(
          'active_object' => &$this->active_company
        ));
      } // if

	    if ($this->history_of_changes_delegate instanceof HistoryOfChangesController) {
		    $this->history_of_changes_delegate->__setProperties(array(
			    'active_object' => &$this->active_company
		    ));
	    } // if
    } // __before
    
    /**
     * Needed for user permalink
     */
    function index() {
      PeopleController::index();
      $this->setView(get_view_path('index', 'people', SYSTEM_MODULE));
    } // index
    
    /**
     * Show company details
     */
    function view() {
      if($this->active_company->isLoaded()) {

        // Shared page actions between interfaces
        if($this->request->isPageCall() || $this->request->isPhone() || $this->request->isPrintCall()) {
          $this->wireframe->setPageObject($this->active_company, $this->logged_user);
        } // if

        // Regular web browser request
        if($this->request->isWebBrowser()) {
          $this->wireframe->print->enable();

          if ($this->request->isSingleCall() || $this->request->isQuickViewCall()) {
            if(Users::canAdd($this->logged_user, $this->active_company)) {
              $this->wireframe->actions->addBefore('add_user', lang('New User'), $this->active_company->getAddUserUrl(), 'object_options', array(
                'onclick' => new FlyoutFormCallback('user_created'),
                'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
              ));
            } // if
          } else {
            $this->__forward('index');
          } // if

        // Request made by phone
        } elseif($this->request->isPhone()) {
          $this->response->assign(array(
            'users' => $this->active_company->getUsers($this->logged_user->visibleUserIds()),
            'active_projects' => Projects::findActiveByUserAndCompany($this->logged_user, $this->active_company, true),
            'formatted_invoices' => AngieApplication::isModuleLoaded('invoicing') ? Invoices::findForPhoneList($this->logged_user, $this->active_company) : false,
            'can_access_company_invoices' => AngieApplication::isModuleLoaded('invoicing') && Invoices::canAccessCompanyInvoices($this->logged_user, $this->active_company),
            'completed_projects_url' => Router::assemble('people_company_projects_archive', array('company_id' => $this->active_company->getId()))
          ));

          $this->wireframe->actions->remove('export_vcard');

        // Request made by tablet device
        } elseif($this->request->isTablet()) {
          throw new NotImplementedError(__METHOD__);
        
        // Print
        } elseif($this->request->isPrintCall()) {
          $this->response->assign(array(
            'company_projects' => Projects::findActiveByUserAndCompany($this->logged_user, $this->active_company, false),
            'company_invoices' => AngieApplication::isModuleLoaded('invoicing') ? Invoices::findByCompany($this->active_company, $this->logged_user) : false,
            'company_quotes' => AngieApplication::isModuleLoaded('invoicing') ? Quotes::findByCompany($this->active_company, $this->logged_user) : false,
            'can_access_company_invoices' => AngieApplication::isModuleLoaded('invoicing') && Invoices::canAccessCompanyInvoices($this->logged_user, $this->active_company),
          ));

        // API call
        } else {
          $this->response->respondWithData($this->active_company, array(
            'as' => 'company', 
            'detailed' => true,
          ));
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // index
    
    /**
     * Create a new company
     */
    function add() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted()) || $this->request->isMobileDevice()) {
        if(Companies::canAdd($this->logged_user)) {
          $company = new Company();
          $options = array('office_address', 'office_phone', 'office_fax', 'office_homepage');
          
          $company_data = $this->request->post('company');
          $this->response->assign(array(
            'company_data' => $company_data,
            'active_company' => $company,
          ));

          if ($this->request->isSubmitted()) {
            try {
              DB::beginWork('Adding new company @ ' . __CLASS__);
              $company_data['office_homepage'] = $this->active_company->validateHomepage($company_data['office_homepage']);

              // Check if we can and should update note value
              $note = false;
              if(array_key_exists('note', $company_data)) {
                $note = trim(array_var($company_data, 'note', null, true));

                if(empty($note)) {
                  $note = null;
                } else {
                  if(strlen_utf($note) > 255) {
                    $note = substr_utf($note, 0, 255);
                  } // if
                } // if
              } // if

              $this->active_company->setAttributes($company_data);
              $this->active_company->setState(STATE_VISIBLE);
              $this->active_company->setIsOwner(false);

              // Update note if we have it set for update
              if($note !== false && Companies::canSeeNotes($this->logged_user)) {
                $this->active_company->setNote($note);
              } // if

              $this->active_company->save();
              
              foreach($options as $option) {
                $value = trim(array_var($company_data, $option));
                
                if($value != '') {
                  ConfigOptions::setValueFor($option, $this->active_company, $value);
                } // if
              } // foreach
              
              DB::commit('Company added @ ' . __CLASS__);

              clean_menu_projects_and_quick_add_cache();

              if(AngieApplication::behaviour()->isTrackingEnabled()) {
                AngieApplication::behaviour()->recordFulfilment($this->request->post('_intent_id'), null, null, function() {
                  return array('company_created');
                });
              } // if
              
              if($this->request->isPageCall()) {
                $this->response->redirectToUrl($this->active_company->getViewUrl());
              } else {
                $this->response->respondWithData($this->active_company, array('as' => 'company', 'detailed' => true));
              } // if
            } catch(Exception $e) {
              DB::rollback('Failed to add company @ ' . __CLASS__);
              
              if($this->request->isPageCall()) {
                $this->response->redirectToReferer();
              } else {
                $this->response->exception($e);
              } // if
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
     * Edit Company Info
     */
    function edit() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted()) || $this->request->isMobileDevice()) {
        if($this->active_company->isLoaded()) {
          if($this->active_company->canEdit($this->logged_user)) {
            $options = array('office_address', 'office_phone', 'office_fax', 'office_homepage');
      
            $company_data = $this->request->post('company', array_merge(array(
              'name' => $this->active_company->getName(),
              'note' => $this->active_company->getNote(),
            ), ConfigOptions::getValueFor($options, $this->active_company)));
            $this->response->assign('company_data',  $company_data);
            
            if($this->request->isSubmitted()) {
              try {
                DB::beginWork('Updating company @ ' . __CLASS__);

                // Check if we can and should update note value
                $note = false;
                if(array_key_exists('note', $company_data)) {
                  $note = trim(array_var($company_data, 'note', null, true));

                  if(empty($note)) {
                    $note = null;
                  } else {
                    if(strlen_utf($note) > 255) {
                      $note = substr_utf($note, 0, 255);
                    } // if
                  } // if
                } // if

                $this->active_company->validateHomepage($company_data['office_homepage']);
                $this->active_company->setAttributes($company_data);

                // Update note if we have it set for update
                if($note !== false && Companies::canSeeNotes($this->logged_user)) {
                  $this->active_company->setNote($note);
                } // if

                $this->active_company->save();
                
                foreach($options as $option) {
                  if(!array_key_exists($option, $company_data)) {
                    continue; // Skip if not present in request
                  } // if

                  $value = trim(array_var($company_data, $option));
                  
                  if($option == 'office_homepage') {
                    $value = valid_url_protocol($value);
                    if(!is_valid_url($value)) {
                      $value = '';
                    } // if
                  } // if
                  
                  if($value == '') {
                    ConfigOptions::removeValuesFor($this->active_company, $option);
                  } else {
                    ConfigOptions::setValueFor($option, $this->active_company, $value);
                  } // if
                } // foreach
                
                if($this->active_company->getIsOwner()) {
                  AngieApplication::cache()->remove('owner_company'); // force cache refresh on next load
                } // if
                
                DB::commit('Company updated @ ' . __CLASS__);

                clean_menu_projects_and_quick_add_cache();
                
                if($this->request->isPageCall()) {
                  $this->response->redirectToUrl($this->active_company->getViewUrl());
                } else {
                  $this->response->respondWithData($this->active_company, array('as' => 'company', 'detailed' => true));
                } // if
              } catch(Exception $e) {
                DB::rollback('Failed to update company @ ' . __CLASS__);
                
                if($this->request->isPageCall()) {
                  $this->response->assign('errors', $e);
                } else {
                  $this->response->exception($e);
                } // if
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
    } // edit
    
    /**
     * Export vCard
     */
    function export_vcard() {
      if($this->active_company->isLoaded()) {
        if($this->request->get('submitted')) {
          try {
            $this->active_company->toVCard($this->request->get('include_users'));
            die();
          } catch(Exception $e) {
            $this->response->exception($e);
          } // try
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // export_vcard
    
//    /**
//     * Delete company
//     */
//    function delete() {
//      if($this->request->isApiCall()) {
//        $this->response->notFound();
//      } // if
//
//      if($this->active_company->isLoaded()) {
//        if($this->active_company->canDelete($this->logged_user)) {
//          if($this->active_company->isNew() || $this->active_company->isOwner()) {
//            $this->response->notFound();
//          } // if
//
//          if($this->request->isSubmitted()) {
//            try {
//              $this->active_company->delete();
//
//              clean_menu_projects_and_quick_add_cache();
//
//              if($this->request->isApiCall()) {
//                $this->response->respondWithData($this->active_company, array(
//                  'as' => 'company',
//                ));
//              } else {
//                $this->flash->success('Company ":name" has been deleted', array('name' => $this->active_company->getName()));
//              } // if
//            } catch(Exception $e) {
//              if($this->request->isApiCall()) {
//                $this->response->operationFailed();
//              } else {
//                $this->flash->error('Failed to delete ":name" company', array('name' => $this->active_company->getName()));
//              } // if
//
//              $this->response->redirectTo('people');
//            } // try
//          } else {
//            $this->response->badRequest();
//          } // if
//        } else {
//          $this->response->forbidden();
//        } // if
//      } else {
//        $this->response->notFound();
//      } // if
//    } // delete

    /**
     * Send client address details
     */
    function company_details() {
      if ($this->request->isAsyncCall()) {
        $client_id = $this->request->get('company_id');

        $client_company = $client_id ? Companies::findById($client_id) : null;
        if ($client_company instanceof Company) {
          if ($client_company->canView($this->logged_user)) {
            $this->response->respondWithData(ConfigOptions::getValueFor('office_address', $client_company));
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // if
  
  }