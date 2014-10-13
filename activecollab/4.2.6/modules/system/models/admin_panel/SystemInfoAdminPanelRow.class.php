<?php

  /**
   * System information administration panel row
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class SystemInfoAdminPanelRow implements IAdminPanelRow {

    /**
     * Return row title
     *
     * @return string
     */
    function getTitle() {
      return lang('System Information');
    } // getTitle

    /**
     * Return true if this row is not empty (it has content to display)
     *
     * @return boolean
     */
    function hasContent() {
      return true;
    } // hasContent

    /**
     * Return row content
     *
     * @return string
     */
    function getContent() {
      AngieApplication::useWidget('activecollab_system_info', SYSTEM_MODULE);

      $config_options = ConfigOptions::getValue(array(
        'license_details_updated_on',
        'latest_version',
        'latest_available_version',
        'license_copyright_removed',
        'license_expires',
        'new_modules_available',
        'remove_branding_url',
        'renew_support_url',
      ));

      $system_info_data = array(
        'application_version'         => AngieApplication::getVersion(),
        'application_build'           => AngieApplication::getBuild(),
        'license_key'                 => LICENSE_KEY,
        'license_uid'                 => LICENSE_UID,
        'license_details_updated_on'  => $config_options['license_details_updated_on'],
        'latest_version'              => $config_options['latest_version'],
        'latest_available_version'    => $config_options['latest_available_version'] ? $config_options['latest_available_version'] : null,
        'license_expires'             => $config_options['license_expires'] ? $config_options['license_expires'] : DateValue::makeFromString(LICENSE_EXPIRES)->getTimestamp(),
        'license_copyright_removed'   => isset($config_options['license_copyright_removed']) ? $config_options['license_copyright_removed'] : LICENSE_COPYRIGHT_REMOVED,
        'new_modules_available'       => isset($config_options['new_modules_available']) ? (boolean) $config_options['new_modules_available'] : false,
        'remove_branding_url'         => $config_options['remove_branding_url'] ? $config_options['remove_branding_url'] : REMOVE_BRANDING_URL,
        'renew_support_url'           => $config_options['renew_support_url'] ? $config_options['renew_support_url'] : RENEW_SUPPORT_URL,
        'php_version'                 => PHP_VERSION,
        'mysql_version'               => DB::getConnection()->getServerVersion(),
        'update_details_url'          => Router::assemble('new_version_details'),
        'update_url'                  => Router::assemble('application_update'),
        'save_license_details_url'    => Router::assemble('save_license_details'),
        'official_modules'            => AngieApplication::getOfficialModuleNames(),
        'force_refresh'               => isset($_GET['check_for_new_version']) && $_GET['check_for_new_version'],
        'check_for_new_version'       => CHECK_FOR_NEW_VERSION
      );

      $result = '<div class="system_info" id="admin_system_info"></div><script type="text/javascript">App.widgets.activeCollabSystemInfo.init("admin_system_info", ' . JSON::encode($system_info_data) . ')</script>';

      return $result;
    } // getContent

  }