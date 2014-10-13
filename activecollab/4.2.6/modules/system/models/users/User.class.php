<?php

  /**
   * User class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  abstract class User extends FwUser {
    
    /**
     * List of protected fields (can't be set using setAttributes() method)
     *
     * @var array
     */
  	protected $protect = array(
  	  'session_id',
  	  'last_login_on',
  	  'last_visit_on',
  	  'last_activity_on',
  	  'auto_assign',
  	  'auto_assign_role_id',
  	  'auto_assign_permissions',
  	  'password_reset_key',
  	  'password_reset_on'
  	);
    
    /**
     * Return users display name
     *
     * @param boolean $short
     * @return string
     */
    function getName($short = false) {
      return $this->getDisplayName($short);
    } // getName
    
    /**
     * Return first name
     *
     * If $force_value is true and first name value is not present, system will
     * use email address part before @domain.tld
     *
     * @param boolean $force_value
     * @return string
     */
    function getFirstName($force_value = false) {
      $result = parent::getFirstName();
      if(empty($result) && $force_value) {
        $email = $this->getEmail();
        return ucfirst_utf(substr_utf($email, 0, strpos_utf($email, '@')));
      } // if
      return $result;
    } // getFirstName
    
    /**
     * Current user date time
     * 
     * @var DateTimeValue
     */
    private $current_date_time = false;
    
    /**
     * Return current date time
     *
     * @return DateTimeValue
     */
    function getCurrentDateTime() {
      if($this->current_date_time === false) {
        $tmp = DateTimeValue::now()->formatForUser($this);
        $this->current_date_time = new DateTimeValue($tmp);
      } // if
      return $this->current_date_time;
    } // getCurrentDateTime
    
    /**
     * Parent company
     *
     * @var Company
     */
    private $company = false;
    
    /**
     * Return parent company
     *
     * @return Company
     */
    function getCompany() {
      if($this->company === false) {
        $this->company = Companies::findById($this->getCompanyId());
      } // if
      return $this->company;
    } // getCompany
    
    /**
     * Set user company
     *
     * @param Company $company
     * @return Company
     * @throws InvalidInstanceError
     */
    function setCompany(Company $company) {
      if($company instanceof Company) {
        $this->setCompanyId($company->getId());
        
        $this->company = $company;
        
        return $this->company;
      } else {
        throw new InvalidInstanceError('company', $company, 'Company');
      } // if
    } // setCompany
    
    /**
     * Return company name
     *
     * @return string
     */
    function getCompanyName() {
    	return $this->getCompany() instanceof Company ? $this->getCompany()->getName() : lang('-- Unknown --');
    } // getCompanyName
    
    /**
     * Return group ID
     * 
     * @return integer
     */
    function getGroupId() {
      return $this->getCompanyId();
    } // getGroupId
    
    /**
     * Return company name
     *
     * @return string
     */
    function getGroupName() {
      return $this->getCompanyName();
    } // getGroupName
    
    /**
     * Cached array of active projects
     *
     * @var array
     */
    protected $active_projects = false;
    
    /**
     * Return all active project this user is involved in
     * 
     * If $separate_favorites is set to true, this function will return array 
     * where first element is array of favorite projects and the second array is 
     * a list of projects that are not added to favorites by this user
     *
     * @param boolean $favorite_first
     * @param boolean $separate_favorites
     * @return array
     */
    function getActiveProjects($favorite_first = false, $separate_favorites = false) {
      if($this->active_projects === false) {
        $this->active_projects = Projects::findActiveByUser($this);
      } // if
      
      if($favorite_first) {
        if(is_foreachable($this->active_projects)) {
          $favorite = array();
          $not_favorite = array();
          
          foreach($this->active_projects as $active_project) {
            if(Favorites::isFavorite($active_project, $this)) {
              $favorite[] = $active_project;
            } else {
              $not_favorite[] = $active_project;
            } // if
          } // foreach
          
          if($separate_favorites) {
            return array($favorite, $not_favorite);
          } else {
            if(count($favorite) && count($not_favorite)) {
              return array_merge($favorite, $not_favorite);
            } elseif(count($favorite)) {
              return $favorite;
            } elseif(count($not_favorite)) {
              return $not_favorite;
            } else {
              return null;
            } // if
          } // if
          
        } else {
          return $separate_favorites ? array(null, null) : null;
        } // if
      } else {
        return $this->active_projects;
      } // if
    } // getActiveProjects
    
    /**
     * Cached display name
     *
     * @var string
     */
    private $display_name = false;
    
    /**
     * Cached short display name
     *
     * @var string
     */
    private $short_display_name = false;
    
    /**
     * Return display name (first name and last name)
     *
     * @param boolean $short
     * @return string
     */
    function getDisplayName($short = false) {
      if ($short) {
        if ($this->short_display_name === false) {
        	$this->short_display_name = Users::getUserDisplayName(array(
        	  'first_name' => $this->getFirstName(), 
        	  'last_name' => $this->getLastName(), 
        	  'email' => $this->getEmail()
        	), $short);
        } // if
        return $this->short_display_name;
      } else {
        if ($this->display_name === false) {
					$this->display_name = Users::getUserDisplayName(array(
					  'first_name' => $this->getFirstName(), 
					  'last_name' => $this->getLastName(), 
					  'email' => $this->getEmail()
					), $short);
        } // if
        return $this->display_name;
      } // if
    } // getDisplayName
    
    /**
     * Prepare list of options that $user can use
     *
     * @param IUser $user
     * @param NamedList $options
     * @param string $interface
     * @return NamedList
     */
    protected function prepareOptionsFor(IUser $user, NamedList $options, $interface = AngieApplication::INTERFACE_DEFAULT) {
      if($this->canChangeRole($user)) {
        $options->add('edit_company_and_role', array(
          'text' => lang('Company and Role'),
          'url'  => $this->getEditCompanyAndRoleUrl(),
          'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? AngieApplication::getImageUrl('icons/12x12/change-company.png', SYSTEM_MODULE, AngieApplication::INTERFACE_DEFAULT) : AngieApplication::getImageUrl('icons/navbar/edit_company_and_role.png', SYSTEM_MODULE, AngieApplication::INTERFACE_PHONE),
        	'important' => true,
          'onclick' => new FlyoutFormCallback('user_updated', array(
            'width' => 800,
            'success_message' => lang('Company and Role have been updated'), 
          )),
        ), true);
      } // if
      
    	if($this->canEdit($user)) {
        $options->add('edit_profile', array(
          'text' => lang('Update Profile'),
          'url'  => $this->getEditProfileUrl(),
        	'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? AngieApplication::getImageUrl('icons/12x12/edit.png', ENVIRONMENT_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT) : '',
        	'important' => true,
          'onclick' => new FlyoutFormCallback('user_updated', array(
            'success_message' => lang('User profile has been updated'), 
          )),
        ), true);
        
        $options->add('edit_settings', array(
          'text' => lang('Change Settings'),
          'url'  => $this->getEditSettingsUrl(),
        	'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? AngieApplication::getImageUrl('icons/12x12/settings.png', ENVIRONMENT_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT) : '',
          'onclick' => new FlyoutFormCallback('user_updated', array(
            'success_message' => lang('Settings updated'),
          )),
        ), true);
      } // if
      
      if($this->canContact($user)) {
        $options->add('export_vcard', array(
          'text' => lang('Export vCard'),
          'url'  => $this->getExportVcardUrl(),
        	'class' => 'notinline',
        	'onclick' => new TargetBlankCallback()
        ), true);
      } // if
      
      if($user instanceof User && $user->isProjectManager()) {
        $options->add('add_to_projects', array(
          'text' => lang('Add to Projects'),
          'url'  => $this->getAddToProjectsUrl(),
        	'onclick' => new FlyoutFormCallback('user_added_to_project', array(
            'success_message' => lang('User has been added to selected projects'),
            'width' => 700
          )),
        ), true);
      } // if
      
      if($this->canSendWelcomeMessage($user)) {
        $options->add('send_welcome_message', array(
          'text' => lang('Send Welcome Message'),
          'url'  => $this->getSendWelcomeMessageUrl(),
          'onclick' => new FlyoutFormCallback(array(
            'success_message' => lang('Welcome message has been sent'),
            'success_event' => 'user_updated',
            'width' => 600,
          )),
        ), true);
      } // if
            
      if($this->canLoginAs($user)) {
      	$options->add('people_company_user_login_as', array(
          'text' => lang('Login As'),
          'url'  => $this->getLoginAsUrl(),
      		'onclick' => new LoginAsFormCallback(array(
            'width' => 400
          )),
        ), true);
      } // if
      
      parent::prepareOptionsFor($user, $options, $interface);
      
      // Edit is not available in user interface, only through API
      if($options->exists('edit')) {
        $options->remove('edit');
      } // if
    } // prepareOptionsFor
    
    /**
     * Returns an array of visible users
     *
     * @param Company $company
     * @param integer $min_state
     * @return array
     */
    function visibleUserIds($company = null, $min_state = STATE_VISIBLE) {
      return Users::findVisibleUserIds($this, $company, $min_state);
    } // visibleUserIds
    
    /**
     * Cached array of visible company ID-s
     *
     * @var array
     */
    private $visible_company_ids = false;
    
    /**
     * Returns array of companies this user can see
     *
     * @return array
     */
    function visibleCompanyIds() {
      if($this->visible_company_ids === false) {
        $this->visible_company_ids = Users::findVisibleCompanyIds($this);
      } // if
      return $this->visible_company_ids;
    } // visibleCompanyIds
    
    /**
     * Return true if user is archived
     * 
     * @return boolean
     */
    function isArchived() {
      return $this->getState() == STATE_ARCHIVED ? true : false;
    } // isArchived
     
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
    	// TODO: resiti problem kesiranja, ovo je samo privremeni fix
      $this->role = false;
      $this->display_name = false;

    	$result = parent::describe($user, $detailed, $for_interface);

    	$result['first_name'] = $this->getFirstName(true);
    	$result['last_name'] = $this->getLastName();
    	$result['display_name'] = $this->getDisplayName();
    	$result['short_display_name'] = $this->getDisplayName(true);
    	$result['email'] = $this->getEmail();
    	$result['last_visit_on'] = $this->getLastVisitOn();
    	$result['last_activity_on'] = $this->getLastActivityOn();
    	$result['invited_on'] = $this->getInvitedOn();
    	$result['is_administrator'] = $this->isAdministrator();
    	$result['is_project_manager'] = $this->isProjectManager();
    	$result['is_people_manager'] = $this->isPeopleManager();

      $result['company_id'] = $this->getCompanyId();
    	if($detailed) {
    	  $result['company'] = $this->getCompany() instanceof Company ? $this->getCompany()->describe($user, false, $for_interface) : null;
    	} // if

      $result['is_archived'] = $this->getState() == STATE_ARCHIVED ? 1 : 0;

      if ($detailed) {
      	$result['title'] = $this->getConfigValue('title');
      	$result['phone_work'] = $this->getConfigValue('phone_work');
      	$result['phone_mobile'] = $this->getConfigValue('phone_mobile');
      	$result['im_type'] = $this->getConfigValue('im_type');
      	$result['im_value'] = $this->getConfigValue('im_value');

      	$now = new DateTimeValue();
      	$result['local_time'] = $now->formatTimeForUser($this, get_user_gmt_offset($this));
      } // if

      $result['permissions']['can_set_as_invited'] = $this->canSetAsInvited($user);
      $result['urls']['set_as_invited'] = $this->getSetAsInvitedUrl();

      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      // TODO: resiti problem kesiranja, ovo je samo privremeni fix
      $this->role = false;
      $this->display_name = false;

      $result = parent::describeForApi($user, $detailed);

      $result['company_id'] = $this->getCompanyId();
      $result['first_name'] = $this->getFirstName(true);
      $result['last_name'] = $this->getLastName();
      $result['display_name'] = $this->getDisplayName();
      $result['short_display_name'] = $this->getDisplayName(true);
      $result['email'] = $this->getEmail();

      if($detailed) {
        $result['last_visit_on'] = $this->getLastVisitOn();
        $result['last_activity_on'] = $this->getLastActivityOn();
        $result['invited_on'] = $this->getInvitedOn();
        $result['is_administrator'] = $this->isAdministrator();
        $result['is_project_manager'] = $this->isProjectManager();
        $result['is_people_manager'] = $this->isPeopleManager();
        $result['company'] = $this->getCompany() instanceof Company ? $this->getCompany()->describeForApi($user) : null;

        $result['title'] = $this->getConfigValue('title');
        $result['phone_work'] = $this->getConfigValue('phone_work');
        $result['phone_mobile'] = $this->getConfigValue('phone_mobile');
        $result['im_type'] = $this->getConfigValue('im_type');
        $result['im_value'] = $this->getConfigValue('im_value');

        $now = new DateTimeValue();
        $result['local_time'] = $now->formatTimeForUser($this, get_user_gmt_offset($this));

        $result['role'] = Users::describeUserRoleForApi($this);
      } // if

      return $result;
    } // describeForApi
    
    /**
     * Return vCard content that represents this user
     * 
     * @param boolean $force_download
     * @param boolean $export_to_file
     * @param Company $company
     * @return File_IMC_Build_Vcard|string
     * @throws Exception
     */
    function toVCard($force_download = true, $export_to_file = false, Company $company = null) {
    	$vcard_content = '';
    	
      $vcard = parent::toVCard();
      
      if($this->getConfigValue('title')) {
				$vcard->setTitle($this->getConfigValue('title'));
			} // if
			
			$vcard->addOrganization($this->getCompanyName());

      if($company instanceof Company) {
        $vcard->addAddress('', '', preg_split("[\n]", $company->getConfigValue('office_address')));
        $vcard->addParam('TYPE', 'WORK');
      } // if
			
			if($this->getConfigValue('phone_work')) {
				$vcard->addTelephone($this->getConfigValue('phone_work'));
				$vcard->addParam('TYPE', 'WORK');
			} // if
			
			if($this->getConfigValue('phone_mobile')) {
				$vcard->addTelephone($this->getConfigValue('phone_mobile'));
				$vcard->addParam('TYPE', 'CELL');
			} // if
			
			$avatar_url = $this->avatar()->getUrl(IUserAvatarImplementation::SIZE_PHOTO);
			$avatar = strtok(basename($avatar_url), '?');
			$type = strtoupper(substr(strrchr($avatar, '.'), 1));
			
			if($avatar != 'default.256x256.png') {
				$vcard->setPhoto(base64_encode(file_get_contents($avatar_url)));
				$vcard->addParam('TYPE', $type == 'JPG' ? 'JPEG' : $type);
				$vcard->addParam('ENCODING', 'b');
			} // if
			
			if($this->getUpdatedOn()) {
				$vcard->setRevision(date('Ymd\THis\Z', strtotime($this->getUpdatedOn())));
			} // if
			
			$vcard_content .= $vcard->fetch() . "\n";
			
      if($force_download) {
				header('Content-Type: text/x-vcard; charset=utf-8');
				header('Content-Disposition: attachment; filename="' . $this->getName() . '.vcf"');
		
		    print $vcard_content;
		    die();
			} elseif($export_to_file) {
				$file_path = WORK_PATH . '/contacts/' . $this->getName() . '.vcf';
				$file_handle = fopen($file_path, 'w+');
				if(!fwrite($file_handle, $vcard_content)) {
					throw new Exception(lang('Could not write user :name vCard into temporary vCard :file file', array('name' => $this->getName(), 'file' => $file_path)));
				} // if
				fclose($file_handle);
				@chmod($file_path, 0777);
			} else {
				return $vcard_content;
			} // if
    } // toVCard
    
    /**
     * Prefered locale
     *
     * @var string
     */
    private $locale = false;
    
    /**
     * Return prefered locale
     *
     * @param string $default
     * @return string
     */
    function getLocale($default = null) {
    	if($this->locale === false) {
    	  $language_id = ConfigOptions::getValueFor('language', $this);
    	  if($language_id) {
    	    $language = Languages::findById($language_id);
    	    if($language instanceof Language) {
    	      $this->locale = $language->getLocale();
    	    } // if
    	  } // if
    	  
    	  if($this->locale === false) {
    	    $this->locale = $default === null ? BUILT_IN_LOCALE : $default;
    	  } // if
    	} // if
    	
    	return $this->locale;
    } // getLocale
    
    /**
     * Cached last visit on value
     *
     * @var DateTimeValue
     */
    private $last_visit_on = false;
    
    /**
     * Return users last visit
     *
     * @param boolean $force
     * @return DateTimeValue
     */
    function getLastVisitOn($force = false) {
      if($this->last_visit_on === false) {
      	$this->last_visit_on = parent::getLastVisitOn();
      } // if
      
      if($force) {
        return $this->last_visit_on instanceof DateTimeValue ? $this->last_visit_on : new DateTimeValue(filectime(ENVIRONMENT_PATH . '/config/config.php'));
      } else {
        return $this->last_visit_on;
      } // if
    } // getLastVisitOn

    /**
     * Prepare user's IM for import
     *
     * @param $vcard_im_type
     * @param $vcard_im_value
     * @param $ac_im_type
     * @param $ac_im_value
     */
    function prepareIM($vcard_im_type, $vcard_im_value, &$ac_im_type, &$ac_im_value) {
    	if(is_foreachable($vcard_im_value[0]['value'])) {
				$value = trim($vcard_im_value[0]['value'][0][0]);

				if($value != '') {
					$ac_im_type = $vcard_im_type;
					$ac_im_value = $value;
				} // if
			} // if
    } // prepareIM

    /**
     * Return quick add data
     *
     * @param string $interface
     * @return array
     */
    function getQuickAddData($interface = AngieApplication::INTERFACE_DEFAULT) {
      $user = $this;

      return AngieApplication::cache()->getByObject($this, array('quick_add', $interface), function() use ($interface, $user) {
        $return = array(
          'items' => new NamedList(),
          'subitems' => new NamedList(),
          'map' => array()
        );

        // Create projects subitems
        $projects = Projects::findForQuickAdd($user);
        if($projects) {
          foreach($projects as $project) {
            $return['subitems']->add('project_' . $project->getId(), array (
              'text' => $project->getName(),
              'icon' => $project->avatar()->getUrl(IProjectAvatarImplementation::SIZE_SMALL),
              'url_replacements' => array('--PROJECT-SLUG--' => $project->getSlug()),
              // For mobile devices
              'slug' => $project->getSlug(),
              'icon_big' => $project->avatar()->getUrl(IProjectAvatarImplementation::SIZE_BIG)
            ));
          } // foreach
        } // if

        // Create companies subitems
        $companies = Companies::findForQuickAdd($user);

        if($companies) {
          foreach($companies as $company) {
            $return['subitems']->add('company_' . $company->getId(), array (
              'text' => $company->getName(),
              'icon' => $company->avatar()->getUrl(ICompanyAvatarImplementation::SIZE_SMALL),
              'url_replacements' => array('--COMPANY-ID--' => $company->getId()),
              // For mobile devices
              'icon_medium' => $company->avatar()->getUrl(ICompanyAvatarImplementation::SIZE_MEDIUM)
            ));
          } // foreach
        } // if

        EventsManager::trigger('on_quick_add', array($return['items'], $return['subitems'], &$return['map'], $user, $projects, $companies, $interface));

        return $return;
      }, true);
    } // getQuickAddData
    
    // ---------------------------------------------------
    //  Utils
    // ---------------------------------------------------
    
    /**
     * Cached values of can see milestones permissions
     *
     * @var array
     */
    private $can_see_milestones = array();
    
    /**
     * Returns true if user can see milestones in $project
     *
     * @param Project $project
     * @param boolean $check_tab
     * @return boolean
     */
    function canSeeMilestones(Project $project, $check_tab = true) {
      $project_id = $project->getId();
    	if(!isset($this->can_see_milestones[$project_id])) {
    	  $this->can_see_milestones[$project_id] = Milestones::canAccess($this, $project, $check_tab);
    	} // if
    	return $this->can_see_milestones[$project_id];
    } // canSeeMilestones
    
    /**
     * Cached can see project budgets value
     *
     * @var boolean
     */
    private $can_see_project_budgets = null;
    
    /**
     * Returns true if $user can see project budgets
     * 
    * @return boolean
     */
    function canSeeProjectBudgets() {
      if($this->can_see_project_budgets === null) {
        $this->can_see_project_budgets = $this->isProjectManager() || $this->getSystemPermission('can_see_project_budgets');
      } // if
      
      return $this->can_see_project_budgets;
    } // canSeeProjectBudgets
    
    /**
     * Return true if $user can see contact details of other users in the system
     *
     * @return boolean
     */
    function canSeeContactDetails() {
      return !($this instanceof Client || $this instanceof Subcontractor); // Contact details are hidden from subcontractors and clients
    } // canSeeContactDetails
    
    /**
     * Returns true if this user has access to reports section
     * 
     * @return boolean
     */
    function canUseReports() {
      return $this->isProjectManager() || $this->isPeopleManager() || $this->isFinancialManager();
    } // canUseReports
    
    /**
     * Is this user member of owner company
     *
     * @var boolean
     */
    private $is_owner = null;
    
    /**
     * Returns true if this user is member of owner company
     *
     * @return boolean
     */
    function isOwner() {
      if($this->is_owner === null) {
        $this->is_owner = $this->getCompany() instanceof Company ? $this->getCompany()->getIsOwner() : false;
      } // if
      return $this->is_owner;
    } // isOwner

    /**
     * Returns true if this user is manager
     *
     * @return bool
     */
    function isManager() {
      return $this instanceof Manager;
    } // isManager
    
    /**
     * Returns true if this user has management permissions in People section
     *
     * @return boolean
     */
    function isPeopleManager() {
      return $this->isAdministrator() || ($this->isManager() && $this->getSystemPermission('can_manage_people'));
    } // isPeopleManager
    
    /**
     * Returns true if this user has global project management permissions
     *
     * @return boolean
     */
    function isProjectManager() {
      return $this->isAdministrator() || ($this->isManager() && $this->getSystemPermission('can_manage_projects'));
    } // isProjectManager
    
    /**
     * Returns true if this user has final management permissions
     * 
     * @return boolean
     */
    function isFinancialManager() {
      return ($this->isAdministrator() || $this->isManager()) && $this->getSystemPermission('can_manage_finances');
    } // isFinancialManager
    
    // ---------------------------------------------------
    //  Project roles and permissions
    // ---------------------------------------------------
    
    /**
     * User projects helper implementation
     *
     * @var IUserProjectsImplementation
     */
    private $projects = false;
    
    /**
     * Return projects helper instance
     *
     * @return IUserProjectsImplementation
     */
    function projects() {
      if($this->projects === false) {
        $this->projects = new IUserProjectsImplementation($this);
      } // if
      
      return $this->projects;
    } // projects
    
    /**
     * Return config option value
     *
     * @param string $name
     * @return mixed
     */
    function getConfigValue($name) {
      return ConfigOptions::getValueFor($name, $this);
    } // getConfigValue
    
    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------
    /**
     * Return object context domain
     *
     * @return string
     */
    function getObjectContextDomain() {
      return 'people';
    } // getObjectContextDomain
    
    /**
     * Return object context path
     * 
     * @return string
     */
    function getObjectContextPath() {
      return $this->getCompany()->getObjectContextPath() . '/users/' . $this->getId();
    } // getObjectContextPath
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'people_company_user';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array(
        'company_id' => $this->getCompanyId(),
        'user_id' => $this->getId(),
      );
    } // getRoutingContextParams
    
    /**
     * Return modification log helper instance
     *
     * @return IHistoryImplementation
     */
    function history() {
      return parent::history()->alsoTrackFields(array('company_id'));
    } // history

    /**
     * Cached inspector instance
     *
     * @var IActiveCollabUserInspectorImplementation
     */
    private $inspector = false;

    /**
     * Return inspector helper instance
     *
     * @return IActiveCollabUserInspectorImplementation
     */
    function inspector() {
      if($this->inspector === false) {
        $this->inspector = new IActiveCollabUserInspectorImplementation($this);
      } // if

      return $this->inspector;
    } // inspector
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can see this account
     *
     * @param User $user
     * @return boolean
     */
    function canView(User $user) {
      if($user instanceof User) {
        $min_state = Trash::canAccess($user) ? STATE_TRASHED : STATE_ARCHIVED;
        return $user->getId() == $this->getId() || $user->getCompanyId() == $this->getCompanyId() || in_array($this->getId(), Users::findVisibleUserIds($user, null, $min_state));
      } else {
        return false;
      } // if
    } // canView
    
    /**
     * Return true if $user can see and use contact details of this user
     * 
     * @param User $user
     * @return boolean
     */
    function canContact(User $user) {
      if($user instanceof User) {
        if($user->getCompanyId() == $this->getCompanyId()) {
          return true;
        } else {
          return $user->canSeeContactDetails();
        } // if
      } else {
        return false;
      } // if
    } // canContact

    /**
     * Check if $user can update this profile
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      if($user instanceof User) {
        return $user->is($this) || $user->isPeopleManager();
      } else {
        return false;
      } // if
    } // canEdit

    /**
     * Return true if $user can delete this user account
     *
     * @param User $user
     * @return bool
     */
    function canDelete(User $user) {
      if($user instanceof User) {
        return $this->isAdministrator() ? $user->isAdministrator() : $user->isPeopleManager();
      } else {
        return false;
      } // if
    } // canDelete

    /**
     * Returns true if $user can change password of this user
     *
     * @param User $user
     * @return bool
     */
    function canChangePassword(User $user) {
      if($user instanceof User) {
        if(AngieApplication::isOnDemand() && OnDemand::isAccountOwner($this) && !$user->is($this)) {
          return false;
        } //if
        if($this->isAdministrator()) {
          return $user->is($this) || $user->isAdministrator();
        } elseif ($this->isAdministrator() && !$user->isAdministrator()) {
          return false;
        } else {
          return $user->is($this) || $user->isPeopleManager();
        } // if
      } else {
        return false;
      } // if
    } // canChangePassword
    
    /**
     * Returns true if $user can change this users role
     *
     * @param User $user
     * @return boolean
     */
    function canChangeRole(User $user) {
      if(AngieApplication::isOnDemand() && OnDemand::isAccountOwner($this)) {
        return false;
      } // if

      if($this->isAdministrator()) {
        return $user->is($this) || $user->isAdministrator(); // Only administrators can change the role of other administrators
      } elseif ($this->isManager()) {
        return $user->isAdministrator(); // Only administrators can change the role of managers
      } else {
        return $user->isPeopleManager(); // People managers can change the role of all users, except managers and administrators
      } // if
    } // canChangeRole
    
    /**
     * Check if $user can view recent activities of the selected user
     *
     * @param User $user
     * @return boolean
     */
    function canViewActivities($user) {
    	return $user->isAdministrator() || $user->isProjectManager();
    } // canViewActivities

    /**
     * Can $this user import contacts from vCard?
     *
     * @return boolean
     */
    function canImportVcard() {
    	return $this->isPeopleManager();
    } // canAdd
    
    /**
     * Returns true if $user can change this users permissions on a $project
     *
     * @param User $user
     * @param Project $project
     * @return boolean
     */
    function canChangeProjectPermissions(User $user, Project $project) {
      if($project->isLeader($this) || $this->isProjectManager() || $this->isAdministrator()) {
        return false;
      } // if
      
      return $project->canManagePeople($user);
    } // canChangeProjectPermissions

    /**
     * Return true if $user can replace this user on $project
     *
     * @param User $user
     * @param Project $project
     * @return bool
     */
    function canReplaceOnProject(User $user, Project $project) {
      return $project->canManagePeople($user);
    } // canReplaceOnProject
    
    /**
     * Check if $user can remove this user from $project
     *
     * @param User $user
     * @param Project $project
     * @return boolean
     */
    function canRemoveFromProject(User $user, Project $project) {
      if($project->isLeader($this)) {
        return false;
      } // if
      
      return $project->canManagePeople($user);
    } // canRemoveFromProject
    
    /**
     * Returns true if $user can (re)send welcome message
     *
     * @param User $user
     * @return boolean
     */
    function canSendWelcomeMessage(User $user) {
      if ($user instanceof User) {
        if ($user->is($this) || ($this->isAdministrator() && !$user->isAdministrator())) {
          return false;
        } else {
          return $user->isPeopleManager();
        } // if
      } else {
        return false;
      } // if
    } // canSendWelcomeMessage

    /**
     * Check if $user can set this user as invited
     *
     * @param User $user
     * @return boolean
     */
    function canSetAsInvited(User $user) {
      if($user instanceof User) {
        if($this->isAdministrator() && !$user->isAdministrator()) {
          return false;
        } else {
          return $user->isPeopleManager();
        } // if
      } else {
        return false;
      } // if
    } // canSetAsInvited

    /**
     * Can $user login as $this selected one
     *
     * @param User $user
     * @return boolean
     */
    function canLoginAs(User $user) {
      if ($this->is($user)) {
        return false; // No sense to log in as yourself
      } // if

      if ($user->isAdministrator() && !$this->isAdministrator()) {
        return true; // Admin can log in as everyone, except as other administrator
      } // if

      // People manager can log in as anyone, except as administrator or manager
      if ($user->isPeopleManager()) {
        return !($this->isAdministrator() || $this->isManager());
      } // if

      return false;
    } // canLoginAs

    /**
     * Returns true if $user can add API subscription for this user
     *
     * @param User $user
     * @return boolean
     */
    function canAddApiSubscription(User $user) {
      if(AngieApplication::isOnDemand() && OnDemand::isAccountOwner($this) && !$user->is($this)) {
        return false;
      } //if
      return parent::canAddApiSubscription($user);
    } // canAddApiSubscription

    /**
     * Can user see this user subscription
     *
     * @param User $user
     * @return bool
     */
    function canSeeApiSubscription(User $user) {
      if(AngieApplication::isOnDemand() && OnDemand::isAccountOwner($this) && !$user->is($this)) {
        return false;
      } // if
      return parent::canSeeApiSubscription($user);
    } // canSeeApiSubscription

    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return View URL
     *
     * @return string
     */
    function getViewUrl() {
    	return Router::assemble('people_company_user', array(
    	  'company_id' => $this->getCompanyId(),
    	  'user_id'    => $this->getId(),
    	));
    } // getViewUrl
    
    /**
     * Return recent activities URL
     *
     * @return string
     */
    function getRecentActivitiesUrl() {
    	return Router::assemble('people_company_user_recent_activities', array(
    	  'company_id' => $this->getCompanyId(),
    	  'user_id'    => $this->getId(),
    	));
    } // getRecentActivitiesUrl
    
    /**
     * Get edit user profile URL
     *
     * @return string
     */
    function getEditProfileUrl() {
      return Router::assemble('people_company_user_edit_profile', array(
        'company_id' => $this->getCompanyId(),
        'user_id'    => $this->getId(),
      ));
    } // getEditProfileUrl
    
    /**
     * Get edit user settings URL
     *
     * @return string
     */
    function getEditSettingsUrl() {
      return Router::assemble('people_company_user_edit_settings', array(
        'company_id' => $this->getCompanyId(),
        'user_id'    => $this->getId(),
      ));
    } // getEditSettingsUrl
    
    /**
     * Return edit company and role URL
     *
     * @return string
     */
    function getEditCompanyAndRoleUrl() {
      return Router::assemble('people_company_user_edit_company_and_role', array(
        'company_id' => $this->getCompanyId(),
        'user_id'    => $this->getId(),
      ));
    } // getEditCompanyAndRoleUrl
    
    /**
     * Get edit password URL
     *
     * @return string
     */
    function getEditPasswordUrl() {
      return Router::assemble('people_company_user_edit_password', array(
        'company_id' => $this->getCompanyId(),
        'user_id'    => $this->getId(),
      ));
    } // getEditPasswordUrl
        
    /**
     * Return delete user URL
     *
     * @return string
     */
    function getDeleteUrl() {
      return Router::assemble('people_company_user_delete', array(
        'company_id' => $this->getCompanyId(),
        'user_id'    => $this->getId(),
      ));
    } // getDeleteUrl
    
    /**
     * Return unsubscribe from object URL
     *
     * @param ProjectObject $object
     * @return string
     */
    function getUnsubscribeUrl($object) {
      return Router::assemble('project_object_unsubscribe_user', array(
        'project_slug' => $object->getProject()->getSlug(),
        'object_id' => $object->getId(),
        'user_id' => $this->getId(),
      ));
    } // getUnsubscribeUrl
    
    /**
     * Return reset password URL
     *
     * @return string
     */
    function getResetPasswordUrl() {
    	return Router::assemble('reset_password', array(
    	  'user_id' => $this->getId(),
    	  'code' => $this->getPasswordResetKey(),
    	));
    } // getResetPasswordUrl
    
    /**
     * Return add to projects URL
     *
     * @return string
     */
    function getAddToProjectsUrl() {
      return Router::assemble('people_company_user_add_to_projects', array(
        'company_id' => $this->getCompanyId(),
        'user_id'    => $this->getId(),
      ));
    } // getAddToProjectsUrl
    
    /**
     * Return user projects URL
     *
     * @return string
     */
    function getProjectsUrl() {
      return Router::assemble('people_company_user_projects', array(
        'company_id' => $this->getCompanyId(),
        'user_id'    => $this->getId(),
      ));
    } // getProjectsUrl
    
    /**
     * Return user projects URL
     *
     * @param mixed $page
     * @return string
     */
    function getProjectsArchiveUrl($page = null) {
      $params = array(
        'company_id' => $this->getCompanyId(),
        'user_id'    => $this->getId(),
      );
      
      if($page) {
        $params['page'] = $page;
      } // if
      
      return Router::assemble('people_company_user_projects_archive', $params);
    } // getProjectsArchiveUrl
    
    /**
     * Return user favorites URL
     *
     * @return string
     */
    function getFavoritesUrl() {
    	return Router::assemble('people_company_user_favorites', array(
    	  'company_id' => $this->getCompanyId(),
    	  'user_id' => $this->getId()
    	));
    } // getFavoritesUrl
    
    // ---------------------------------------------------
    //  Getters and Setters
    // ---------------------------------------------------
    
    /**
     * Set auto-assign data
     *
     * @param boolean $enabled
     * @param integer $role_id
     * @param array $permissions
     */
    function setAutoAssignData($enabled, $role_id, $permissions) {
    	if($enabled) {
    	  $this->setAutoAssign(true);
  	    if($role_id) {
  	      $this->setAutoAssignRoleId($role_id);
  	      $this->setAutoAssignPermissions(null);
  	    } else {
  	      $this->setAutoAssignRoleId(0);
  	      $this->setAutoAssignPermissions($permissions);
  	    } // if
  	  } else {
  	    $this->setAutoAssign(false);
  	    $this->setAutoAssignRoleId(0);
  	    $this->setAutoAssignPermissions(null);
  	  } // if
    } // setAutoAssignData
    
    /**
     * Return auto assign role based on auto assign role ID
     *
     * @return ProjectRole
     */
    function getAutoAssignRole() {
    	return $this->getAutoAssignRoleId() ? ProjectRoles::findById($this->getAutoAssignRoleId()) : null;
    } // getAutoAssignRole
    
    /**
     * Return auto assign permissions
     *
     * @return mixed
     */
    function getAutoAssignPermissions() {
    	$raw = parent::getAutoAssignPermissions();
    	return $raw ? unserialize($raw) : null;
    } // getAutoAssignPermissions
    
    /**
     * Set auto assign permissions
     *
     * @param mixed $value
     * @return mixed
     */
    function setAutoAssignPermissions($value) {
    	return parent::setAutoAssignPermissions(serialize($value));
    } // setAutoAssignPermissions
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      $company_id = $this->getCompanyId();
      if($company_id) {
        $company = Companies::findById($company_id);
        if(!($company instanceof Company)) {
          $errors->addError(lang('Selected company does not exist'), 'company_id');
        } // if
      } else {
        $errors->addError(lang('Please select company'), 'company_id');
      } // if
      
      $mailbox_emais = IncomingMailboxes::findEnabledByEmail($this->getEmail());
      if($mailbox_emais instanceof IncomingMailbox) {
        $errors->addError(lang('There is a mailbox defined with the same email address'), 'email');
      }//if
      
      parent::validate($errors);
    } // validate
    
    /**
     * Delete from database
     */
    function delete() {
      parent::delete();
      
      $this->projects()->clear();
    } // delete

    /**
     * Clear cache on save
     *
     * @return boolean
     */
    function save() {
      $clear_cache = $this->isModifiedField('state') || $this->isModifiedField('company_id');
      $clear_project_users_cache = $clear_cache || $this->isModifiedField('first_name') || $this->isModifiedField('last_name') || $this->isModifiedField('email');

      $save = parent::save();

      if($clear_cache) {
        AngieApplication::cache()->removeByObject($this, 'visible_user_ids');
    	} // if

      if ($clear_project_users_cache) {
        AngieApplication::cache()->removeByModel('projects');
      } // if

    	return $save;
    } // save
  
  }