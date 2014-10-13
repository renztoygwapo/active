<?php

  /**
   * Notebook page activity logs
   * 
   * @package activeCollab.modules.notebooks
   * @subpackage models
   */
  class INotebookPageActivityLogsImplementation extends IActivityLogsImplementation {
  
    /**
     * Return target for given action
     * 
     * @param string $action
     * @return ApplicationObject
     */
    function getTarget($action = null) {
      return $this->object->getNotebook();
    } // getTarget
    
    /**
     * Log update
     * 
     * @param IUser $by
     * @param array $modifications
     * @return ActivityLog
     */
    function logUpdate(IUser $by, $modifications = null) {
      parent::logUpdate($by, $modifications);
      
      if(isset($modifications['version'])) {
        list($old_version, $new_version) = $modifications['version'];
        
        if($new_version && $new_version > $old_version) {
          return ActivityLogs::log($this->object, 'notebook_page/new_version', $by, $this->getTarget('new_version'), $this->getComment('new_version'));
        } // if
      } // if
    } // logUpdate
    
  }