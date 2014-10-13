<?php

/**
 * Framework level disk space implementation
 *
 * @package angie.frameworks.environment
 * @subpackage models
 */
abstract class FwDiskSpace {

  /**
   * Construct new disk space instance
   *
   * @param User $user
   */
  function __construct(User $user) {
    if($user instanceof User && $user->isAdministrator()) {
      $this->user = $user;
    } else {
      throw new InvalidInstanceError('user', $user, 'User');
    } // if
  } // __construct

  /**
   * Check if there is enaugh available space to add attachments from this email
   *
   * @param $incoming_mail
   * @return boolean
   */
  static function canImportEmailBasedOnDiskLimitation(IncomingMail $incoming_mail) {
    $attachments_size = $incoming_mail->getAttachmentsSize();
    if($attachments_size == 0) {
      return true;
    } //if

    if (self::isUsageLimitReached() || !self::has($attachments_size)) {
      return false;
    } // if
    return true;
  }//canImportEmailBasedOnDiskLimitation

  /**
   * Return max disk space
   *
   * If disk space usage is not limited, system will return NULL
   *
   * @return integer
   */
  static function getLimit() {
    // config option is alpha and omega
    if (defined('LIMIT_DISK_SPACE_USAGE') && LIMIT_DISK_SPACE_USAGE) {
      return LIMIT_DISK_SPACE_USAGE;
    } // if

    // on demand package limitation
    if (AngieApplication::isOnDemand()) {
      return OnDemand::getCurrentPlan()->getDiskUsageLimit();
    } // if

    // configuration option from database
    $database_space_limit = ConfigOptions::getValue('disk_space_limit');
    if (isset($database_space_limit) && $database_space_limit) {
      return $database_space_limit;
    } // if

    return null;
  } // getLimit

  /**
   * Get low space threshold value
   *
   * @return bool|float
   */
  static function getLowSpaceThreshold() {
    $limit = self::getLimit();
    if (!$limit) {
      return false;
    } // if

    return floor($limit * ConfigOptions::getValue('disk_space_low_space_threshold', 95) / 100);
  } // getLowSpaceThreshold

  /**
   * Cached used disk space value
   *
   * @var bool
   */
  static private $disk_space_usage = false;

  /**
   * Total disk usage cache
   *
   * @var bool
   */
  static private $total_disk_space_usage = false;

  /**
   * Return amount of used disk space (in bytes)
   *
   * @param boolean $use_cache
   * @return integer
   */
  public static function getTotalUsage($use_cache = true) {
    self::getUsage($use_cache);
    return self::$total_disk_space_usage;
  } // getTotalUsage

  /**
   * Get Disk space usage by entries
   *
   * @param bool $use_cache
   * @return array
   */
  public static function getUsage($use_cache = true) {
    $cache_key = "disk_space_usage_" . date("Y_m_d");
    if ($use_cache) {
      return AngieApplication::cache()->get($cache_key, function() {
        return DiskSpace::calculateUsage();
      });
    } else { // ok, we will re-calculate disk space usage, but we'll cache that value too
      AngieApplication::cache()->remove($cache_key);

      DiskSpace::calculateUsage();
      AngieApplication::cache()->set($cache_key, self::$disk_space_usage);

      return self::$disk_space_usage;
    } // if
  } // getUsage

  /**
   * @return array
   */
  static function calculateUsage() {
    $disk_space_usage = array();

    if (!AngieApplication::isOnDemand()) {
      // log files size
      $log_files_size = self::calculateLogsFolderSize();
      if ($log_files_size) {
        $disk_space_usage['log_files'] = array(
          'title'         => lang('Log Files'),
          'size'          => $log_files_size,
          'color'         => '#009fb0',
          'cleanup'       => array(
            'url'                 => Router::assemble('disk_space_remove_logs'),
            'title'               => lang('Remove application logs'),
            'success_message'     => lang('Application logs removed successfully'),
            'confirm_message'     => lang('Are you sure that you want to remove application logs?')
          )
        );
      } // if

      $current_app_size = self::calculateCurrentVersionSize();
      if ($current_app_size) {
        $disk_space_usage['current_versions'] = array(
          'title'         => lang('Current :application_name version', array("application_name" => AngieApplication::getName())),
          'size'          => $current_app_size,
          'color'         => '#9d1195'
        );
      } // if

      // old application versions size
      $old_app_size = self::calculateOldVersionsSize();
      if ($old_app_size) {
        $deny_old_app_cleanup = defined("DISK_SPACE_DENY_OLD_APP_CLEANUP") && DISK_SPACE_DENY_OLD_APP_CLEANUP === true;

        $disk_space_usage['old_versions'] = array(
          'title'         => lang('Old :application_name Versions', array("application_name" => AngieApplication::getName())),
          'size'          => $old_app_size,
          'color'         => '#114d9c'
        );

        if (!$deny_old_app_cleanup) {
          $disk_space_usage['old_versions']['cleanup'] = array(
            'url'                 => Router::assemble('disk_space_remove_old_application_versions'),
            'title'               => lang('Remove old application versions'),
            'success_message'     => lang('Old application versions removed successfully'),
            'confirm_message'     => lang('Are you sure that you want to remove old application versions?')
          );
        } // if
      } // if

      // work folder size
      $work_folder_size = DiskSpace::calculateWorkFolderSize();
      if ($work_folder_size) {
        $disk_space_usage['work_folder_size'] = array(
          'title' => lang("Size of 'Work' folder"),
          'size' => $work_folder_size,
          'color' => '#d11d3b',
        );
      } // if

      if (CACHE_BACKEND == 'FileCacheBackend') {
        $cache_files_size = dir_size(CACHE_PATH);
        if ($cache_files_size) {
          $disk_space_usage['cache_size'] = array(
            'title'         => lang('Application Cache'),
            'size'          => $cache_files_size,
            'color'         => '#65303a',
            'cleanup'       => array(
              'url'                 => Router::assemble('disk_space_remove_application_cache'),
              'title'               => lang('Remove application cache'),
              'success_message'     => lang('Application cache removed successfully'),
              'confirm_message'     => lang('Are you sure that you want to remove application cache?')
            )
          );
        } // if
      } // if

      if(strtolower(AngieApplication::getName()) == 'activecollab') {
        $orphan_files_size = DiskSpace::calculateOrphansSize();
        if($orphan_files_size) {
          $disk_space_usage['orphan_files'] = array(
            'title'         => lang('Orphan Files'),
            'size'          => $orphan_files_size,
            'color'         => '#FDFF09',
            'cleanup'       => array(
              'url'                 => Router::assemble('disk_space_remove_orphan_files'),
              'title'               => lang('Remove Orphan files'),
              'success_message'     => lang('Orphan files removed successfully'),
              'confirm_message'     => lang('Are you sure that you want to remove Orphan files?')
            )
          );
        } //if
      } //if

    } // if !on_demand

    // get all disk space usage items
    EventsManager::trigger('on_used_disk_space', array(&$disk_space_usage));

    // sort by size
    uasort($disk_space_usage, function ($first, $second) {
      if ($first['size'] == $second['size']) {
        return 0;
      } // if

      return $first['size'] < $second['size'] ? 1 : -1;
    });

    // cache disk space usage
    self::$disk_space_usage = $disk_space_usage;

    // now calculate total disk space usage
    if (is_foreachable(self::$disk_space_usage)) {
      self::$total_disk_space_usage = 0;
      foreach (self::$disk_space_usage as $disk_space_usage_item) {
        self::$total_disk_space_usage += array_var($disk_space_usage_item, 'size');
      } // foreach
    } // if

    return self::$disk_space_usage;
  } // calculateUsage

  /**
   * Calculate free spaces
   *
   * @param boolean $use_cache
   * @return number
   */
  public static function getFreeSpace($use_cache = true) {
    if (!DiskSpace::getLimit()) {
      return false;
    };

    $free_space = self::getLimit() - self::getTotalUsage($use_cache);
    $free_space = $free_space < 0 ? 0 : $free_space;

    return $free_space;
  } // getFreeSpace


  /**
   * Check if we have $bytes free space
   *
   * @param integer $bytes
   * @param bool $use_cache
   * @return bool
   */
  public static function has($bytes, $use_cache = true) {
    if (!DiskSpace::getLimit()) {
      return true;
    };

    return self::getFreeSpace($use_cache) >= $bytes;
  } // has

  /**
   * Returns true if disk space usage limit is reached
   *
   * @return bool
   */
  public static function isUsageLimitReached() {
    if (DiskSpace::getLimit()) {
      return DiskSpace::getLimit() <= DiskSpace::getTotalUsage();
    } // if

    return false;
  } // isUsageLimitReached

  /**
   * Get disk space settings
   *
   * @return array
   */
  public static function getSettings() {
    return array(
      'disk_space_limit'                => self::getLimit(),
      'disk_space_email_notifications'  => (boolean) ConfigOptions::getValue('disk_space_email_notifications'),
      'disk_space_low_space_threshold'  => ConfigOptions::getValue('disk_space_low_space_threshold'),
    );
  } // getSettings

  /**
   * Set settings
   *
   * @param array $settings
   * @return boolean
   */
  public static function setSettings($settings) {
    $existing_settings = self::getSettings();

    if (is_foreachable($settings)) {
      foreach ($settings as $setting_name => $setting_value) {
        if (array_key_exists($setting_name, $existing_settings)) {
          self::setSetting($setting_name, $setting_value);
        } // if
      } // foreach
    } // if
  } // setSettings

  /**
   * Set setting
   *
   * @param string $name
   * @param mixed $value
   * @return bool|mixed
   */
  private static function setSetting($name, $value) {
    // we cannot override setting from config.php constant or if we are in on demand mode
    if ($name == 'disk_space_limit') {
      if ((defined('LIMIT_DISK_SPACE_USAGE') && LIMIT_DISK_SPACE_USAGE) || AngieApplication::isOnDemand()) {
        return false;
      } // if
    } // if

    return ConfigOptions::setValue($name, $value);
  } // setSetting

  /**
   * Describe the disk space properties
   *
   * @return array
   */
  public static function describe() {
    return array(
      'settings'                => self::getSettings(),
      'disk_space_usage'        => self::getUsage(false),
      'total_disk_space_usage'  => self::getTotalUsage(true),
      'disk_free_space'         => self::getFreeSpace(true)
    );
  } // describe


  /**
   * Perform free space check. If total usage is over threshold, notify administrators
   *
   * @return bool
   */
  public static function dailyFreeSpaceCheck() {
    $limit = self::getLimit();

    // if there is no limit set
    if (!$limit) {
      return false;
    } // if

    // if sending notifications is disabled
    if (!((boolean) ConfigOptions::getValue('disk_space_email_notifications'))) {
      return false;
    } // if

    $total_usage = self::getTotalUsage();
    $threshold = self::getLowSpaceThreshold();

    // if we are in safe zone, do nothing
    if ($total_usage < $threshold) {
      return false;
    } // if

    // determine which email template will we use
    if ($total_usage < $limit) {
      $email_template = 'low_disk_space';
    } else {
      $email_template = 'disk_space_quota_reached';
    } // if

    // send email notifications to administrators
    AngieApplication::notifications()
      ->notifyAbout('environment/' . $email_template, null)
      ->setDiskSpaceUsed(format_file_size($total_usage))
      ->setDiskSpaceLimit(format_file_size($limit))
      ->setDiskSpaceAdminUrl(Router::assemble('disk_space_admin'))
      ->sendToAdministrators();

  } // checkForLowSpace

  /**
   * Calculate current version size
   *
   * @param bool $use_cache
   * @return integer
   */
  static function calculateCurrentVersionSize($use_cache = true) {
    // get the old versions cache if needed
    $cached_value = (array) ($use_cache ? ConfigOptions::getValue('disk_space_old_versions_size') : array());
    $cached_value_updated = array();

    $application_folder = ROOT . '/' . APPLICATION_VERSION;
    if (!is_dir($application_folder)) {
      return false;
    } // if

    $application_folder_name = APPLICATION_VERSION;

    if (array_key_exists($application_folder_name, $cached_value)) {
      $folder_size = $cached_value[$application_folder_name];
    } else {
      // if there is package size that specifies package size use it
      $package_size_file = $application_folder . '/resources/package-size.php';
      if (is_file($package_size_file)) {
        $folder_size = include $package_size_file;
        // calculate directory size manually
      } else {
        // $folder_size = dir_size($application_folder);
        $folder_size = 60*1024*1024; // approximate the size to ~60MB
      } // if
    } // if

    $cached_value_updated[$application_folder_name] = $folder_size;

    // save cache
    ConfigOptions::setValue('disk_space_old_versions_size', $cached_value_updated);

    return $folder_size;
  } // calculateCurrentVersionSize

  /**
   * Calculate old versions size
   *
   * @param bool $use_cache
   * @return integer
   */
  static function calculateOldVersionsSize($use_cache = true) {
    // get all folders in ROOT
    $folders = get_folders(ROOT);

    // get the old versions cache if needed
    $cached_value = (array) ($use_cache ? ConfigOptions::getValue('disk_space_old_versions_size') : array());
    $cached_value_updated = array();

    $size = 0;
    if (is_foreachable($folders)) {
      foreach ($folders as $folder) {
        $folder_name = basename($folder); // version name

        if (strtolower($folder_name) == strtolower(AngieApplication::getVersion())) {
          continue; // skip the current version folder
        } // if

        // if we have cached value use it
        if (array_key_exists($folder_name, $cached_value)) {
          $folder_size = $cached_value[$folder_name];
        } else {
          // if there is package size that specifies package size use it
          $package_size_file = $folder . '/resources/package-size.php';
          if (is_file($package_size_file)) {
            $folder_size = include $package_size_file;
            // calculate directory size manually
          } else {
            // $folder_size = dir_size($folder);
            $folder_size = 60*1024*1024; // approximate the size to ~60MB
          } // if
        } // if

        $cached_value_updated[$folder_name] = $folder_size;
        $size += $folder_size;
      } // foreach
    } // if

    // save cache
    ConfigOptions::setValue('disk_space_old_versions_size', $cached_value_updated);

    return $size;
  } // calculateOldVersionsSize

  /**
   * Remove old versions
   *
   * @return boolean
   */
  static function removeOldVersions() {
    $folders = get_folders(ROOT);

    if (is_foreachable($folders)) {
      foreach ($folders as $folder) {
        $folder_name = basename($folder);

        if (strtolower($folder_name) == strtolower(AngieApplication::getVersion())) {
          continue;
        } // if

        empty_dir($folder);
        rmdir($folder);
      } // foreach
    } // if

    return true;
  } // removeOldVersions

  /**
   * Calculate size of work folder
   *
   * @return integer
   */
  static function calculateWorkFolderSize() {
    return dir_size(WORK_PATH);
  } // calculateWorkFolderSize

  /**
   * Calculate Logs folder size
   *
   * @return integer
   */
  static function calculateLogsFolderSize() {
    return dir_size(ENVIRONMENT_PATH . '/logs/');
  } // calculateLogsFolderSize

  /**
   * Does log files taking lot of disk space (more than 200MB)
   *
   * @return boolean
   */
  static function logsNeedRemoving() {
    return self::calculateLogsFolderSize() > (1024 * 1024 * 200);
  } // logsNeedRemoving

  /**
   * Return the list of Orphan files
   *
   * @return array
   */
  static function findOrphanFiles() {
    $project_object_table = TABLE_PREFIX . 'project_objects';
    $attachments_table = TABLE_PREFIX . 'attachments';
    $file_versions_table = TABLE_PREFIX . "file_versions";
    $documents_table = TABLE_PREFIX . "documents";

    // project objects
    $files_from_project_objects = DB::executeFirstColumn("SELECT varchar_field_2 FROM $project_object_table WHERE type = ? AND state > ?", 'File', STATE_DELETED);
    if(!$files_from_project_objects) {
      $files_from_project_objects = array();
    } //if

    // attachments
    $attachments = DB::executeFirstColumn("SELECT location FROM $attachments_table WHERE state > ?", STATE_DELETED);
    if(!$attachments) {
      $attachments = array();
    } //if

    $tables = DB::listTables(TABLE_PREFIX);

    // file versions
    $file_versions = array();
    if (in_array($file_versions_table, $tables)) { // maybe no Files module
      $file_versions = DB::executeFirstColumn("SELECT location FROM {$file_versions_table}");
      if (!is_foreachable($file_versions)) {
        $file_versions = array();
      } // if
    } // if

    // documents
    $documents = array();
    if (in_array($documents_table, $tables)) { // maybe no Documents module
      $documents = DB::executeFirstColumn("SELECT location FROM {$documents_table} WHERE type = 'file' AND state > ?", STATE_DELETED);
      if (!is_foreachable($documents)) {
        $documents = array();
      } // if
    } // if

    $files_in_db = array_unique(array_merge($files_from_project_objects, $attachments, $file_versions, $documents));

    $files_in_upload = array();
    if ($handle = opendir(UPLOAD_PATH)) {
      while (false !== ($entry = readdir($handle))) {
        if(!str_starts_with($entry, '.')) {
          $files_in_upload[] = $entry;
        } //if
      } //while
      closedir($handle);
    } //if

    return array_diff($files_in_upload, $files_in_db);
  } //findOrphanFiles

  /**
   * Calculate orhan files size
   *
   * @return int
   */
  static function calculateOrphansSize() {
    $orphan_files = DiskSpace::findOrphanFiles();
    $size = 0;
    if($orphan_files && is_foreachable($orphan_files)) {
      foreach($orphan_files as $file) {
        $size += filesize(UPLOAD_PATH . '/' . $file);
      } //foreach
    } //if
    return $size;
  } //calculateOrphansSize

  /**
   * Remove Orphan Files
   *
   * @return array [$deleted_files, $delete_failures]
   */
  static function removeOrphanFiles() {
    $orphan_files = DiskSpace::findOrphanFiles();

    $deleted_files = array();
    $delete_failures = array();
    if($orphan_files && is_foreachable($orphan_files)) {
      foreach($orphan_files as $orphan_file) {
        if(@unlink(UPLOAD_PATH . '/' . $orphan_file)) {
          $deleted_files[] = $orphan_file;
        } else {
          $delete_failures[] = $orphan_file;
        } //if
      } //foreach
    } //if
    return array($deleted_files, $delete_failures);
  } //removeOrphanFiles

}