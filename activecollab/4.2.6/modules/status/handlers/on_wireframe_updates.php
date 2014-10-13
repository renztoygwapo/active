<?php

  /**
   * on_wireframe_updates event handler implementation
   *
   * @package activeCollab.modules.status
   * @subpackage handlers
   */

  /**
   * Handle wireframe updates even
   *
   * @param array $wireframe_data
   * @param array $response_data
   * @param boolean $on_unload
   * @param User $user
   */
  function status_handle_on_wireframe_updates(&$wireframe_data, &$response_data, $on_unload, &$user) {
    if(empty($on_unload)) {
      $response_data['status_bar_badges']['status_updates'] = StatusUpdates::countNewMessagesForUser(Authentication::getLoggedUser());
    } // if
  } // status_handle_on_wireframe_updates