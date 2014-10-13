<?php

  // Build on top of backend controller
  AngieApplication::useController('backend', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * People controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class PeopleController extends BackendController {
    
    /**
     * Actions available through the API
     *
     * @var array
     */
    protected $api_actions = array('index');
    
    /**
     * Can user import contacts from vCard?
     *
     * @var boolean
     */
    protected $can_import_vcard = false;
  
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      $this->wireframe->tabs->clear();
      $this->wireframe->tabs->add('people', lang('People'), Router::assemble('people'), null, true);
      
      EventsManager::trigger('on_people_tabs', array(&$this->wireframe->tabs, &$this->logged_user));
      
      $this->wireframe->breadcrumbs->add('people', lang('People'), Router::assemble('people'));
      $this->wireframe->setCurrentMenuItem('people');
      
      if(get_class($this) == 'PeopleController') {
        if(Companies::canAdd($this->logged_user)) {
          $this->wireframe->actions->add('invite_people', lang('Invite People'), Router::assemble('people_invite'), array(
            'onclick' => new FlyoutFormCallback('people_invited', array(
              'success_message' => lang('People are invited'),
              'width' => 950,
            )),
            'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
            'primary' => true
          ));

          $this->wireframe->actions->add('new_company', lang('New Company'), Router::assemble('people_companies_add'), array(
            'onclick' => new FlyoutFormCallback('company_created', array(
              'width' => 600,
            )),
            'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
            'primary' => true
          ));
        } // if
      } // if

      $this->response->assign('can_import_vcard', $this->logged_user->canImportVcard());
      $this->response->assign('can_add_company', Companies::canAdd($this->logged_user));
    } // __construct
    
    /**
     * Show companies index page
     */
    function index() {

      // Phone people
      if($this->request->isPhone()) {
        $companies = Companies::findActive($this->logged_user);
        $this->response->assign(array(
          'companies' => $companies,
          'archived_companies_url' => Router::assemble('people_archive'),
          'visible_user_ids' => $this->logged_user->visibleUserIds()
        ));

      // Tablet people
      } elseif($this->request->isTablet()) {
        throw new NotImplementedError(__METHOD__);

      // API people
      } elseif($this->request->isApiCall()) {
        $this->response->respondWithData(Companies::findActive($this->logged_user), array(
          'as' => 'companies', 
        ));
      
      // Printer
      } else if ($this->request->isPrintCall()) {
        $group_by = strtolower($this->request->get('group_by', null));
        $filter_by = $this->request->get('filter_by', null);
        
        // page title
        $filter_by_status = array_var($filter_by, 'is_archived', null); 
        if ($filter_by_status === '0') {
          $page_title = lang('Active Companies');
        } else if ($filter_by_status === '1') {
          $page_title = lang('Archived Companies');
        } else {
          $page_title = lang('All Companies');
        } // if

        // find tasks
        $companies = Companies::findForPrint($this->logged_user, $filter_by);
        
        $this->smarty->assignByRef('companies', $companies);
        
        $this->response->assign(array(
          'page_title' => $page_title,
        )); 
         
      // Regular people
      } else {
        $this->wireframe->showPrintButton(Router::assemble('people_printable'));
        $this->wireframe->list_mode->enable();

        $companies_map = Companies::findForObjectsList($this->logged_user);
        $users_map = Users::findForObjectsList($this->logged_user);
        
        $this->response->assign(array(
          'can_manage_people' => ($this->logged_user->isPeopleManager() || $this->logged_user->isAdministrator()),
          'users' => $users_map,
          'companies_map' => $companies_map,
          'active_user' => isset($this->active_user) && $this->active_user instanceof User ? $this->active_user : Users::getUserInstance(),
          'active_company' => isset($this->active_company) && $this->active_company instanceof Company ? $this->active_company : new Company()
        ));
        
        // mass manager
        if ($this->logged_user->isPeopleManager() || $this->logged_user->isAdministrator()) {
        	$mass_manager = new MassManager($this->logged_user, Users::getUserInstance());
        	$this->response->assign('mass_manager', $mass_manager->describe($this->logged_user));
        } // if
        
      } // if
    } // index
    
    /**
     * Companies for printing
     */
    function index_printable() {
      throw new NotImplementedError(__CLASS__ . '::' . __FUNCTION__);
    } // companies
    
    /**
     * Import vCard
     */
    function import_vcard() {
      if($this->logged_user->canImportVcard()) {
        $step = $this->request->post('wizard_step');

        if($this->request->isSubmitted()) {
          try {
            switch($step) {
              case "review":
                // check whether vCard has been uploaded
                $vcard_file = $_FILES['vcard']['tmp_name'];
                if(!is_file($vcard_file)) {
                  throw new Exception(lang('You need to upload vCard file first'));
                } // if

                // parse vCard
                $parse = File_IMC::parse('vCard');
                $parsed_vcard = $parse->fromFile($vcard_file);

                $parsed_vcard_array_keys = array_keys($parsed_vcard);
                if(!is_foreachable($parsed_vcard_array_keys)) {
                  throw new Exception(lang('vCard file is corrupted'));
                } // if

                $prepared_contacts = array();
                foreach($parsed_vcard_array_keys as $parsed_vcard_array_key) {
                  foreach($parsed_vcard[$parsed_vcard_array_key] as $vcard) {
                    Companies::prepare_contacts($prepared_contacts, $vcard, $this->logged_user);
                  } // foreach
                } // foreach

                $this->response->assign('prepared_contacts', $prepared_contacts);
                $this->setView('import_vcard_review');
                break;
              case "import":
                $companies = array(); $users = array();
                $count_companies = 0; $count_users = 0;
  
                $companies_data = $this->request->post('company');
                if(is_foreachable($companies_data)) {
                  foreach($companies_data as $company_data) {
                    if(isset($company_data['import']) && array_var($company_data, 'import') == 'ok') {
                      $company_imported = Companies::fromVCard($company_data, $companies, $users, $count_users);

                      if($company_imported) {
                        $count_companies++;
                      } // if
                    } // if
                  } // foreach

                  AngieApplication::cache()->removeByModel(Companies::getModelName(true));
                } // if

                $users_data = $this->request->post('user');
                if(is_foreachable($users_data)) {
                  foreach($users_data as $user_data) {
                    if(isset($user_data['import']) && array_var($user_data, 'import') == 'ok') {
                      $user_imported = Users::fromVCard($user_data, $users);

                      if($user_imported) {
                        $count_users++;
                      } // if
                    } // if
                  } // foreach

                  AngieApplication::cache()->removeByModel(Users::getModelName(true));
                } // if

                if(is_foreachable($users) && array_var($users_data, 'send_welcome_message')) {
                  foreach($users as $user) {
                    if(!$user['is_new']) {
                      unset($user['password']);
                      continue;
                    } // if

                    $welcome_message = trim(array_var($users_data, 'welcome_message'));
                    if($welcome_message) {
                      ConfigOptions::setValueFor('welcome_message', $user['user'], $welcome_message);
                    } // if

                    AngieApplication::notifications()
                      ->notifyAbout(AUTHENTICATION_FRAMEWORK_INJECT_INTO . '/welcome', $user['user'], $this->logged_user)
                      ->setPassword($user['password'])
                      ->setWelcomeMessage($welcome_message)
                      ->sendToUsers($user['user']);

                    unset($user['password']);
                  } // if
                } // if

                echo JSON::encode(array(
                  'message' => lang('Contacts have been successfully imported from vCard'),
                  'companies' => $companies,
                  'users' => $users
                ));
                die();
                break;
            } // switch
          } catch(Exception $e) {
            echo lang('An error occurred: :message', array('message' => $e->getMessage()));
            die();
          } // try
        } // if
      } else {
        $this->response->forbidden();
      } // if
    } // import_vcard
    
    /**
     * Export vCard
     */
    function export_vcard() {
      $companies = Companies::findActive($this->logged_user);
      if($companies) {
        try{
          Companies::render_vcard($companies);
          die();
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->operationFailed();
      } // if
    } // export_vcard
    
    /**
     * Export individual vCards
     */
    function export_individual_vcards() {
      $companies = Companies::findActive($this->logged_user);
      if($companies) {
        try{
          Companies::render_vcard($companies, true);
          die();
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->operationFailed();
      } // if
    } // export_individual_vcards

    /**
     * Invite people
     */
    function invite() {
      if($this->request->isAsyncCall()) {
        if(Companies::canAdd($this->logged_user)) {
          $invite_data = $this->request->post('invite', array(
            'type' => Users::getDefaultUserClass(),
            'send_welcome_message' => true
          ));

          $this->response->assign(array(
            'invite_data' => $invite_data,
            'default_project_role_id' => ProjectRoles::getDefaultId()
          ));

          if($this->request->isSubmitted()) {
            try {
              DB::beginWork('Inviting people @ ' . __CLASS__);

              $users = array_var($invite_data, 'users');
              if(!is_foreachable($users)) {
                throw new Error(lang('There are no people to invite.'));
              } // if

              $company = Companies::findById($invite_data['company_id']);
              if(!($company instanceof Company)) {
                throw new Error(lang('Selected company does not exists.'));
              } // if

              if(AngieApplication::isOnDemand() && !OnDemand::canAddUsersBasedOnCurrentPlan($invite_data['type'], count($users))) {
                throw new Error(OnDemand::ERROR_USERS_LIMITATION_REACHED);
              }//if

              $project_ids = $this->request->post('projects');
              if($project_ids) {
                $projects = Projects::findByIds($project_ids);
              } // if

              $project_permissions = $this->request->post('project_permissions');
              $role_id = (integer) array_var($project_permissions, 'role_id');

              $role = null;

              if($role_id) {
                $role = ProjectRoles::findById($role_id);
              } // if

              if($role instanceof ProjectRole) {
                $permissions = null;
              } else {
                $permissions = array_var($project_permissions, 'permissions');
                if(!is_array($permissions)) {
                  $permissions = null;
                } // if
              } // if

              $send_welcome_message = (boolean) array_var($invite_data, 'send_welcome_message');
              $welcome_message = $send_welcome_message ? trim(array_var($invite_data, 'welcome_message')) : '';

              $invited_users_count = 0;

              foreach($users as $user) {
                $password = Authentication::getPasswordPolicy()->generatePassword();

                $instance_class = Users::getDefaultUserClass();
                $custom_permissions = null;

                if(isset($invite_data['type'])) {
                  $instance_class = $invite_data['type'];
                  unset($invite_data['type']);

                  $custom_permissions = array_var($invite_data, 'custom_permissions', null, true);
                } // if

                $new_user = Users::getUserInstance($instance_class, true);
                $new_user->setAttributes($user);
                $new_user->setPassword($password);
                $new_user->setState(STATE_VISIBLE);
                $new_user->setCompany($company);

                if(array_var($invite_data, 'send_welcome_message')) {
                  $new_user->setInvitedOn(new DateTimeValue());
                } // if

                if(is_foreachable($custom_permissions)) {
                  $new_user->setSystemPermissions($custom_permissions);
                } // if

                $new_user->save();

                if($send_welcome_message) {
                  if($welcome_message) {
                    ConfigOptions::setValueFor('welcome_message', $new_user, $welcome_message);
                  } // if

                  AngieApplication::notifications()
                    ->notifyAbout(AUTHENTICATION_FRAMEWORK_INJECT_INTO . '/welcome', $new_user, $this->logged_user)
                    ->setPassword($password)
                    ->setWelcomeMessage($welcome_message)
                    ->sendToUsers($new_user);
                } // if

                if(isset($projects) && is_foreachable($projects)) {
                  foreach($projects as $project) {
                    $project->users()->add($new_user, $role, $permissions);
                  } // foreach
                } // if

                $invited_users_count++;
              } // foreach

              DB::commit('People invited @ ' . __CLASS__);

              if(AngieApplication::behaviour()->isTrackingEnabled() && $invited_users_count) {
                $extra_event_tags = array("invited_{$invited_users_count}_users");

                if($send_welcome_message) {
                  $extra_event_tags[] = 'email_sent';
                } // if

                if(isset($projects) && count($projects)) {
                  $extra_event_tags[] = 'added_to_projects';
                } // if

                AngieApplication::behaviour()->recordFulfilment($this->request->post('_intent_id'), $extra_event_tags, null, function() use ($extra_event_tags) {
                  return array('people_invited', $extra_event_tags);
                });
              } // if

              $this->response->respondWithData(Companies::findById(array_var($invite_data, 'company_id')), array(
                'as' => 'company',
                'detailed' => true
              ));
            } catch(Exception $e) {
              DB::rollback('Failed to invite people @ ' . __CLASS__);
              $this->response->exception($e);
            } // try
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // invite
    
    /**
     * Show archive page
     */
    function archive() {
      if($this->request->isApiCall()) {
        $this->response->respondWithData(Companies::findArchived($this->logged_user), array(
          'as' => 'companies',
        ));
      } else {
        $this->response->badRequest();
      } // if
    } // archive
    
    /**
     * Mass Edit action
     */
    function mass_edit() {
    	if ($this->getControllerName() == 'people') {
        $this->mass_edit_objects = Users::findByIds($this->request->post('selected_item_ids'), STATE_ARCHIVED, $this->logged_user->getMinVisibility());
      } // if

      parent::mass_edit();
    } // mass_edit
  
  }