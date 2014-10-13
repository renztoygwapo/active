<?php

  /**
   * Framework level API client subscription implementation
   * 
   * @package angie.frameworks.authentication
   * @subpackage models
   */
  abstract class FwApiClientSubscription extends BaseApiClientSubscription implements IRoutingContext {
    
    /**
     * Return name
     * 
     * @return string
     */
    function getName() {
      return lang('API Subscription #:num', array('num' => $this->getId()));
    } // getName
    
    /**
     * Cached parent user account instance
     *
     * @var User
     */
    private $user = false;
    
    /**
     * Return parent user account
     * 
     * @return User
     */
    function getUser() {
      if($this->user === false) {
        $this->user = Users::findById($this->getUserId());
      } // if
      
      return $this->user;
    } // getUser
    
    /**
     * Set user
     * 
     * @param User $user
     * @return User
     */
    function setUser(User $user) {
      if($user instanceof User) {
        $this->setUserId($user->getId());
        $this->user = $user;
      } else {
        throw new InvalidInstanceError('user', $user, 'User');
      } // if
      
      return $user;
    } // setUser
    
    /**
     * Enable this subscription instance
     * 
     * @param User $by
     * @param boolean $save
     */
    function enable(User $by, $save = true) {
      $this->setIsEnabled(true);
      
      if($save) {
        $this->save();
      } // if
    } // enable
    
    /**
     * Disable this subscription instance
     * 
     * @param User $by
     * @param boolean $save
     */
    function disable(User $by, $save = true) {
      $this->setIsEnabled(false);
      
      if($save) {
        $this->save();
      } // if
    } // disable

    /**
     * Return API URL
     *
     * @return string
     */
    function getApiUrl() {
      return ROOT_URL . '/api.php';
    } // getApiUrl
    
    /**
     * Return formatted token
     * 
     * @return string
     */
    function getFormattedToken() {
      return $this->getUserId() . '-' . $this->getToken();
    } // getFormattedToken
    
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
      
      $result['user_id'] = $this->getUserId();
      $result['client_name'] = $this->getClientName();
      $result['client_vendor'] = $this->getClientVendor();
      $result['last_used_on'] = $this->getLastUsedOn();
      $result['is_enabled'] = $this->getIsEnabled();
      $result['is_read_only'] = $this->getIsReadOnly();
      
      if($this->canChangeStatus($user)) {
        $result['urls']['enable'] = $this->getEnableUrl();
        $result['urls']['disable'] = $this->getDisableUrl();
      } // if
      
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
      throw new NotImplementedError(__METHOD__);
    } // describeForApi
    
    // ---------------------------------------------------
    //  Routing context
    // ---------------------------------------------------
    
    /**
     * Cached routing context
     *
     * @var string
     */
    private $routing_context = false;
  
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      if($this->routing_context === false) {
        $this->routing_context = $this->getUser()->getRoutingContext() . '_api_client_subscription';
      } // if
      
      return $this->routing_context;
    } // getRoutingContext
    
    /**
     * Cached routing context parameters
     *
     * @var array
     */
    private $routing_context_params = false;
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      if($this->routing_context_params === false) {
        $this->routing_context_params = $this->getUser()->getRoutingContextParams();
        
        if(is_array($this->routing_context_params)) {
          $this->routing_context_params['api_client_subscription_id'] = $this->getId();
        } else {
          $this->routing_context_params = array('api_client_subscription_id' => $this->getId());
        } // if
      } // if
      
      return $this->routing_context_params;
    } // getRoutingContextParams
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return enable subscription URL
     * 
     * @return string
     */
    function getEnableUrl() {
      return Router::assemble($this->getRoutingContext() . '_enable', $this->getRoutingContextParams());
    } // getEnableUrl
    
    /**
     * Return enable subscription URL
     * 
     * @return string
     */
    function getDisableUrl() {
      return Router::assemble($this->getRoutingContext() . '_disable', $this->getRoutingContextParams());
    } // getDisableUrl
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can update this API client subscription
     * 
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return $this->getUser() instanceof User ? $this->getUser()->canEdit($user) : false;
    } // canEdit
    
    /**
     * Returns true if $user can change status of this subscription
     * 
     * @param User $user
     * @return string
     */
    function canChangeStatus(User $user) {
      return $this->canEdit($user);
    } // canChangeStatus
    
    /**
     * Returns true if $user can delete this API client subscription
     * 
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return $this->getUser() instanceof User ? $this->getUser()->canEdit($user) : false;
    } // canDelete
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Validate before save
     * 
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if(!$this->validatePresenceOf('client_name')) {
        $errors->addError(lang('Client name is required'), 'client_name');
      } // if
      
      if($this->validatePresenceOf('token') && strlen($this->getToken()) == 40) {
        if(!$this->validateUniquenessOf('token')) {
          $errors->addError(lang('Subscription token needs to be unique'), 'token');
        } // if
      } else {
        $errors->addError(lang('Subscription token is required'), 'token');
      } // if
    } // validate
    
  }