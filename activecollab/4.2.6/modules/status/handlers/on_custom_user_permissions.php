<?php

  /**
   * Status on_custom_user_permissions handler
   *
   * @package activeCollab.modules.status
   * @subpackage handlers
   */
  
  /**
   * Handle on_system_permissions
   *
   * @param NamedList $permissions
   * @param User $user
   */
  function status_handle_on_custom_user_permissions(User &$user, NamedList &$permissions) {
    if($user->isMember() && !$user->isAdministrator()) {
      $permissions->add('can_use_status_updates', array(
        'name' => lang('Use Status Updates'),
        'description' => lang('Let people use a simple communication tool that is easily accessible from all system pages'),
      ));
    } // if
  } // status_handle_on_custom_user_permissions