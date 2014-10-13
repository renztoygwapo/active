<?php

  /**
   * on_object_context_changed event handler implemenation
   * 
   * @package angie.frameworks.search
   * @subpackage handlers
   */

  /**
   * Handle on_object_context_changed event
   * 
   * @param IObjectContext $object
   * @param string $old_context
   * @param string $new_context
   */
  function search_handle_on_object_context_changed(IObjectContext &$object, $old_context, $new_context) {
    Search::updateItemContext($object, $old_context, $new_context);
  } // search_handle_on_object_context_changed