<?php
  /**
   * Configuration file used by activeCollab before system is installed
   */

  define('ROOT', realpath(CONFIG_PATH . '/../activecollab'));
  define('ROOT_URL', 'http://activecollab.dev/public');

  define('FORCE_ROOT_URL', false);

  if(!defined('CONFIG_PATH')) {
    define('CONFIG_PATH', dirname(__FILE__));
  } // if

  define('USE_UNPACKED_FILES', true);

  define('APPLICATION_MODULES', 'system,discussions,milestones,files,calendar,tasks,status,documents,project_exporter,tracking,invoicing,notebooks,source');

  require_once CONFIG_PATH . '/license.php';
  require_once CONFIG_PATH . '/version.php';
  require_once CONFIG_PATH . '/defaults.php';

?>