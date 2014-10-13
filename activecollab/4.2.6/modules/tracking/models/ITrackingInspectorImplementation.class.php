<?php

  /**
   * Base Tracking Inspector implementation
   * 
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  class ITrackingInspectorImplementation extends IInspectorImplementation {
  	
    /**
     * do load data for given interface
     * 
     * @param IUser $user
     * @param string $interface
     */
    protected function do_load(IUser $user, $interface) {
      parent::do_load($user, $interface);
      
      // There are no time records and expenses inspector shown in the full interface
      if($interface == AngieApplication::INTERFACE_PHONE) {
      	$this->addProperty('summary', lang('Summary'), new SimpleFieldInspectorProperty($this->object, 'summary'));
      	$this->addProperty('billable_status', lang('Is Billable?'), new SimpleFieldInspectorProperty($this->object, 'billable_status_verbose'));
      } // if
    } // do_load
    
  } // ITrackingInspectorImplementation