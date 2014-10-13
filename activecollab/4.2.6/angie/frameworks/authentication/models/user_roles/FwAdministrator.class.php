<?php

  /**
   * Administrator class
   *
   * @package angie.frameworks.authentication
   * @subpackage models
   */
  abstract class FwAdministrator extends Member {

    /**
     * Return role name
     *
     * @return string
     */
    function getRoleName() {
      return lang('Administrator');
    } // getRoleName

    /**
     * Return role description
     *
     * @return string
     */
    function getRoleDescription() {
      return lang("Administrators have access to system's control panel and can configure different aspects of the system");
    } // getRoleDescription

    /**
     * Make sure that administrators can manage trash
     *
     * @return bool
     */
    function canManageTrash() {
      return true;
    } // canManageTrash

  }