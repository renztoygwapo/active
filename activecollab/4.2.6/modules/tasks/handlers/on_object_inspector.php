<?php

  /**
   * Tasks module on_object_inspector events handler
   *
   * @package activeCollab.modules.tasks
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
	function tasks_handle_on_object_inspector(IInspectorImplementation &$inspector, IInspector &$object, IUser &$user, $interface) {
  	if($object instanceof Task) {
  	  $inspector->addProperty('related_tasks', lang('Related Tasks'), new RelatedTasksInspectorProperty($object));
    } // if
  } // tasks_handle_on_object_inspector