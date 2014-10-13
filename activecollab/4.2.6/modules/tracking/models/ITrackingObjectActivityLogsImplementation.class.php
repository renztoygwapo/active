<?php

  /**
   * Tracking object activity log implementation
   * 
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  class ITrackingObjectActivityLogsImplementation extends IActivityLogsImplementation {
  
    /**
     * Return target for given action
     * 
     * @param string $action
     * @return ApplicationObject
     */
    function getTarget($action = null) {
      return $this->object->getParent();
    } // getTarget

    /**
     * Describe for log
     *
     * @param IUser $user
     * @return array
     */
    function describeForLog(IUser $user) {
      return array_merge(parent::describeForLog($user), array(
        'value' => $this->object->getValue(),
        'billable_status' => $this->object->getBillableStatus(),
      ));
    } // describeForLog
    
  }