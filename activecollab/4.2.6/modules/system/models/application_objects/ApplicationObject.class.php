<?php

  /**
   * Class that all application objects inherit
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  abstract class ApplicationObject extends FwApplicationObject implements ICodeSnippets {
  	
    /**
     * Cached code snippets implementation instance
     *
     * @var ICodeSnippetsImplementation
     */
    private $code_snippets;
    
    /**
     * Return code snippets implementation instance for this object
     *
     * @return ICodeSnippetsImplementation
     */
    function code_snippets() {
      if(empty($this->code_snippets)) {
        $this->code_snippets = new ICodeSnippetsImplementation($this);
      } // if
      
      return $this->code_snippets;
    } // code_snippets
    
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

			if ($this instanceof ISchedule) {
      	$this->schedule()->describe($user, $detailed, $for_interface, $result);
     	} // if

    	if ($this instanceof ITracking && $detailed) {
    	  $this->tracking()->describe($user, $detailed, $for_interface, $result);
    	} // if

    	if ($this instanceof ISharing && $detailed) {
    		$this->sharing()->describe($user, $detailed, $for_interface, $result);
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
      $result = parent::describeForApi($user, $detailed);

      if ($this instanceof ISchedule) {
        $this->schedule()->describeForApi($user, $detailed, $result);
      } // if

      if ($this instanceof ITracking && $detailed) {
        $this->tracking()->describeForApi($user, $detailed, $result);
      } // if

      if ($this instanceof ISharing && $detailed) {
        $this->sharing()->describeForApi($user, $detailed, $result);
      } // if

      return $result;
    } // describeForApi
    
  }