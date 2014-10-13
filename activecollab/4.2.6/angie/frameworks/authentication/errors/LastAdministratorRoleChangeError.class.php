<?php

  /**
   * Error that is throw when we try to change the role of the last administrator
   *
   * @package angie.frameworks.authentication
   * @subpackage models
   */
  class LastAdministratorRoleChangeError extends Error {

    /**
     * Construct error instance
     *
     * @param string $user
     * @param string $message
     */
    function __construct($user, $message = null) {
      $user_id = $user instanceof User ? $user->getId() : $user;

      if(empty($message)) {
        $message = "Can't change role of user #{$user_id} because that is the last account with Administrator role";
      } // if

      parent::__construct($message, array(
        'user_id' =>$user_id,
      ));
    } // __construct

  }