<?php

  /**
   * Project objects user context implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class IProjectObjectRemindersImplementation extends IRemindersImplementation {
  
  	/**
     * User context is limited to project members
     * 
     * @return IUsersContext
     */
    function getUsersContext() {
    	return $this->object->getProject();
    } // getUsersContext

    /**
     * Return notification subject prefix, so recipient can sort and filter notifications
     *
     * @return string
     */
    function getNotificationSubjectPrefix() {
      return $this->object->getProject() instanceof Project ? '[' . $this->object->getProject()->getName() . '] ' : '';
    } // getNotificationSubjectPrefix
  	
  }