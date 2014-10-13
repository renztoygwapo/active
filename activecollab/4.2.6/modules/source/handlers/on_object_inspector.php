<?php

  /**
   * Source module on_object_inspector events handler
   *
   * @package activeCollab.modules.source
   * @subpackage handlers
   */
  
  /**
   * Populate object inspector
   *
   * @param IInspectorImplementation $inspector
   * @param mixed $object
   * @param IUser $user
   * @param mixed $interface
   */
	function source_handle_on_object_inspector(IInspectorImplementation &$inspector, IInspector &$object, IUser &$user, $interface) {

    if ($object instanceof SourceCommit) {
    	$inspector->addProperty('revision_id', lang('Revision ID'), new SimpleFieldInspectorProperty($object, 'name'));
    	$inspector->addProperty('commited_on', lang('Commited On'), new SimpleFieldInspectorProperty($object, 'commited_on.formatted_date'));
    	$inspector->addProperty('commited_by', lang('Commited By'), new SourceCommitCommitedByInspectorProperty($object, 'commited_by'));
    	
			if (($object->getSourceRepository()->getType() == 'GitRepository')) {
				$inspector->addProperty('authored_by', lang('Author'), new SourceCommitCommitedByInspectorProperty($object, 'authored_by'));	
			} // if

      $inspector->addProperty('commit_message', lang('Message'), new SimpleFieldInspectorProperty($object, 'commit_message', array(
        'modifier' => 'App.nl2br',
        'no_clean' => true
      )));

      $inspector->addProperty('origin', lang('Origin URL/Path'), new SimpleFieldInspectorProperty($object->getSourceRepository(), 'repository_path_url'));

      $inspector->addProperty('branch_name', lang('Branch'), new SimpleFieldInspectorProperty($object, 'branch_name'));
    } // if

    if ($object instanceof ProjectSourceRepository) {
      $inspector->addProperty('repository_name', lang('Name'), new SimpleFieldInspectorProperty($object, 'name'));
      $inspector->addProperty('repository_location', lang('Repository Location'), new SimpleFieldInspectorProperty($object, 'repository_location'));
      if ($object->source_repository->hasBranches()) {
        $inspector->addProperty('branch_name', lang('Branch'), new SourceProjectSourceRepositoryBranchInspectorProperty($object, 'branch_name'));
      } //if
    } //if

  } // tasks_handle_on_object_inspector