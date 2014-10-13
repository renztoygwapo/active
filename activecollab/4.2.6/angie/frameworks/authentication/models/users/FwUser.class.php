<?php

  /**
   * Authentication level single user
   *
   * @package angie.frameworks.authentication
   * @subpackage models
   */
  abstract class FwUser extends BaseUser implements IUser, IRoutingContext, IHistory, ISearchItem, IObjectContext, IConfigContext, IState, IAvatar, IHomescreen, ISecurityLog {
    
    /**
     * Return base type name
     * 
     * @param boolean $singular
     * @return string
     */
    function getBaseTypeName($singular = true) {
      return $singular ? 'user' : 'users';
    } // getBaseTypeName
    
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
     * Return group ID
     *
     * @return int
     */
    function getGroupId() {
      return 0;
    } // getGroupId

    /**
     * Return group name
     *
     * @return string
     */
    function getGroupName() {
      return lang('Unknown');
    } // getGroupName

    // ---------------------------------------------------
    //  Describe
    // ---------------------------------------------------

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
      $this->display_name = false; // Caching fix

      $result = parent::describe($user, $detailed, $for_interface);

      $result['first_name'] = $this->getFirstName(true);
      $result['last_name'] = $this->getLastName();
      $result['display_name'] = $this->getDisplayName();
      $result['short_display_name'] = $this->getDisplayName(true);
      $result['email'] = $this->getEmail();
      $result['last_visit_on'] = $this->getLastVisitOn();
      $result['last_activity_on'] = $this->getLastActivityOn();
      $result['is_administrator'] = $this->isAdministrator();
      $result['invited_on'] = $this->getInvitedOn();
      $result['is_archived'] = $this->getState() == STATE_ARCHIVED ? 1 : 0;

      if ($detailed) {
        $result['local_time'] = DateTimeValue::now()->formatTimeForUser($this, get_user_gmt_offset($this));
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
      $this->display_name = false; // Caching fix

      $result = parent::describeForApi($user, $detailed);

      $result['first_name'] = $this->getFirstName(true);
      $result['last_name'] = $this->getLastName();
      $result['display_name'] = $this->getDisplayName();
      $result['short_display_name'] = $this->getDisplayName(true);
      $result['email'] = $this->getEmail();
      $result['last_visit_on'] = $this->getLastVisitOn();
      $result['invited_on'] = $this->getInvitedOn();

      if($detailed) {
        $result['last_activity_on'] = $this->getLastActivityOn();
        $result['is_administrator'] = $this->isAdministrator();

        $now = new DateTimeValue();
        $result['local_time'] = $now->formatTimeForUser($this, get_user_gmt_offset($this));
      } // if

      return $result;
    } // describeForApi

    // ---------------------------------------------------
    //  Localisation
    // ---------------------------------------------------
    
    /**
     * Return users language
     *
     * @return Language
     */
    function getLanguage() {
      $language = DataObjectPool::get('Language', $this->getLanguageId());
          
      if(empty($language)) {
        $language = Languages::getBuiltIn();
      } // if

      return $language;
    } // getLanguage
    
    /**
     * Cached language ID value
     *
     * @var integer
     */
    private $language_id = false;
    
    /**
     * Return language ID from configuration
     * 
     * @param boolean $use_cache
     * @return integer
     */
    function getLanguageId($use_cache = true) {
      if(empty($use_cache) || $this->language_id === false) {
        $this->language_id = ConfigOptions::getValueFor('language', $this);
      } // if
      
      return $this->language_id;
    } // getLanguageId

    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------

    /**
     * Return role name
     *
     * @return string
     */
    abstract function getRoleName();

    /**
     * Return role description
     *
     * @return string
     */
    abstract function getRoleDescription();

    /**
     * Return role icon URL
     *
     * @param int $size
     * @return string
     */
    function getRoleIconUrl($size = IUserAvatarImplementation::SIZE_SMALL) {
      return AngieApplication::getImageUrl("user-roles/member.{$size}x{$size}.png", AUTHENTICATION_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT);
    } // getRoleIconUrl

    /**
     * Return system permission value
     *
     * @param string $name
     * @return boolean
     * @throws InvalidParamError
     */
    function getSystemPermission($name) {
      $value = false;

      if($this->isRoleDefinedPermission($name, $value)) {
        return (boolean) $value;
      } elseif($this->isCustomPermission($name, $value)) {
        return (boolean) $value;
      } else {
        return false;
      } // if
    } // getSystemPermission

    /**
     * Return array of custom permissions
     *
     * @return array
     */
    function getSystemPermissions() {
      return $this->getAdditionalProperty('custom_permissions');
    } // getSystemPermissions

    /**
     * Set system permission
     *
     * Option to skip check was added so modules can set permission right after module installation,
     * without the need to refresh the page (and re-trigger the event that collects names of
     * registered permissions)
     *
     * @param string $name
     * @param boolean $value
     * @param boolean $check_if_permission_exists
     * @throws InvalidParamError
     */
    function setSystemPermission($name, $value, $check_if_permission_exists = true) {
      $current_value = null;

      if($check_if_permission_exists && !$this->isCustomPermission($name, $current_value)) {
        throw new InvalidParamError('name', $name, '$name is not a custom permission in this role');
      } // if

      $value = (boolean) $value;

      if($current_value != $value) {
        $custom_permissions = $this->getAdditionalProperty('custom_permissions');

        if(!is_array($custom_permissions)) {
          $custom_permissions = array();
        } // if

        if($value) {
          $custom_permissions[] = $name;
        } else {
          $keys = array_keys($custom_permissions, $name);

          if($keys) {
            foreach($keys as $key) {
              unset($custom_permissions[$key]);
            } // if
          } // if
        } // if

        $this->setAdditionalProperty('custom_permissions', $custom_permissions);
      } // if
    } // setSystemPermission

    /**
     * Bulk set custom user permissions
     *
     * @param array $permissions
     */
    function setSystemPermissions($permissions) {
      $to_set = array();

      if(is_array($permissions)) {
        $available_permissions = $this->getAvailableCustomPermissions();

        foreach($permissions as $permission) {
          if($available_permissions->exists($permission)) {
            $to_set[] = $permission;
          } // if
        } // foreach
      } // if

      $this->setAdditionalProperty('custom_permissions', $to_set);
    } // setSystemPermissions

    /**
     * Returns true if $name is permission controlled by the role, and populate $value with role specific value
     *
     * @param string $name
     * @param boolean $value
     * @return bool
     */
    protected function isRoleDefinedPermission($name, &$value) {
      return false;
    } // isRoleDefinedPermission

    /**
     * Return list of custom permissions that are available to this particular role
     *
     * @return NamedList
     */
    function getAvailableCustomPermissions() {
      $result = new NamedList();

      if(!$this->isAdministrator()) {
        $result->add('can_use_api', array(
          'name' => lang('Use API and Feeds'),
          'description' => lang('Check to enable this user to use API to integrate external application with the system'),
        ));

        if($this->isMember()) {
          $result->add('can_manage_trash', array(
            'name' => lang('Manage Trash'),
            'description' => lang('Check to enable this user to see trashed items and permanently remove them'),
          ));
        } // if
      } // if

      EventsManager::trigger('on_custom_user_permissions', array(&$this, &$result));

      return $result;
    } // getAvailableCustomPermissions

    /**
     * Return true if $name is a custom permission, and populate $value with permission value
     *
     * @param string $name
     * @param mixed $value
     * @return boolean
     */
    protected function isCustomPermission($name, &$value) {
      if($this->getAvailableCustomPermissions()->exists($name)) {
        $custom_permissions = $this->getAdditionalProperty('custom_permissions');
        $value = is_array($custom_permissions) && in_array($name, $custom_permissions);

        return true;
      } // if

      return false;
    } // isCustomPermission

    /**
     * Return array of user ID-s that this user can see
     *
     * @param IUsersContext $context
     * @param integer $min_state
     * @return array
     */
    function getVisibleUserIds($context = null, $min_state = STATE_VISIBLE) {
      if($context instanceof IUsersContext) {
        return $context->users()->getIds($min_state);
      } else {
        return DB::executeFirstColumn('SELECT id FROM ' . Users::getTableName() . ' WHERE state = ?', $min_state);
      } // if
    } // getVisibleUserIds

    // ---------------------------------------------------
    //  Utility
    // ---------------------------------------------------
    
    /**
     * Prepare list of options that $user can use
     *
     * @param IUser $user
     * @param NamedList $options
     * @param string $interface
     * @return NamedList
     */
    protected function prepareOptionsFor(IUser $user, NamedList $options, $interface = AngieApplication::INTERFACE_DEFAULT) {
    	if($this->canEdit($user)) {
        if($this->homescreen()->canHaveOwn() && $this->homescreen()->canManageSet($user)) {
          $options->add('homescreen', array(
            'text' => lang('Home Screen'),
            'url' => $this->homescreen()->getManageUrl(),
          ));
        } // if
      } // if
      
      if($this->canChangePassword($user)) {
        $options->add('edit_password', array(
          'text' => lang('Change Password'),
          'url'  => $this->getEditPasswordUrl(),
          'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? AngieApplication::getImageUrl('icons/12x12/change-password.png', ENVIRONMENT_FRAMEWORK) : AngieApplication::getImageUrl('icons/navbar/change_password.png', SYSTEM_MODULE, AngieApplication::INTERFACE_PHONE),
					'important' => true,
          'onclick' => new FlyoutFormCallback(array(
          	'width' => 'narrow', 
            'success_message' => lang('Password has been changed'), 
          )),
        ), true);
      } // if
      
      if($this->canSeeApiSubscription($user)) {
        //don't allow to see account owner API subscription if on demand
        $options->add('api_subscriptions', array(
          'text' => lang('API Subscriptions'),
          'url' => $this->getApiSubscriptionsUrl(),
        ));

      } // if
      
      parent::prepareOptionsFor($user, $options, $interface);
    } // prepareOptionsFor
    
    /**
     * Return vCard instance that represents this user
     * 
     * @return File_IMC_Build_Vcard
     */
    function toVCard() {
			$vcard = File_IMC::build('vCard');
			
			$vcard->setName($this->getLastName(), $this->getFirstName());
			$vcard->setFormattedName($this->getName());
			$vcard->addEmail($this->getEmail());
			$vcard->addParam('TYPE', 'INTERNET');
			
			return $vcard;
    } // toVCard

    // ---------------------------------------------------
    //  Password and password policy
    // ---------------------------------------------------

    /**
     * Returns true if we have a valid password
     *
     * @param string $password
     * @return boolean
     */
    function isCurrentPassword($password) {
      if(Authentication::getPasswordPolicy()->isCurrentPassword($password, $this->getPassword(), $this->getPasswordHashedWith())) {

        // Hash using PBKDF2 if password is hashed with SHA1
        if($this->getPasswordHashedWith() == PasswordPolicy::HASHED_WITH_SHA1) {
          DB::execute('UPDATE ' . TABLE_PREFIX . 'users SET password = ?, password_hashed_with = ? WHERE id = ?', Authentication::getPasswordPolicy()->hashPassword($password, PasswordPolicy::HASHED_WITH_PBKDF2), PasswordPolicy::HASHED_WITH_PBKDF2, $this->getId());

          AngieApplication::cache()->removeByObject($this);
        } // if

        return true;
      } else {
        return false;
      } // if
    } // isCurrentPassword

    /**
     * Returns true if password for this account is expired
     *
     * @return boolean
     */
    function isPasswordExpired() {
      return $this->getPasswordExpiresOn() instanceof DateValue && $this->getPasswordExpiresOn()->getTimestamp() <= DateValue::now()->getTimestamp();
    } // isPasswordExpired

    /**
     * Set password for expiry in N months
     *
     * @param integer $months
     * @param boolean $save
     * @return DateValue
     */
    function expirePasswordIn($months, $save = false) {
      if($months > 0) {
        $expire_on = DateValue::makeFromString("+$months months");
      } else {
        $expire_on = null;
      } // if

      $this->setPasswordExpiresOn($expire_on);

      if($save) {
        $this->save();
      } // if

      return $expire_on;
    } // expirePasswordIn
    
    // ---------------------------------------------------
    //  Date and Time Formats
    // ---------------------------------------------------
    
    /**
     * Cached date format value
     *
     * @var string
     */
    private $date_format = false;
    
    /**
     * Return date format
     *
     * @return string
     */
    function getDateFormat() {
      if($this->date_format === false) {
        $this->date_format = ConfigOptions::getValueFor('format_date', $this, FORMAT_DATE);
      } // if
      
      return $this->date_format;
    } // getDateFormat
    
    /**
     * Cached time format value
     *
     * @var string
     */
    private $time_format = false;
    
    /**
     * Return time format
     *
     * @return string
     */
    function getTimeFormat() {
      if($this->time_format === false) {
        $this->time_format = ConfigOptions::getValueFor('format_time', $this, FORMAT_TIME);
      } // if
      
      return $this->time_format;
    } // getTimeFormat
    
    /**
     * Cached date time format value
     *
     * @var string
     */
    private $date_time_format = false;
    
    /**
     * Return date time format
     *
     * @return string
     */
    function getDateTimeFormat() {
      if($this->date_time_format === false) {
        $this->date_time_format = $this->getDateFormat() . ' ' . $this->getTimeFormat();
      } // if
      
      return $this->date_time_format;
    } // getDateTimeFormat
    
    // ---------------------------------------------------
    //  Mailing
    // ---------------------------------------------------
    
    /**
     * Cached mailing method value
     * 
     * @var string
     */
    private $mailing_method = false;
    
    /**
     * Return prefered mailing method for this user
     * 
     * @return string
     */
    function getMailingMethod() {
    	if($this->mailing_method === false) {
    		$this->mailing_method = $this->isLoaded() ? ConfigOptions::getValueFor('mailing_method', $this) : AngieApplication::mailer()->getDefaultMailingMethod();
    	} // if
    	
    	return $this->mailing_method;
    } // getMailingMethod
    
    /**
     * Set mailing method for this user
     * 
     * @param string $value
     */
    function setMailingMethod($value) {
    	ConfigOptions::setValueFor('mailing_method', $this, $value);
    	$this->mailing_method = $value;
    } // setMailingMethod
    
    // ---------------------------------------------------
    //  Access level indicators
    // ---------------------------------------------------
    
    /**
     * Returns true if this particular account is active
     *
     * @return boolean
     */
    function isActive() {
      return $this->getState() > STATE_ARCHIVED;
    } // isActive
    
    /**
     * Returns true if this user is API user
     * 
     * @return boolean
     * @throws Exception
     */
    function isApiUser() {
      return $this->isAdministrator() || $this->getSystemPermission('can_use_api');
    } // isApiUser
    
    /**
     * Returns true if this user is feed user
     * 
     * @return boolean
     */
    function isFeedUser() {
      return $this->isApiUser();
    } // isFeedUser

    /**
     * Return true if this instance is a member
     *
     * @param bool $strict
     * @return bool
     */
    function isMember($strict = false) {
      return $strict ?
        get_class($this) == 'Member' : // Strictly check for Member class
        $this instanceof Member;       // Return true in case of all classes that extend member
    } // isMember
    
    /**
     * Returns true only if this person has administration permissions
     *
     * @return boolean
     */
    function isAdministrator() {
      return $this instanceof Administrator;
    } // isAdministrator

    /**
     * Not used in activeCollab
     *
     * @return bool
     * @throws NotImplementedError
     */
    function isManager() {
      throw new NotImplementedError(__METHOD__);
    } // isManager

    /**
     * Highest level of permissions (above administrator, not used in activeCollab)
     *
     * @return bool
     * @throws NotImplementedError
     */
    function isOwner() {
      throw new NotImplementedError(__METHOD__);
    } // isOwner
    
    /**
     * Check if this user is the last administrator
     *
     * @return boolean
     */
    function isLastAdministrator() {
      return Users::isLastAdministrator($this);
    } // isLastAdministrator
    
    /**
     * By default, no user is financial manager
     * 
     * @return boolean
     */
    function isFinancialManager() {
      return false;
    } // isFinancialManager

    /**
     * Returns true if this user can manage accounts of other users
     *
     * @return bool
     */
    function isPeopleManager() {
      return $this->isAdministrator();
    } // isPeopleManager

    /**
     * Return true if this user can manage trash
     *
     * @return bool
     * @throws Exception
     */
    function canManageTrash() {
      return $this->isAdministrator() || $this->getSystemPermission('can_manage_trash');
    } // canManageTrash

    /**
     * Returns true if this user has access to reports section
     *
     * @return boolean
     */
    function canUseReports() {
      return $this->isAdministrator();
    } // canUseReports

    // ---------------------------------------------------
    //  Permissions, for this Instance
    // ---------------------------------------------------

    /**
     * Returns true if $user can see this account
     *
     * @param User $user
     * @return boolean
     */
    function canView(User $user) {
      if($user instanceof User) {
        if($this->getState() == STATE_TRASHED) {
          return $user->canManageTrash();
        } // if

        return true;
      } // if

      return false;
    } // canView

    /**
     * Returns true if $user can (re)send welcome message
     *
     * @param User $user
     * @return boolean
     */
    function canSendWelcomeMessage(User $user) {
      return $user instanceof User && $user->isAdministrator();
    } // canSendWelcomeMessage

    /**
     * Check if $user can set this user as invited
     *
     * @param User $user
     * @return boolean
     */
    function canSetAsInvited(User $user) {
      return $user instanceof User && $user->isAdministrator();
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

      return false;
    } // canLoginAs

    /**
     * Returns true if this user can see who is online
     *
     * @return bool
     */
    function canSeeWhoIsOnline() {
      return true;
    } // canSeeWhoIsOnline
    
    // ---------------------------------------------------
    //  Visibility
    // ---------------------------------------------------
    
    /**
     * Returns true if this user can see $object
     *
     * @param IVisibility $object
     * @return boolean
     * @throws InvalidInstanceError
     */
    function canSee(IVisibility $object) {
      if($object instanceof IVisibility) {
        return $object->getVisibility() >= $this->getMinVisibility();
      } else {
        throw new InvalidInstanceError('object', $object, 'IVisibility');
      } // if
    } // canSee
    
    /**
     * Returns true if this user have permissions to see private objects
     *
     * @return boolean
     */
    function canSeePrivate() {
      return $this->isMember();
    } // canSeePrivate
    
    /**
     * Returns min visibility that this particular user can see
     * 
     * Default implementation lets user see all objects. Override in User class 
     * to implement visibility filtering that you want
     *
     * @return integer
     */
    function getMinVisibility() {
      return $this->canSeePrivate() ? VISIBILITY_PRIVATE : VISIBILITY_NORMAL;
    } // getMinVisibility
    
    // ---------------------------------------------------
    //  Interface implementation
    // ---------------------------------------------------
    
    /**
     * Return object context domain
     * 
     * @return string
     */
    function getObjectContextDomain() {
      return 'users';
    } // getObjectContextDomain
    
    /**
     * Return object context path
     * 
     * @return string
     */
    function getObjectContextPath() {
      return 'users/' . $this->getId();
    } // getObjectContextPath

	  /**
	   * Cached security log helper instance
	   *
	   * @var ISecurityLogImplementation
	   */
	  private $security_log = false;

	  /**
	   * Return security log helper instance
	   *
	   * @return ISecurityLogImplementation
	   */
	  function securityLog() {
		  if($this->security_log === false) {
			  $this->security_log = new ISecurityLogImplementation($this);
		  } // if

		  return $this->security_log;
	  } // accessLog
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'user';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('user_id' => $this->getId());
    } // getRoutingContextParams

    /**
     * Cached inspector instance
     *
     * @var IUserInspectorImplementation
     */
    private $inspector = false;

    /**
     * Return inspector helper instance
     *
     * @return IUserInspectorImplementation
     */
    function inspector() {
      if($this->inspector === false) {
        $this->inspector = new IUserInspectorImplementation($this);
      } // if

      return $this->inspector;
    } // inspector

    /**
     * UserAvatar implementation instance for this object
     *
     * @var IUserAvatarImplementation
     */
    private $avatar;

    /**
     * Return subtasks implementation for this object
     *
     * @return IUserAvatarImplementation
     */
    function avatar() {
      if(empty($this->avatar)) {
        $this->avatar = new IUserAvatarImplementation($this);
      } // if

      return $this->avatar;
    } // avatar
    
    /**
     * Cached history helper
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
        $this->history = new IHistoryImplementation($this, array('type', 'first_name', 'last_name', 'email', 'password'));
      } // if
      
      return $this->history;
    } // history
    
    /**
     * Cached search helper instance
     *
     * @var IUserSearchItemImplementation
     */
    private $search = false;
    
    /**
     * Return search helper instance
     * 
     * @return IUserSearchItemImplementation
     */
    function &search() {
      if($this->search === false) {
        $this->search = new IUserSearchItemImplementation($this);
      } // if
      
      return $this->search;
    } // search
    
    /**
     * Cached user reminders helper instance
     *
     * @var IUserRemindersImplementation
     */
    private $reminders = false;
    
    /**
     * Return user reminders helper instance
     * 
     * @return IUserRemindersImplementation
     * @throws NotImplementedError
     */
    function reminders() {
      if(AngieApplication::isFrameworkLoaded('reminders')) {
        if($this->reminders === false) {
          $this->reminders = new IUserRemindersImplementation($this);
        } // if
        
        return $this->reminders;
      } else {
        throw new NotImplementedError(__METHOD__);
      } // if
    } // reminders

    /**
     * State helper instance
     *
     * @var IUserStateImplementation
     */
    private $state = false;

    /**
     * Return state helper instance
     *
     * @return IUserStateImplementation
     */
    function state() {
      if($this->state === false) {
        $this->state = new IUserStateImplementation($this);
      } // if

      return $this->state;
    } // state

    /**
     * Homescreen helper instance
     *
     * @var IHomescreenImplementation
     */
    private $homescreen = false;

    /**
     * Return homescreen helper instance
     *
     * @return IHomescreenImplementation
     */
    function homescreen() {
      if($this->homescreen === false) {
        $this->homescreen = new IHomescreenImplementation($this);
      } // if

      return $this->homescreen;
    } // homescreen

    /**
     * Cached favorites instance
     *
     * @var IUserFavoritesImplementation
     */
    private $favorites = false;

    /**
     * Return favorites helper
     *
     * @return IUserFavoritesImplementation
     */
    function favorites() {
      if($this->favorites === false) {
        $this->favorites = new IUserFavoritesImplementation($this);
      } // if

      return $this->favorites;
    } // favorites
    
    // ---------------------------------------------------
    //  URLs
    // ---------------------------------------------------
    
    /**
     * Get edit password URL
     *
     * @return string
     */
    function getEditPasswordUrl() {
      return Router::assemble($this->getRoutingContext() . '_edit_password', $this->getRoutingContextParams());
    } // getEditPasswordUrl

    /**
     * Return edit profile URL
     *
     * @return string
     */
    function getEditProfileUrl() {
      return Router::assemble($this->getRoutingContext() . '_edit_profile', $this->getRoutingContextParams());
    } // getEditProfileUrl

    /**
     * Return edit settings URL
     *
     * @return string
     */
    function getEditSettingsUrl() {
      return Router::assemble($this->getRoutingContext() . '_edit_settings', $this->getRoutingContextParams());
    } // getEditSettingsUrl
    
    /**
     * Return API subscriptions URL
     * 
     * @return string
     */
    function getApiSubscriptionsUrl() {
      return Router::assemble($this->getRoutingContext() . '_api_client_subscriptions', $this->getRoutingContextParams());
    } // getApiSubscriptionsUrl
    
    /**
     * Return add API subscription URL
     * 
     * @return string
     */
    function getAddApiSubscriptionUrl() {
      return Router::assemble($this->getRoutingContext() . '_api_client_subscriptions_add', $this->getRoutingContextParams());
    } // getAddApiSubscriptionUrl
    
    /**
     * Return export vCard URL
     *
     * @return string
     */
    function getExportVcardUrl() {
      return Router::assemble($this->getRoutingContext() . '_export_vcard', $this->getRoutingContextParams());
    } // getExportVcardUrl

    /**
     * Return user activities RSS feed
     *
     * @param User $user
     * @return string
     */
    function getRssUrl(User $user) {
      return Router::assemble($this->getRoutingContext() . '_activity_log_rss', $this->getRoutingContextParams());
    } // getRssUrl

    /**
     * Return send welcome message URL
     *
     * @return string
     */
    function getSendWelcomeMessageUrl() {
      return Router::assemble($this->getRoutingContext() . '_send_welcome_message', $this->getRoutingContextParams());
    } // getSendWelcomeMessageUrl

    /**
     * Return set as invited URL
     *
     * @return string
     */
    function getSetAsInvitedUrl() {
      return Router::assemble($this->getRoutingContext() . '_set_as_invited', $this->getRoutingContextParams());
    } // getSetAsInvitedUrl

    /**
     * Return login as URL
     *
     * @return string
     */
    function getLoginAsUrl() {
      return Router::assemble($this->getRoutingContext() . '_login_as', $this->getRoutingContextParams());;
    } // getLoginAsUrl
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Check if $user can update this profile
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      if($user instanceof User) {
        return $user->is($this) || $user->isAdministrator();
      } else {
        return false;
      } // if
    } // canEdit
    
    /**
     * Returns true if $user can change password of this user
     * 
     * @param User $user
     * @return boolean
     */
    function canChangePassword(User $user) {
      if($user instanceof User) {
        return $user->is($this) || $user->isAdministrator();
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
    	return $user->isAdministrator() || $user->isPeopleManager();
    } // canChangeRole
    
    /**
     * Returns true if $user can add API subscription for this user
     * 
     * @param User $user
     * @return boolean
     */
    function canAddApiSubscription(User $user) {
      return $this->canEdit($user);
    } // canAddApiSubscription

    /**
     * Can user see this user subscription
     *
     * @param User $user
     * @return bool
     */
    function canSeeApiSubscription(User $user) {
      return $this->canEdit($user) && $this->isApiUser();
    } // canSeeApiSubscription
    
    // ---------------------------------------------------
    //  Authentication
    // ---------------------------------------------------
    
    /**
     * Raw password value before it is encoded
     *
     * @var string
     */
    protected $raw_password = false;
    
    /**
     * Set field value
     *
     * @param string $field
     * @param mixed $value
     * @return mixed
     * @throws InvalidParamError
     */
    function setFieldValue($field, $value) {
      if($field == 'password' && !$this->isLoading()) {
        $this->raw_password = (string) $value; // Remember raw password

        $value = Authentication::getPasswordPolicy()->hashPassword($value, PasswordPolicy::HASHED_WITH_PBKDF2); // Hash password with PBKDF2

        $this->setPasswordHashedWith(PasswordPolicy::HASHED_WITH_PBKDF2);
        $this->expirePasswordIn(Authentication::getPasswordPolicy()->getAutoExpire());
      } // if
      
      return parent::setFieldValue($field, $value);
    } // setFieldValue

    // ---------------------------------------------------
    //  Feed functions
    // ---------------------------------------------------

    /**
     * Returns user feed token
     *
     * @var boolean $formatted
     * @return string
     * @throws Exception
     */
    function getFeedToken($formatted = true) {
      if ($this->isFeedUser()) {
        $feed_client_subscription = FeedClientSubscriptions::findByUser($this);

        if (!($feed_client_subscription instanceof FeedClientSubscription)) {
          $feed_client_subscription = new FeedClientSubscription();
          $feed_client_subscription->setUser($this);
          try {
            DB::beginWork('Creating new feed client subscription @ ' . __CLASS__);

            $feed_client_subscription->setAttributes(array(
              'client_name' => $this->getName() . ' ' . lang('Feed Token'),
              'is_read_only' => 1
            ));
            $feed_client_subscription->setToken(ApiClientSubscriptions::generateToken());
            $feed_client_subscription->setIsEnabled(true);

            $feed_client_subscription->save();

            DB::commit('New feed client subscription created @ ' . __CLASS__);

          } catch(Exception $e) {
            DB::rollback('Failed to create new feed client subscription @ ' . __CLASS__);
            throw $e;
          } // try
        } // if
        return $formatted ? $feed_client_subscription->getFormattedToken() : $feed_client_subscription->getToken();
      } else {
        throw new Exception("User doesn't have permission to use feed");
      } //if
    } // getFeedToken
    
    // ---------------------------------------------------
    //  Email
    // ---------------------------------------------------

    /**
     * Return array of additional email addresses
     *
     * @return null|array
     */
    function getAdditionalEmailAddresses() {
      return DB::executeFirstColumn('SELECT email FROM ' . TABLE_PREFIX . 'user_addresses WHERE user_id = ? ORDER BY email', $this->getId());
    } // getAdditionalEmailAddresses

    /**
     * Set additional email addresses
     *
     * @param array|null $addresses
     * @throws InvalidParamError
     * @throws Exception
     */
    function setAdditionalEmailAddresses($addresses) {
      try {
        DB::beginWork('Set additional addresses @ ' . __CLASS__);

        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'user_addresses WHERE user_id = ?', $this->getId());

        if($addresses && is_foreachable($addresses)) {
          $to_add = array();

          $primary_email_address = strtolower($this->getEmail());

          foreach($addresses as $address) {
            $validate_address = strtolower(trim($address));

            if(empty($validate_address) || $validate_address == $primary_email_address || in_array($validate_address, $to_add)) {
              continue;
            } // if

            if(!is_valid_email($validate_address)) {
              throw new InvalidParamError('to_add', $validate_address, 'Invalid email address');
            } // if

            if(Users::isEmailAddressInUse($validate_address, $this)) {
              throw new InvalidParamError('to_add', $validate_address, 'Email address in use');
            } // if

            $to_add[] = $validate_address;
          } // foreach

          if(count($to_add)) {
            $batch = new DBBatchInsert(TABLE_PREFIX . 'user_addresses', array('user_id', 'email'));

            foreach($to_add as $address) {
              $batch->insert($this->getId(), $address);
            } // foreach

            $batch->done();
          } // if
        } // if

        DB::commit('Additional addresses set @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to set additional addresses @ ' . __CLASS__);
        throw $e;
      } // try
    } // setAdditionalEmailAddresses
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------

    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if($this->validatePresenceOf('email', 5)) {
        if(is_valid_email($this->getEmail())) {
          if($this->isNew()) {
            $in_use = Users::isEmailAddressInUse($this->getEmail());
          } else {
            $in_use = Users::isEmailAddressInUse($this->getEmail(), $this->getId());
          } // if

          if($in_use) {
            $errors->addError(lang('Email address you provided is already in use'), 'email');
          } // if
        } else {
          $errors->addError(lang('Email value is not valid'), 'email');
        } // if
      } else {
        $errors->addError(lang('Email value is required'), 'email');
      } // if

      if($this->isNew() || $this->raw_password !== false) {
        Authentication::getPasswordPolicy()->validateUserPassword($this->raw_password, $errors);
      } // if

      if(!$this->validatePresenceOf('type')) {
        $errors->addError(lang('Role type is required'), 'type');
      } // if
    } // validate
    
    /**
     * Save user into the database
     *
     * @return boolean
     */
    function save() {
      $modified_fields = $this->getModifiedFields();

      if($this->isNew()) {
        $this->expirePasswordIn(Authentication::getPasswordPolicy()->getAutoExpire());
      } // if
      
      parent::save();
      
      if(in_array('type', $modified_fields)) {
        AngieApplication::cache()->clearModelCache();
      } // if
    } // save
    
    /**
     * Delete from database
     *
     * @return boolean
     * @throws Exception
     */
    function delete() {
      try {
        DB::beginWork('Removing user and cleaning up @ ' . __CLASS__);
        
        parent::delete();

        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'user_addresses WHERE user_id = ?', $this->getId());
        
        ConfigOptions::removeValuesFor($this);
        
        if(AngieApplication::isFrameworkLoaded('avatar')) {
          $this->avatar()->remove();
        } // if
        
        if(AngieApplication::isFrameworkLoaded('assignments')) {
          Assignments::deleteByUser($this);
        } // if
        
        if(AngieApplication::isFrameworkLoaded('subscriptions')) {
          Subscriptions::deleteByUser($this);
        } // if
        
        if(AngieApplication::isFrameworkLoaded('favorites')) {
          Favorites::deleteByUser($this);
        } // if
        
        if(AngieApplication::isFrameworkLoaded('reminders')) {
          Reminders::deleteByUser($this);
        } // if
        
        $cleanup = array();
        EventsManager::trigger('on_user_cleanup', array(&$cleanup));
        
        if(is_foreachable($cleanup)) {
          foreach($cleanup as $table_name => $fields) {
            foreach($fields as $field) {
              $condition = '';
              if(is_array($field)) {
                $id_field = array_var($field, 'id');
                $name_field = array_var($field, 'name');
                $email_field = array_var($field, 'email');
                $condition = array_var($field, 'condition');
              } else {
                $id_field = $field . '_id';
                $name_field = $field . '_name';
                $email_field = $field . '_email';
              } // if
              
              if($condition) {
                DB::execute('UPDATE ' . TABLE_PREFIX . "$table_name SET $id_field = 0, $name_field = ?, $email_field = ? WHERE $id_field = ? AND $condition", $this->getName(), $this->getEmail(), $this->getId());
              } else {
                DB::execute('UPDATE ' . TABLE_PREFIX . "$table_name SET $id_field = 0, $name_field = ?, $email_field = ? WHERE $id_field = ?", $this->getName(), $this->getEmail(), $this->getId());
              } // if
            } // foreach
          } // foreach
        } // if
        
        DB::commit('User removed and data has been cleaned up @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to remove user or clean up the data @ ' . __CLASS__);
        
        throw $e;
      } // try
    } // delete

    /**
     * Force delete
     */
    function forceDelete() {
      try {
        DB::beginWork('Deleting user @ ' . __CLASS__);

        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'user_addresses WHERE user_id = ?', $this->getId());

        ConfigOptions::removeValuesFor($this);

        if(AngieApplication::isFrameworkLoaded('avatar')) {
          $this->avatar()->remove();
        } // if

        if(AngieApplication::isFrameworkLoaded('assignments')) {
          Assignments::deleteByUser($this);
        } // if

        if(AngieApplication::isFrameworkLoaded('subscriptions')) {
          Subscriptions::deleteByUser($this);
        } // if

        if(AngieApplication::isFrameworkLoaded('favorites')) {
          Favorites::deleteByUser($this);
        } // if

        if(AngieApplication::isFrameworkLoaded('reminders')) {
          Reminders::deleteByUser($this);
        } // if

        parent::forceDelete();

        DB::commit('User deleted @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to delete user @ ' . __CLASS__);
        throw $e;
      } // try
    } // forceDelete
    
  }