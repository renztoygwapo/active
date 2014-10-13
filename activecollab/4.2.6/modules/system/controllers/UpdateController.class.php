<?php

// Build on top of admin controller
AngieApplication::useController('admin', SYSTEM_MODULE);

/**
 * Update Controller
 *
 * @package activeCollab.modules.system
 * @subpackage controllers
 */
class UpdateController extends AdminController {

  /**
   * Index action
   */
  function index() {
    $this->response->assign(array(
      'upgrade_script_url' => UPGRADE_SCRIPT_URL,
      'new_modules' => ConfigOptions::getValue('new_modules_available'),
    ));
  } // index

  /**
   * New version page action
   */
  function check_for_new_version() {
    $config_options = ConfigOptions::getValue(array(
      'latest_version',
      'latest_available_version',
      'renew_support_url',
      'update_instructions_url'
    ));

    $this->response->assign(array(
      'current_version' => AngieApplication::getVersion(),
      'latest_version' => array_var($config_options, 'latest_version'),
      'latest_available_version' => array_var($config_options, 'latest_available_version'),
      'update_instructions_url' => $config_options['update_instructions_url'] ? $config_options['update_instructions_url'] : UPDATE_INSTRUCTIONS_URL,
      'renew_support_url' => $config_options['renew_support_url'] ? $config_options['renew_support_url'] : RENEW_SUPPORT_URL,
    ));
  } // new_version

  /**
   * Save system information
   */
  function save_license_details() {
    if ($this->request->isSubmitted()) {
      $license_details = $this->request->post('license_details');

      try {
        ConfigOptions::setValue(array(
          'license_details_updated_on' => time(),
          'latest_version' => array_var($license_details, 'latest_version'),
          'latest_available_version' => array_var($license_details, 'latest_available_version'),
          'license_expires' => array_var($license_details, 'license_expires'),
          'license_copyright_removed' => (bool) array_var($license_details, 'license_copyright_removed'),
          'remove_branding_url' => array_var($license_details, 'remove_branding_url'),
          'renew_support_url' => array_var($license_details, 'renew_support_url'),
          'update_instructions_url' => array_var($license_details, 'update_instructions_url'),
          'new_modules_available' => array_var($license_details, 'new_modules_available')
        ));
        $this->response->ok();
      } catch (Exception $e) {
        $this->response->exception($e);
      } // if
    } else {
      $this->response->badRequest();
    } // if
  } // save_system_info

  /**
   * Check password
   */
  function check_password() {
    if (!($this->request->isSubmitted() && $this->request->isAsyncCall())) {
      $this->response->badRequest();
    } // if

    try {
      $entered_password = $this->request->post('password');
      if (!$entered_password) {
        throw new Error(lang('Password is required'));
      } // if

      if (!$this->logged_user->isCurrentPassword($entered_password)) {
        throw new Error(lang('Password you entered is not valid'));
      } // if

      // validate before upgrade
      require_once ANGIE_PATH . "/classes/application/upgrader/AngieApplicationUpgrader.class.php";
      require_once ANGIE_PATH . "/classes/application/upgrader/AngieApplicationUpgraderAdapter.class.php";
      require_once ANGIE_PATH . "/classes/application/upgrader/AngieApplicationUpgradeScript.class.php";

      // validate current setup
      $errors = array();
      AngieApplicationUpgrader::init();
      if (!AngieApplicationUpgrader::validateBeforeUpgrade(array('email' => $this->logged_user->getEmail(), 'pass' => $entered_password))) {
        $validation_errors = AngieApplicationUpgrader::getValidationLog();
        if (is_foreachable($validation_errors)) {
          foreach ($validation_errors as $validation_error) {
            if ($validation_error['status'] == 'error') {
              $errors[] = $validation_error['message'];
            } // if
          } // foreach
        } // if
      } // if

      // we are using phar, and were upgrading to same version
      if (str_starts_with(__FILE__, 'phar://') && (version_compare(ConfigOptions::getValue('latest_available_version'), APPLICATION_VERSION) == 0)) {
        $phar_file = ROOT . '/' . APPLICATION_VERSION . '.phar';
        if (is_file($phar_file) && !is_writable($phar_file)) {
          $errors[] = lang('File :file_path has to be writable', array('file_path' => $phar_file));
        } // if
      } // if

      // if there are errors, let user know
      if (is_foreachable($errors)) {
        throw new Error(lang('There are some errors that need to be corrected. Please run the upgrade again after they are fixed'), array('validation_errors' => $errors));
      } // if

      // reset the license details updated timestamp, so next visit to the administration page will retrieve latest information from server
      ConfigOptions::setValue('license_details_updated_on', null);

      $this->response->ok();
    } catch (Exception $e) {
      $this->response->exception($e);
    } // try
  } // check_password

  /**
   * Download update package action
   */
  function download_update_package() {
    // take as much time you need
    set_time_limit(0);

    /**
     * Set update download progress
     *
     * @param int $progress
     * @param bool $force
     * @return bool
     */
    function set_update_download_progress($progress, $force = false) {
      static $old_progress = 0;

      // progress has to be number between 0 and 100
      $progress = $progress < 0 ? 0 : $progress > 100 ? 100 : $progress;

      if ($progress <= $old_progress && !$force) {
        return false;
      } // if

      $old_progress = $progress;

      // set the config option
      ConfigOptions::setValue('update_download_progress', $progress);
    } // set_update_download_progress

    try {
      set_update_download_progress(0, true); // set the download progress to 0

      @session_write_close(); // free up the activecollab for other requests

      // download update from activecollab server
      list ($file_name, $file_path, $file_size, $headers) = download_from_server(AngieApplication::getDownloadUpdateUrl(), WORK_PATH, get_application_authentication_headers(), function ($total, $downloaded) {
        $percents = 0;
        if ($total > 0) {
          $percents = round($downloaded * 100 / $total);
        } // if
        
        set_update_download_progress($percents);
      });

      if (!is_file($file_path) || !$headers) {
        $error_message = lang("Update download went wrong. Please try again later");
        Logger::log($error_message, Logger::ERROR, 'upgrade');
        throw new Error($error_message);
      } // if

      $response_error = array_var($headers, 'ac-error', null);
      if ($response_error) {
        Logger::log($response_error, Logger::ERROR, 'upgrade');
        throw new Error($response_error);
      } // if

      $response_archive_filename = array_var($headers, 'ac_archive_filename', null);
      $response_archive_md5 = array_var($headers, 'ac_archive_md5', null);
      $response_archive_version = str_ends_with($response_archive_filename, '.phar') ? substr($response_archive_filename, 0, strlen($response_archive_filename) - 5) : $response_archive_filename;
      $downloaded_file_md5 = md5_file($file_path);

      if ($downloaded_file_md5 != $response_archive_md5) {
        $error_message = lang("Downloading update failed. File size of downloaded file doesn't match one on the server");
        Logger::log($error_message, Logger::ERROR, 'upgrade');
        throw new Error($error_message);
      } // if

      // complete the download
      set_update_download_progress(100, true);

      // respond with data
      $this->response->respondWithData(array(
        "success" => "success",
        "package_filename" => $file_name,
        "package_version" => $response_archive_version
      ));
    } catch (Exception $e) {
      $this->response->exception($e);
    } // try
  } // download_update_package

  /**
   * Get download progress
   */
  function check_download_progress() {
    if (!$this->request->isAsyncCall()) {
      $this->response->badRequest();
    } // if

    $this->response->respondWithData(array(
      'progress' => ConfigOptions::getValue('update_download_progress')
    ));
  } // check_download_progress

  /**
   * Unpack update package
   */
  function unpack_update_package() {
    if (!$this->request->isAsyncCall()) {
      $this->response->badRequest();
    } // if

    if (!$this->request->isSubmitted()) {
      $this->response->badRequest();
    } // if

    try {
      $package_version = strtolower($this->request->post('package_version'));
      if (!$package_version) {
        throw new Error(lang('Package version is required parameter'));
      } // if

      $package_filename = $this->request->post('package_filename');
      if (!$package_filename) {
        throw new Error(lang('Package filename is required parameter'));
      } // if

      $package_file_path = WORK_PATH . '/' . $package_filename;
      if (!is_file($package_file_path)) {
        throw new Error(lang('Package file does not exists. Please try downloading package again'));
      } // if

      if (!folder_is_writable(ROOT)) {
        throw new Error(lang(':folder is not writable. To use auto upgrade, that folder has to be writable', array('folder' => ROOT)));
      } // if

      // determine destination directory
      $destination_folder = ROOT . '/' . $package_version;

      // if directory already exists try to rename it
      if (is_dir($destination_folder)) {

        // we needed to use JSON class because auto load
        // had to load it, before we move the directory
        // of active activecollab version
        JSON::encode('');

        // determine new name for existing folder
        $counter = 1;
        do {
          $new_folder_name = $destination_folder . '_' . str_pad($counter, 2, '0', STR_PAD_LEFT);
          $counter++;
        } while(is_dir($new_folder_name));

        // try to move old folder
        if (!@rename($destination_folder, $new_folder_name)) {
          throw new Error(lang('Failed to rename existing folder :folder', array('folder' => $destination_folder)));
        } // if
      } // if

      // try to create destination folder
      if (!@mkdir($destination_folder)) {
        throw new Error(lang('Could not create destination folder :folder', array('folder' => $destination_folder)));
      } // if

      // load phar file
      $phar = new Phar($package_file_path);

      // extract it to destination folder
      $phar->extractTo($destination_folder, null, true);

      // remove update package
      @unlink($package_file_path);

      $this->response->respondWithData(array(
        'success' => 'success',
      ));
    } catch (Exception $e) {
      $this->response->exception($e);
    } // try
  } // unpack_update_package

  /**
   * Get upgrade steps
   */
  function get_upgrade_steps() {
    if (!$this->request->isAsyncCall()) {
      $this->response->badRequest();
    } // if

    // include latest upgrade classes
    AngieApplication::includeLatestUpgradeClasses();
    // initialize upgrader
    AngieApplicationUpgrader::init();

    $steps = array();
    $grouped_actions = AngieApplicationUpgrader::getGroupedActions();

    if (is_foreachable($grouped_actions)) {
      $steps[] = array(
        'action'      => 'startUpgrade',
        'description' => 'Starting upgrade',
      );
      foreach ($grouped_actions as $group => $group_actions) {
        if (is_foreachable($group_actions)) {
          foreach ($group_actions as $action => $description) {
            $steps[] = array(
              'group'       => $group,
              'action'      => $action,
              'description' => $description
            );
          }
        } // if
      } // foreach

      $steps[0]['group'] = $group;
    } // if

    $this->response->respondWithData(array(
      'steps' => $steps
    ));
  } // get_upgrade_steps

  /**
   * Install new modules
   */
  function install_new_modules() {
    if (!$this->request->isAsyncCall()) {
      $this->response->badRequest();
    } // if

    $modules = $this->request->get('modules');
    if (!is_foreachable($modules)) {
      $this->response->ok();
    } // if

    $errors = array();
    $warnings = array();

    // install every module
    foreach ($modules as $module_name) {
      try {
        $module = AngieApplication::getModule($module_name, false);

        // check if module exists
        if (!($module instanceof AngieModule)) {
          throw new Error(lang('Module :module_name cannot be found', array('module_name' => $module_name)));
        } // if

        // if module is installed already, we need to skip further progress
        if ($module->isInstalled(false)) {
          throw new Error(lang('Module :module_name is already installed', array('module_name' => $module_name)));
        } // if

        // check if module can be installed
        if (!$module->canBeInstalled($log)) {
          throw new Error(lang('Module :module_name cannot be installed automatically, please install it manually', array('module_name' => $module_name)));
        } // if

        $module->install();

      } catch (Error $e) {
        $errors[] = $e->getMessage();
      } // try
    } // foreach

    $this->response->respondWithData(array(
      'errors' => $errors,
    ));
  } // install_new_modules
}