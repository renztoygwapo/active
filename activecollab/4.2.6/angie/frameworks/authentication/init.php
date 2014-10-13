<?php

  /**
   * Authentication framwork initialization class
   *
   * @package angie.frameworks.authentication
   */

  const AUTHENTICATION_FRAMEWORK = 'authentication';
  const AUTHENTICATION_FRAMEWORK_PATH = __DIR__;

  // ---------------------------------------------------
  //  Configuration
  // ---------------------------------------------------

  defined('AUTH_PROVIDER') or define('AUTH_PROVIDER', 'BasicAuthenticationProvider'); // Authentication provider
  defined('USER_SESSION_LIFETIME') or define('USER_SESSION_LIFETIME', 1800); // 30 minutes;
  defined('CLEAN_OLD_SESSION_ON_EACH_REQUEST') or define('CLEAN_OLD_SESSION_ON_EACH_REQUEST', true); // Clean old session request on each request, good for setups that don't have scheduled tasks configured
  defined('AUTHENTICATION_FRAMEWORK_INJECT_INTO') or define('AUTHENTICATION_FRAMEWORK_INJECT_INTO', 'system');
  defined('AUTHENTICATION_FRAMEWORK_ADMIN_ROUTE_BASE') or define('AUTHENTICATION_FRAMEWORK_ADMIN_ROUTE_BASE', 'admin');
  
  require_once AUTHENTICATION_FRAMEWORK_PATH . '/models/Authentication.class.php';
  require_once AUTHENTICATION_FRAMEWORK_PATH . '/models/providers/AuthenticationProvider.class.php';
  
  AngieApplication::setForAutoload(array(
    'IUser' => AUTHENTICATION_FRAMEWORK_PATH . '/models/IUser.class.php',

    'PasswordPolicy' => AUTHENTICATION_FRAMEWORK_PATH . '/models/PasswordPolicy.class.php',
    
    'IUsersContext' => AUTHENTICATION_FRAMEWORK_PATH . '/models/IUsersContext.class.php', 
    'IUsersContextImplementation' => AUTHENTICATION_FRAMEWORK_PATH . '/models/IUsersContextImplementation.class.php', 
    
    'IUserAvatarImplementation' => AUTHENTICATION_FRAMEWORK_PATH . '/models/IUserAvatarImplementation.class.php',
    'IUserInspectorImplementation' => AUTHENTICATION_FRAMEWORK_PATH . '/models/IUserInspectorImplementation.class.php',
    'IUserStateImplementation' => AUTHENTICATION_FRAMEWORK_PATH . '/models/IUserStateImplementation.class.php',

    'FwAnonymousUser' => AUTHENTICATION_FRAMEWORK_PATH . '/models/FwAnonymousUser.class.php', 
    'IAnonymousUserAvatarImplementation' => AUTHENTICATION_FRAMEWORK_PATH . '/models/IAnonymousUserAvatarImplementation.class.php', 

    // Base user classes
    'FwUser' => AUTHENTICATION_FRAMEWORK_PATH . '/models/users/FwUser.class.php', 
    'FwUsers' => AUTHENTICATION_FRAMEWORK_PATH . '/models/users/FwUsers.class.php',

    // User roles
    'FwMember' => AUTHENTICATION_FRAMEWORK_PATH . '/models/user_roles/FwMember.class.php',
    'FwAdministrator' => AUTHENTICATION_FRAMEWORK_PATH . '/models/user_roles/FwAdministrator.class.php',
   
    'WhosOnlineHomescreenWidget' => AUTHENTICATION_FRAMEWORK_PATH . '/models/homescreen_widgets/WhosOnlineHomescreenWidget.class.php',

    // Errors
  	'AuthenticationError' => AUTHENTICATION_FRAMEWORK_PATH . '/errors/AuthenticationError.class.php',
  	'LastAdministratorRoleChangeError' => AUTHENTICATION_FRAMEWORK_PATH . '/errors/LastAdministratorRoleChangeError.class.php',

    // Search
    'FwUsersSearchIndex' => AUTHENTICATION_FRAMEWORK_PATH . '/models/search/FwUsersSearchIndex.class.php', 
    'FwIUserSearchItemImplementation' => AUTHENTICATION_FRAMEWORK_PATH . '/models/search/FwIUserSearchItemImplementation.class.php', 
  
    // API client subscriptions
    'FwApiClientSubscription' => AUTHENTICATION_FRAMEWORK_PATH . '/models/api_client_subscriptions/FwApiClientSubscription.class.php',
    'FwApiClientSubscriptions' => AUTHENTICATION_FRAMEWORK_PATH . '/models/api_client_subscriptions/FwApiClientSubscriptions.class.php',
    'ApiClientSubscriptionError' => AUTHENTICATION_FRAMEWORK_PATH . '/models/api_client_subscriptions/ApiClientSubscriptionError.class.php',

    // Callbacks
    'LoginAsFormCallback' => AUTHENTICATION_FRAMEWORK_PATH . '/models/javascript_callbacks/LoginAsFormCallback.class.php', 
    'ProfileMenuItemCallback' => AUTHENTICATION_FRAMEWORK_PATH . '/models/javascript_callbacks/ProfileMenuItemCallback.class.php',

    // Notifications
    'FwWelcomeNotification' => AUTHENTICATION_FRAMEWORK_PATH . '/notifications/FwWelcomeNotification.class.php',
    'FwForgotPasswordNotification' => AUTHENTICATION_FRAMEWORK_PATH . '/notifications/FwForgotPasswordNotification.class.php',
    'FwPasswordChangedNotification' => AUTHENTICATION_FRAMEWORK_PATH . '/notifications/FwPasswordChangedNotification.class.php',
	  'FwFailedLoginNotification' => AUTHENTICATION_FRAMEWORK_PATH . '/notifications/FwFailedLoginNotification.class.php',
	  'FwUserFailedLoginNotification' => AUTHENTICATION_FRAMEWORK_PATH . '/notifications/FwUserFailedLoginNotification.class.php',

	  // Security
	  'FwSecurityLog' => AUTHENTICATION_FRAMEWORK_PATH . '/models/security_logs/FwSecurityLog.class.php',
	  'FwSecurityLogs' => AUTHENTICATION_FRAMEWORK_PATH . '/models/security_logs/FwSecurityLogs.class.php',
	  'ISecurityLog' => AUTHENTICATION_FRAMEWORK_PATH . '/models/security_logs/ISecurityLog.class.php',
	  'ISecurityLogImplementation' => AUTHENTICATION_FRAMEWORK_PATH . '/models/security_logs/ISecurityLogImplementation.class.php',
  ));

  DataObjectPool::registerTypeLoader(array('User', 'Member', 'Administrator'), function($ids) {
    return Users::findByIds($ids);
  });
