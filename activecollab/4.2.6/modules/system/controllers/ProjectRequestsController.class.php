<?php

  // Inherit projects controller
  AngieApplication::useController('projects', SYSTEM_MODULE);

  /**
   * Main project requests controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ProjectRequestsController extends ProjectsController {
    
    /**
     * Active project request
     *
     * @var ProjectRequest
     */
    protected $active_project_request;
    
    /**
     * Comments delegate instance
     * 
     * @var CommentsController
     */
    protected $comments_delegate;
    
    /**
     * Subscriptions controller delegate
     *
     * @var SubscriptionsController
     */
    protected $subscriptions_delegate;

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
     * Construct project requests controller
     *
     * @param Request $parent
     * @param string $context
     */
    function __construct(Request $parent, $context = null) {
      parent::__construct($parent, $context);
      if($this->getControllerName() == 'project_requests') {
        $this->comments_delegate = $this->__delegate('comments', COMMENTS_FRAMEWORK_INJECT_INTO, 'project_request');
        $this->subscriptions_delegate = $this->__delegate('subscriptions', SUBSCRIPTIONS_FRAMEWORK_INJECT_INTO, 'project_request');

        if(AngieApplication::isModuleLoaded('footprints')) {
          $this->access_logs_delegate = $this->__delegate('access_logs', FOOTPRINTS_MODULE, 'project_request');
          $this->history_of_changes_delegate = $this->__delegate('history_of_changes', FOOTPRINTS_MODULE, 'project_request');
        } // if
      } // if
    } // __construct
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      if(ProjectRequests::canUse($this->logged_user)) {
        if($this->request->isWebBrowser()) {
          $this->wireframe->breadcrumbs->add('project_requests', 'Requests', Router::assemble('project_requests'));
          $this->wireframe->tabs->setCurrentTab('project_requests');
        } // if
        
        $project_request_id = $this->request->getId('project_request_id');
        if($project_request_id) {
          $this->active_project_request = ProjectRequests::findById($project_request_id);
        } // if
        
        if($this->active_project_request instanceof ProjectRequest) {
          $this->wireframe->breadcrumbs->add('project_request', $this->active_project_request->getName(), $this->active_project_request->getViewUrl());
        } else {
          $this->active_project_request = new ProjectRequest();
        } // if
        
        $this->response->assign('active_project_request', $this->active_project_request);
        
        if($this->comments_delegate instanceof CommentsController) {
          $this->comments_delegate->__setProperties(array(
            'active_object' => &$this->active_project_request, 
          ));
        } // if
        
        if($this->subscriptions_delegate instanceof SubscriptionsController) {
          $this->subscriptions_delegate->__setProperties(array(
            'active_object' => &$this->active_project_request, 
          ));
        } // if

        if ($this->access_logs_delegate instanceof AccessLogsController) {
          $this->access_logs_delegate->__setProperties(array(
            'active_object' => &$this->active_project_request
          ));
        } // if

        if ($this->history_of_changes_delegate instanceof HistoryOfChangesController) {
          $this->history_of_changes_delegate->__setProperties(array(
            'active_object' => &$this->active_project_request
          ));
        } // if
      } else {
        $this->response->forbidden();
      } // if
    } // __before
    
    /**
     * Show project requests
     */
    function index() {
      
      // API
      if($this->request->isApiCall()) {
        $this->response->respondWithData(ProjectRequests::findActive($this->logged_user), 'project_requests');
        
      // Printer
      } else if ($this->request->isPrintCall()) {
        $group_by = strtolower($this->request->get('group_by', null));
        $filter_by = $this->request->get('filter_by', null);
        
        // page title
        $filter_by_completion = array_var($filter_by, 'is_closed', null); 
        if ($filter_by_completion === '0') {
          $page_title = lang('Active Project Requests');
        } else if ($filter_by_completion === '1') {
          $page_title = lang('Closed Project Requests');         
        } else {
          $page_title = lang('All Project Requests');
        } // if
        
        $this->response->assign(array(
          'project_requests' => ProjectRequests::findForPrint($this->logged_user, $group_by, $filter_by),
          'page_title' => $page_title,
        ));
        
      // Regular, browser page
      } elseif($this->request->isWebBrowser()) {
        $this->wireframe->list_mode->enable();
        $this->response->assign('project_requests', ProjectRequests::findForObjectsList($this->logged_user));

        if(ProjectRequests::canAdd($this->logged_user)) {
          $this->wireframe->actions->add('project_request_add', lang('New Project Request'), Router::assemble('project_requests_add'), array(
            'onclick' => new FlyoutFormCallback('project_request_created'),
            'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
          ));
        } // if
        
        // mass manager
        if ($this->logged_user->isProjectManager()) {
          $mass_manager = new MassManager($this->logged_user, new ProjectRequest());          
          $this->response->assign('mass_manager', $mass_manager->describe($this->logged_user));
        } // if
      } // if
    } // index
    
    /**
     * Project requests archive
     */
    function archive() {
      if($this->request->isApiCall()) {
        $this->response->respondWithData(ProjectRequests::findClosed($this->logged_user), array(
          'as' => 'project_requests', 
        ));
      } else {
        $this->response->badRequest();
      } // if
    } // archive
    
    /**
     * Process mass edit request
     */
    function mass_edit() {
      
    } // mass_edit
    
    /**
     * Show project request details
     */
    function view() {
      if ($this->active_project_request->isLoaded()) {
        if ($this->active_project_request->canView($this->logged_user)) {
          if ($this->request->isApiCall()) {
            $this->response->respondWithData($this->active_project_request, array(
              'as' => 'project_request', 
              'detailed' => true, 
            ));
          } elseif ($this->request->isWebBrowser()) {
            if ($this->request->isSingleCall() || $this->request->isQuickViewCall()) {
              $this->wireframe->setPageObject($this->active_project_request, $this->logged_user);
              $this->wireframe->print->enable();
              $this->response->assign('custom_fields', ConfigOptions::getValue('project_requests_custom_fields'));

              $this->active_project_request->accessLog()->log($this->logged_user);
              
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
     * Create a new project request
     */
    function add() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        if(ProjectRequests::canAdd($this->logged_user)) {
          $project_request_data = $this->request->post('project_request');
            
          if(empty($project_request_data)) {
            if($this->logged_user instanceof Client) {
              $project_request_data = array(
                'created_by_company_address' => $this->logged_user->getCompany()->getConfigValue('office_address'),
              );
            } else {
              $project_request_data = array(
                'taken_by_id' => $this->logged_user->getId(),
              );
            } // if
            
            foreach(ProjectRequests::getCustomFields() as $field_name => $field_settings) {
              if($field_settings['enabled']) {
                $project_request_data[$field_name] = $this->active_project_request->getCustomFieldValue($field_name);
              } // if
            } // foreach
          } // if
          
          $this->response->assign(array(
            'custom_fields' => ProjectRequests::getCustomFields(), 
            'project_request_data' => $project_request_data,
            'js_company_details_url' => Router::assemble('people_company_details')
          ));
          
          if($this->request->isSubmitted()) {
            try {
              DB::beginWork('Creating project request @ ' . __CLASS__);
              
              $taken_by_id = array_var($project_request_data, 'taken_by_id');
              if($taken_by_id && !($this->logged_user instanceof Client)) {
                $taken_by = Users::findById($taken_by_id);
              } else {
                $taken_by = null;
              } // if

              $client_data = $this->request->post('client');

              if($this->logged_user instanceof Client) {
                $client_type = ProjectRequest::CLIENT_TYPE_EXISTING;

                $client_data['created_by_id'] = $this->logged_user->getId();
                $client_data['created_by_company_id'] = $this->logged_user->getCompanyId();
              } else {
                $client_type = $this->request->post('client_type');

                if ($client_type == ProjectRequest::CLIENT_TYPE_EXISTING && !array_var($client_data, 'created_by_id')) {
                  throw new Error('Please select Contact Person (they need to have permission to submit project requests)');
                } // if
              } // if
              
              $this->active_project_request->setAttributes($project_request_data);
              $this->active_project_request->setPublicId(make_string(32, 'abcdefghijklmnopqrstuvwxyz1234567890'));

              $this->active_project_request->setClientInfo(
                $client_type,
                $client_data,
                $this->request->post('new_client')
              );
              
              $this->active_project_request->save();
              
              DB::commit('Project request created @ ' . __CLASS__);

              if($this->logged_user instanceof Client) {
                $this->active_project_request->subscriptions()->subscribe($this->logged_user);
              } // if

              if($taken_by instanceof User) {
                $this->active_project_request->setTakenBy($taken_by);
                $this->active_project_request->subscriptions()->subscribe($this->active_project_request->getTakenBy());
              } // if
              
              $this->active_project_request->notifyRepresentatives();

              if ($this->request->post('project_request_notify_client')) {
                DB::beginWork('Subscribing client @ ' . __CLASS__);
                $this->active_project_request->subscriptions()->subscribe($this->active_project_request->getCreatedBy());
                $this->active_project_request->notifyClient();
                DB::commit('Client subscribed @ ' . __CLASS__);
              } // if
              
              $this->response->respondWithData($this->active_project_request, array(
                'as' => 'project_request', 
                'detailed' => true, 
              ));
            } catch(Exception $e) {
              DB::rollback('Faield to create project request @ ' . __CLASS__);
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
     * Update project request
     */
    function edit() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
        if($this->active_project_request->isLoaded()) {
          if($this->active_project_request->canEdit($this->logged_user)) {
            $project_request_data = $this->request->post('project_request');
            
            if(!is_array($project_request_data)) {
              $project_request_data = array(
                'name' => $this->active_project_request->getName(),
                'body' => $this->active_project_request->getBody(),
                'taken_by_id' => $this->active_project_request->getTakenById(),
                'created_by_id' => $this->active_project_request->getCreatedById(),
                'created_by_company_id' => $this->active_project_request->getCreatedByCompanyId(),
                'created_by_company_address' => $this->active_project_request->getCreatedByCompanyAddress(),
              );
              
              foreach(ProjectRequests::getCustomFields() as $field_name => $field_settings) {
                if($field_settings['enabled']) {
                  $project_request_data[$field_name] = $this->active_project_request->getCustomFieldValue($field_name);
                } // if
              } // foreach
            } // if

            $new_client_data = $this->request->post('new_client');
            if (!is_array($new_client_data) && !$this->active_project_request->getCompany() instanceof Company) {
              $new_client_data = array(
                'created_by_company_name' => $this->active_project_request->getCompanyName(),
                'created_by_company_address' => $this->active_project_request->getCompanyAddress(),
                'created_by_name' => $this->active_project_request->getCreatedByName(),
                'created_by_email' => $this->active_project_request->getCreatedByEmail(),
              );
            } // if
            
            $this->response->assign(array(
              'custom_fields' => ProjectRequests::getCustomFields(),
              'new_client' => $new_client_data,
              'project_request_data' => $project_request_data,
              'js_company_details_url' => Router::assemble('people_company_details')
            ));
            
            if($this->request->isSubmitted()) {
              try {
                DB::beginWork('Updating project request @ ' . __CLASS__);
                
                $taken_by_id = array_var($project_request_data, 'taken_by_id');
                if($taken_by_id) {
                  $taken_by = Users::findById($taken_by_id);
                } else {
                  $taken_by = null;
                } // if

                $this->active_project_request->setClientInfo(
                  $this->request->post('client_type'),
                  $this->request->post('client'),
                  $this->request->post('new_client')
                );
                
                $this->active_project_request->setAttributes($project_request_data);
                $this->active_project_request->setTakenBy($taken_by);
                $this->active_project_request->save();
                
                DB::commit('Project request updated @ ' . __CLASS__);
                
                $this->response->respondWithData  ($this->active_project_request, array(
                  'as' => 'project_request', 
                  'detailed' => true, 
                ));
              } catch(Exception $e) {
                DB::rollback('Updating project request @ ' . __CLASS__);
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
    } // edit
    
    /**
     * Open closed project request
     */
    function take() {
      if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted()) {
        if($this->active_project_request->isLoaded()) {
          if($this->active_project_request->canTake($this->logged_user)) {
            try {
              $this->active_project_request->setTakenBy($this->logged_user);
              $this->active_project_request->save();
              
              $this->response->respondWithData  ($this->active_project_request, array(
                'as' => 'project_request', 
                'detailed' => true, 
              ));
            } catch(Exception $e) {
              $this->response->exception($e);
            } // try
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // take
    
    /**
     * Open closed project request
     */
    function open() {
      if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted()) {
        if($this->active_project_request->isLoaded()) {
          if($this->active_project_request->canChangeStatus($this->logged_user)) {
            try {
              $this->active_project_request->open($this->logged_user);
              $this->response->respondWithData  ($this->active_project_request, array(
                'as' => 'project_request', 
                'detailed' => true, 
              ));
            } catch(Exception $e) {
              $this->response->exception($e);
            } // try
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // open
    
    /**
     * Just close project request
     */
    function close() {
      if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted()) {
        if($this->active_project_request->isLoaded()) {
          if($this->active_project_request->canChangeStatus($this->logged_user)) {
            try {
              $this->active_project_request->close($this->logged_user);
              $this->response->respondWithData($this->active_project_request, array(
                'as' => 'project_request', 
                'detailed' => true, 
              ));
            } catch(Exception $e) {
              $this->response->exception($e);
            } // try
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // close
    
    /**
     * Delete project request
     */
    function delete() {
      if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted()) {
        if($this->active_project_request->isLoaded()) {
          if($this->active_project_request->canDelete($this->logged_user)) {
            try {
              $this->active_project_request->delete();
              $this->response->respondWithData(array(
                'id' => $this->active_project_request->getId(),
              ), array(
                'as' => 'project_request', 
                'detailed' => true, 
              ));
            } catch(Exception $e) {
              $this->response->exception($e);
            } // try
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // delete

    /**
     * Save client data from request
     */
    function save_client() {
      if($this->request->isAsyncCall()) {
        if($this->logged_user->isPeopleManager()) {
          $company_data = $this->request->post('company_data');
          $user_data = $this->request->post('user_data');

          if (!is_foreachable($company_data)) {
            $company_data = array(
              'company_name' => $this->active_project_request->getCreatedByCompanyName(),
              'company_address' => $this->active_project_request->getCreatedByCompanyAddress()
            );
          } // if

          if (!is_foreachable($user_data)) {
            $user_info = explode(" ", $this->active_project_request->getCreatedByName());
            if (count($user_info) > 1) {
              $first_name = $user_info['0'];
              $last_name = substr($this->active_project_request->getCreatedByName(), strpos($this->active_project_request->getCreatedByName(), " "), strlen($this->active_project_request->getCreatedByName()));
            } else {
              $first_name = $this->active_project_request->getCreatedByName();
              $last_name = "";
            } // if

            $user_data = array(
              'email' => $this->active_project_request->getCreatedByEmail(),
              'first_name' => $first_name,
              'last_name' => $last_name
            );
          } // if

          $this->response->assign(array(
            'user_data' => $user_data,
            'company_data' => $company_data,
            'save_client_url' => $this->active_project_request->getSaveClientUrl(),
          ));

          if ($this->request->isSubmitted()) {
            try {
              DB::beginWork('Adding company and user @ ' . __CLASS__);

              $errors = new ValidationErrors();

              $company_name = isset($company_data['company_name']) && $company_data['company_name'] ? trim($company_data['company_name']) : null;
              if (!$company_name) {
                $errors->addError(lang('Company Name is required'), 'company_name');
              } // if

              if (Companies::findByName($company_name) instanceof Company) {
                $errors->addError(lang('Company with that name ":name" already exists', array('name' => $company_name)), 'company_name');
              } // if

              $company_address = isset($company_data['company_address']) && $company_data['company_address'] ? trim($company_data['company_address']) : null;
              if (!$company_address) {
                $errors->addError(lang('Company Address is required'), 'company_address');
              } // if

              $user_email = isset($user_data['email']) && $user_data['email'] ? trim($user_data['email']) : null;
              if (!$user_email || !is_valid_email($user_email)) {
                $errors->addError(lang("Client's Email is required"), 'email');
              } elseif (Users::findByEmail($user_email, true) instanceof User) {
                $errors->addError(lang('User with email address ":email" already exists', array('email' => $user_email)), 'email');
              } // if

              if ($errors->hasErrors()) {
                throw $errors;
              } else {
                // save company
                $company = new Company();
                $company->setName($company_name);
                $company->setState(STATE_VISIBLE);
                $company->setIsOwner(false);
                $company->save();

                ConfigOptions::setValueFor('office_address', $company, $company_address);

                // save user
                $user = new Client();
                $user->setEmail($user_email);
                $user->setFirstName($user_data['first_name']);
                $user->setLastName($user_data['last_name']);
                $user->setCompany($company);
                $user->setState(STATE_VISIBLE);

                $password = Authentication::getPasswordPolicy()->generatePassword();
                $user->setPassword($password);

                $custom_permissions = array('can_request_project');

                if(AngieApplication::isModuleLoaded('invoicing')) {
                  $custom_permissions[] = 'can_manage_client_finances';
                } // if

                $user->setSystemPermissions($custom_permissions);

                $user->save();

                EventsManager::trigger('on_client_saved', array('object' => $this->active_project_request, 'user' => $user, 'company' => $company));

                DB::commit('Company and user added @ ' . __CLASS__);

                // send welcome email to the client
                if ($this->request->post('notify_client')) {
                  AngieApplication::notifications()
                    ->notifyAbout(AUTHENTICATION_FRAMEWORK_INJECT_INTO . '/welcome', $user, $this->logged_user)
                    ->setPassword($password)
                    ->sendToUsers($user);
                } // if

                $this->response->respondWithData(ProjectRequests::findById($this->active_project_request->getId()), array(
                  'as' => 'project_request',
                  'detailed' => true,
                ));
              } // if
            } catch (Exception $e) {
              DB::rollback('Failed to add company and user @ ' . __CLASS__);
              $this->response->exception($e);
            } // try
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // save_client
    
  }