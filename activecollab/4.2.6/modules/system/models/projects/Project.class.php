<?php

  /**
   * Project class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class Project extends BaseProject implements IRoutingContext, IConfigContext, IUsersContext, IComplete, ILabel, ICategoriesContext, ICategory, IHistory, IState, ISearchItem, IAvatar, ICanBeFavorite, ITracking, IInvoiceBasedOn, IActivityLogs, IObjectContext, ICustomFields, IAccessLog, ICalendarContext {
    
    // Project stauts filters 
    const STATUS_ANY = 'any';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    
    /**
     * List of rich text fields
     *
     * @var array
     */
    protected $rich_text_fields = array('overview');
    
    /**
     * List of protected fields (can't be set using setAttributes() method)
     *
     * @var array
     */
  	protected $protect = array(
  	  'id',
  	  'completed_on',
  	  'completed_by_id',
  	  'completed_by_name',
  	  'completed_by_email',
  	  'created_on',
  	  'created_by_id',
  	  'created_by_name',
  	  'created_by_email',
  	  'open_tasks_count',
  	  'total_tasks_count'
  	);

    /**
     * Return email for mail to project intercaptor
     *
     * @return string
     */
    function getMailToProjectEmail() {
      list($from_email, $from_name) = AngieApplication::mailer()->getFromEmailAndName();
      if(!$from_email) {
        $from_email = ADMIN_EMAIL;
      } //if
      $email = explode('@',$from_email);
      return $email[0] . MailToProjectInterceptor::M2P_DELIMITER . $this->getMailToProjectCode() . '@' . $email[1];
    } //getMailToProjectEmail

    /**
     * Return verbose type name
     *
     * @param boolean $lowercase
     * @param Language $language
     * @return string
     */
    function getVerboseType($lowercase = false, $language = null) {
      return $lowercase ? lang('project', null, true, $language) : lang('Project', null, true, $language);
    } // getVerboseType
  	
  	/**
  	 * Return template that's been used to create this project
  	 * 
  	 * @return ProjectTemplate
  	 */
  	function getTemplate() {
      return DataObjectPool::get('ProjectTemplate', $this->getTemplateId());
  	} // getTemplate
  	
  	/**
  	 * Set template
  	 * 
  	 * @param ProjectTemplate $value
  	 * @param boolean $save
  	 * @return Project
     * @throws InvalidInstanceError
  	 */
    function setTemplate($value, $save = false) {
  	  if($value instanceof ProjectTemplate) {
  	    $this->setTemplateId($value->getId());
  	  } elseif($value === null) {
  	    $this->setTemplateId(null);
  	  } else {
  	    throw new InvalidInstanceError('value', $value, 'Project');
  	  } // if

      if($save) {
        $this->save();
      } // if

      return $this->getTemplate();
  	} // setTemplate
    
    /**
     * Cached based on instance
     *
     * @var ApplicationObject
     */
    protected $based_on = false;
    
    /**
     * Return parent project that this object is based on
     * 
     * @return IProjectBasedOn
     */
    function getBasedOn() {
      if($this->based_on === false) {
        $based_on_class = $this->getBasedOnType();
        $based_on_id = $this->getBasedOnId();

        if (strtolower($based_on_class) == "quote" && !AngieApplication::isModuleLoaded('invoicing')) {
          $this->based_on = null;
        } else {
          if($based_on_class && $based_on_id) {
            $this->based_on = new $based_on_class($based_on_id);

            if(!($this->based_on instanceof IProjectBasedOn)) {
              $this->based_on = null;
            } // if
          } else {
            $this->based_on = null;
          } // if
        } // if

      } // if

      return $this->based_on;
    } // getBasedOn
    
    /**
     * Set project based on value
     * 
     * @param IProjectBasedOn $value
     * @param boolean $save
     * @return IProjectBasedOn|null
     * @throws InvalidInstanceError
     */
    function setBasedOn($value, $save = false) {
      if($value instanceof IProjectBasedOn) {
        $this->setBasedOnType(get_class($value));
        $this->setBasedOnId($value->getId());
      } elseif($value === null) {
        $this->setBasedOnType(null);
        $this->setBasedOnId(null);
      } else {
        throw new InvalidInstanceError('value', $value, 'ApplicationObject');
      } // if
      
      $this->based_on = $value;
      
      if($save) {
        $this->save();
      } // if
      
      return $this->based_on;
    } // setBasedOn
    
    /**
     * Project leader
     *
     * @var User
     */
    private $leader = false;
    
    /**
     * Return project leader
     *
     * @return User
     */
    function getLeader() {
      if($this->leader === false) {
        if($this->getLeaderId()) {
          $this->leader = Users::findById($this->getLeaderId());
        } // if
        
        if(!($this->leader instanceof User)) {
          if($this->getLeaderEmail()) {
            $this->leader = new AnonymousUser($this->getLeaderName(), $this->getLeaderEmail());
          } else {
            $this->leader = null;
          } // if
        } // if
      } // if
      return $this->leader;
    } // getLeader
    
    /**
     * Set leader data
     *
     * @param IUser $leader
     * @return User
     * @throws InvalidInstanceError
     */
    function setLeader(IUser $leader) {
      if($leader instanceof User) {
        $this->setLeaderId($leader->getId());
        $this->setLeaderName($leader->getDisplayName());
        $this->setLeaderEmail($leader->getEmail());
      } else {
        throw new InvalidInstanceError('$leader', $leader, 'User', '$leader is expected to be an instance of User or AnonymousUser class');
      } // if
      $this->leader = false;
      return $leader;
    } // setLeader
    
    /**
     * Returns true if $user is leader of this particular project
     *
     * @param User $user
     * @return boolean
     */
    function isLeader(User $user) {
      return $this->getLeaderId() == $user->getId();
    } // isLeader
    
    /**
     * Return company instance
     *
     * @return Company
     */
    function getCompany() {
      $company_id = $this->getCompanyId();
      
      if($company_id && DataObjectPool::get('Company', $company_id) instanceof Company) {
        return DataObjectPool::get('Company', $company_id);
      } else {
        return Companies::findOwnerCompany();
      } // if
    } // getCompany
    
    /**
     * Return project currency
     * 
     * @return Currency
     */
    function getCurrency() {
      $currency_id = $this->getCurrencyId();
      
      if($currency_id && DataObjectPool::get('Currency', $currency_id) instanceof Currency) {
        return DataObjectPool::get('Currency', $currency_id);
      } else {
        return Currencies::getDefault();;
      } // if
    } // getCurrency
    
    /**
     * Set currency value
     * 
     * $currency can be Currency instance, or NULL. In case of NULL, this 
     * project will use default currency
     * 
     * @param Currency $currency
     * @param boolean $save
     * @return Currency
     * @return int
     * @throws InvalidInstanceError
     */
    function setCurrency($currency, $save = false) {
      if($currency instanceof Currency) {
        $this->setCurrencyId($currency->getId());
      } elseif($currency === null) {
        $this->setCurrencyId(null);
      } else {
        throw new InvalidInstanceError('currency', $currency, 'Currency');
      } // if
      
      if($save) {
        $this->save();
      } // if
      
      return $this->getCurrencyId();
    } // setCurrency
    
    /**
     * Cached cost so far value
     *
     * @var float
     */
    private $cost_so_far = false;
    
    /**
     * Return cost so far
     * 
     * @param IUser $user
     * @return float
     */
    function getCostSoFar(IUser $user) {
      if($this->cost_so_far === false) {
        $this->cost_so_far = TrackingObjects::sumCostByProject($user, $this);
      } // if
      
      return $this->cost_so_far;
    } // getCostSoFar
    
    /**
     * Return cost so far in percent
     * 
     * @param IUser $user
     * @return float
     */
    function getCostSoFarInPercent(IUser $user) {
      if($this->getBudget() > 0) {
        $cost_so_far = $this->getCostSoFar($user);
        
        if($cost_so_far > 0) {
          return ceil(($cost_so_far * 100) / $this->getBudget());
        } else {
          return 0;
        } // if
      } else {
        return null;
      } // if
    } // getCostSoFarInPercent
    
    /**
     * Return verbose status
     *
     * @return string
     */
    function getVerboseStatus() {
      return $this->getCompletedOn() instanceof DateValue ? lang('Completed') : lang('Active');
    } // getVerboseStatus
    
    /**
  	 * Set attributes
  	 * 
  	 * @param array $attributes
  	 */
  	function setAttributes($attributes) {
  		if(isset($attributes['budget'])) {
  			$attributes['budget'] = moneyval($attributes['budget']);
  		} // if
  		
  		parent::setAttributes($attributes);
  	} // setAttributes
    
    /**
     * Prepare list of options that $user can use
     *
     * @param IUser $user
     * @param NamedList $options
     * @param string $interface
     * @return NamedList
     */
    protected function prepareOptionsFor(IUser $user, NamedList $options, $interface = AngieApplication::INTERFACE_DEFAULT) {
      
      // Default interface
      if($interface == AngieApplication::INTERFACE_DEFAULT) {
        parent::prepareOptionsFor($user, $options, $interface);
        
        if($this->canEdit($user)) {
          $options->addAfter('edit_settings', array(
            'url' => $this->getSettingsUrl(),
            'text' => lang('Change Settings'),
            'onclick' => new FlyoutFormCallback('project_settings_updated', array(
              'success_message' => lang('Settings updated'),
            )),
          ), 'edit');
        } // if
        
        if(AngieApplication::isModuleLoaded('invoicing') && $user->isFinancialManager() && $this->tracking()->hasBillable($user,true)) {
          $options->add('make_invoice', array(
            'url' => $this->invoice()->getUrl(),
            'text' => lang('Create Invoice'),
            'onclick' => new FlyoutFormCallback('create_invoice_from_project', array(
              'focus_first_field' => false,
            )),
          ));
        } // if

	      if (ProjectTemplates::canAdd($user)) {
		      $options->add('convert_to_a_template', array(
			      'url' => $this->getConvertToATemplateUrl(),
			      'text' => lang('Convert to a Template'),
			      'onclick' => new FlyoutFormCallback('template_created', array(
				      'width' => 350,
				      'success_message' => lang('Project converted to a template')
			      ))
		      ));
	      } // if
        
      // Mobile devices
      } elseif($interface == AngieApplication::INTERFACE_PHONE || $interface == AngieApplication::INTERFACE_TABLET) {
        $this->complete()->prepareObjectOptions($options, $user, $interface);
        $this->state()->prepareObjectOptions($options, $user, $interface);
      } // if
      
      return $options;
    } // prepareOptionsFor
    
    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
    	$result = parent::describe($user, $detailed, $for_interface);
      
      $result['icon'] = $this->avatar()->getUrl(IAvatarImplementation::SIZE_SMALL);
      $result['overview'] = $this->getOverview();
      $result['overview_formatted'] = HTML::toRichText($this->getOverview());
      $result['currency'] = $this->getCurrency();
      $result['currency_code'] = $this->getCurrency() instanceof Currency ? $this->getCurrency()->getCode() : null;
      $result['based_on'] = $this->getBasedOn();
      $result['status_verbose'] = $this->getVerboseStatus();
      
      $result['progress'] = lang(':completed of :total task completed (:percentage % done)', array('completed' => $this->getCompletedTaskCount(), 'total' => $this->getTotalTasksCount(), 'percentage' => $this->getPercentsDone()));

	    // get start_on and due_on
	    $dates = DB::executeFirstRow("SELECT MIN(date_field_1) as start_on, MAX(due_on) as due_on FROM " . TABLE_PREFIX ."project_objects WHERE project_id = ? AND type = ? AND state = ? GROUP BY project_id", $this->getId(), 'Milestone', STATE_VISIBLE);
	    $start_on = $dates['start_on'];
	    $due_on = $dates['due_on'];
	    $result['start_on'] = $start_on ? new DateValue(strtotime($start_on)) : null;
	    $result['due_on'] = $due_on ? new DateValue(strtotime($due_on)) : null;

	    // get percents done
	    $result['percents_done'] = $this->getPercentsDone();

	    // additional routes
	    $result['urls']['reschedule'] = Router::assemble('project_reschedule', array('project_slug' => $this->getSlug()));
	    $result['urls']['milestones'] = Router::assemble('project_milestones', array('project_slug' => $this->getSlug()));
      
      if($user instanceof User && $user->canSeeProjectBudgets()) {
        $result['budget'] = $this->getBudget();
        $result['permissions']['can_see_budget'] = true;
      } else {
        $result['permissions']['can_see_budget'] = false;
      } // if
      
      $this->category()->describe($user, false, $for_interface, $result);
      
      if($detailed) {
      	$result['cost_summarized'] = AngieApplication::isModuleLoaded('tracking') ? TrackingObjects::sumCostByProject($user, $this) : 0;
      	
        $result['leader'] = $this->getLeader() instanceof IUser ? $this->getLeader()->describe($user, false, $for_interface) : null;
        $result['company'] = $this->getCompany() instanceof Company ? $this->getCompany()->describe($user, false, $for_interface) : null;
        
        // Progress
        list($total_assignments, $open_assignments) = ProjectProgress::getProjectProgress($this);
        
        $result['total_assignments'] = $total_assignments;
        $result['open_assignments'] = $open_assignments;
        $result['label_id'] = $this->getLabelId();
        
        // Permissions
        $logged_user_permissions = array(
          'role' => null,
          'permissions' => array(),
        );
        
        $permissions = array_keys(ProjectRoles::getPermissions());
        if($user->isAdministrator()) {
          $logged_user_permissions['role'] = 'administrator';
        } elseif($user->isProjectManager()) {
          $logged_user_permissions['role'] = 'project-manager';
        } elseif($this->isLeader($user)) {
          $logged_user_permissions['role'] = 'project-leader';
        } // if
        
        if($logged_user_permissions['role'] === null) {
          $project_role = $user->projects()->getRole($this);
          if($project_role instanceof ProjectRole) {
            $logged_user_permissions['role'] = $project_role->getId();
          } else {
            $logged_user_permissions['role'] = 'custom';
          } // if
          
          foreach($permissions as $permission) {
            $logged_user_permissions['permissions'][$permission] = (integer) $user->projects()->getPermission($permission, $this);
          } // foreach
        } else {
          foreach($permissions as $permission) {
            $logged_user_permissions['permissions'][$permission] = ProjectRole::PERMISSION_MANAGE;
          } // foreach
        } // if
        
        $result['logged_user_permissions'] = $logged_user_permissions;
      } else {
        $result['leader_id'] = $this->getLeaderId();
        $result['company_id'] = $this->getCompanyId();
      } // if
      
      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      $result = parent::describeForApi($user, $detailed);

      $result['slug'] = $this->getSlug();
      $result['company_id'] = $this->getCompanyId();
      $result['leader_id'] = $this->getLeaderId();

      if($detailed) {
        $result['currency'] = $this->getCurrency() instanceof Currency ? $this->getCurrency()->describeForApi($user) : null;
      } else {
        $result['currency_id'] = $this->getCurrencyId();
      } // if

      if($user instanceof User && $user->canSeeProjectBudgets()) {
        $result['budget'] = $this->getBudget();

        if($detailed) {
          $result['permissions']['can_see_budget'] = true;
        } // if
      } else {
        if($detailed) {
          $result['permissions']['can_see_budget'] = false;
        } // if
      } // if

      if($detailed) {
        $result['overview'] = $this->getOverview();
        $result['overview_formatted'] = HTML::toRichText($this->getOverview());
        $result['based_on'] = $this->getBasedOn() instanceof ApplicationObject ? $this->getBasedOn()->describeForApi($user) : null;

        list($total_tasks, $open_tasks) = ProjectProgress::getQuickProgress($this->getId());

        // Added on customer's request, and for compatibility with activeCollab 2 API responses
        $result['progress'] = array(
          'percent_done' => $this->getPercentsDone(),
          'total_tasks' => $total_tasks,
          'open_tasks' => $open_tasks,
        );
      } // if

      if($detailed) {
        $result['cost_summarized'] = AngieApplication::isModuleLoaded('tracking') ? TrackingObjects::sumCostByProject($user, $this) : 0;

        $result['leader'] = $this->getLeader() instanceof IUser ? $this->getLeader()->describeForApi($user) : null;
        $result['company'] = $this->getCompany() instanceof Company ? $this->getCompany()->describeForApi($user) : null;

        // Permissions
        $logged_user_permissions = array(
          'role' => null,
          'permissions' => array(),
        );

        $permissions = array_keys(ProjectRoles::getPermissions());
        if($user->isAdministrator()) {
          $logged_user_permissions['role'] = 'administrator';
        } elseif($user->isProjectManager()) {
          $logged_user_permissions['role'] = 'project-manager';
        } elseif($this->isLeader($user)) {
          $logged_user_permissions['role'] = 'project-leader';
        } // if

        if($logged_user_permissions['role'] === null) {
          $project_role = $user->projects()->getRole($this);
          if($project_role instanceof ProjectRole) {
            $logged_user_permissions['role'] = $project_role->getId();
          } else {
            $logged_user_permissions['role'] = 'custom';
          } // if

          foreach($permissions as $permission) {
            $logged_user_permissions['permissions'][$permission] = (integer) $user->projects()->getPermission($permission, $this);
          } // foreach
        } else {
          foreach($permissions as $permission) {
            $logged_user_permissions['permissions'][$permission] = ProjectRole::PERMISSION_MANAGE;
          } // foreach
        } // if

        $result['logged_user_permissions'] = $logged_user_permissions;
      } else {
        $result['is_member'] = $this->users()->isMember($user);
      } // if

      return $result;
    } // describeForApi

    /**
     * Disable describe cache
     *
     * @return bool
     */
    function disableDescribeCache() {
      return true;
    } // disableDescribeCache

    /**
     * Describe this project for export
     *
     * @param User $user
     * @param string $output_file
     * @return array
     * @throws Error
     */
    function exportToFile(User $user, $output_file) {
      if(!($output_handle = fopen($output_file, 'w+'))) {
        throw new Error(lang('Failed to write JSON file to :file_path', array('file_path' => $output_file)));
      } // if

      $project = array();

      $project['id'] = $this->getId();
      $project['name'] = $this->getName();
      $project['overview'] = $this->getOverview();
      $project['icon'] = base64_encode(file_get_contents($this->avatar()->getUrl(IProjectAvatarImplementation::SIZE_PHOTO)));
      $project['permalink'] = $this->getViewUrl();
      $project['category_id'] = $this->getCategoryId();
      $project['company_id'] = $this->getCompanyId();
      $project['label_id'] = $this->getLabelId();
      $project['leader_id'] = $this->getLeaderId();
      $project['completed_by_id'] = $this->getCompletedById();
      $project['completed_on'] = $this->getCompletedOn() instanceof DateTimeValue ? $this->getCompletedOn()->getTimestamp() : null;
      $project['created_by_id'] = $this->getCreatedById();
      $project['created_on'] = $this->getCreatedOn() instanceof DateTimeValue ? $this->getCreatedOn()->getTimestamp() : null;
      $project['currency_id'] = $this->getCurrencyId();
      $project['currency_code'] = $this->getCurrency() instanceof Currency ? $this->getCurrency()->getCode() : null;
      $project['state'] = $this->getState();
      $project['status_verbose'] = $this->getVerboseStatus();
      $project['updated_by_id'] = $this->getUpdatedById();
      $project['updated_on'] = $this->getUpdatedOn() instanceof DateTimeValue ? $this->getUpdatedOn()->getTimestamp() : null;

      if($user instanceof User && $user->canSeeProjectBudgets()) {
        $result['budget'] = $this->getBudget();
      } // if

      // Progress
      list($total_tasks, $open_tasks) = ProjectProgress::getProjectProgress($this);

      $project['progress'] = array();
      $project['progress']['open_tasks'] = $open_tasks;
      $project['progress']['total_tasks'] = $total_tasks;
      $project['progress']['percent_done'] = $this->getPercentsDone();

      // Users
      $project_users = $this->users()->get($user);
      if(is_foreachable($project_users)) {
        $project['users'] = array();

        foreach($project_users as $k => $project_user) {
          $project['users'][$k]['id'] = $project_user->getId();
          $project['users'][$k]['email'] = $project_user->getEmail();
          $project['users'][$k]['first_name'] = $project_user->getFirstName();
          $project['users'][$k]['last_name'] = $project_user->getLastName();
          $project['users'][$k]['name'] = $project_user->getName();
          $project['users'][$k]['display_name'] = $project_user->getDisplayName();
          $project['users'][$k]['short_display_name'] = $project_user->getDisplayName(true);
          $project['users'][$k]['permalink'] = $project_user->getViewUrl();
          $project['users'][$k]['avatar_full_size'] = base64_encode(file_get_contents($project_user->avatar()->getUrl(IUserAvatarImplementation::SIZE_PHOTO)));
          $project['users'][$k]['company_id'] = $project_user->getCompanyId();
          $project['users'][$k]['created_by_id'] = $project_user->getCreatedById();
          $project['users'][$k]['created_on'] = $project_user->getCreatedOn() instanceof DateTimeValue ? $project_user->getCreatedOn()->getTimestamp() : null;
          $project['users'][$k]['is_administrator'] = $project_user->isAdministrator();
          $project['users'][$k]['is_archived'] = $project_user->isArchived();
          $project['users'][$k]['is_people_manager'] = $project_user->isPeopleManager();
          $project['users'][$k]['is_project_manager'] = $project_user->isProjectManager();
          $project['users'][$k]['last_activity_on'] = $project_user->getLastActivityOn() instanceof DateTimeValue ? $project_user->getLastActivityOn()->getTimestamp() : null;
          $project['users'][$k]['state'] = $project_user->getState();
          $project['users'][$k]['updated_by_id'] = $project_user->getUpdatedById();
          $project['users'][$k]['updated_on'] = $project_user->getUpdatedOn() instanceof DateTimeValue ? $project_user->getUpdatedOn()->getTimestamp() : null;

          // User permissions
          $user_permissions = array(
            'role' => null,
            'permissions' => array()
          );

          $permissions = array_keys(ProjectRoles::getPermissions());
          if($project_user->isAdministrator()) {
            $user_permissions['role'] = 'administrator';
          } elseif($project_user->isProjectManager()) {
            $user_permissions['role'] = 'project-manager';
          } elseif($this->isLeader($project_user)) {
            $user_permissions['role'] = 'project-leader';
          } // if

          if($user_permissions['role'] === null) {
            $project_role = $project_user->projects()->getRole($this);
            if($project_role instanceof ProjectRole) {
              $user_permissions['role'] = $project_role->getId();
            } else {
              $user_permissions['role'] = 'custom';
            } // if

            foreach($permissions as $permission) {
              $user_permissions['permissions'][$permission] = (integer) $project_user->projects()->getPermission($permission, $this);
            } // foreach
          } else {
            foreach($permissions as $permission) {
              $user_permissions['permissions'][$permission] = ProjectRole::PERMISSION_MANAGE;
            } // foreach
          } // if

          $project['users'][$k]['role'] = $user_permissions['role'];
          $project['users'][$k]['permissions'] = $user_permissions['permissions'];
        } // foreach
      } // if

      fwrite($output_handle, '[' . JSON::encode($project) . ']');

      // Close the handle and set correct permissions
      fclose($output_handle);
      @chmod($output_file, 0777);

      return $project;
    } // exportToFile
    
    // ---------------------------------------------------
    //  Context
    // ---------------------------------------------------
    
    /**
     * Return object domain
     * 
     * @return string
     */
    function getObjectContextDomain() {
      return 'projects';
    } // getContextDomain
    
    /**
     * Return object path
     */
    function getObjectContextPath() {
      return 'projects/' . $this->getId();
    } // getContextPath
    
    // ---------------------------------------------------
    //  Project creation
    // ---------------------------------------------------
    
    /**
     * Cached array of reserved slug values
     *
     * @var array
     */
    private $reserved_slugs = false;
    
    /**
     * Returns true if $slug is reserved (by router)
     * 
     * @param string $slug
     * @return boolean
     */
    function isReservedSlug($slug) {
      if($this->reserved_slugs === false) {
        $this->reserved_slugs = array('add', 'archive', 'categories', 'timeline', 'requests', 'templates');
        
        EventsManager::trigger('on_reserved_project_slugs', array(&$this->reserved_slugs));
      } // if
      
      return in_array($slug, $this->reserved_slugs);
    } // isReservedSlug
    
    // ---------------------------------------------------
    //  Default visibility
    // ---------------------------------------------------
    
    /**
     * Return default visibility for all new objects
     * 
     * @return integer
     */
    function getDefaultVisibility() {
      return ConfigOptions::getValueFor('default_project_object_visibility', $this);
    } // getDefaultVisibility
    
    /**
     * Return default visibility for all new objects
     * 
     * @param integer $value
     * @return integer
     */
    function setDefaultVisibility($value) {
      return ConfigOptions::setValueFor('default_project_object_visibility', $this, $value);
    } // setDefaultVisibility
    
    /**
     * Use system default new project object visibility value
     */
    function resetDefaultVisibility() {
      return ConfigOptions::removeValueFor($this, 'default_project_object_visibility');
    } // resetDefaultVisibility
    
    // ---------------------------------------------------
    //  Operations
    // ---------------------------------------------------
    
    /**
     * Return user assignments
     * 
     * @param User $user
     * @param boolean $return_map
     * @return array
     * @throws Exception
     */
    function getUserAssignments(User $user, $return_map = false) {
      $filter = new AssignmentFilter();
      
      $filter->filterByUsers(array($user->getId()));
      $filter->filterByProjects(array($this->getId()));
      $filter->setIncludeSubtasks(true);
      $filter->setAdditionalColumn1(AssignmentFilter::ADDITIONAL_COLUMN_CATEGORY);
      $filter->setAdditionalColumn2(AssignmentFilter::ADDITIONAL_COLUMN_MILESTONE);
      $filter->setGroupBy(AssignmentFilter::GROUP_BY_DUE_ON);
      
      try {
        $assignments = $filter->run($user);
      } catch(DataFilterConditionsError $e) {
        $assignments = null;
      } catch(Exception $e) {
        throw $e;
      } // try

      if($return_map && $assignments) {
        $filter->resultToMap($assignments);
      } // if

      return $assignments;
    } // getUserAssignments
    
    /**
     * Copy project items into a destination project
     *
     * @param Project $to
     * @throws Exception
     */
    function copyItems(Project &$to) {
      try {
        DB::beginWork('Copying project items @ ' . __CLASS__);
        
        // Clone users
        $this->users()->cloneToProject($to);
        
        // Now move categories
        $categories_map  = array();
        
        $categories = Categories::findBy($this, null);
        
        if($categories) {
          foreach($categories as $category) {
            if($category instanceof ProjectObjectCategory) {
              $copied_category = $category->copyToProject($to, true);
              
              if($copied_category instanceof ProjectObjectCategory) {
                $categories_map[$category->getId()] = $copied_category->getId();
              } // if
            } // if
          } // foreach
        } // if

        // Fix milestone ID-s
        Milestones::fixMilestoneIds($this);
        
        // We need to copy milestones in order to get milestones map
        $milestones = Milestones::findAllByProject($this, VISIBILITY_PRIVATE);
        if($milestones) {
          foreach($milestones as $milestone) {
            $milestone->copyToProject($to, null, $categories_map, true);
          } // foreach
        } // if
        
        $milestoneless_objects = ProjectObjects::findWithoutMilestone($this, STATE_VISIBLE);
        if($milestoneless_objects) {
          $created_on = new DateTimeValue();
          foreach($milestoneless_objects as $object) {
            if($object instanceof ICategory && $object->getCategoryId()) {
              $update_attributes = array(
                'category_id' => isset($categories_map[$object->getCategoryId()]) ? $categories_map[$object->getCategoryId()] : 0,
                'created_on' => $created_on
              );
            } else {
              $update_attributes = null;
            } // if
            
            $object->copyToProject($to, $update_attributes, true);
          } // foreach
        } // if
        
        DB::commit('Project items copied @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to copy project items @ ' . __CLASS__);
        throw $e;
      } // try
    } // copyItems

    /**
     * Version of export routine
     *
     * @var NamedList
     */
    protected $export_routine_version = '1.0';

    /**
     * Export project as file
     *
     * @param User $user
     * @param boolean $skip_files
     * @param integer $changes_since
     * @throws Exception
     */
    function exportAsFile(User $user, $skip_files, $changes_since) {
      $export_dir_path = WORK_PATH . '/' . $this->getSlug();

      if(!is_dir($export_dir_path) && !mkdir($export_dir_path, 0777, true)) {
        throw new Error(lang('Failed to create export project directory: :export_dir_path', array('export_dir_path' => $export_dir_path)));
      } // if
      @chmod($export_dir_path, 0777);

      $this->createSignature($export_dir_path . '/signature.json');
      $this->exportToFile($user, $export_dir_path . '/project.json');

      $parents_map = array();

      $milestones_count = Milestones::exportToFileByProject($this, $user, $export_dir_path . '/milestones.json', $parents_map, $changes_since);
      $tasks_count = Tasks::exportToFileByProject($this, $user, $export_dir_path . '/tasks.json', $parents_map, $changes_since);
      $discussions_count = Discussions::exportToFileByProject($this, $user, $export_dir_path . '/discussions.json', $parents_map, $changes_since);
      $assets_count = ProjectAssets::exportToFileByProject($this, $user, $export_dir_path . '/assets.json', $parents_map, $changes_since);
      $notebooks_count = Notebooks::exportToFileByProject($this, $user, $export_dir_path . '/notebooks.json', $parents_map, $changes_since);
      $notebook_pages_count = NotebookPages::exportToFileByProject($this, $user, $export_dir_path . '/notebook_pages.json', $parents_map, $changes_since);
      $tracking_count = TrackingObjects::exportToFileByProject($this, $user, $export_dir_path . '/tracking.json', $parents_map, $changes_since);
      $comments_count = Comments::exportToFileByProject($this, $export_dir_path . '/comments.json', $parents_map, $changes_since);
      $subtasks_count = Subtasks::exportToFileByProject($this, $export_dir_path . '/subtasks.json', $parents_map, $changes_since);
      $attachments_count = Attachments::exportToFileByProject($this, $export_dir_path . '/attachments.json', $parents_map, $changes_since);

      // export
      if(!$skip_files) {
        Project::exportBinaryData($this, $user, $parents_map, $changes_since, $export_dir_path);
      } // if

      // compress exported files to archive
      $this->compressExportedFiles($export_dir_path);

      // path to export archive
      $export_archive = $export_dir_path . '.zip';

      // set content type and disposition
      header('Content-Type: application/zip; charset=UTF-8');
      header('Content-Disposition: attachment; filename="' . basename($export_archive) . '"');

      // custom headers needed for progressbar
      header('Project-Archive-Size: ' . filesize($export_archive));
      header('Project-Milestones-Count: ' . $milestones_count);
      header('Project-Tasks-Count: ' . $tasks_count);
      header('Project-Discussions-Count: ' . $discussions_count);
      header('Project-Assets-Count: ' . $assets_count);
      header('Project-Notebooks-Count: ' . $notebooks_count);
      header('Project-Notebook-Pages-Count: ' . $notebook_pages_count);
      header('Project-Tracking-Count: ' . $tracking_count);
      header('Project-Comments-Count: ' . $comments_count);
      header('Project-Subtasks-Count: ' . $subtasks_count);
      header('Project-Attachments-Count: ' . $attachments_count);

      @readfile($export_archive);
      die();
    } // exportAsFile

    /**
     * Create export signtaure
     *
     * @param string $output_file
     * @return array
     */
    function createSignature($output_file) {
      $signtaure = array(
        'timestamp' => DateTimeValue::now()->getTimestamp(),
        'export_routine_version' => $this->export_routine_version
      );

      if(!($output_handle = fopen($output_file, 'w+'))) {
        throw new Error(lang('Failed to write JSON file to :file_path', array('file_path' => $output_file)));
      } // if

      if(!fwrite($output_handle, '[' . JSON::encode($signtaure) . ']')) {
        throw new Error(lang('Failed to write JSON file to :file_path', array('file_path' => $output_file)));
      } // if

      fclose($output_handle);
      @chmod($output_file, 0777);
    } // createSignature

    /**
     * Export project binary data
     *
     * @param Project $project
     * @param User $user
     * @param array $parents_map
     * @param integer $changes_since
     * @param string $export_dir_path
     * @throws Exception
     */
    static function exportBinaryData(Project $project, User $user, $parents_map, $changes_since, $export_dir_path) {
      $file_binary_data_dir_path = $export_dir_path . '/files';
      $attachment_binary_data_dir_path = $export_dir_path . '/files/attachments';

      if(!is_dir($file_binary_data_dir_path) && !mkdir($file_binary_data_dir_path, 0777, true) || !is_dir($attachment_binary_data_dir_path) && !mkdir($attachment_binary_data_dir_path, 0777, true)) {
        throw new Exception(lang('Failed to create project binary data directories: :file_binary_data_dir_path and :attachment_binary_data_dir_path', array('file_binary_data_dir_path' => $file_binary_data_dir_path, 'attachment_binary_data_dir_path' => $attachment_binary_data_dir_path)));
      } // if

      @chmod($file_binary_data_dir_path, 0777);
      @chmod($attachment_binary_data_dir_path, 0777);

      $files = ProjectAssets::findForExport($project, $user, $parents_map, $changes_since, array('File'));
      $attachments = isset($parents_map['Attachment']) && count($parents_map['Attachment']) ? $parents_map['Attachment'] : array();

      if(is_foreachable($files) || is_foreachable($attachments)) {
        foreach(array_merge($files, $attachments) as $file) {
          $file_name = $file['name'];

          if($file['type'] == 'Attachment') {
            if(!copy(UPLOAD_PATH . '/' . $file['location'], $attachment_binary_data_dir_path . '/' . $file['id'] . '-' . $file_name)) {
              throw new Error(lang('Failed to export project attachments binary data: :file_name', array('file_name' => $file_name)));
            } // if
          } else {
            if(!copy(UPLOAD_PATH . '/' . $file['location'], $file_binary_data_dir_path . '/' . $file_name)) {
              throw new Error(lang('Failed to export project files binary data: :file_name', array('file_name' => $file_name)));
            } // if
          } // if
        } // foreach
      } // if
    } // exportBinaryData

    /**
     * Compress project exported files
     *
     * @param string $export_dir_path
     * @return void
     */
    function compressExportedFiles($export_dir_path) {
      require_once ANGIE_PATH . '/classes/PclZip.class.php';

      $compressed_project_file_path = $export_dir_path . '.zip';

      // delete existing compressed project
      @unlink($compressed_project_file_path);

      $zip = new PclZip($compressed_project_file_path);

      // get all exported files
      $exported_files = get_files($export_dir_path, null, true);

      $result = $zip->add($exported_files, PCLZIP_OPT_REMOVE_PATH, WORK_PATH);
      if(!$result) {
        throw new Error(lang('Could not add exported files to archive file :archive', array('archive' => $compressed_project_file_path)));
      } // if

      @chmod($compressed_project_file_path, 0777);
      safe_delete_dir($export_dir_path, WORK_PATH);
    } // compressExportedFiles

    /**
     * Lock project synchronization
     *
     * @param integer $changes_num
     * @return null
     */
    function syncLock($changes_num) {
      $now = DateTimeValue::now();

      if(!ConfigOptions::getValueFor('project_sync_locked', $this) || ConfigOptions::getValueFor('project_last_sync_locked_until', $this) < $now->getTimestamp()) {
        ConfigOptions::setValueFor('project_sync_locked', $this, true);
        ConfigOptions::setValueFor('project_last_synced_on', $this, $now->getTimestamp());

        if($changes_num <= 100) {
          ConfigOptions::setValueFor('project_last_sync_locked_until', $this, $now->advance(180, false)->getTimestamp());
        } else {
          ConfigOptions::setValueFor('project_last_sync_locked_until', $this, $now->advance(600, false)->getTimestamp());
        } // if
      } // if
    } // syncLock

    /**
     * Unlock project synchronization
     *
     * @param void
     * @return null
     */
    function syncUnlock() {
      ConfigOptions::setValueFor('project_sync_locked', $this, false);
    } // syncUnlock
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can view this project
     * 
     * @param User $user
     * @return boolean
     */
    function canView(User $user) {
      return $user->isProjectManager() || $this->users()->isMember($user);
    } // canView
    
    /**
     * Can edit project properties
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return $this->isLeader($user) || $user->isProjectManager();
    } // canEdit
    
    /**
     * Returns true if $user can manage budget for this particular project
     * 
     * @param IUser $user
     * @return boolean
     */
    function canManageBudget(IUser $user) {
      if($user instanceof User) {
        return ($this->isLeader($user) || $user->isProjectManager()) && $user->canSeeProjectBudgets();
      } else {
        return false;
      } // if
    } // canManageBudget

    /**
     * Returns true if $user can manage people on this particular projeect
     *
     * @param IUser $user
     * @return boolean
     */
    function canManagePeople(IUser $user) {
      return $this->canEdit($user) || $user->isPeopleManager();
    } // canManagePeople

    /**
     * Returns true if $user can lock/unlock synchronization for this project
     *
     * @param User $user
     * @return boolean
     */
    function canLockUnlockSync(User $user) {
      return $user->isProjectManager() || $this->users()->isMember($user);
    } // canLockUnlockSync
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Additional step URL pattern
     *
     * @var string
     */
    private $additional_step_url_pattern = false;
    
    /**
     * Return additional step URL
     * 
     * @param string $step
     * @return string
     */
    function getAdditionalStepUrl($step) {
      if($this->additional_step_url_pattern === false) {
        $this->additional_step_url_pattern = Router::assemble('project_additional_step', array(
          'project_slug' => $this->getSlug(), 
          'step' => '--ADDITIONAL-STEP--', 
        ));
      } // if
      
      return str_replace('--ADDITIONAL-STEP--', $step, $this->additional_step_url_pattern);
    } // getAdditionalStepUrl
    
    /**
     * Return project settings URL
     *
     * @return string
     */
    function getSettingsUrl() {
      return Router::assemble('project_settings', array('project_slug' => $this->getSlug()));
    } // getSettingsUrl

    /**
     * Return project settings URL
     *
     * @return string
     */
    function getMailToProjectLearnMoreUrl() {
      return Router::assemble('project_mail_to_project_learn_more', array('project_slug' => $this->getSlug()));
    } // getMailToProjectLearnMoreUrl

    /**
     * Return people URL
     *
     * @return string
     */
    function getPeopleUrl() {
      return Router::assemble('project_people', array('project_slug' => $this->getSlug()));
    } // getPeopleUrl
    
    /**
     * Return add people URL
     *
     * @return string
     */
    function getAddPeopleUrl() {
      return Router::assemble('project_people_add', array('project_slug' => $this->getSlug()));
    } // getAddPeopleUrl
    
    /**
     * Return replace people URL
     *
     * @param User $user
     * @return string
     */
    function getReplaceUserUrl($user) {
      return Router::assemble('project_replace_user', array(
        'project_slug' => $this->getSlug(),
        'user_id' => $user instanceof User ? $user->getId() : $user,
      ));
    } // getReplaceUserUrl
    
    /**
     * Return remove user URL
     *
     * @param User $user
     * @return string
     */
    function getRemoveUserUrl($user) {
      return Router::assemble('project_remove_user', array(
        'project_slug' => $this->getSlug(),
        'user_id' => $user instanceof User ? $user->getId() : $user,
      ));
    } // getRemoveUserUrl
    
    /**
     * Return URL of user permissions page
     *
     * @param User $user
     * @return string
     */
    function getUserPermissionsUrl($user) {
      return Router::assemble('project_user_permissions', array(
        'project_slug' => $this->getSlug(),
        'user_id' => $user instanceof User ? $user->getId() : $user,
      ));
    } // getUserPermissionsUrl

    /**
     * Return project RSS URL
     *
     * @param User $user
     * @return string
     */
    function getRssUrl(User $user) {
      return Router::assemble('project_activity_log_rss', array(
        'project_slug' => $this->getSlug(),
        AngieApplication::API_TOKEN_VARIABLE_NAME => $user->getFeedToken(),
      ));
    } // getRssUrl

	  /**
	   * Convert project to a template URL
	   * @return string
	   */
	  function getConvertToATemplateUrl() {
		  return Router::assemble('project_convert_to_a_template' , array('project_slug' => $this->getSlug()));
	  } // getConvertToATemplateUrl
    
    // ---------------------------------------------------
    //  Tasks count caching
    // ---------------------------------------------------
    
    /**
     * Return value of total_tasks_count field
     *
     * @return integer
     */
    function getTotalTasksCount() {
      return array_var(ProjectProgress::getQuickProgress($this->getId()), 0);
    } // getTotalTasksCount
    
    /**
     * Return value of open_tasks_count field
     *
     * @return integer
     */
    function getOpenTasksCount() {
      return array_var(ProjectProgress::getQuickProgress($this->getId()), 1);
    } // getOpenTasksCount
    
    /**
     * Return number of completed tasks in this project
     *
     * @return integer
     */
    function getCompletedTaskCount() {
      return $this->getTotalTasksCount() - $this->getOpenTasksCount();
    } // getCompletedTaskCount
    
    /**
     * Return number of percents this object is done
     *
     * @return integer
     */
    function getPercentsDone() {
      list($total, $open) = ProjectProgress::getQuickProgress($this->getId());
      
      $completed = $total - $open;
      
      if($total && $completed) {
        return floor($completed / $total * 100);
      } // if
      
      return $this->complete()->isCompleted() ? 100 : 0;
    } // getPercentsDone
    
    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'project';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array(
        'project_slug' => $this->getSlug(),
      );
    } // getRoutingContextParams
    
    /**
     * Cached inspector instance
     * 
     * @var IProjectInspectorImplementation
     */
    private $inspector = false;
    
    /**
     * Return inspector helper instance
     * 
     * @return IProjectInspectorImplementation
     */
    function inspector() {
      if($this->inspector === false) {
        $this->inspector = new IProjectInspectorImplementation($this);
      } // if
      
      return $this->inspector;
    } // inspector
    
    /**
     * Return invoice implementation
     * 
     * @return IInvoiceBasedOnProjectImplementation
     */
    function &invoice() {
      return $this->getDelegateInstance('invoice', function() {
        return AngieApplication::isModuleLoaded('invoicing') ? 'IInvoiceBasedOnProjectImplementation' : 'IInvoiceBasedOnImplementationStub';
      });
    } // invoice
    
    /**
     * ProjectAvatar implementation instance for this object
     *
     * @var IProjectAvatarImplementation
     */
  	private $avatar;
    
    /**
     * Return avatar implementation for this object
     *
     * @return IProjectAvatarImplementation
     */
    function avatar() {
      if(empty($this->avatar)) {
        $this->avatar = new IProjectAvatarImplementation($this);
      } // if
      
      return $this->avatar;
    } // avatar
    
    /**
     * Cached search helper instance
     *
     * @var IProjectSearchItemImplementation
     */
    private $search = false;
    
    /**
     * Return search helper instance
     * 
     * @return IProjectSearchItemImplementation
     */
    function &search() {
      if($this->search === false) {
        $this->search = new IProjectSearchItemImplementation($this);
      } // if
      
      return $this->search;
    } // search
    
    /**
     * Cached complete implementation instance
     *
     * @var ICompleteImplementation
     */
    private $complete = false;
    
    /**
     * Return complete interface implementation
     *
     * @return ICompleteImplementation
     */
    function complete() {
      if($this->complete === false) {
        $this->complete = new ICompleteImplementation($this);
      } // if
      
      return $this->complete;
    } // complete
    
    /**
     * Users helper instance
     *
     * @var IProjectUsersContextImplementation
     */
    private $users = false;
    
    /**
     * Return users helper implementation
     *
     * @return IProjectUsersContextImplementation
     */
    function users() {
      if($this->users === false) {
        $this->users = new IProjectUsersContextImplementation($this);
      } // if
      
      return $this->users;
    } // users
    
    /**
     * Cached labels implementation instance
     *
     * @var IProjectLabelImplementation
     */
    private $label = false;
    
    /**
     * Return labels implementation instance for this object
     *
     * @return IProjectLabelImplementation
     */
    function label() {
      if($this->label === false) {
        $this->label = new IProjectLabelImplementation($this);
      } // if
      
      return $this->label;
    } // label
    
    /**
     * Categories context
     *
     * @var boolean
     */
    private $categories_context = false;
    
    /**
     * Return categories context implementation
     *
     * @return IProjectCategoriesContextImplementation
     */
    function availableCategories() {
      if($this->categories_context === false) {
        $this->categories_context = new IProjectCategoriesContextImplementation($this);
      } // if
      
      return $this->categories_context;
    } // availableCategories
    
    /**
     * Category helper instance
     *
     * @var IProjectCategoryImplementation
     */
    private $category = false;
    
    /**
     * Return category helper instance
     *
     * @return IProjectCategoryImplementation
     */
    function category() {
      if($this->category === false) {
        $this->category = new IProjectCategoryImplementation($this);
      } // if
      
      return $this->category;
    } // category
    
    /**
     * History helper instance
     *
     * @var IHistoryImplementation
     */
    private $history = false;
    
    /**
     * Return history helper instance
     *
     * @return IHistoryImplementation
     */
    function history() {
      if($this->history === false) {
        $this->history = new IHistoryImplementation($this, array('slug', 'company_id', 'currency_id', 'budget', 'leader_id', 'overview'));
      } // if
      
      return $this->history;
    } // history
    
    /**
     * Cached state helper instance
     *
     * @var IProjectStateImplementation
     */
    private $state = false;
    
    /**
     * Return state helper instance
     *
     * @return IProjectStateImplementation
     */
    function state() {
      if($this->state === false) {
        $this->state = new IProjectStateImplementation($this);
      } // if
      
      return $this->state;
    } // state
    
    /**
     * Tracking helper instance
     *
     * @var ITrackingImplementation
     */
    private $tracking = false;
    
    /**
     * Return tracking helper instance
     *
     * @return ITrackingImplementation
     */
    function tracking() {
      if($this->tracking === false) {
        if(AngieApplication::isModuleLoaded('tracking')) {
          $this->tracking = new ITrackingImplementation($this);
        } else {
          $this->tracking = new ITrackingImplementationStub($this);
        } // if
      } // if
      
      return $this->tracking;
    } // tracking
    
    /**
     * Cached activity logs helper instance
     *
     * @var IActivityLogsImplementation
     */
    private $activity_logs = false;
    
    /**
     * Return activity logs helper
     * 
     * @return IActivityLogsImplementation
     */
    function activityLogs() {
      if($this->activity_logs === false) {
        $this->activity_logs = new IActivityLogsImplementation($this);
      } // if
      
      return $this->activity_logs;
    } // activityLogs

    /**
     * Cached search helper instance
     *
     * @var IProjectCustomFieldsImplementation
     */
    private $custom_fields = false;

    /**
     * Return search heper instance
     *
     * @return IProjectCustomFieldsImplementation
     */
    function customFields() {
      if($this->custom_fields === false) {
        $this->custom_fields = new IProjectCustomFieldsImplementation($this);
      } // if

      return $this->custom_fields;
    } // custom_fields

	  /**
	   * Cached access log helper instance
	   *
	   * @var IAccessLogImplementation
	   */
	  private $access_log = false;

	  /**
	   * Return access log helper instance
	   *
	   * @return IAccessLogImplementation
	   */
	  function accessLog() {
		  if($this->access_log === false) {
			  $this->access_log = new IAccessLogImplementation($this);
		  } // if

		  return $this->access_log;
	  } // accessLog

	  /**
	   * Cached calendar context helper instance
	   *
	   * @var IProjectCalendarContextImplementation
	   */
	  private $calendar_context = false;

	  /**
	   * Return calendar context helper instance
	   *
	   * @return IProjectCalendarContextImplementation
	   */
	  function calendar_context() {
		  if($this->calendar_context === false) {
			  $this->calendar_context = new IProjectCalendarContextImplementation($this);
		  } // if

		  return $this->calendar_context;
	  } // calendar_context
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
  
    /**
     * Validate model object before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if(!$this->validatePresenceOf('company_id')) {
        $errors->addError(lang('Project client is required'), 'company_id');
      } // if
      
      if(!$this->validatePresenceOf('leader_id') && !$this->validatePresenceOf('leader_name') && !$this->validatePresenceOf('leader_email')) {
        $errors->addError(lang('Project leader is required'), 'leader_id');
      } // if
      
      if(!$this->validatePresenceOf('name')) {
        $errors->addError(lang('Project name is required.'), 'name');
      } // if
      
      if($this->validatePresenceOf('slug')) {
        if(!$this->validateUniquenessOf('slug')) {
          $errors->addError(lang('Short project name needs to unique'), 'slug');
        } // if
      } else {
        $errors->addError(lang('Short project name is required'), 'slug');
      } // if
    } // validate
    
    /**
     * Save project
     *
     * @return boolean
     * @throws Exception
     */
    function save() {
      try {
        DB::beginWork('Saving project @ ' . __CLASS__);
        
        if($this->isNew()) {
          $slug = trim($this->getSlug());
          
          if($slug) {
            $slug = Inflector::slug($slug);
          } else {
            $slug = Inflector::slug($this->getName());
          } // if
          
          if(is_numeric($slug) || strlen_utf($slug) < 1) {
            $slug = 'unknown-project';
          } elseif(strlen_utf($slug) > 40) {
            $slug = trim(substr_utf($slug, 0, 40), '-');
          } // if
          
          $original_slug = $slug;
          $counter = 1;
          
          while($this->isReservedSlug($slug) || DB::executeFirstCell("SELECT COUNT(id) FROM " . TABLE_PREFIX . "projects WHERE slug = ?", $slug) > 0) {
	        	$slug = $original_slug . '-' . $counter++;
	        } // while
	        
	        $this->setSlug($slug);
        } else {
          $update_search_index = $this->isModifiedField('overview') && !($this->isModifiedField('name') || $this->isModifiedField('state')); // @TODO remove this check when project's overview field is renamed to body
        } // if
        
        parent::save();

        if(isset($update_search_index) && $update_search_index) {
          $this->search()->create();
        } // if

        AngieApplication::cache()->clearModelCache();
        
        DB::commit('Project saved @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to save project @ ' . __CLASS__);
        
        throw $e;
      } // try
      
      return true; // Legacy
    } // save
    
    /**
     * Delete project and all related data
     *
     * @param boolean $force_delete
     * @return boolean
     * @throws Exception
     */
    function delete($force_delete = false) {
      try {
        DB::beginWork('Deleting project @ ' . __CLASS__);

        $force_delete ? parent::forceDelete() : parent::delete();
        
        $this->users()->clear(Authentication::getLoggedUser());
        
        ProjectObjects::deleteByProject($this, $force_delete);
        Favorites::deleteByParent($this);
        Categories::deleteByParent($this->getId());

        AngieApplication::cache()->clearModelCache();

        EventsManager::trigger('on_project_deleted', array($this));

        clean_menu_projects_and_quick_add_cache();
        
        DB::commit('Project deleted @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to delete project @ ' . __CLASS__);
        throw $e;
      } // try
      
      return true;
    } // delete
    
    /**
     * Cached tab settings
     *
     * @var array
     */
    private $project_tabs = array();
    
    /**
     * Return available tabs
     *
     * @param User user
     * @param string $interface
     * @param bool $use_cache
     * @return NamedList
     */
    function getTabs(User $user, $interface = AngieApplication::INTERFACE_DEFAULT, $use_cache = true) {
      $user_id = $user->getId();
      
      if((empty($this->project_tabs[$user_id]) || empty($this->project_tabs[$user_id][$interface])) || !$use_cache) {
        $tabs_settings = (array) ConfigOptions::getValueFor('project_tabs', $this);
      
        $tabs = new NamedList();
        
        // Register milestones tab
        if(in_array('milestones', $tabs_settings) && Milestones::canAccess($user, $this, false)) {
        	$tabs->add('milestones', array(
            'text' => lang('Milestones'),
            'url' => Router::assemble('project_milestones', array('project_slug' => $this->getSlug())),
            'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? 
            	AngieApplication::getImageUrl('icons/16x16/milestones-tab-icon.png', SYSTEM_MODULE) : 
            	AngieApplication::getImageUrl('icons/listviews/milestones.png', SYSTEM_MODULE, AngieApplication::INTERFACE_PHONE)
          ));

          // Register outline tab
          if ($interface == AngieApplication::INTERFACE_DEFAULT && in_array('outline', $tabs_settings)) {
            $tabs->add('outline', array(
              'text' => lang('Outline'),
              'url' => Router::assemble('project_outline', array(
                'project_slug' => $this->getSlug()
              )),
              'icon' => AngieApplication::getImageUrl('icons/16x16/outline-tab-icon.png', SYSTEM_MODULE)
            ));
          } // if
        } // if
        
        EventsManager::trigger('on_project_tabs', array(&$tabs, &$user, &$this, &$tabs_settings, $interface));
        
        $separator_counter = 1;
        
        $result = new NamedList();
        
        if($interface == AngieApplication::INTERFACE_DEFAULT) {
          $result->add('overview', array(
            'text' => str_excerpt($this->getName(), 25),
            'url' => $this->getViewUrl(),
          	'icon' => $this->avatar()->getUrl(IProjectAvatarImplementation::SIZE_SMALL)
          ));
          
          $result->add('separator-' . $separator_counter++, array(
            'text' => '-',
            'url' => null,
          ));
        } // if
        
        foreach($tabs_settings as $add_tab) {
          if($add_tab == '-') {
            $result->add('separator-' . $separator_counter++, array(
              'text' => '-',
              'url' => null,
            ));
          } else {
            if(isset($tabs[$add_tab])) {
              $result->add($add_tab, $tabs[$add_tab]);
            } // if
          } // if
        } // foreach
        
        if(!isset($this->project_tabs[$user->getId()])) {
          $this->project_tabs[$user->getId()] = array();
        } // if
          
        $this->project_tabs[$user->getId()][$interface] = $result;
      } // if
      
      return $this->project_tabs[$user->getId()][$interface];
    } // getTabs
    
    /**
     * Returns true if this project has $tab_name tab
     * 
     * @param string $tab_name
     * @param User $user
     * @param string $interface
     * @return boolean
     */
    function hasTab($tab_name, User $user, $interface = AngieApplication::INTERFACE_DEFAULT) {
      $tabs = $this->getTabs($user, $interface);
      
      return $tabs instanceof NamedList && $tabs->exists($tab_name);
    } // hasTab
  
  }