<?php

  /**
   * Throw authentication error
   *
   * @package angie.frameworks.authentication
   * @subpackage models
   */
  class AuthenticationError extends Error {

    // Known authentication errors
    const UNKNOWN_ERROR = 0;
    const USER_NOT_FOUND = 1;
    const USER_NOT_ACTIVE = 2;
    const INVALID_PASSWORD = 3;
    const IN_MAINTENANCE_MODE = 4;
    const ACCOUNT_SUSPENDED = 5;
    const ACCOUNT_PENDING_FOR_DELETION = 6;

    /**
     * Construct authentication error
     *
     * @param int $reason
     * @param string $message
     */
    function __construct($reason = AuthenticationError::UNKNOWN_ERROR, $message = null) {
      if(empty($message)) {
        switch($reason) {
          case self::USER_NOT_FOUND:
            $message = lang('User account not found');
            break;
          case self::USER_NOT_ACTIVE:
            $message = lang('User account is no longer active');
            break;
          case self::INVALID_PASSWORD:
            $message = lang('Invalid password');
            break;
          case self::IN_MAINTENANCE_MODE:
            $message = ConfigOptions::getValue('maintenance_message');
            if(empty($message)) {
              $message = lang('System is in maintenance mode');
            } // if
            break;
          case self::ACCOUNT_SUSPENDED:
            $message = lang('This account is suspended at the moment. Please come back later.');
            break;
          case self::ACCOUNT_PENDING_FOR_DELETION:
            $message = lang('Account is pending for deletion');
            break;
          default:
            $message = lang('Unknown error. Please contact support for assistance');
        } // if
      } // if

      parent::__construct($message, array(
        'reason' => $reason,
      ));
    } // __construct

  }