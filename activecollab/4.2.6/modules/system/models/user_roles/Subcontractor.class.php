<?php

  /**
   * Subcontractor implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class Subcontractor extends Member {

    /**
     * Return role name
     *
     * @return string
     */
    function getRoleName() {
      return lang('Subcontractor');
    } // getRoleName

    /**
     * Return role description
     *
     * @return string
     */
    function getRoleDescription() {
      $owner_company = Companies::findOwnerCompany();

      return lang('Subcontractor hired by ":company_name"', array(
        'company_name' => $owner_company instanceof Company ? $owner_company->getName() : lang('Owner Company'),
      ));
    } // getRoleDescription

    // ---------------------------------------------------
    //  Permission Overrides
    // ---------------------------------------------------

    /**
     * Return list of available permissions
     *
     * @return NamedList
     */
    function getAvailableCustomPermissions() {
      $result = parent::getAvailableCustomPermissions();

      $result->remove('can_manage_trash');

      return $result;
    } // getAvailableCustomPermissions

    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------

    /**
     * Subcontractors are treated as members in this regard
     *
     * @return boolean
     */
    function canSeePrivate() {
      return true;
    } // canSeePrivate

    /**
     * Returns true if this user is considered to be employee of the owner company
     *
     * @return mixed
     */
    function isEmployee() {
      return false;
    } // isEmployee

  }