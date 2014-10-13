<?php

  /**
   * File activity logs implementation
   * 
   * @package activeCollab.modules.files
   * @subpackage models
   */
  class IFileActivityLogsImplementation extends IProjectObjectActivityLogsImplementation {
  
    /**
     * Log update
     * 
     * @param IUser $by
     * @param array $modifications
     * @return ActivityLog
     */
    function logUpdate(IUser $by, $modifications = null) {
      parent::logUpdate($by, $modifications);
      
      if(isset($modifications['integer_field_1'])) {
        list($old_version, $new_version) = $modifications['integer_field_1'];
        
        if($new_version && $new_version > $old_version) {
          return ActivityLogs::log($this->object, 'file/new_version', $by, $this->getTarget('new_version'), $this->getComment('new_version'), 'versions/' . $new_version);
        } // if
      } // if
    } // logUpdate
    
  }