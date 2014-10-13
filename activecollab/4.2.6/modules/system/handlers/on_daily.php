<?php

  /**
   * System handle daily tasks
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */

  /**
   * Do daily tasks
   */
  function system_handle_on_daily() {
    if (!AngieApplication::isOnDemand() && CHECK_FOR_NEW_VERSION) {
      $version_details = response_from_server(AngieApplication::getCheckForUpdatesUrl());

      if (isset($version_details) && strpos($version_details, 'latest_version') !== false) {
        $version_details = JSON::decode($version_details);
        $license_details = array_var($version_details, 'license');
        $license_urls = array_var($license_details, 'urls');

        $new_modules_available = array();
        $available_module_names = AngieApplication::getOfficialModuleNames();
        $paid_modules = array_var($license_details, 'modules_list');
        if (is_foreachable($paid_modules)) {
          foreach ($paid_modules as $paid_module) {
            if (!in_array($paid_module, $available_module_names)) {
              $new_modules_available[] = $paid_module;
            } // if
          } // if
        } // if

        if (!is_foreachable($new_modules_available)) {
          $new_modules_available = false;
        } // if

        ConfigOptions::setValue(array(
          'license_details_updated_on' => time(),
          'latest_version' => array_var($version_details, 'latest_version'),
          'latest_available_version' => array_var($version_details, 'latest_available_version'),
          'license_expires' => array_var($license_details, 'expires'),
          'license_copyright_removed' => array_var($license_details, 'branding_removed'),
          'remove_branding_url' => array_var($license_urls, 'remove_branding'),
          'renew_support_url' => array_var($license_urls, 'renew_support'),
          'update_instructions_url' => array_var($license_urls, 'update_instructions'),
          'update_archive_url' => array_var($license_urls, 'update_archive'),
          'new_modules_available' => $new_modules_available
        ));
      } // if
    } // if
  } // system_handle_on_daily
