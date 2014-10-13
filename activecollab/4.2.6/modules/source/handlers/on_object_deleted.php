<?php

  /**
   * Source module on_object_deleted event handler
   *
   * @package activeCollab.modules.source
   * @subpackage handlers
   */

  /**
   * on_object_deleted handler implementation
   *
   * @param Object $object
   */
  function source_handle_on_object_deleted($object) {
    if($object instanceof Task || $object instanceof Discussion || $object instanceof Milestone || $object instanceof ProjectObjectSubtask) {
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'commit_project_objects WHERE parent_id = ? AND project_id = ?', $object->getId(), $object->getProjectId());
    } // if
  } // source_handle_on_object_deleted