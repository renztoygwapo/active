<?php

  /**
   * Managers are employees with managerial permissions
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class Manager extends Member {

    /**
     * Return role name
     *
     * @return string
     */
    function getRoleName() {
      return lang('Manager');
    } // getRoleName

    /**
     * Return role description
     *
     * @return string
     */
    function getRoleDescription() {
      $owner_company = Companies::findOwnerCompany();

      return lang('Manager at ":company_name". Set areas of responsibility with extra permissions', array(
        'company_name' => $owner_company instanceof Company ? $owner_company->getName() : lang('Owner Company'),
      ));
    } // getRoleDescription

    // ---------------------------------------------------
    //  Permission Overrides
    // ---------------------------------------------------

    /**
     * Managers have access to reporting section of the system
     *
     * @return bool
     */
    function canUseReports() {
      return true;
    } // canUseReports

    /**
     * Managers can always use API
     *
     * @return bool
     */
    function isApiUser() {
      return true;
    } // isApiUser

    /**
     * Return true if this user can manage trash
     *
     * @return bool
     * @throws Exception
     */
    function canManageTrash() {
      return $this->isAdministrator() || $this->getSystemPermission('can_manage_trash');
    } // canManageTrash

  }