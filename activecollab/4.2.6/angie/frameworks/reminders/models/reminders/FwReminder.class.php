<?php

  /**
   * Framework level reminder implementation
   * 
   * @package angie.frameworks.reminders
   * @subpackage models
   */
  abstract class FwReminder extends BaseReminder implements IRoutingContext {
  	
  	// Remind constants
  	const REMIND_SELF = 'self';
  	const REMIND_ASSIGNEES = 'assignees';
  	const REMIND_SUBSCRIBERS = 'subscribers';
  	const REMIND_COMMENTERS = 'commenters';
  	const REMIND_SELECTED = 'selected';
  	
  	/**
  	 * Send this reminder
  	 * 
  	 * @param boolean $nudge
     * @throws Exception
  	 */
    function send($nudge = false) {
  		try {
  		  DB::beginWork('Sending reminder @ ' . __CLASS__);
  		  
  		  $users_table = TABLE_PREFIX . 'users';
	  		$reminder_users_table = TABLE_PREFIX . 'reminder_users';
	  		
	  		switch($this->getSendTo()) {
	  			case self::REMIND_SELF:
	  				$to_remind = array($this->getCreatedBy());
	  				break;
	  			case self::REMIND_ASSIGNEES:
	  				$to_remind = $this->getParent()->assignees()->getAllAssignees();
	  				break;
	  			case self::REMIND_SUBSCRIBERS:
	  				$to_remind = $this->getParent()->subscriptions()->get();
	  				break;
	  		  case self::REMIND_COMMENTERS:
	  		  	$to_remind = $this->getParent()->comments()->getCommenters($this->getCreatedBy());
	  		  	break;
	  		  case self::REMIND_SELECTED:
	  				$to_remind = array(Users::findById($this->getSelectedUserId()));
	  				break;
	  		  default:
	  		  	$to_remind = array();
	  		  	
	  		  	$loaded_user_ids = array();
	  		  	
	  		  	$users = Users::findBySQL("SELECT $users_table.* FROM $users_table, $reminder_users_table WHERE $reminder_users_table.user_id > 0 AND $users_table.id = $reminder_users_table.user_id AND $reminder_users_table.reminder_id = ?", $this->getId());
	  		  	if($users instanceof DBResult && $users->count()) {
	  		  		foreach($users as $user) {
	  		  			$loaded_user_ids[] = $user->getId();
	  		  			$to_remind[] = $user;
	  		  		} // foreach
	  		  	} // if
	  		  	
	  		  	if(count($loaded_user_ids)) {
	  		  		$anonymous_users = DB::execute("SELECT user_email AS 'email', user_name AS 'name' FROM $reminder_users_table WHERE reminder_id = ? AND user_id NOT IN (?)", $this->getId(), $loaded_user_ids);
	  		  	} else {
	  		  		$anonymous_users = DB::execute("SELECT user_email AS 'email', user_name AS 'name' FROM $reminder_users_table WHERE reminder_id = ?", $this->getId());
	  		  	} // if
	  		  	
	  		  	if(is_foreachable($anonymous_users)) {
	  		  		foreach($anonymous_users as $anonymous_user) {
	  		  			$to_remind[] = new AnonymousUser($anonymous_user['name'], $anonymous_user['email']);
	  		  		} // foreach
	  		  	} // if
	  		} // switch
	  		
	  		if(is_foreachable($to_remind)) {
          if($this->getSendTo() == self::REMIND_SELF) {
            AngieApplication::notifications()
              ->notifyAbout(REMINDERS_FRAMEWORK_INJECT_INTO . '/remind_self', $this->getParent())
              ->setReminder($this)
              ->sendToUsers($to_remind, true);
          } else {
            if($nudge) {
              AngieApplication::notifications()
                ->notifyAbout(REMINDERS_FRAMEWORK_INJECT_INTO . '/nudge', $this->getParent(), $this->getCreatedBy())
                ->setReminder($this)
                ->sendToUsers($to_remind, true);
            } else {
              AngieApplication::notifications()
                ->notifyAbout(REMINDERS_FRAMEWORK_INJECT_INTO . '/remind', $this->getParent(), $this->getCreatedBy())
                ->setReminder($this)
                ->sendToUsers($to_remind, true);
            } // if
          } // if
	  			
	  			// Insert users in reminder users table in case we are not sending 
	  			// reminders to preselected users (they are already set in reminder 
	  			// users table)
	  			if($this->getSendTo()) {
	  				$to_insert = array();
	  				
	  				foreach($to_remind as $user) {
	  					$to_insert[] = DB::prepare('(?, ?, ?, ?)', $this->getId(), $user->getId(), $user->getDisplayName(), $user->getEmail());
	  				} // foreach
	  				
	  				DB::execute("INSERT INTO $reminder_users_table (reminder_id, user_id, user_name, user_email) VALUES " . implode(', ', $to_insert));
	  			} // if
	  		} // if
	  		
	  		// Mark this reminder as sent
	  		$this->setSentOn(new DateTimeValue());
	  		$this->save();
  		  
  		  DB::commit('Reminder sent @ ' . __CLASS__);
  		} catch(Exception $e) {
  		  DB::rollback('Failed to send reminder @ ' . __CLASS__);
  		  throw $e;
  		} // try
  	} // send
  	
  	/**
  	 * Dismiss reminder for specific recipeint or for all recipients
  	 *  
  	 * @param IUser $user
  	 */
  	function dismiss($user = null) {
  		if(!$this->isDismissed()) {
  			try {
	  			DB::beginWork('Dismissing reminder @ ' . __CLASS__);
	  			
	  			$now = DateTimeValue::now();
	  			
	  			DB::execute('UPDATE ' . TABLE_PREFIX . 'reminder_users SET dismissed_on = ? WHERE reminder_id = ?', $now, $this->getId());
	  			
	  			$this->setDismissedOn($now);
	  			$this->save();
	  			
	  	    DB::commit('Reminder dismissed @ ' . __CLASS__);
	  		} catch(Exception $e) {
	    	  DB::rollback('Failed to dismiss reminder @ ' . __CLASS__);
	  		  throw $e;
	 			} // try
  		} // if
  	} // dismiss
  	
  	/**
  	 * Dismiss reminder for a given user
  	 * 
  	 * @param IUser $user
  	 */
  	function dismissForUser(IUser $user) {
  		if(!$this->isDismissedByUser($user)) {
	  		try {
	  			DB::beginWork('Dismissing reminder for user @ ' . __CLASS__);
	  			
	  			$now = DateTimeValue::now();
	  			
	  			if($user instanceof User) {
	  				DB::execute('UPDATE ' . TABLE_PREFIX . 'reminder_users SET dismissed_on = ? WHERE reminder_id = ? AND user_id = ?', $now, $this->getId(), $user->getId());
	  			} else {
	  				DB::execute('UPDATE ' . TABLE_PREFIX . 'reminder_users SET dismissed_on = ? WHERE reminder_id = ? AND user_id = ? AND user_email = ?', $now, $this->getId(), 0, $user->getEmail());
	  			} // if
	  			
	  			// Mark reminder as dismissed if all users dismissed it
	  			if(DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'reminder_users WHERE reminder_id = ? AND dismissed_on IS NULL', $this->getId()) == 0) {
	  				$this->setDismissedOn($now);
	  				$this->save();
	  			} // if
	  			
	  	    DB::commit('Reminder dismissed for user @ ' . __CLASS__);
	  		} catch(Exception $e) {
	    	  DB::rollback('Failed to dismiss reminder for user @ ' . __CLASS__);
	  		  throw $e;
	 			} // try
  		} // if
  	} // dismissForUser
  	
  	/**
  	 * Returns true if this reminder is sent by a user
  	 * 
  	 * @return boolean
  	 */
  	function isSent() {
  		return $this->isLoaded() && $this->getSentOn() instanceof DateTimeValue;
  	} // isSent
  	
  	/**
  	 * Returns true if $user is reminder user (works only if reminder is sent)
  	 *
  	 * @param IUser $user
  	 * @return boolean
  	 */
  	function isReminderUser(IUser $user) {
  		return $this->isSent() && (boolean) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'reminder_users WHERE reminder_id = ? AND user_email = ?', $this->getId(), $user->getEmail());
  	} // isReminderUser
  	
  	/**
  	 * Returns true if this reminder is dismissed by all recipients
  	 * 
  	 * @return boolean
  	 */
  	function isDismissed() {
  		return $this->isSent() && $this->getDismissedOn() instanceof DateTimeValue;
  	} // isDismissed
  	
  	/**
  	 * Returns true if $user is reminded user and he dismissed the reminder 
  	 * notification
  	 * 
  	 * @param IUser $user
  	 * @return boolean
  	 */
  	function isDismissedByUser(IUser $user) {
  		if($this->isDismissed()) {
  			return true;
  		} else {
  			return $this->isSent() && (boolean) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'reminder_users WHERE reminder_id = ? AND user_email = ? AND dismissed_on IS NOT NULL', $this->getId(), $user->getEmail());  			
  		} // if
  	} // isDismissed
  	
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
  		
  		$result['created_by'] = $this->getCreatedBy()->describe($user, false, $for_interface);
  		$result['send_to'] = $this->getSendTo();
  		$result['send_on'] = $this->getSendOn();
  		$result['sent_on'] = $this->getSentOn();
  		$result['dismissed_on'] = $this->getDismissedOn();
  		$result['comment'] = $this->getComment();
  		
  		$result['permissions']['can_send'] = $this->canSend($user); 
  		$result['permissions']['can_dismiss'] = $this->canDismiss($user); 
  		
  		if($result['permissions']['can_send']) {
  			$result['urls']['send'] = $this->getSendUrl();
  		} // if
  		
  		if($result['permissions']['can_dismiss']) {
  			$result['urls']['dismiss'] = $this->getDismissUrl();
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
      $result = array(
        'id' => $this->getId(),
        'created_by' => $this->getCreatedBy()->describeForApi($user),
        'send_to' => $this->getSendTo(),
        'send_on' => $this->getSendOn(),
        'sent_on' => $this->getSentOn(),
        'dismissed_on' => $this->getDismissedOn(),
        'comment' => $this->getComment(),
        'permissions' => array(),
        'urls' => array(),
      );

      if($this->canSend($user)) {
        $result['permissions']['can_send'] = true;
        $result['urls']['send'] = $this->getSendUrl();
      } // if

      if($this->canDismiss($user)) {
        $result['permissions']['can_dismiss'] = true;
        $result['urls']['dismiss'] = $this->getDismissUrl();
      } // if

      if(count($result['permissions']) == 0) {
        unset($result['permissions']);
      } // if

      if(count($result['urls']) == 0) {
        unset($result['urls']);
      } // if

      return $result;
    } // describeForApi
  	
  	// ---------------------------------------------------
  	//  Permissions
  	// ---------------------------------------------------
  	
  	/**
  	 * Returns true if $user can send this reminder before it is automatically 
  	 * sent
  	 * 
  	 * @param IUser $user
  	 * @return boolean
  	 */
  	function canSend(IUser $user) {
  		if($this->isSent()) {
  			return false;
  		} else {
  			return $user->isAdministrator() || $this->getCreatedById() == $user->getId(); 
  		} // if
  	} // canSend
  
  	/**
     * Returns true if $user can dismiss this reminder
     *
     * @param Iuser $user
     * @param bool $for_user
     * @return boolean
     */
    function canDismiss(IUser $user, $for_user = false) {
      if ($for_user) {
        $reminder_users = (array) DB::executeFirstColumn('SELECT user_id FROM ' . TABLE_PREFIX . 'reminder_users WHERE reminder_id = ?', $this->getId());
        return $user->isAdministrator() || in_array($user->getId(), $reminder_users);
      } else {
        return $user->isAdministrator() || $this->getCreatedById() == $user->getId();
      } // if
    } // canDismiss
    
    /**
     * Returns true if $user can delete this reminder
     *
     * @param User $user
     * @return bool
     */
    function canDelete(User $user) {
    	return $user->isAdministrator();
    } // canDelete
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return send reminder URL
     *
     * @return string
     */
    function getSendUrl() {
    	return Router::assemble($this->getRoutingContext() . '_send', $this->getRoutingContextParams());
    } // getSendUrl
    
    /**
     * Return dismiss reminder URL
     *
     * @param bool $for_user
     * @return string
     */
    function getDismissUrl($for_user = false) {
      $params = $this->getRoutingContextParams();
      if ($for_user) {
        $params['for_user'] = true;
      } // if
    	return Router::assemble($this->getRoutingContext() . '_dismiss', $params);
    } // getDismissUrl
    
    // ---------------------------------------------------
    //  Interface implementaitons
    // ---------------------------------------------------
    
    protected $routing_context = false;
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
    	if($this->routing_context === false) {
    		$this->routing_context = $this->getParent()->getRoutingContext() . '_reminder';
    	} // if
    	
    	return $this->routing_context;
    } // getRoutingContext
    
    /**
     * Routing context parameters
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
      	$params = $this->getParent()->getRoutingContextParams();
      	
      	$this->routing_context_params = is_array($params) ? array_merge($params, array('reminder_id' => $this->getId())) : array('reminder_id' => $this->getId());
      } // if
      
      return $this->routing_context_params;
    } // getRoutingContextParams
  
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
    	if(!$this->validatePresenceOf('parent_type') || !$this->validatePresenceOf('parent_id')) {
    	  $errors->addError(lang('Reminder parent needs to be selected'), 'parent');
    	} // if
    	
    	if(!$this->validatePresenceOf('send_to')) {
    		$errors->addError(lang('Reminder recipient is required'), 'send_to');
    	} // if
    	
    	if(!$this->validatePresenceOf('send_on')) {
    		$errors->addError(lang('Reminder time is required'), 'send_on');
    	} else {
        $timestamp = strtotime($this->getSendOn());
        if (!$timestamp) {
          $errors->addError(lang('Reminder time is invalid', 'send_on'));
        } // if
      } // if
    } // validate
  	
  }