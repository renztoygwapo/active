<?php

  /**
   * Custom fields initialization file
   *
   * @package angie.frameworks.custom_fields
   */

  const CUSTOM_FIELDS_FRAMEWORK = 'custom_fields';
  const CUSTOM_FIELDS_FRAMEWORK_PATH = __DIR__;

  // Inject custom fields framework into specified module
  defined('CUSTOM_FIELDS_FRAMEWORK_INJECT_INTO') or define('CUSTOM_FIELDS_FRAMEWORK_INJECT_INTO', 'system');

  AngieApplication::setForAutoload(array(
    'FwCustomFields' => CUSTOM_FIELDS_FRAMEWORK_PATH . '/models/FwCustomFields.class.php',

    'ICustomFields' => CUSTOM_FIELDS_FRAMEWORK_PATH . '/models/ICustomFields.class.php',
    'ICustomFieldsImplementation' => CUSTOM_FIELDS_FRAMEWORK_PATH . '/models/ICustomFieldsImplementation.class.php',

    'CustomFieldInspectorProperty' => CUSTOM_FIELDS_FRAMEWORK_PATH . '/models/CustomFieldInspectorProperty.class.php',
  ));