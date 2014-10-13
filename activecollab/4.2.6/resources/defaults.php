<?php

  /**
   * Default configuration values
   *
   * @package activeCollab.resources
   */
  
  define('APPLICATION_NAME', 'ActiveCollab');
  define('APPLICATION_BUILD', '14155');
  define('APPLICATION_PATH', ROOT . '/' . APPLICATION_VERSION); // If we are using unpacked file, make sure that value is well set

  define('FAVORITES_FRAMEWORK_DEFINE_ROUTES', false); // Use activeCollab specific favorites routes

  define('TEST_SMTP_BY_SENDING_EMAIL_TO', 'noreply@activecollab.com');

  defined('APPLICATION_MODE') or define('APPLICATION_MODE', 'in_production');
  defined('USE_UNPACKED_FILES') or define('USE_UNPACKED_FILES', APPLICATION_MODE == 'in_development'); // Debug mode and production are valid modes for PHAR distribution

  defined('ANGIE_PATH') or define('ANGIE_PATH', APPLICATION_PATH . '/angie');

  defined('APPLICATION_FRAMEWORKS') or define('APPLICATION_FRAMEWORKS', 'environment,modules,help,globalization,authentication,activity_logs,reports,history,email,download,preview,homescreens,announcements,complete,schedule,attachments,notifications,subscriptions,comments,categories,labels,assignees,subtasks,favorites,visual_editor,file_uploader,payments,avatar,text_compare,reminders,search,custom_fields,calendars,data_sources');
  defined('APPLICATION_MODULES') or define('APPLICATION_MODULES', 'system,discussions,milestones,files,calendar,tasks,project_exporter,status,documents,notebooks');

  defined('GLOBALIZATION_ADAPTER') or define('GLOBALIZATION_ADAPTER', 'ActiveCollabGlobalizationAdapter');
  defined('APPLICATION_UNIQUE_KEY') or define('APPLICATION_UNIQUE_KEY', LICENSE_KEY);
  
  // ---------------------------------------------------
  //  Defaults MVC mapping
  // ---------------------------------------------------

  define('DEFAULT_CONTROLLER', 'backend');

  define('UPDATE_INSTRUCTIONS_URL', 'http://www.activecollab.com/docs/manuals/admin-version-3/upgrade/latest-stable');
  define('UPGRADE_TO_CORPORATE_URL', 'http://www.activecollab.com/user/' . LICENSE_UID . '/upgrade-to-corporate?license_key=' . LICENSE_KEY);
  define('REMOVE_BRANDING_URL', 'http://www.activecollab.com/user/' . LICENSE_UID . '/purchase-branding-removal?license_key=' . LICENSE_KEY);
  define('RENEW_SUPPORT_URL', 'http://www.activecollab.com/user/' . LICENSE_UID . '/extend-support?license_key=' . LICENSE_KEY);

  // ---------------------------------------------------
  //  Load framewok default configuration
  // ---------------------------------------------------

  require_once ANGIE_PATH . '/defaults.php';
