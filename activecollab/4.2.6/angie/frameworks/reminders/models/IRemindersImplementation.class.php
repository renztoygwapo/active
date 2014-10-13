<?php

  /**
   * Reminders helper implementation
   * 
   * @package angie.frameworks.reminders
   * @subpackage models
   */
  class IRemindersImplementation {
  	
  	/**
  	 * Parent object instance
  	 *
  	 * @var IReminders
  	 */
  	protected $object;
  
  	/**
  	 * Construct reminders helper instance
  	 * 
  	 * @param IReminders $object
  	 */
  	function __construct(IReminders $object) {
  		$this->object = $object;
  	} // __construct

    /**
     * Return notification subject prefix, so recipient can sort and filter notifications
     *
     * @return string
     */
    function getNotificationSubjectPrefix() {
      return '';
    } // getNotificationSubjectPrefix
  	
  	/**
  	 * Cached array of object reminders
  	 *
  	 * @var DBResult
  	 */
  	protected $reminders = false;
  	
  	/**
  	 * Returns true if this object has active reminders
  	 * 
  	 * @return boolean
  	 */
  	function has() {
  		if($this->reminders === false) {
  			return (boolean) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'reminders WHERE parent_type = ? AND parent_id = ?', get_class($this->object), $this->object->getId());
  		} else {
  			return $this->reminders instanceof DBResult ? (boolean) $this->reminders->count() : false;
  		} // if
  	} // has
  	
  	/**
  	 * Return all object reminders
  	 * 
  	 * @param integer $count
  	 * @return DBResult
  	 */
  	function get($count = null) {
  		if($this->reminders === false) {
  			$this->reminders = Reminders::find(array(
	  		  'conditions' => array('parent_type = ? AND parent_id = ?', get_class($this->object), $this->object->getId()), 
	  		  'order' => 'created_on DESC', 
	  		));
  		} // if
  		
  		return $this->reminders;
  	} // get
  	
  	/**
  	 * Return range of reminders that meet the given criteria
  	 * 
  	 * This function is designed to be used to progressively populate paged list
  	 * 
  	 * @param integer $num
  	 * @param array $exclude
  	 * @param integer $timestamp
  	 * @return DBResult
  	 */
  	function getSlice($num = 10, $exclude = null, $timestamp = null) {
  		$max_date = $timestamp ? new DateTimeValue($timestamp) : new DateTimeValue();
  		
  		if($exclude) {
  			return Reminders::find(array(
	  		  'conditions' => array('parent_type = ? AND parent_id = ? AND id NOT IN (?) AND created_on <= ?', get_class($this->object), $this->object->getId(), $exclude, $max_date), 
	  		  'order' => 'created_on DESC', 
	  		  'limit' => $num,  
	  		));
  		} else {
  			return Reminders::find(array(
	  		  'conditions' => array('parent_type = ? AND parent_id = ? AND created_on <= ?', get_class($this->object), $this->object->getId(), $max_date), 
	  		  'order' => 'created_on DESC', 
	  		  'limit' => $num,  
	  		));
  		} // if
  	} // getSlice
  	
  	/**
  	 * Return number of object reminders
  	 * 
  	 * @return integer
  	 */
  	function count() {
  		if($this->reminders === false) {
  			return DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'reminders WHERE parent_type = ? AND parent_id = ?', get_class($this->object), $this->object->getId());
  		} else {
  			return $this->reminders instanceof DBResult ? $this->reminders->count() : 0;
  		} // if
  	} // count
  	
  	/**
  	 * Returns true if there are active reminders related to parent object
  	 * 
  	 * @return boolean
  	 */
  	function hasActive() {
  		if($this->active === false) {
  			return (boolean) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'reminders WHERE parent_type = ? AND parent_id = ? AND dismissed_on IS NULL', get_class($this->object), $this->object->getId());
  		} else {
  			return $this->active instanceof DBResult && $this->active->count() > 0;
  		} // if
  	} // hasActive
  	
  	/**
  	 * Cached array of active reminders
  	 *
  	 * @var DBResult
  	 */
  	private $active = false;
  	
  	/**
  	 * Return all active reminders
  	 * 
  	 * @return DBResult
  	 */
  	function getActive() {
  		if($this->active === false) {
	  		$this->active = Reminders::find(array(
	  		  'conditions' => array('parent_type = ? AND parent_id = ? AND dismissed_on IS NULL', get_class($this->object), $this->object->getId()), 
	  		  'order' => 'created_on', 
	  		));
  		} // if
  		
  		return $this->active;
  	} // getActive
  	
  	/**
  	 * Returns true if there are dismissed remidners related to this object
  	 * 
  	 * @return boolean
  	 */
  	function hasDismissed() {
  		if($this->dismissed === false) {
  			return (boolean) DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . 'reminders WHERE parent_type = ? AND parent_id = ? AND dismissed_on IS NOT NULL', get_class($this->object), $this->object->getId());
  		} else {
  			return $this->dismissed instanceof DBResult && $this->dismissed->count() > 0;
  		} // if
  	} // hasDismissed
  	
  	/**
  	 * Cached array of dismissed reminders
  	 *
  	 * @var DBResult
  	 */
  	protected $dismissed = false;
  	
  	/**
  	 * Return all dismissed reminders related to this object
  	 * 
  	 * @return DBResult
  	 */
  	function getDismissed() {
  		if($this->dismissed === false) {
	  		$this->dismissed = Reminders::find(array(
	  		  'conditions' => array('parent_type = ? AND parent_id = ? AND dismissed_on IS NOT NULL', get_class($this->object), $this->object->getId()), 
	  		  'order' => 'dismissed_on DESC', 
	  		));
  		} // if
  		
  		return $this->dismissed;
  	} // getDismissed
  	
  	/**
     * Prepare state related object options
     *
     * @param NamedList $options
     * @param IUser $user
     * @param string $interface
     */
    function prepareObjectOptions(NamedList $options, IUser $user, $interface = AngieApplication::INTERFACE_DEFAULT) {
    	if($interface == AngieApplication::INTERFACE_DEFAULT) {
    		if($this->canAdd($user)) {
	    		$options->add('nudge', array(
	          'text' => lang('Nudge'),
	          'url' => $this->getNudgeUrl(),
	    			'icon' => AngieApplication::getImageUrl('icons/12x12/nudge.png', REMINDERS_FRAMEWORK),
	          'onclick' => new FlyoutFormCallback('reminder_created', array(
		    		  'success_message' => lang('Selected users have been nudged')
		    		)), 
	        ));
	    		
	    		$options->add('set_reminder', array(
	          'text' => lang('Set a Reminder'),
	          'url' => $this->getAddUrl(),
	          'onclick' => new FlyoutFormCallback('reminder_created', array(
	            'success_message' 	=> lang('Reminder has been created'),
              'width' => 'narrow',
              'focus_first_field'	=> false,
              'discover_permalink_on_success' => false,
	    			)), 
	        ));
	    	} // if
	    	
	    	if($this->canManage($user)) {
	    		$options->add('manage_reminders', array(
	          'text' => lang('Manage Reminders'),
	          'url' => $this->getUrl(),
	    		  'icon' => AngieApplication::getImageUrl('icons/12x12/reminder.png', REMINDERS_FRAMEWORK), 
	          'onclick' => new FlyoutCallback(array(
	    				'width'	=> 650
	    			)), 
	        ));
	    	} // if
    	} elseif($interface == AngieApplication::INTERFACE_PHONE) {
    		if($this->canAdd($user)) {
	    		$options->addBefore('nudge', array(
	          'text' => lang('Nudge'),
	          'url' => $this->getNudgeUrl(),
	    			'icon' => AngieApplication::getImageUrl('icons/navbar/nudge.png', REMINDERS_FRAMEWORK, AngieApplication::INTERFACE_PHONE) 
	        ), 'trash');
	    	} // if
    	} // if
    } // prepareObjectOptions
    
    /**
     * Create a new reminder instance for this parent
     * 
     * @return Reminder
     */
    function newReminder() {
    	return new Reminder();
    } // newReminder
    
    /**
     * Return users context, if context is limited
     * 
     * Context is not limited by default, but subclasses can limit it to a 
     * specific group of users, people involved in a project etc
     * 
     * @return IUsersContext
     */
    function getUsersContext() {
    	return null;
    } // getUsersContext
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can create new reminder for parent object
     * 
     * @param IUser $user
     * @return boolean
     */
    function canAdd(IUser $user) {
    	return $this->object->canView($user);
    } // canAdd
    
    /**
     * Returns true if $user can manage existing reminders for parent object
     * 
     * @param IUser $user
     * @return boolean
     */
    function canManage(IUser $user) {
    	return $user->isAdministrator();
    } // canManage
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return reminders URL
     * 
     * @return string
     */
    function getUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_reminders', $this->object->getRoutingContextParams());
    } // getUrl
    
    /**
     * Return add reminder URL
     * 
     * @return string
     */
    function getAddUrl() {
    	return Router::assemble($this->object->getRoutingContext() . '_reminders_add', $this->object->getRoutingContextParams());
    } // getAddUrl
    
    /**
     * Return nudge URL
     * 
     * @return string
     */
    function getNudgeUrl() {
    	return Router::assemble($this->object->getRoutingContext() . '_reminders_nudge', $this->object->getRoutingContextParams());
    } // getNudgeUrl
  	
  }