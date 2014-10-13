<?php

  /**
   * ProjectRequest class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ProjectRequest extends BaseProjectRequest implements IRoutingContext, IComments, IObjectContext, IAttachments, ISubscriptions, IProjectBasedOn, IHistory, IAccessLog {
    
    // Statuses
    const STATUS_NEW = 0;
    const STATUS_REPLIED = 1;
    const STATUS_CLOSED = 2;

    const CLIENT_TYPE_NEW = 'new_client';
    const CLIENT_TYPE_EXISTING = 'existing_client';

    /**
     * List of rich text fields
     *
     * @var array
     */
    protected $rich_text_fields = array('body');
    
    /**
     * Return proper type name in user's language
     *
     * @param boolean $lowercase
     * @param Language $language
     * @return string
     */
    function getVerboseType($lowercase = false, $language = null) {
      return $lowercase ? lang('project request', null, true, $language) : lang('Project Request', null, true, $language);
    } // getVerboseType

    /**
     * Open a project request
     *
     * @param User $user
     * @param bool $save
     * @return bool
     */
    function open(User $user, $save = true) {
      if($this->canChangeStatus($user)) {
        $last_comment = $this->comments()->getLast($user);
        
        if($last_comment instanceof Comment && ProjectRequests::canManage($last_comment->getCreatedBy())) {
          $this->setStatus(ProjectRequest::STATUS_REPLIED);
        } else {
          $this->setStatus(ProjectRequest::STATUS_NEW);
        } // if
        
        $this->setClosedOn(null);
        $this->setClosedBy(null);
        
        if($save) {
          $this->save();
        } // if

        return true;
      } else {
        return false;
      } // if
    } // open

    /**
     * Close project request
     *
     * @param User $user
     * @param bool $save
     * @return bool
     */
    function close(User $user, $save = true) {
      if($this->canChangeStatus($user)) {
        $this->setStatus(ProjectRequest::STATUS_CLOSED);
        $this->setClosedBy($user);
        $this->setClosedOn(new DateTimeValue());
        
        if($save) {
          $this->save();
        } // if

        return true;
      } else {
        return false;
      } // if
    } // close
    
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
      
      $result['status'] = $this->getStatus();
      $result['is_closed'] = $this->getStatus() == ProjectRequest::STATUS_CLOSED;
      
      $result['closed_on'] = $this->getClosedOn();
      $result['closed_by'] = $this->getClosedBy() instanceof IUser ? $this->getClosedBy()->describe($user) : null;
      $result['taken_by'] = $this->getTakenBy() instanceof IUser ? $this->getTakenBy()->describe($user) : null;
      
      $result['custom_fields'] = array();

      $result['public_url'] = $this->getStatus() !== ProjectRequest::STATUS_CLOSED ? $this->getPublicUrl() : null;
      
      $result['created_by'] = $this->getCreatedBy();
      $result['created_by_company'] = $this->getCompany();
      if (!$this->getCompany() instanceof Company) {
        $result['created_by_company'] = $this->getCompanyName();
      } // if
      
      $custom_fields = $this->getCustomFields();
      
      if (is_foreachable($custom_fields)) {
	      foreach($custom_fields as $field_name => $field_settings) {
          $result['custom_fields'][$field_name] = array(
            'label' => $field_settings['label'], 
            'value' => $this->getCustomFieldValue($field_name), 
          );
	      } // foreach
      } // if
      
      $result['urls']['public'] = $this->getPublicUrl();
      
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

      $result['status'] = $this->getStatus();
      $result['is_closed'] = $this->getStatus() == ProjectRequest::STATUS_CLOSED;

      $result['closed_on'] = $this->getClosedOn();
      $result['closed_by'] = $this->getClosedBy() instanceof IUser ? $this->getClosedBy()->describeForApi($user) : null;
      $result['taken_by'] = $this->getTakenBy() instanceof IUser ? $this->getTakenBy()->describeForApi($user) : null;

      $result['custom_fields'] = array();

      $result['public_url'] = $this->getStatus() !== ProjectRequest::STATUS_CLOSED ? $this->getPublicUrl() : null;

      $result['created_by'] = $this->getCreatedBy();
      $result['created_by_company'] = $this->getCompany();
      if (!$this->getCompany() instanceof Company) {
        $result['created_by_company'] = $this->getCompanyName();
      } // if

      $custom_fields = $this->getCustomFields();

      if (is_foreachable($custom_fields)) {
        foreach($custom_fields as $field_name => $field_settings) {
          $result['custom_fields'][$field_name] = array(
            'label' => $field_settings['label'],
            'value' => $this->getCustomFieldValue($field_name),
          );
        } // foreach
      } // if

      $result['urls']['public'] = $this->getPublicUrl();

      return $result;
    } // describeForApi

    // ---------------------------------------------------
    //  Notify
    // ---------------------------------------------------

    /**
     * Notify representatives that new request has been submitted
     */
    function notifyRepresentatives() {
      $representative_ids = ConfigOptions::getValue('project_requests_notify_user_ids');

      if($representative_ids && is_foreachable($representative_ids)) {
        $representatives = Users::findByIds($representative_ids);
        
        if($representatives) {
          AngieApplication::notifications()
            ->notifyAbout('system/new_project_request_for_representatives', $this)
            ->sendToUsers($representatives);
        } // if
      } // if
    } // notifyRepresentatives
    
    /**
     * Notify client that their request has been received
     */
    function notifyClient() {
      AngieApplication::notifications()
        ->notifyAbout('system/new_project_request_for_client', $this)
        ->sendToUsers($this->getCreatedBy(), true);
    } // notifyClient
  	
  	// ---------------------------------------------------
  	//  Getters and Setters
  	// ---------------------------------------------------
  	
    /**
     * Return custom field and custom field values
     * 
     * @return array
     */
    function getCustomFields() {
      $result = array();
      
      foreach(ProjectRequests::getCustomFields() as $field_name => $field_settings) {
        if($field_settings['enabled']) {
          $result[$field_name] = array(
            'label' => $field_settings['name'], 
            'value' => $this->getCustomFieldValue($field_name), 
          );
        } // if
      } // foreach
      
      return $result;
    } // getCustomFields

    /**
     * Prepare data from custom fields for project overview
     *
     * @return string
     */
    function getVerboseCustomFields() {
      $verbose_data = "";
      
      $custom_fields = $this->getCustomFields();
      if (is_foreachable($custom_fields)) {
        $verbose_data .= "<ul>";
        foreach($custom_fields as $custom_field) {
          $verbose_data .= "<li><b>".$custom_field['label'].":</b> ".$custom_field['value']."</li>";
        } // foreach
        $verbose_data .= "</ul>";
      } // if

      return $verbose_data;
    } // getVerboseCustomFields
  	
  	/**
  	 * Return value of provided custom field
  	 *
  	 * @param string $custom_field
  	 * @return string
  	 */
  	function getCustomFieldValue($custom_field) {
  		$method_name = 'get' . Inflector::camelize($custom_field);
  		return $this->$method_name();
  	} // getCustomFieldValue
  	
  	/**
     * Return project request read/unread icon URL
     *
     * @return string
     */
    function getIconUrl() {
      if($this->getIsRead()) {
        return AngieApplication::getImageUrl('disscusion_read.gif', DISCUSSIONS_MODULE);
      } else {
      	return AngieApplication::getImageUrl('disscusion_unread.gif', DISCUSSIONS_MODULE);
      } // if
    } // getIconUrl
    
    /**
     * Cached created by instance
     *
     * @var AnonymousUser
     */
    private $created_by = false;
    
    /**
     * Return user who created this request
     * 
     * @return AnonymousUser
     */
    function getCreatedBy() {
      if($this->created_by === false) {
        if ($this->getCreatedById()) {
          $this->created_by = Users::findById($this->getCreatedById());
        } // if

        if (!$this->created_by instanceof User || $this->created_by->getState() === 0) {
          $this->created_by = new AnonymousUser($this->getCreatedByName(), $this->getCreatedByEmail());
        } // if
      } // if
      
      return $this->created_by;
    } // getCreatedBy
    
    /**
     * Set created by value
     * 
     * @param IUser $by
     * @return IUser
     */
    function setCreatedBy(IUser $by) {
      $this->setCreatedById($by->getId());
      $this->setCreatedByName($by->getDisplayName());
      $this->setCreatedByEmail($by->getEmail());
      
      $this->created_by = $by;
      return $by;
    } // setCreatedBy

    /**
     * Client company instance
     *
     * @var Company
     */
    private $company = false;

    /**
     * Return client company
     *
     * @return Company
     */
    function getCompany() {
      if($this->company === false) {
        $company = Companies::findById($this->getCreatedByCompanyId());
        if ($company instanceof Company && $company->getState() === 0) {
          $this->company = null;
        } else {
          $this->company = $company;
        } // if
      } // if

      return $this->company;
    } // getCompany

    /**
     * Get client company name
     *
     * @return string
     */
    function getCompanyName() {
      $company = $this->getCompany();

      if ($company instanceof Company) {
        return $company->getName();
      } else {
        return $this->getFieldValue('created_by_company_name');
      } // if
    } // getCompanyName

    /**
     * Get client company address
     *
     * @return string
     */
    function getCompanyAddress() {
      $company = $this->getCompany();

      if ($company instanceof Company) {
        $request_company_address = $this->getFieldValue('created_by_company_address');
        $company_address = $company->getConfigValue('office_address');

        return $request_company_address !== $company_address ? $request_company_address : $company_address;
      } else {
        return $this->getFieldValue('created_by_company_address');
      } // if
    } // getCompanyAddress

    /**
     * Set client information based on data provided
     *
     * @param string $client_type
     * @param array $client_data
     * @param array $new_client_data
     * @throws InvalidInstanceError
     */
    function setClientInfo($client_type, $client_data, $new_client_data) {
      $client = null;

      switch ($client_type) {
        case self::CLIENT_TYPE_NEW:
          $client = new AnonymousUser($new_client_data['created_by_name'], $new_client_data['created_by_email']);
          $this->setCreatedByCompanyId(0);
          $this->setCreatedByCompanyName($new_client_data['created_by_company_name']);
          $this->setCreatedByCompanyAddress($new_client_data['created_by_company_address']);
          break;
        case self::CLIENT_TYPE_EXISTING:
          $client = Users::findById($client_data['created_by_id']);
          $company = Companies::findById($client_data['created_by_company_id']);
          $this->setCreatedByCompanyId($client_data['created_by_company_id']);
          $this->setCreatedByCompanyName($company->getName());
          $this->setCreatedByCompanyAddress($client_data['created_by_company_address']);
          break;
      } // switch

      if ($client instanceof IUser) {
        $this->setCreatedBy($client);
      } else {
        throw new InvalidInstanceError("client", $client, "IUser");
      } // if
    } // setClientInfo
    
    /**
     * Closed by instance
     *
     * @var User
     */
    private $closed_by = false;
    
    /**
     * Return user who closed $this project request
     *
     * This function may return registred user only
     *
     * @return User
     */
    function getClosedBy() {
      if($this->closed_by === false) {
        $this->closed_by = Users::findById($this->getClosedById());
      } // if
      
      return $this->closed_by;
    } // getClosedBy
    
    /**
     * Set person who closed $this object
     *
     * $closed_by can be an instance of User / AnonymousUser class or null
     *
     * @param mixed $closed_by
     * @return mixed
     */
    function setClosedBy($closed_by) {
      if($closed_by === null) {
        $this->setClosedById(0);
        $this->setClosedByName('');
        $this->setClosedByEmail('');
      } elseif($closed_by instanceof IUser) {
        $this->setClosedById($closed_by->getId());
        $this->setClosedByName($closed_by->getDisplayName());
        $this->setClosedByEmail($closed_by->getEmail());
      } else {
        throw new InvalidInstanceError('closed_by', $closed_by, 'IUser');
      } // if

      $this->closed_by = $closed_by;
      
      return $closed_by;
    } // setClosedBy
    
    /**
     * Cached taken by instance
     *
     * @var IUser
     */
    private $taken_by = false;
    
    /**
     * Return taken by instance
     * 
     * @return IUser
     */
    function getTakenBy() {
      if($this->taken_by === false) {
        if($this->getTakenById()) {
          $this->taken_by = Users::findById($this->getTakenById()); 
        } elseif($this->getTakenByEmail()) {
          $this->taken_by = new AnonymousUser($this->getTakenByName(), $this->getTakenByEmail());
        } else {
          $this->taken_by = null;
        } // if
      } // if
      
      return $this->taken_by;
    } // getTakenBy
    
    /**
     * Set taken by
     * 
     * @param IUser $by
     * @return IUser
     */
    function setTakenBy($by) {
      if($by === null) {
        $this->setTakenById(0);
        $this->setTakenByName('');
        $this->setTakenByEmail('');
      } elseif($by instanceof IUser) {
        $this->setTakenById($by->getId());
        $this->setTakenByName($by->getDisplayName());
        $this->setTakenByEmail($by->getEmail());
      } else {
        throw new InvalidInstanceError('by', $by, 'IUser');
      } // if
      
      $this->taken_by = $by;
      
      return $by;
    } // setTakenBy
    
    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'project_request';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('project_request_id' => $this->getId());
    } // getRoutingContextParams
    
    /**
     * Cached comment interface instance
     *
     * @var IProjectRequestCommentsImplementation
     */
    private $comments;
    
    /**
     * Return project request comments interface instance
     *
     * @return IProjectRequestCommentsImplementation
     */
    function &comments() {
      if(empty($this->comments)) {
        $this->comments = new IProjectRequestCommentsImplementation($this);
      } // if
      return $this->comments;
    } // comments
    
    /**
     * Cached attachment manager instance
     *
     * @var ProjectRequestAttachment
     */
    private $attachments;
    
    /**
     * Return attachments manager instance for this object
     *
     * @return IProjectRequestAttachmentsImplementation
     */
    function &attachments() {
      if(empty($this->attachments)) {
        $this->attachments = new IProjectRequestAttachmentsImplementation($this);
      } // if
      
      return $this->attachments;
    } // attachments
    
    /**
     * Subscriptions helper instance
     *
     * @var IProjectRequestSubscriptionsImplementation
     */
    private $subscriptions;
    
    /**
     * Return subscriptions helper for this object
     *
     * @return IProjectRequestSubscriptionsImplementation
     */
    function &subscriptions() {
      if(empty($this->subscriptions)) {
        $this->subscriptions = new IProjectRequestSubscriptionsImplementation($this);
      } // if
      
      return $this->subscriptions;
    } // subscriptions
    
    /**
     * Return object domain
     * 
     * @return string
     */
    function getObjectContextDomain() {
      return 'project-request';
    } // getContextDomain
    
    /**
     * Return object path
     * 
     * @return string
     */
    function getObjectContextPath() {
      return 'project-request/' . $this->getId();
    } // getContextPath
    
    /**
     * Cached history implementation instance
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
        $this->history = new IHistoryImplementation($this, array('status', 'created_by_company_name', 'created_by_company_address', 'custom_field_1', 'custom_field_2', 'custom_field_3', 'custom_field_4', 'custom_field_5', 'is_locked', 'taken_by_id'));
      } // if
      
      return $this->history;
    } // history

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
    
    // ------------------------------------------------------------
    //  Workaround that meets comments implementation prerequisites
    // ------------------------------------------------------------
    
    /**
     * Return project object visibility
     *
     * @return integer
     */
    function getVisibility() {
    	return VISIBILITY_NORMAL;
    } // getVisibility
    
    /**
     * Return project request state
     *
     * @return integer
     */
    function getState() {
    	return STATE_VISIBLE;
    } // getState
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Return true if $user can access $this project request
     *
     * @param User $user
     * @return boolean
     */
    function canView(User $user) {
      if($user instanceof Client) {
        return $this->getCreatedByCompanyId() == $user->getCompanyId();
      } else {
        return ProjectRequests::canManage($user);
      } // if
    } // canView
    
    /**
     * Returns true if user can take this request
     * 
     * @param User $user
     * @return boolean
     */
    function canTake(User $user) {
      if($user instanceof User) {
        return $this->getTakenById() != $user->getId() && $this->getStatus() != ProjectRequest::STATUS_CLOSED && ProjectRequests::canManage($user);
      } else {
        return false;
      } // if
    } // canTake
    
    /**
     * Return true if $user can edit $this project request
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return $this->getStatus() != ProjectRequest::STATUS_CLOSED && ProjectRequests::canManage($user);
    } // canEdit
    
    /**
     * Returns true if $user can change status of this project request
     * 
     * @param User $user
     * @return boolean
     */
    function canChangeStatus(User $user) {
      return ProjectRequests::canManage($user);
    } // canChangeStatus
    
    /**
     * Return true if $user can convert $this project request to quote
     *
     * @param User $user
     * @return boolean
     */
    function canCreateQuote(User $user) {
      if(AngieApplication::isModuleLoaded('invoicing')) {
        return Quotes::canAdd($user, $this);
      } else {
        return false;
      } // if
    } // canCreateQuote
    
    /**
     * Return true if $user can convert $this project request to quote
     *
     * @param User $user
     * @return boolean
     */
    function canCreateProject(User $user) {
      return ProjectRequests::canManage($user);
    } // canCreateProject
    
    /**
     * Return true if a user can delete project request
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return ProjectRequests::canManage($user);
    } // canDelete
  	
  	// ---------------------------------------------------
  	//  Options
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
      $options->beginWith('view_public', array(
        'text' => lang('View Public Page'), 
        'url' => $this->getPublicUrl(), 
        'onclick' => new TargetBlankCallback(), 
      ));
      
      if($this->canTake($user)) {
        $options->addAfter('take_request', array(
          'text' => lang('Take It'),
	        'url' => $this->getTakeUrl(),
          'onclick' => new AsyncLinkCallback(array(
            'confirmation' => lang('Are you sure that you want to take this project request?'),
            'success_event' => $this->getUpdatedEventName(), 
            'success_message' => lang('You successfully took over this request'), 
          )), 
        ), 'view_public');
      } // if
      
      if($this->canChangeStatus($user)) {
	    	$options->add('close_open_project_request', array(
	        'text' => lang('Close'),
	        'url' => $this->getCloseUrl(), 
	    	  'onclick' => new AsyncTogglerCallback(array(
            'url' => $this->getOpenUrl(), 
        		'text' => lang('Open'), 
        		'title' => lang('Click to Open'),
            'success_event' => $this->getUpdatedEventName(), 
          ), array(
            'url' => $this->getCloseUrl(),
          	'text' => lang('Close'),  
        		'title' => lang('Click to Close'),
          	'success_event' => $this->getUpdatedEventName(),
          ), $this->getStatus() == ProjectRequest::STATUS_CLOSED),
	      ), true);
	    } // if

      if($this->canCreateQuote($user)) {
	      $options->add('create_quote', array(
	        'text' => lang('Create a Quote'),
	        'url' => $this->getCreateQuoteUrl(), 
	        'onclick' => new FlyoutFormCallback('quote_created'), 
	      ), true);
      } // if

      if($this->canCreateProject($user)) {
	      $options->add('create_project', array(
	        'text' => lang('Create a Project'),
	        'url' => $this->getCreateProjectUrl(),
          'onclick' => new FlyoutFormCallback('project_created'),
        ), true);
      } // if
      
      parent::prepareOptionsFor($user, $options, $interface);
    } // prepareOptionsFor
  	
  	// ---------------------------------------------------
  	//  URL-s
  	// ---------------------------------------------------
  	
  	/**
     * Return view project request public URL
     *
     * @return string
     */
    function getPublicUrl() {
    	return Router::assemble('project_request_check', array(
    	  'project_request_public_id' => $this->getPublicId(), 
    	));
    } // getPublicUrl
    
    /**
     * Return take request URL
     * 
     * @return string
     */
    function getTakeUrl() {
      return Router::assemble('project_request_take', $this->getRoutingContextParams());
    } // getTakeUrl
    
    /**
     * Return open project request URL
     *
     * @return string
     */
    function getOpenUrl() {
    	return Router::assemble('project_request_open', $this->getRoutingContextParams());
    } // getOpenUrl
    
    /**
     * Return close project request URL
     *
     * @return string
     */
    function getCloseUrl() {
    	return Router::assemble('project_request_close', $this->getRoutingContextParams());
    } // getCloseUrl
    
    /**
     * Return create a project from $this project request URL
     *
     * @return string
     */
    function getCreateProjectUrl() {
    	return Router::assemble('projects_add', $this->getRoutingContextParams());
    } // getCreateProjectUrl
    
    /**
     * Return create a quote based on $this project request URL
     *
     * @return string
     */
    function getCreateQuoteUrl() {
    	return Router::assemble('quotes_add', array('project_request_id' => $this->getId()));
    } // getCreateQuoteUrl

    /**
     * Get URL for saving client
     *
     * @return string
     */
    function getSaveClientUrl() {
      return Router::assemble('project_request_save_client', array('project_request_id' => $this->getId()));
    } // getSaveClientUrl
    
    /**
     * Validate before save
     * 
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if(!$this->validatePresenceOf('created_by_name')) {
        $errors->addError(lang("Client's name is required"), 'created_by_name');
      } // if
      
      if($this->validatePresenceOf('created_by_email')) {
        if(!is_valid_email($this->getCreatedByEmail())) {
          $errors->addError(lang("Valid e-mail address is required"), 'created_by_email');
        } // if
      } else {
        $errors->addError(lang("Valid e-mail address is required"), 'created_by_email');
      } // if
      
      if(!$this->validatePresenceOf('name')) {
        $errors->addError(lang('Project name is required'), 'name');
      } // if
    } // validate
    
  }