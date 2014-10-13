<?php

  /**
   * Framework level mailing activity log instance
   *
   * @package angie.frameworks.email
   * @subpackage models
   */
  abstract class FwMailingActivityLog extends BaseMailingActivityLog implements IRoutingContext {
  	
  	// Mail direction
  	const DIRECTION_IN = 'in';
  	const DIRECTION_OUT = 'out';
  	
  	/**
  	 * Return mailing long entry name
  	 * 
  	 * @return string
  	 */
  	function getName() {
  		return '-- Unknown Mailing Log Entry --';
  	} // getName
    
  	/**
  	 * Return from user
  	 * 
  	 * @return IUser
  	 */
  	function getFrom() {
  	  $from = $this->getFromId() ? DataObjectPool::get('User', $this->getFromId()) : null;
  	  
  	  if(empty($from) && $this->getFromEmail()) {
  	    try {
  	      $from = new AnonymousUser($this->getFromName(), $this->getFromEmail());
  	    } catch(InvalidParamError $e) {
  	      $from = null;
  	    } // try
  	  } // if
  	  
  	  return $from;
  	} // getFrom
  	
  	/**
  	 * Set from user
  	 * 
  	 * @param IUser $from
  	 * @return IUser
     * @throws InvalidInstanceError
  	 */
  	function setFrom($from) {
  		if($from instanceof IUser) {
  			$this->setFromId($from->getId());
  			$this->setFromName($from->getName());
  			$this->setFromEmail($from->getEmail());
  		} elseif($from === null) {
  			$this->setFromId(null);
  			$this->setFromName(null);
  			$this->setFromEmail(null);
  		} else {
  			throw new InvalidInstanceError('from', $from, 'IUser');
  		} // if
  		
  		return $from;
  	} // setFrom
  	
  	/**
  	 * Return user who received the message
  	 * 
  	 * @return IUser
  	 */
  	function getTo() {
  	  $to = $this->getToId() ? DataObjectPool::get('User', $this->getToId()) : null;
  	  
  	  if(empty($to) && $this->getToEmail()) {
  	    try {
  	      $to = new AnonymousUser($this->getToName(), $this->getToEmail());
  	    } catch(InvalidParamError $e) {
  	      $to = null;
  	    } // try
  	  } // if
  	  
  	  return $to;
  	} // getTo
  	
  	/**
  	 * Set to user
  	 * 
  	 * @param IUser $to
  	 * @return IUser
  	 * @throws InvalidInstanceError
  	 */
  	function setTo($to) {
  		if($to instanceof IUser) {
  			$this->setToId($to->getId());
  			$this->setToName($to->getName());
  			$this->setToEmail($to->getEmail());
  		} elseif($to === null) {
  			$this->setToId(null);
  			$this->setToName(null);
  			$this->setToEmail(null);
  		} else {
  			throw new InvalidInstanceError('to', $to, 'IUser');
  		} // if
  		
  		return $to;
  	} // setTo
  	
  	/**
  	 * Log activity and save it to database
  	 * 
  	 * @param IUser $from
  	 * @param IUser $to
  	 * @param array $properties
  	 * @param boolean $save
  	 */
  	function log(IUser $from, IUser $to, $properties = null, $save = true) {
  	  if($from instanceof IUser && $to instanceof IUser) {
  	    $this->setFrom($from);
    		$this->setTo($to);
    		
    		if(is_foreachable($properties)) {
    			foreach($properties as $k => $v) {
    				$this->setAdditionalProperty($k, $v);
    			} // foreach
    		} // if
    		
    		if($save) {
    			$this->save();
    		} // if
  	  } // if
  	} // log
  	
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
    	
    	$result['from'] = $this->getFrom() instanceof IUser ? $this->getFrom()->describe($user, false, $for_interface) : null;
    	$result['to'] = $this->getTo() instanceof IUser ? $this->getTo()->describe($user, false, $for_interface) : null;
    	$result['has_details'] = $this->hasDetails();
    	$result['icon_url'] = $this->getIconUrl();
    	
    	return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      throw new NotImplementedError(__METHOD__);
    } // describeForApi
    
    // ---------------------------------------------------
    //  Details
    // ---------------------------------------------------
    
    /**
     * Returns true if this log entry has details to show in separate view
     * 
     * @return boolean
     */
    function hasDetails() {
    	return false;
    } // hasDetails
    
    /**
     * Render log entry details in separate view
     * 
     * @param Smarty $smarty
     * @param string $tpl_path
     * @param mixed $tpl_vars
     * @return string
     * @throws InvalidParamError
     */
    function renderDetails(Smarty $smarty, $tpl_path = null, $tpl_vars = null) {
    	if(empty($tpl_path)) {
    		throw new InvalidParamError('template', $tpl_path, 'Template path is required');
    	} // if
    	
    	$template = $smarty->createTemplate($tpl_path);
    	$template->assign('log_entry', $this);
    	if($tpl_vars) {
    		foreach($tpl_vars as $k => $v) {
    			$template->assign($k, $tpl_vars[$k]);
    		} // foreach
    	} // if
    	
    	return $template->fetch();
    } // renderDetails
  	
  	// ---------------------------------------------------
  	//  Interface Implementation
  	// ---------------------------------------------------
  	
  	/**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
    	return 'email_admin_log_entry';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
    	return array('log_entry_id' => $this->getId());
    } // getRoutingContextParams
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return log entry icon URL
     * 
     * @return string
     */
    function getIconUrl() {
    	return AngieApplication::getImageUrl('icons/16x16/mail-blank.png', ENVIRONMENT_FRAMEWORK);
    } // getIconUrl
  	
  }