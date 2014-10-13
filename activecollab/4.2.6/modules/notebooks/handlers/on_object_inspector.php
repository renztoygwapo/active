<?php

  /**
   * Notebooks module on_object_inspector events handler
   *
   * @package activeCollab.modules.notebooks
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
	function notebooks_handle_on_object_inspector(IInspectorImplementation &$inspector, IInspector &$object, IUser &$user, $interface) {
  	if($object instanceof NotebookPage) {
  		$inspector->addProperty('parent_notebook_page', lang('Parent Page'), new SimplePermalinkInspectorProperty($object, 'parent.permalink', 'parent.name'));
  		$inspector->addProperty('current_version', lang('Current Version'), new SimpleFieldInspectorProperty($object, 'revision_num', array('prefix' => '#')));
    } // if
  } // notebooks_handle_on_object_inspector