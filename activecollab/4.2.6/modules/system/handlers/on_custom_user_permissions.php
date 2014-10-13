<?php

  /**
   * Handle on_custom_user_permissions event
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */

  /**
   * Populate user permissions
   *
   * @param User $user
   * @param NamedList $permissions
   */
  function system_handle_on_custom_user_permissions(User &$user, NamedList &$permissions) {
    if($user->isManager()) {
      $permissions->remove('can_use_api');

      $permissions->add('can_manage_people', array(
        'name' => lang('Manage People'),
        'description' => lang('Let user create new and manage all existing users and companies, except Administrators'),
      ));

      $permissions->add('can_manage_projects', array(
        'name' => lang('Manage Projects'),
        'description' => lang('Give access to all projects and let user have all permission within them'),
      ));

      $permissions->add('can_manage_project_requests', array(
        'name' => lang('Manage Project Requests'),
        'description' => lang('Let user manage project requests submitted by clients'),
      ));
    } // if

    if($user->isMember(true)) {
      $permissions->add('can_see_company_notes', array(
        'name' => lang('See Company Notes'),
        'description' => lang("Select this permission if you wish client company notes to be visible to this user"),
      ));

      $permissions->add('can_see_project_budgets', array(
        'name' => lang('See Project Budgets'),
        'description' => lang("Set whether user can see budget details for projects"),
      ));
    } // if

    if($user instanceof Client) {
      $permissions->add('can_request_projects', array(
        'name' => lang('Submit Project Requests'),
        'description' => lang('Check if you want to allow this user to request new projects (subject to review)'),
      ));
    } elseif($user instanceof Subcontractor || $user->isMember(true)) {
      $permissions->remove('can_manage_trash');

      $permissions->add('can_add_projects', array(
        'name' => lang('Create New Projects'),
        'description' => lang('Let user create new projects'),
      ));
    } // if
  } // system_handle_on_custom_user_permissions