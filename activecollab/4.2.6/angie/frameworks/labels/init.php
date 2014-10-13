<?php

  /**
   * Labels framework initialization file
   *
   * @package angie.frameworks.labels
   */
  
  const LABELS_FRAMEWORK = 'labels';
  const LABELS_FRAMEWORK_PATH = __DIR__;
  
  defined('LABELS_FRAMEWORK_INJECT_INTO') or define('LABELS_FRAMEWORK_INJECT_INTO', 'system'); // Inject labels framework into given module
  defined('LABELS_FRAMEWORK_ADMIN_ROUTE_BASE') or define('LABELS_FRAMEWORK_ADMIN_ROUTE_BASE', 'admin'); // Base string for all admin routes
  
  AngieApplication::setForAutoload(array(
    'ILabel' => LABELS_FRAMEWORK_PATH . '/models/ILabel.class.php',
    'ILabelImplementation' => LABELS_FRAMEWORK_PATH . '/models/ILabelImplementation.class.php',
    
    'FwLabel' => LABELS_FRAMEWORK_PATH . '/models/labels/FwLabel.class.php',
    'FwLabels' => LABELS_FRAMEWORK_PATH . '/models/labels/FwLabels.class.php',
   
    'LabelInspectorTitlebarWidget' => LABELS_FRAMEWORK_PATH . '/models/LabelInspectorTitlebarWidget.class.php',
    'LabelInspectorProperty' => LABELS_FRAMEWORK_PATH . '/models/LabelInspectorProperty.class.php',
  ));

  DataObjectPool::registerTypeLoader('Label', function($ids) {
    return Labels::findByIds($ids);
  });