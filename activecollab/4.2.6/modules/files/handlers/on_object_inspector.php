<?php

  /**
   * Files module on_object_inspector events handler
   *
   * @package activeCollab.modules.files
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
  function files_handle_on_object_inspector(IInspectorImplementation &$inspector, IInspector &$object, IUser &$user, $interface) {
		if ($object instanceof File) {
			$inspector->addProperty('file_size', lang('File Size'), new SimpleFieldInspectorProperty($object, 'formated_size'));
			$inspector->addProperty('last_version', lang('Last Version'), new ActionOnByInspectorProperty($object, 'last_version'));
		} else if ($object instanceof TextDocumentVersion) {
			$inspector->addProperty('version', lang('Version'), new SimpleFieldInspectorProperty($object, 'version', array('prefix' => '#')));
		} else if ($object instanceof TextDocument) {
			$inspector->addProperty('version', lang('Version'), new SimpleFieldInspectorProperty($object, 'version', array('prefix' => '#')));
		} // if
  } // files_handle_on_object_inspector