<?php

  /**
   * Avatars framework initialisation file
   *
   * @package angie.framework.avatars
   */
  
  define('AVATAR_FRAMEWORK', 'avatar');
  define('AVATAR_FRAMEWORK_PATH', ANGIE_PATH . '/frameworks/avatar');
  
  define('AVATAR_FRAMEWORK_INJECT_INTO', 'system');
  
  AngieApplication::setForAutoload(array(
		'IAvatar' => AVATAR_FRAMEWORK_PATH . '/models/IAvatar.class.php',
		'IAvatarImplementation' => AVATAR_FRAMEWORK_PATH . '/models/IAvatarImplementation.class.php',
		'AvatarInspectorWidget' => AVATAR_FRAMEWORK_PATH . '/models/AvatarInspectorWidget.class.php',  
  ));