<?php

  /**
   * System module on_object_inspector events handler
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */
  
  /**
   * Populate object inspector
   *
   * @param IInspectorImplementation $inspector
   * @param IInspector $object
   * @param IUser $user
   * @param string $interface
   */
  function system_handle_on_object_inspector(IInspectorImplementation $inspector, IInspector &$object, IUser &$user, $interface) {
  	// Can have milstone
    if ($object instanceof ProjectObject && $object->fieldExists('milestone_id') && ($object->getProject() instanceof Project && $object->getProject()->hasTab('milestones', $user))) {
  		$inspector->addProperty('milestone', lang('Milestone'), new MilestoneInspectorProperty($object));  		
   	} // if

    if($object instanceof ProjectObject && $interface == AngieApplication::INTERFACE_PRINTER) {
      $inspector->addProperty('project_name', lang('Project'), new SimpleFieldInspectorProperty($object, 'project.name'));
    } //if
   	
   	// ISchedule
   	if ($object instanceof ISchedule) {
   		$inspector->addProperty('due_on', $object->schedule()->isRange() ? lang('Scheduled') : lang('Due On'), new ScheduleInspectorProperty($object));
   	} // if
   	
   	if ($object instanceof Project) {
   	  if(AngieApplication::isModuleLoaded(TASKS_MODULE)) {
   	    if(Tasks::canAccess($user, $object)) {
   	      $inspector->addProperty('project_progress', lang('Progress'), new SimpleFieldInspectorProperty($object, 'progress'));
   	    }//if
   	  }//if
   		
   	} // if
   	
   	// Language
   	if ($object instanceof Language) {
   		$inspector->addProperty('language_name', lang('Language Name'), new SimpleFieldInspectorProperty($object, 'name'));
   		$inspector->addProperty('locale', lang('Locale'), new SimpleFieldInspectorProperty($object, 'locale'));
   	} // if
    	
    	// Project Request
   	if ($object instanceof ProjectRequest) {
    	$inspector->addProperty('client', lang('Client'), new ProjectRequestClientInspectorProperty($object));
    	
    	// custom fields
    	$custom_fields = $object->getCustomFields();
    	if (is_foreachable($custom_fields)) {
    		foreach ($custom_fields as $custom_field_name => $custom_field) {
    			$inspector->addProperty($custom_field_name, $custom_field['label'], new SimpleFieldInspectorProperty($object, 'custom_fields.' . $custom_field_name . '.value'));		  				
   			} // if
   		} // if
   		
       if($object->getStatus() !== ProjectRequest::STATUS_CLOSED) {
         $inspector->addProperty('public_url', lang('Public Page'), new SimplePermalinkInspectorProperty($object, 'public_url', 'public_url', array("target"=>"_blank")));
		  } // if
   	} // if
    	
   	if ($object instanceof ISharing) {
   		$inspector->addIndicator('sharing', lang('Sharing'), new SharingInspectorIndicator($object));
   	} // if
  } // system_handle_on_object_inspector