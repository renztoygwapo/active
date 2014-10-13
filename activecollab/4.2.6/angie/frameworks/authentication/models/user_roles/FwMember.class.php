<?php

  /**
   * Base member class
   *
   * @package angie.frameworks.authentication
   * @subpackage models
   */
  abstract class FwMember extends User {

    /**
     * Return role name
     *
     * @return string
     */
    function getRoleName() {
      return lang('Member');
    } // getRoleName

    /**
     * Return role description
     *
     * @return string
     */
    function getRoleDescription() {
      return lang('Role used by the regular company employees or organisation member. This role provides limited access by default, but has a lot of extra options that administrators can give to individual employees to broaden their reach within activeCollab');
    } // getRoleDescription

  }