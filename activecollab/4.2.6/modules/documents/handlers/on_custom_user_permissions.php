<?php

  /**
   * Documents on_system_permissions handler
   *
   * @package activeCollab.modules.documents
   * @subpackage handlers
   */
  
  /**
   * Handle on_system_permissions
   *
   * @param User $user
   * @param NamedList $permissions
   */
  function documents_handle_on_custom_user_permissions(User &$user, NamedList &$permissions) {
    if($user instanceof Client || $user instanceof Subcontractor || $user->isMember(true)) {
      $permissions->add('can_use_documents', array(
        'name' => lang('Use Global Documents'),
        'description' => lang('Let users access Global Documents section'),
      ));
    } elseif($user->isManager()) {
      $permissions->add('can_manage_documents', array(
        'name' => lang('Manage Global Documents'),
        'description' => lang('Let user access and manage documents in Global Documents section'),
      ));
    } // if
  } // documents_handle_on_custom_user_permissions