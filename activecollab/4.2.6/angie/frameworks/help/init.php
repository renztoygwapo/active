<?php

  /**
   * Help framework initialization file
   *
   * @package angie.frameworks.help
   */

  const HELP_FRAMEWORK = 'help';
  const HELP_FRAMEWORK_PATH = __DIR__;

  // Inject help framework into given module
  defined('HELP_FRAMEWORK_INJECT_INTO') or define('HELP_FRAMEWORK_INJECT_INTO', 'system');

  AngieApplication::setForAutoload(array(
    'FwAngieHelpDelegate' => HELP_FRAMEWORK_PATH . '/models/FwAngieHelpDelegate.class.php',

    'FwHelpElement' => HELP_FRAMEWORK_PATH . '/models/elements/FwHelpElement.class.php',
    'FwHelpBook' => HELP_FRAMEWORK_PATH . '/models/elements/FwHelpBook.class.php',
    'FwHelpBookPage' => HELP_FRAMEWORK_PATH . '/models/elements/FwHelpBookPage.class.php',
    'FwHelpVideo' => HELP_FRAMEWORK_PATH . '/models/elements/FwHelpVideo.class.php',
    'FwHelpWhatsNewArticle' => HELP_FRAMEWORK_PATH . '/models/elements/FwHelpWhatsNewArticle.class.php',
    'FwHelpElementHelpers' => HELP_FRAMEWORK_PATH . '/models/elements/FwHelpElementHelpers.class.php',

    'FwHelpSearchIndex' => HELP_FRAMEWORK_PATH . '/models/search/FwHelpSearchIndex.class.php',
    'FwIHelpElementSearchItemImplementation' => HELP_FRAMEWORK_PATH . '/models/search/FwIHelpElementSearchItemImplementation.class.php',

    'TwitterIntentCallback' => HELP_FRAMEWORK_PATH . '/models/javascript_callbacks/TwitterIntentCallback.class.php',
    'InitLiveChatCallback' => HELP_FRAMEWORK_PATH . '/models/javascript_callbacks/InitLiveChatCallback.class.php',
  ));