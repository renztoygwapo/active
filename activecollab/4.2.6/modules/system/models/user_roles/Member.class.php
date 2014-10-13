<?php

  /**
   * Member/Employee user role implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class Member extends FwMember {

    /**
     * Return role name
     *
     * @return string
     */
    function getRoleName() {
      return lang('Member/Employee');
    } // getRoleName

    /**
     * Return role description
     *
     * @return string
     */
    function getRoleDescription() {
      $owner_company = Companies::findOwnerCompany();

      return lang('Member of ":company_name" organisation', array(
        'company_name' => $owner_company instanceof Company ? $owner_company->getName() : lang('Owner Company'),
      ));
    } // getRoleDescription

    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------

    /**
     * Returns true if this user is considered to be employee of the owner company
     *
     * @return mixed
     */
    function isEmployee() {
      return true;
    } // isEmployee

    /**
     * Managers and administrators can manage trash
     *
     * @return bool
     */
    function canManageTrash() {
      return false;
    } // canManageTrash

  }