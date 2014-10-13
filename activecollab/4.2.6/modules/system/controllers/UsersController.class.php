<?php

  // Use company profile module
  AngieApplication::useController('companies', SYSTEM_MODULE);

  /**
   * User profile controller
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class UsersController extends CompaniesController {
    
    /**
     * Name of the parent module
     *
     * @var mixed
     */
    protected $active_module = SYSTEM_MODULE;
      
    /**
     * Selected use
     *
     * @var User
     */
    protected $active_user;
    
    /**
     * State controller delegate
     *
     * @var StateController
     */
    protected $state_delegate;
    
    /**
     * API client subscriptions delegate
     *
     * @var ApiClientSubscriptionsController
     */
    protected $api_client_subscriptions_delegate;
    
    /**
     * Avatar controller delegate
     *
     * @var UserAvatarController
     */
    protected $avatar_delegate;
    
    /**
     * Home screen delegate
     *
     * @var HomescreenController
     */
    protected $homescreen_delegate;

    /**
     * Activity logs delegate
     *
     * @var ActivityLogsController
     */
    protected $activity_logs_delegate;
    
    /**
     * Reminders delegate controller
     *
     * @var UserRemindersController
     */
    protected $reminders_delegate;
    
    /**
     * Array of controller actions that can be accessed through API
     *
     * @var array
     */
    protected $api_actions = array('view', 'add', 'edit', 'delete');
    
    /**
     * Construct users controller
     *
     * @param Request $parent
     * @param mixed $context
     */
    function __construct(Request $parent, $context = null) {
      parent::__construct($parent, $context);
      
      if($this->getControllerName() == 'users') {
        $this->state_delegate = $this->__delegate('state', ENVIRONMENT_FRAMEWORK_INJECT_INTO, 'people_company_user');
        $this->api_client_subscriptions_delegate = $this->__delegate('api_client_subscriptions', AUTHENTICATION_FRAMEWORK_INJECT_INTO, 'people_company_user');
        $this->avatar_delegate = $this->__delegate('user_avatar', AVATAR_FRAMEWORK_INJECT_INTO, 'people_company_user');
        $this->homescreen_delegate = $this->__delegate('homescreen', HOMESCREENS_FRAMEWORK_INJECT_INTO, 'people_company_user');
        $this->activity_logs_delegate = $this->__delegate('activity_logs', ACTIVITY_LOGS_FRAMEWORK_INJECT_INTO, 'people_company_user');
        $this->reminders_delegate = $this->__delegate('user_reminders', REMINDERS_FRAMEWORK_INJECT_INTO, 'people_company_user');
      } // if
    } // __construct
       
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();

      if($this->active_company->isNew()) {
        $this->response->notFound();
      } // if

      $this->wireframe->breadcrumbs->add('users', lang('Users'), $this->active_company->getUsersUrl());

      $user_id = $this->request->get('user_id');

      if($user_id) {
        $this->active_user = Users::findById($user_id);
      } // if

      if($this->active_user instanceof User) {
        $min_state = Trash::canAccess($this->logged_user) ? STATE_TRASHED : STATE_ARCHIVED;

        if(!in_array($this->active_user->getId(), $this->logged_user->visibleUserIds(null, $min_state))) {
          $this->response->notFound();
        } // if
        $this->wireframe->breadcrumbs->add('user', $this->active_user->getName(), $this->active_user->getViewUrl());

        if($this->active_user->getId() == $this->logged_user->getId()) {
          $this->wireframe->setCurrentMenuItem('profile');
        } // if
      } else {
        $this->active_user = Users::getUserInstance();
      } // if

      $this->response->assign('active_user', $this->active_user);

      if($this->getControllerName() == 'users') {
        if($this->api_client_subscriptions_delegate instanceof ApiClientSubscriptionsController) {
          $this->api_client_subscriptions_delegate->__setProperties(array(
            'active_object' => &$this->active_user,
          ));
        } // if

        if($this->state_delegate instanceof StateController) {
          $this->state_delegate->__setProperties(array(
            'active_object' => &$this->active_user,
          ));
        } // if
  
        if($this->avatar_delegate instanceof UserAvatarController) {
          $this->avatar_delegate->__setProperties(array(
            'active_object' => &$this->active_user
          ));
        } // if
        
        if($this->homescreen_delegate instanceof HomescreenController) {
          $this->homescreen_delegate->__setProperties(array(
            'active_object' => &$this->active_user
          ));
        } // if

        if($this->activity_logs_delegate instanceof ActivityLogsController) {
          $this->activity_logs_delegate->__setProperties(array(
            'show_activities_by' => &$this->active_user
          ));
        } // if
        
        if($this->reminders_delegate instanceof UserRemindersController) {
          $this->reminders_delegate->__setProperties(array(
            'active_object' => &$this->active_user
          ));
        } // if
      } // if
    } // __before

    /**
     * Needed for user permalink
     */
    function index() {
      if($this->request->isApiCall()) {
        $this->response->respondWithData($this->active_company->getUsers(), array(
          'as' => 'users',
        ));
      } else {
        PeopleController::index();
        $this->setView(get_view_path('index', 'people', SYSTEM_MODULE));
      } // if
    } // index
    
    /**
     * Show user profile page
     */
    function view() {
      if($this->active_user->isLoaded()) {
        if($this->active_user->canView($this->logged_user)) {
          //View personality type 
          $this->response->assign(array(
            'personality_type' => $this->active_user->getPersonalityType(),
          ));

          // Phone user
          if($this->request->isPhone()) {
            $this->wireframe->setPageObject($this->active_user, $this->logged_user);
            
            if($this->active_user->canEdit($this->logged_user)) {
              $this->wireframe->actions->add('edit', lang('Edit'), $this->active_user->getEditProfileUrl(), array(
                'icon' => AngieApplication::getImageUrl('layout/buttons/edit.png', SYSTEM_MODULE, AngieApplication::INTERFACE_PHONE),
                'primary' => true
              ));
            } // if
            $this->wireframe->actions->remove(array('edit_profile', 'edit_settings', 'desktop_set', 'export_vcard', 'add_to_projects', 'send_welcome_message', 'people_company_user_login_as', 'api_subscriptions', 'calendar', 'homescreen'));
            
            if($this->logged_user->is($this->active_user) || $this->logged_user->isProjectManager()) {
              $active_projects = Projects::findActiveByUser($this->active_user);
            } else {
              $active_projects = Projects::findCommonProjects($this->logged_user, $this->active_user, "completed_on = NULL");
            } // if

            $this->response->assign(array(
              'active_projects' => $active_projects,
              'completed_projects_url' => Router::assemble('people_company_user_projects_archive', array('company_id' => $this->active_company->getId(), 'user_id' => $this->active_user->getId())),
              'can_view_activities' => $this->active_user->canViewActivities($this->logged_user)
            ));

          // Tablet user
          } elseif($this->request->isTablet()) {
            throw new NotImplementedError(__METHOD__);

          // Regular user
          } elseif($this->request->isWebBrowser()) {
            $this->wireframe->print->enable();

            if ($this->request->isSingleCall() || $this->request->isQuickViewCall()) {
              if(Users::canAdd($this->logged_user, $this->active_company)) {
                $this->wireframe->actions->add('add_user', lang('New User'), $this->active_company->getAddUserUrl(), array(
                  'onclick' => new FlyoutFormCallback('user_created'),
                  'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
                ));
              } // if
              $this->wireframe->setPageObject($this->active_user, $this->logged_user);
              $this->render();
            } else {
              $this->__forward('index'); 
            } // if
            
          // print
          } else if ($this->request->isPrintCall()) {

            $this->wireframe->setPageObject($this->active_user, $this->logged_user);

            if ($this->logged_user->getId() == $this->active_user->getId() || $this->logged_user->isProjectManager()) {
              $projects = Projects::findByUser($this->active_user, false, DB::prepare("state >= ?", STATE_VISIBLE));
            } else {
              $projects = Projects::findCommonProjects($this->logged_user, $this->active_user, DB::prepare("state >= ?", STATE_VISIBLE));
            } // if
            
            $this->response->assign(array(
              'activity_logs' => ActivityLogs::findRecentBy($this->logged_user, $this->active_user),
              'user_projects' => $projects
            ));
          } else {
            $this->response->respondWithData($this->active_user, array(
              'as' => 'user', 
              'detailed' => true,
            ));
          } // if
          
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // view
    
    /**
     * Create new user
     */
    function add() {
      if($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted()) || $this->request->isMobileDevice()) {
        if(Users::canAdd($this->logged_user, $this->active_company)) {
          $user_data = $this->request->post('user', array(
            'auto_assign' => false,
            'rep_site_domain' => ConfigOptions::getValue('rep_site_domain'),
          ));

         // $config = ConfigOptions::getValue('who_can_view_private_url');

          $this->response->assign('user_data', $user_data);
          
          if($this->request->isSubmitted(true, $this->response)) {

            try {
              if(AngieApplication::isOnDemand()) {
                if(!OnDemand::canAddUsersBasedOnCurrentPlan($user_data['type'])) {
                  throw new Error(OnDemand::ERROR_USERS_LIMITATION_REACHED);
                }//if
              } //if

              DB::beginWork('Adding user @ ' . __CLASS__);
              
              // Validate password
              if($this->request->isApiCall() || array_var($user_data, 'specify_password')) {
                $errors = new ValidationErrors();

                $password = array_var($user_data, 'password');
                $password_a = array_var($user_data, 'password_a');

                if(strlen(trim($password)) < 3) {
                  $errors->addError(lang('Password has to be at least 3 letters long'), 'password');
                } else {
                  if($password != $password_a) {
                    $errors->addError(lang('Passwords Mismatch'), 'password_a');
                  } // if
                } // if

                if($errors->hasErrors()) {
                  throw $errors;
                } // if
              } else {
                $password = Authentication::getPasswordPolicy()->generatePassword();
              } // if

              $instance_class = Users::getDefaultUserClass();
              $custom_permissions = null;

              if(isset($user_data['type'])) {
                if($this->logged_user->isPeopleManager()) {
                  $instance_class = $user_data['type'];
                } // if

                unset($user_data['type']);

                $custom_permissions = array_var($user_data, 'custom_permissions', null, true);
              } // if

              $this->active_user = Users::getUserInstance($instance_class, true);

              $this->active_user->setAttributes($user_data);
              $this->active_user->setPassword($password);
              $this->active_user->setCompany($this->active_company);
              $this->active_user->setState(STATE_VISIBLE);

              if($this->logged_user->isPeopleManager()) {
                if(is_foreachable($custom_permissions)) {
                  $this->active_user->setSystemPermissions($custom_permissions);
                } // if

                $this->active_user->setAutoAssignData(
                  (boolean) array_var($user_data, 'auto_assign'),
                  (integer) array_var($user_data, 'auto_assign_role_id'),
                  array_var($user_data, 'auto_assign_permissions')
                );
              } // if

              if(array_var($user_data, 'send_welcome_message')) {
                $this->active_user->setInvitedOn(new DateTimeValue());
              } // if

              $this->active_user->save();

              $send_welcome_message = array_var($user_data, 'send_welcome_message');

              if($send_welcome_message) {
                $welcome_message = trim(array_var($user_data, 'welcome_message'));
                if($welcome_message) {
                  ConfigOptions::setValueFor('welcome_message', $this->active_user, $welcome_message);
                } // if

                AngieApplication::notifications()
                  ->notifyAbout(AUTHENTICATION_FRAMEWORK_INJECT_INTO . '/welcome', $this->active_user, $this->logged_user)
                  ->setPassword($password)
                  ->setWelcomeMessage($welcome_message)
                  ->sendToUsers($this->active_user);
              } // if

              $title = trim(array_var($user_data, 'title'));
              if($title) {
                ConfigOptions::setValueFor('title', $this->active_user, $title);
              } // if
              
              DB::commit('User added @ ' . __CLASS__);

              AngieApplication::cache()->removeByModel(Users::getModelName(true));

              if(AngieApplication::behaviour()->isTrackingEnabled()) {
                $extra_event_tags = array(Inflector::underscore(get_class($this->active_user)));

                if($send_welcome_message) {
                  $extra_event_tags[] = 'email_sent';
                } // if

                AngieApplication::behaviour()->recordFulfilment($this->request->post('_intent_id'), $extra_event_tags, null, function() use ($extra_event_tags) {
                  return array('user_created', $extra_event_tags);
                });
              } // if
              
              if($this->request->isPageCall()) {
                $this->response->redirectToUrl($this->active_user->getViewUrl());
              } else {
                $this->response->respondWithData($this->active_user, array(
                  'as' => 'user',
                  'detailed' => true,
                ));
              } // if
            } catch(Exception $e) {
              DB::rollback('Failed to add user @ ' . __CLASS__);
              
              AngieApplication::revertCsfrProtectionCode();

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
        $this->response->badRequest();
      } // if
    } // add
    
    /**
     * API method
     */
    function edit() {
      if($this->request->isApiCall() && $this->request->isSubmitted()) {
        if($this->active_user->isLoaded()) {
          if($this->active_user->canEdit($this->logged_user)) {
            $config_options = array('title', 'phone_work', 'phone_mobile', 'im_type', 'im_value', 'format_date', 'format_time', 'time_timezone', 'time_dst', 'time_first_week_day', 'language');
            
            $user_data = $this->request->post('user');
            
            // Unset fields user cannot change if he is not people manager
            if(!$this->logged_user->isPeopleManager()) {
              if(isset($user_data['company_id'])) {
                unset($user_data['company_id']);
              } // if
            } // if

            if(isset($user_data['type'])) {
              unset($user_data['type']);
            } // if
            
            try {
              DB::beginWork('Updating user @ ' . __CLASS__);

              $this->active_user->setAttributes($user_data);
              $this->active_user->save();

              if(AngieApplication::isOnDemand() && OnDemand::isAccountOwner($this->active_user)) {
                //if is on demand and if user is account owner
                $options = array(
                  'i' => ON_DEMAND_INSTANCE_NAME,
                  'o' => $this->active_user->getDisplayName() . ' ' . '<' . $this->active_user->getEmail() . '>'
                );
                OnDemand::executeViaCLI('update_owner', $options);
              } //if

              foreach($config_options as $config_option) {
                if(!array_key_exists($config_option, $user_data)) {
                  continue;
                } // if

                if($config_option == 'time_dst') {
                  $value = (boolean) array_var($user_data, $config_option);
                } elseif($config_option == 'time_timezone' || $config_option == 'time_first_week_day ') {
                  $value = (integer) array_var($user_data, $config_option);
                } else {
                  $value = trim(array_var($user_data, $config_option));
                } // if

                if($value === '') {
                  ConfigOptions::removeValuesFor($this->active_user, $config_option);
                } else {
                  ConfigOptions::setValueFor($config_option, $this->active_user, $value);
                } // if
              } // foreach

              DB::commit('User updated @ ' . __CLASS__);

              $this->response->respondWithData($this->active_user, array(
                'as' => 'user',
                'detailed' => true,
              ));
            } catch(Exception $e) {
              DB::rollback('Failed to update user @ ' . __CLASS__);
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
    } // edit
    
    /**
     * Update user profile
     */
    function edit_profile() {
      if($this->request->isApiCall() || $this->request->isAsyncCall() || $this->request->isMobileDevice()) {
        if($this->active_user->isLoaded()) {
          if($this->active_user->canEdit($this->logged_user)) {
            $config_options = array('title', 'phone_work', 'phone_mobile', 'im_type', 'im_value');
      
            $user_data = $this->request->post('user', array_merge(array(
              'first_name' => $this->active_user->getFirstName(),
              'last_name'  => $this->active_user->getLastName(),
              'email'      => $this->active_user->getEmail(),
            ), ConfigOptions::getValueFor($config_options, $this->active_user)));
            
            $this->response->assign(array(
              'user_data' => $user_data,
              'additional_email_addresses' => $this->active_user->getAdditionalEmailAddresses(),
            ));
      
            if($this->request->isSubmitted(true, $this->response)) {
              try {
                DB::beginWork('Updating user profile @ ' . __CLASS__);

                if(isset($user_data['company_id'])) {
                  unset($user_data['company_id']);
                } // if

                if(isset($user_data['type'])) {
                  unset($user_data['type']);
                } // if

                // people who cannot change admin's password also cannot change their e-mail
                if (!$this->active_user->canChangePassword($this->logged_user)) {
                  $user_data['email'] = $this->active_user->getEmail();
                } // if

                // Skip mobile devices for the time being
                if(!$this->request->isMobileDevice()) {

                  // Get additional email addresses
                  $additional_addresses = array_var($user_data, 'additional_email_addresses', null, true);
                  if($additional_addresses && is_foreachable($additional_addresses)) {
                    foreach($additional_addresses as $additional_address) {
                      if($additional_address && !is_valid_email($additional_address)) {
                        throw new ValidationErrors(array(
                          'additional_email_addresses' => lang("':address' is not a valid email address", array(
                            'address' => $additional_address,
                          )),
                        ));
                      } // if
                    } // foreach
                  } // if

                } // if

                $this->active_user->setAttributes($user_data);

                $update_owner = is_foreachable(array_intersect(array('first_name', 'last_name', 'email'), $this->active_user->getModifiedFields()));

                $this->active_user->save();

                // Skip mobile devices for the time being
                if(!$this->request->isMobileDevice()) {
                  if($additional_addresses && is_foreachable($additional_addresses)) {
                    $this->active_user->setAdditionalEmailAddresses($additional_addresses);
                  } else {
                    $this->active_user->setAdditionalEmailAddresses(null);
                  } // if
                } // if


	              $old_field_values = ConfigOptions::getValueFor($config_options, $this->active_user);
	              if (is_foreachable($old_field_values)) {
		              $modified_fields = array();
		              foreach ($old_field_values as $field => $old_value) {
			              $new_value = array_var($user_data, $field);
			              if ($new_value != $old_value) {
				              $modified_fields[$field] = array($old_value, $new_value);
			              } // if
		              } // foreach
		              $this->active_user->history()->alsoTrackFields($config_options);
		              $this->active_user->history()->commitModifications($modified_fields, $this->logged_user);
	              } // if

                foreach($config_options as $config_option) {
                  $value = trim(array_var($user_data, $config_option));

                  if($value === '') {
                    ConfigOptions::removeValuesFor($this->active_user, $config_option);
                  } else {
                    ConfigOptions::setValueFor($config_option, $this->active_user, $value);
                  } // if
                } // foreach
                
                DB::commit('User profile updated @ ' . __CLASS__);

                if ($update_owner) {
                  $user = Users::findById($this->active_user->getId()); // caching issue with $this->active_user
                  if(AngieApplication::isOnDemand() && OnDemand::isAccountOwner($user)) {
                    //if is on demand and if user is account owner
                    $options = array(
                      'i' => ON_DEMAND_INSTANCE_NAME,
                      'o' => $user->getDisplayName() . ' ' . '<' . $user->getEmail() . '>'
                    );
                    OnDemand::executeViaCLI('update_owner', $options);
                  } //if
                } // if
                
                if($this->request->isPageCall()) {
                  $this->response->redirectToUrl($this->active_user->getViewUrl());
                } else {
                  $this->response->respondWithData($this->active_user, array(
                    'as' => 'user',
                    'detailed' => true,
                  ));
                } // if
              } catch(Exception $e) {
                DB::rollback('Failed to update user profile @ ' . __CLASS__);
                
                AngieApplication::revertCsfrProtectionCode();

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
    } // edit_profile
    
    /**
     * Show and process edit settings page
     */
    function edit_settings() {
      if($this->request->isApiCall() || $this->request->isAsyncCall()) {
        if($this->active_user->isLoaded()) {
          if($this->active_user->canEdit($this->logged_user)) {
            $config_options = array('format_date', 'format_time', 'time_timezone', 'time_dst', 'time_first_week_day', 'language', 'job_type_id', 'notifications_show_indicators', 'default_homescreen_tab_id');
            
            $user_data = $this->request->post('user');
            if(!is_array($user_data)) {
              $user_data = array_merge(array(
                'auto_assign' => $this->active_user->getAutoAssign(),
                'auto_assign_role_id' => $this->active_user->getAutoAssignRoleId(),
                'auto_assign_permissions' => $this->active_user->getAutoAssignPermissions(),
                'morning_paper_enabled' => ConfigOptions::getValueFor('morning_paper_enabled', $this->active_user),
                'morning_paper_include_all_projects' => ConfigOptions::getValueFor('morning_paper_include_all_projects', $this->active_user),
              ), ConfigOptions::getValueFor($config_options, $this->active_user));
              
              if(!ConfigOptions::hasValueFor('language', $this->active_user)) {
                $user_data['language'] = null; 
              } // if

              if(!ConfigOptions::hasValueFor('time_timezone', $this->active_user)) {
                $user_data['time_timezone'] = null;
              } // if

              if(!ConfigOptions::hasValueFor('time_dst', $this->active_user)) {
                $user_data['time_dst'] = null;
              } // if

              if(!ConfigOptions::hasValueFor('format_date', $this->active_user)) {
                $user_data['format_date'] = null;
              } // if

              if(!ConfigOptions::hasValueFor('format_time', $this->active_user)) {
                $user_data['format_time'] = null;
              } // if

              $user_data['notification_channels_settings'] = array();

              foreach(AngieApplication::notifications()->getChannels() as $channel) {
                if($channel instanceof WebInterfaceNotificationChannel || !$channel->canOverrideDefaultStatus($this->active_user)) {
                  continue;
                } // if

                $user_data['notification_channels_settings'][$channel->getShortName()] = ConfigOptions::hasValueFor($channel->getShortName() . '_notifications_enabled', $this->active_user) ? $channel->isEnabledFor($this->active_user) : null;
              } // foreach

              if(!ConfigOptions::hasValueFor('default_homescreen_tab_id', $this->active_user)) {
                $user_data['default_homescreen_tab_id'] = null;
              } // if
            } // if
            
            $this->response->assign(array(
              'user_data' => $user_data,
              'default_dst_value' => (boolean) ConfigOptions::getValue('time_dst'),
              'can_override_notification_settings' => AngieApplication::notifications()->canOverrideDefaultSettings($this->active_user),
            ));
      
            if($this->request->isSubmitted(true, $this->response)) {
              try {
                DB::beginWork('Updating user settings @ ' . __CLASS__);

                if(isset($user_data['company_id'])) {
                  unset($user_data['company_id']);
                } // if

                if(isset($user_data['type'])) {
                  unset($user_data['type']);
                } // if

              	$original_language = ConfigOptions::getValueFor('language', $this->active_user);

                $this->active_user->setAttributes($user_data);

                if($this->active_user->canChangeRole($this->logged_user) || (AngieApplication::isOnDemand() && OnDemand::isAccountOwner($this->logged_user) && $this->active_user->getId() == $this->logged_user->getId())) {
                  $this->active_user->setAutoAssignData(
                    (boolean) array_var($user_data, 'auto_assign'),
                    (integer) array_var($user_data, 'auto_assign_role_id'),
                    array_var($user_data, 'auto_assign_permissions')
                  );
                } // if

                $this->active_user->save();

	              $old_field_values = ConfigOptions::getValueFor($config_options, $this->active_user);
	              if (is_foreachable($old_field_values)) {
		              $modified_fields = array();
		              foreach ($old_field_values as $field => $old_value) {
			              $new_value = array_var($user_data, $field);
			              if ($new_value != $old_value) {
				              $modified_fields[$field] = array($old_value, $new_value);
			              } // if
		              } // foreach
		              $this->active_user->history()->alsoTrackFields($config_options);
		              $this->active_user->history()->commitModifications($modified_fields, $this->logged_user);
	              } // if

                if(ConfigOptions::getValue('morning_paper_enabled') && MorningPaper::canReceiveMorningPaper($this->active_user)) {
                  ConfigOptions::setValueFor('morning_paper_enabled', $this->active_user, (boolean) array_var($user_data, 'morning_paper_enabled', true, true));

                  if($this->active_user->isProjectManager()) {
                    ConfigOptions::setValueFor('morning_paper_include_all_projects', $this->active_user, (boolean) array_var($user_data, 'morning_paper_include_all_projects', false, true));
                  } else {
                    ConfigOptions::removeValuesFor($this->active_user, 'morning_paper_include_all_projects');
                  } // if
                } else {
                  ConfigOptions::removeValuesFor($this->active_user, array('morning_paper_enabled', 'morning_paper_include_all_projects'));
                } // if

                foreach($config_options as $config_option) {
                  if($config_option == 'time_dst') {
                    $value = array_var($user_data, $config_option) === '' ? '' : (boolean) array_var($user_data, $config_option);
                  } elseif($config_option == 'time_timezone' || $config_option == 'time_first_week_day' || $config_option == 'notifications_show_indicators') {
                    $value = is_numeric($user_data[$config_option]) ? (integer) array_var($user_data, $config_option) : '';
                  } elseif($config_option == 'job_type_id') {
                    $value = array_var($user_data, $config_option) == 0 ? '' : (integer) array_var($user_data, $config_option);
                  } else {
                    $value = trim(array_var($user_data, $config_option));
                  } // if

                  if($value === '') {
                    ConfigOptions::removeValuesFor($this->active_user, $config_option);
                  } else {
                    ConfigOptions::setValueFor($config_option, $this->active_user, $value);
                  } // if
                } // foreach

                if (array_var($user_data, 'language') != $original_language) {
                  clean_menu_projects_and_quick_add_cache($this->active_user);
                } // if

                if(AngieApplication::notifications()->canOverrideDefaultSettings($this->active_user)) {
                  $notification_settings = array_var($user_data, 'notification_channels_settings');

                  if(empty($notification_settings) || !is_foreachable($notification_settings)) {
                    $notification_settings = array();
                  } // if

                  foreach(AngieApplication::notifications()->getChannels() as $channel) {
                    if($channel instanceof WebInterfaceNotificationChannel || !$channel->canOverrideDefaultStatus($this->active_user)) {
                      continue;
                    } // if

                    $short_name = $channel->getShortName();

                    if(isset($notification_settings[$short_name]) && ($notification_settings[$short_name] === '1' || $notification_settings[$short_name] === '0')) {
                      $value_to_set = $notification_settings[$short_name] === '1';
                    } else {
                      $value_to_set = null;
                    } // if

                    $channel->setEnabledFor($this->active_user, $value_to_set);
                  } // foreach
                } // if

                DB::commit('User settings updated @ ' . __CLASS__);

                $this->active_user->getLanguageId(false); // Force cached language ID refresh before describe() call

                $this->response->respondWithData($this->active_user, array(
                  'as' => 'user',
                  'detailed' => true,
                ));
              } catch(Exception $e) {
                AngieApplication::revertCsfrProtectionCode();

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
    } // edit_settings
    
    /**
     * Update user's company and role information
     */
    function edit_company_and_role() {
      if($this->request->isAsyncCall() || $this->request->isMobileDevice()) {
        if($this->active_user->isLoaded()) {
          if($this->active_user->canChangeRole($this->logged_user)) {
            $user_data = $this->request->post('user', array(
              'company_id' => $this->active_user->getCompanyId(),
              'type' => $this->active_user->getType(),
              'custom_permissions' => $this->active_user->getSystemPermissions(),
              'managed_by_id' => $this->active_user->getManagedById(),
              'personality_type' => $this->active_user->getPersonalityType(),
              'user_id' => $this->active_user->getId(),
              'rep_site_domain' => ConfigOptions::getValue('rep_site_domain'),
              'private_url' => $this->active_user->getPrivateUrl(),
              'private_url_enabled' => $this->active_user->getPrivateUrlEnabled(),
            ));

            $exclude_company_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'companies WHERE state != ? AND id != ?', STATE_VISIBLE, $this->active_user->getCompanyId());

            $this->response->assign(array(
              'user_data' => $user_data,
              'exclude_ids' => $exclude_company_ids
            ));
            
            if($this->request->isSubmitted(true, $this->response)) {
              try {
	              $old_type = $this->active_user->getType();
                if(isset($user_data['type']) && $user_data['type']) {
                  $this->active_user = Users::changeUserType($this->active_user, $user_data['type'], $this->logged_user);
                  clean_menu_projects_and_quick_add_cache($this->active_user);
                } // if

	              $old_custom_permissions = serialize($this->active_user->getSystemPermissions());
                $this->active_user->setSystemPermissions(array_var($user_data, 'custom_permissions', array()));

                if(isset($user_data['company_id']) && $user_data['company_id'] && $this->active_user->getCompanyId() != $user_data['company_id']) {
                  $this->active_user->setCompanyId($user_data['company_id']);
                } // if

                if(isset($user_data['managed_by_id']) && $user_data['managed_by_id'] && $this->active_user->getManagedById() != $user_data['managed_by_id']) {
                  $this->active_user->setManagedById($user_data['managed_by_id']);
                } // if

                if(isset($user_data['personality_type']) && $user_data['personality_type'] && $this->active_user->getPersonalityType() != $user_data['personality_type']) {
                  $this->active_user->setPersonalityType($user_data['personality_type']);
                } // if

                if(isset($user_data['private_url_enabled']) && $user_data['private_url_enabled'] && $this->active_user->getPrivateUrlEnabled() != $user_data['private_url_enabled']) {
                  $this->active_user->setPrivateUrlEnabled($user_data['private_url_enabled']);
                } // if

                $this->active_user->save();

	              $name_of_modified_fields = array('type', 'custom_permissions');
	              $new_type = array_var($user_data, 'type');
	              if ($new_type != $old_type) {
		              $modified_fields['type'] = array($old_type, $new_type);
		              $old_custom_permissions = null;
	              } // if

	              $new_custom_permissions = serialize(array_var($user_data, 'custom_permissions', array()));
	              if ($new_custom_permissions != $old_custom_permissions) {
		              $modified_fields['custom_permissions'] = array($old_custom_permissions, $new_custom_permissions);
	              } // if
	              $this->active_user->history()->alsoTrackFields($name_of_modified_fields);
	              $this->active_user->history()->commitModifications($modified_fields, $this->logged_user);

                AngieApplication::cache()->removeByModel(Users::getModelName(true));
                
                if($this->request->isPageCall()) {
                  $this->response->redirectToUrl($this->active_user->getViewUrl());
                } else {
                  $this->response->respondWithData($this->active_user, array(
                    'as' => 'user',
                    'detailed' => true,
                  ));
                } // if
              } catch(Exception $e) {
                AngieApplication::revertCsfrProtectionCode();
                
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
    } // edit_company_and_role
    
    /**
     * Export vCard
     */
    function export_vcard() {
      if($this->active_user->isLoaded()) {
        if($this->active_user->canView($this->logged_user)) {
          try{
            $this->active_user->toVCard(true, false, $this->active_company);
            die();
          } catch(Exception $e) {
            $this->response->exception($e);
          } // try
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // export_vcard
    
    /**
     * Edit user password
     */
    function edit_password() {
      if($this->request->isAsyncCall() || $this->request->isMobileDevice()) {
        if($this->active_user->isLoaded()) {
          if($this->active_user->canChangePassword($this->logged_user)) {
            $user_data = $this->request->post('user');
            $this->response->assign('user_data', $user_data);
            
            if($this->request->isSubmitted(true, $this->response)) {
              try {
                $errors = new ValidationErrors();

                $password = array_var($user_data, 'password');
                $repeat_password = array_var($user_data, 'repeat_password');

                if(empty($password)) {
                  $errors->addError(lang('Password value is required'), 'password');
                } // if

                if(empty($repeat_password)) {
                  $errors->addError(lang('Repeat Password value is required'), 'repeat_password');
                } // if

                if(!$errors->hasErrors() && ($password !== $repeat_password)) {
                  $errors->addError(lang('Inserted values does not match'));
                } // if

                if($errors->hasErrors()) {
                  throw $errors;
                } // if

	              $password_expired = $this->active_user->isPasswordExpired();
	              /*pre_var_dump($password_expired);
	              exit*/;

	              if ($password_expired) {
		              $this->active_user->history()->alsoRemoveFields(array('password'));
	              } // if

                $this->active_user->setPassword($user_data['password']);
                $this->active_user->save();

	              if ($password_expired) {
		              $field = 'expired_password';
		              $modified_fields = array($field => array($user_data['password'], $user_data['password']));
		              $this->active_user->history()->alsoTrackFields($field);
		              $this->active_user->history()->commitModifications($modified_fields, $this->logged_user);
	              } // if

                if($this->request->isPageCall()) {
                  $this->response->redirectToUrl($this->active_user->getViewUrl());
                } else {
                  $this->response->respondWithData($this->active_user, array(
                    'as' => 'user',
                    'detailed' => true,
                  ));
                } // if
              } catch(Exception $e) {
                AngieApplication::revertCsfrProtectionCode();
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
    } // edit_password
    
    /**
     * Return client company managers
     */
    function users_with_permissions() {
      if($this->request->isAsyncCall() || $this->request->isApiCall()) {
        $permissions = $this->request->get('permissions') ? explode(',', $this->request->get('permissions')) : null;
        $require_all_permissions = (boolean) $this->request->get('require_all_permissions');

        if($permissions) {
          $type_filter = $this->request->get('type_filter') ? explode(',', $this->request->get('type_filter')) : null;

          if($type_filter) {
            $all_users = Users::findByType($type_filter, array('company_id = ?', $this->active_company->getId()));
          } else {
            $all_users = Users::findByCompany($this->active_company);
          } // if

          $users = array();

          if($all_users) {
            foreach($all_users as $user) {
              if($user->isAdministrator()) {
                $users[] = $user;
                continue;
              } // if

              // Require all permissions
              if($require_all_permissions) {
                $has_all_permissions = true;
                foreach($permissions as $permission) {
                  if(!$user->getSystemPermission($permission)) {
                    $has_all_permissions = false;
                    continue;
                  } // if
                } // foreach

                if($has_all_permissions) {
                  $users[] = $user;
                } // if

              // Has any permission
              } else {
                $has_any_permission = false;
                foreach($permissions as $permission) {
                  if($user->getSystemPermission($permission)) {
                    $has_any_permission = true;
                    continue;
                  } // if
                } // foreach

                if($has_any_permission) {
                  $users[] = $user;
                } // if
              } // if
            } // foreach
          } // if

          if(count($users)) {
            $users = Users::sortUsersForSelect($users);
          } else {
            $users = null;
          } // if

          $this->response->respondWithData($users);
        } else {
          $this->response->badRequest();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // users_with_permissions
    
    /**
     * Show company users archive page
     */
    function archive() {
      if($this->request->isAsyncCall()) {
        $this->response->assign('archived_users', Users::findArchivedByCompany($this->active_company));
      } else {
        $this->response->badRequest();
      } // if
    } // archive
    
    /**
     * Delete user
     */
    function delete() {
      if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted()) {
        if($this->active_user->isLoaded()) {
          if($this->active_user->canDelete($this->logged_user)) {
            try {
              $this->active_user->delete();

              AngieApplication::cache()->removeByModel(Users::getModelName(true));

              $this->response->ok();
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
     * Recent activities for selected user
     */
    function recent_activities() {
      if($this->request->isAsyncCall()) {
        if($this->active_user->isLoaded()) {
          if(!$this->active_user->canViewActivities($this->logged_user)) {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // recent_activities
    
    /**
     * Send welcome message
     */
    function send_welcome_message() {
      if($this->request->isAsyncCall()) {
        if($this->active_user->isLoaded()) {
          if($this->active_user->canSendWelcomeMessage($this->logged_user)) {
            $welcome_message_data = $this->request->post('welcome_message', array(
              'message' => ConfigOptions::getValueFor('welcome_message', $this->active_user),
            ));
            $this->response->assign('welcome_message_data', $welcome_message_data);
            
            if($this->request->isSubmitted()) {
              try {
                DB::beginWork('Sending welcome message @ ' . __CLASS__);

                $welcome_message = trim(array_var($welcome_message_data, 'message'));
                if($welcome_message) {
                  ConfigOptions::setValueFor('welcome_message', $this->active_user, $welcome_message);
                } else {
                  ConfigOptions::removeValuesFor($this->active_user, 'welcome_message');
                } // if

                $password = Authentication::getPasswordPolicy()->generatePassword();
                $this->active_user->setPassword($password);
                $this->active_user->setInvitedOn(new DateTimeValue());
      
                $this->active_user->save();

                AngieApplication::notifications()
                  ->notifyAbout(AUTHENTICATION_FRAMEWORK_INJECT_INTO . '/welcome', $this->active_user, $this->logged_user)
                  ->setPassword($password)
                  ->setWelcomeMessage($welcome_message)
                  ->sendToUsers($this->active_user);

                DB::commit('Welcome message sent @ ' . __CLASS__);

                $this->response->respondWithData($this->active_user, array(
                  'as' => 'user',
                  'detailed' => true,
                ));
              } catch(Exception $e) {
                DB::rollback('Failed to send welcome message @ ' . __CLASS__);
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
    } // send_welcome_message

    /**
     * Set user as invited
     */
    function set_as_invited() {
      if($this->request->isAsyncCall()) {
        if($this->active_user->isLoaded()) {
          if($this->active_user->canSetAsInvited($this->logged_user)) {
            if($this->request->isSubmitted()) {
              try {
                DB::beginWork('Setting user as invited @ ' . __CLASS__);

                $this->active_user->setInvitedOn(new DateTimeValue());
                $this->active_user->save();

                DB::commit('User set as invited @ ' . __CLASS__);

                $this->response->respondWithData($this->active_user, array(
                  'as' => 'user',
                  'detailed' => true,
                ));
              } catch(Exception $e) {
                DB::rollback('Failed to set user as invited @ ' . __CLASS__);
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
    } // set_as_invited
    
    /**
     * Login as a selected user
     */
    function login_as() {
      if($this->active_user->isLoaded()) {
        if($this->active_user->canLoginAs($this->logged_user)) {
          if($this->request->isSubmitted()) {
            Authentication::getProvider()->logUserIn($this->active_user);

	          // log login as
	          $this->logged_user->securityLog()->log('login', $this->active_user);

            $this->response->respondWithData($this->active_user, array(
              'as' => 'user', 
            ));
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->notFound();
      } // if
    } // login_as
    
  }
