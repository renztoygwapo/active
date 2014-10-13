<?php

  /**
   * on_context_domains event handler implementation
   * 
   * @package angie.frameworks.authentication
   * @subpackage handlers
   */

  /**
   * Handle on_context_domains events
   * 
   * @param IUser $user
   * @param array $contexts
   */
  function authentication_handle_on_context_domains(IUser &$user, &$contexts) {
    $contexts[] = 'users';
  } // authentication_handle_on_context_domains