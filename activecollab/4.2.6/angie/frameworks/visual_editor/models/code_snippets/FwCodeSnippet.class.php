<?php

  /**
   * CodeSnippet class
   *
   * @package angie.frameworks.visual_editor
   * @subpackage models
   */
  abstract class FwCodeSnippet extends BaseCodeSnippet implements IRoutingContext {

    /**
     * Render plain text (used for email notifications, logs etc)
     *
     * @return string
     */
    function renderPlain() {
      return '<pre style="text-align: left; font-family: monospace;">' . clean($this->getBody()) . '</pre>';
    } // renderPlain
  	
  	/**
  	 * Render the higlighted code in html
  	 * 
  	 * @return string
  	 */
  	function render() {
  		return HyperlightForAngie::htmlPreview($this->getBody(), $this->getSyntax());
  	} // render
  	
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'code_snippet';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('code_snippet_id' => $this->getId());
    } // getRoutingContextParams
    
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
      
      unset($result['name']);
      $result['syntax'] = $this->getSyntax();
      $result['body'] = $this->getBody();
      
      $result['urls']['view'] = $this->getViewUrl();
      $result['urls']['edit'] = $this->getEditUrl();
      
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
      $result = parent::describeForApi($user, $detailed);

      unset($result['name']);
      $result['syntax'] = $this->getSyntax();
      $result['body'] = $this->getBody();

      $result['urls']['view'] = $this->getViewUrl();
      $result['urls']['edit'] = $this->getEditUrl();

      return $result;
    } // describeForApi
        
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can view specific code snippet
     *
     * @param User $user
     * @return boolean
     */
    function canView(User $user) {
    	// if user is administrator or object creator 
   		if ($user->isAdministrator() || ($user->getId() == $this->getCreatedById())) {
    		return true;
    	} // if
    	
    	// we need to check parent for permission
    	$parent = $this->getParent();
    	if ($parent && !$parent->isNew() && method_exists($parent, 'canView')) {
    		return $parent->canView($user);
    	} // if
    	
      return false;
    } // canView

    /**
     * Returns true if $user can edit this code snippet
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
    	// if user is administrator or object creator 
   		if ($user->isAdministrator() || ($user->getId() == $this->getCreatedById())) {
    		return true;
    	} // if
    	
    	// we need to check parent for permission
    	$parent = $this->getParent();
    	if ($parent && !$parent->isNew() && method_exists($parent, 'canEdit')) {
    		return $parent->canEdit($user);
    	} // if
    	
      return false;
    } // canEdit

    /**
     * Returns true if $user can delete this code snippet
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
    	// if user is administrator or object creator 
   		if ($user->isAdministrator() || ($user->getId() == $this->getCreatedById())) {
    		return true;
    	} // if
    	
    	// we need to check parent for permission
    	$parent = $this->getParent();
    	if ($parent && !$parent->isNew() && method_exists($parent, 'canDelete')) {
    		return $parent->canDelete($user);
    	} // if
    	
      return false;
    } // canDelete
  
  }