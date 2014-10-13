<?php

  /**
   * History framework initialization file
   *
   * @package angie.frameworks.history
   */
  
  const HISTORY_FRAMEWORK = 'history';
  const HISTORY_FRAMEWORK_PATH = __DIR__;
  
  // Inject history framework into given module
  defined('HISTORY_FRAMEWORK_INJECT_INTO') or define('HISTORY_FRAMEWORK_INJECT_INTO', 'system');
  
  AngieApplication::setForAutoload(array(
    'FwModificationLog' => HISTORY_FRAMEWORK_PATH . '/models/modification_logs/FwModificationLog.class.php', 
    'FwModificationLogs' => HISTORY_FRAMEWORK_PATH . '/models/modification_logs/FwModificationLogs.class.php', 
    
    'IHistory' => HISTORY_FRAMEWORK_PATH . '/models/IHistory.class.php', 
    'IHistoryImplementation' => HISTORY_FRAMEWORK_PATH . '/models/IHistoryImplementation.class.php', 
    'FwHistoryRenderer' => HISTORY_FRAMEWORK_PATH . '/models/FwHistoryRenderer.class.php', 
  ));