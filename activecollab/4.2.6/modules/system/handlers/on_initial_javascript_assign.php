<?php

  /**
   * on_initial_javascript_assign event handler
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */

  /**
   * Populate initial JavaScript variables list
   *
   * @param $variables
   */
  function system_handle_on_initial_javascript_assign(&$variables) {
    $variables['branding_removed'] = ConfigOptions::getValue('license_copyright_removed');
    $variables['branding_title'] = 'Powered by activeCollab';
    $variables['branding_description'] = 'Project Management, Time Tracking and Billing';
    $variables['branding_website'] = 'http://www.activecollab.com';
  } // system_handle_on_initial_javascript_assign