<?php

  /**
   * Client implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class Client extends User {

    /**
     * Returns true if this user can manage company finances - receive and pay invoices, quotes etc
     *
     * @return bool
     */
    function canManageCompanyFinances() {
      return $this->getSystemPermission('can_manage_client_finances');
    } // canManageCompanyFinances

    /**
     * Returns true if this user can request new projects
     *
     * @return bool
     */
    function canRequestProjects() {
      return $this->getSystemPermission('can_request_project');
    } // canRequestProjects

    /**
     * Clients can't see who is online
     *
     * @return bool
     */
    function canSeeWhoIsOnline() {
      return false;
    } // canSeeWhoIsOnline

    // ---------------------------------------------------
    //  OLD
    // ---------------------------------------------------

    /**
     * Return role name
     *
     * @return string
     */
    function getRoleName() {
      return lang('Client');
    } // getRoleName

    /**
     * Return role description
     *
     * @return string
     */
    function getRoleDescription() {
      return lang('Member of a client company, with restricted system access and permissions');
    } // getRoleDescription

  }