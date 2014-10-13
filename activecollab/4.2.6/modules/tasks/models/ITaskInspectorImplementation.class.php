<?php

  /**
   * Base Task Inspector implementation
   * 
   * @package activeCollab.modules.tasks
   * @subpackage models
   */
  class ITaskInspectorImplementation extends IProjectObjectInspectorImplementation {

    /**
     * Custom name format
     *
     * @var null|string
     */
    protected $name_format = '#:task_id: :name';
  	
    /**
     * Load data for given interface
     * 
     * @param IUser $user
     * @param string $interface
     */
    public function load(IUser $user, $interface = AngieApplication::INTERFACE_DEFAULT) {
    	parent::load($user, $interface);
    	
    	// Body needs to be regularly displayed in phone interface
    	if($interface == AngieApplication::INTERFACE_DEFAULT) {
    		$this->supports_body = false;
	    } else {
      	$this->supports_body = true;      	
      } // if
    } // load
      
  	/**
     * do load data for given interface
     * 
     * @param IUser $user
     * @param string $interface
     */
    protected function do_load(IUser $user, $interface) {
    	parent::do_load($user, $interface);
    	
    	// Assignees are property in phone interface
      if($interface == AngieApplication::INTERFACE_PHONE) {
      	$this->addProperty('assignees', lang('Assignees'), new AssigneesInspectorWidget($this->object));

      // Printer
      } else if ($interface == AngieApplication::INTERFACE_PRINTER) {
        $this->addProperty('assignees', lang('Assignees'), new AssigneesInspectorProperty($this->object));
        $this->addProperty('label', lang('Label'), new LabelInspectorProperty($this->object));
        if(AngieApplication::isModuleLoaded('tracking')) {
          $this->addProperty('estimate', lang('Estimate'), new EstimateInspectorProperty($this->object));
        } // if

        foreach(CustomFields::getEnabledCustomFieldsByType('Task') as $field_name => $details) {
          $this->addProperty($field_name, $details['label'], new CustomFieldInspectorProperty($this->object));
        } // foreach

      // Default backend
      } else {
      	$this->addWidget('assignees', lang('Assignees'), new AssigneesInspectorWidget($this->object));

        foreach(CustomFields::getEnabledCustomFieldsByType('Task') as $field_name => $details) {
          $this->addProperty($field_name, $details['label'], new CustomFieldInspectorProperty($this->object));
        } // foreach
      } // if

      if (AngieApplication::isModuleLoaded('tracking') && $this->object instanceof ITracking && $this->object instanceof ProjectObject && TrackingObjects::canAccess($user, $this->object->getProject())) {
        if($interface == AngieApplication::INTERFACE_PHONE) {
          $this->addProperty('tracking', lang('Tracking'), new TrackingInspectorWidget($this->object));
        } else {
          $this->addWidget('tracking', lang('Tracking'), new TrackingInspectorWidget($this->object));
        } // if
    	} // if
    } // do_load
    
  }