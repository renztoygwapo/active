<?php

  /**
   * on_object_context_changed event handler implemenation
   * 
   * @package angie.frameworks.activity_logs
   * @subpackage handlers
   */

  /**
   * Handle on_object_context_changed event
   * 
   * @param IObjectContext $object
   * @param string $old_context
   * @param string $new_context
   */
  function activity_logs_handle_on_object_context_changed(IObjectContext &$object, $old_context, $new_context) {
    ActivityLogs::updateObjectContext($object, $old_context, $new_context);
  } // activity_logs_handle_on_object_context_changed