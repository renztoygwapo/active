<?php
  
  /**
   * on_daily event handler
   * 
   * @package angie.frameworks.authentication
   * @subpackage handlers
   */

  /**
   * Handle on_daily event
   */
  function authentication_handle_on_daily() {
    if(AUTH_PROVIDER == 'BasicAuthenticationProvider' && !CLEAN_OLD_SESSION_ON_EACH_REQUEST) {
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'user_sessions WHERE expires_on < ?', date(DATETIME_MYSQL)); // Expire old sessions
    } // if
  } // authentication_handle_on_daily