<?php

  /**
   * drop_project_progress_cache event handler implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage handlers
   */

  /**
   * Drop project progress cache
   * 
   * @param ApplicationObject $object
   */
  function system_handle_drop_project_progress_cache(&$object) {
    if($object instanceof IComplete) {
      if($object instanceof ProjectObject || $object instanceof ProjectObjectSubtask) {
        ProjectProgress::dropProjectProgressCache($object->getProject());
      } else {
        ProjectProgress::dropProjectProgressCache();
      } // if
    } // if
  } // system_handle_drop_project_progress_cache