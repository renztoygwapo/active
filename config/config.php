<?php

  /**
   * activeCollab configuration file
   *
   * Automatically generated by installer script on 2014-04-21
   */
  
  const ROOT = 'C:\\wamp\\www\\active\\activecollab';
  const ROOT_URL = 'http://localhost/active/public';
  const DB_HOST = 'localhost';
  const DB_USER = 'root';
  const DB_PASS = '';
  const DB_NAME = 'abag_devcrm';

  const DB_CAN_TRANSACT = NULL;
  const TABLE_PREFIX = 'acx_';
  const ADMIN_EMAIL = 'renztoygwapo@gmail.com';
  const APPLICATION_UNIQUE_KEY = 'UHDHndnt0ECAnjyqFmadtN7U07LvQFAcC8qGkKEP';
  const USE_UNPACKED_FILES = true;
  const COOKIE_DOMAIN = '';


    defined('CONFIG_PATH') or define('CONFIG_PATH', dirname(__FILE__));

  require_once CONFIG_PATH . '/version.php';
  require_once CONFIG_PATH . '/license.php';
  require_once CONFIG_PATH . '/defaults.php';
  /** [development environment]
    *
    * local development configuration
    * uncomment for local development
  
  const ROOT = '/var/www/crm.drivegas.com/activecollab';
  const ROOT_URL = 'http://crm.drivegas.com/public';
  const DB_HOST = 'localhost';
  const DB_USER = 'root';
  const DB_PASS = 'bebe28217joy';
  const DB_NAME = 'crm_drivegas_ac_db';
  const DB_CAN_TRANSACT = NULL;
  const TABLE_PREFIX = 'acx_';
  const ADMIN_EMAIL = 'jeromedacones@gmail.com';
  const APPLICATION_UNIQUE_KEY = 'UHDHndnt0ECAnjyqFmadtN7U07LvQFAcC8qGkKEP';
  const USE_UNPACKED_FILES = true;
  const COOKIE_DOMAIN = '';
  
  **/
