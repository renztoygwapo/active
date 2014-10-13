<?php

  // Build on top of administration controller
  AngieApplication::useController('admin', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Disk space admin controller
   *
   * @package angie.frameworks.environment
   * @subpackage controllers
   */
  abstract class FwDiskSpaceAdminController extends AdminController {

    /**
     * Show and process project settings form
     */
    function index() {
      AngieApplication::useWidget('flot', ENVIRONMENT_FRAMEWORK);

      $this->response->assign(array(
        'disk_usage_data' => DiskSpace::describe()
      ));
    } // index

    /**
     * Get Disk Space usage
     */
    function usage() {
      if (!$this->request->isAsyncCall()) {
        $this->response->badRequest();
      } // if

      $total_usage = DiskSpace::getTotalUsage(false);
      $this->response->respondWithData(is_numeric($total_usage) ? $total_usage : 0);
    } // usage

    /**
     * Disk Space Admin Settings
     */
    function settings() {
      if (!($this->request->isAsyncCall() || $this->request->isSubmitted())) {
        $this->response->badRequest();
      } // if

      $one_gigabyte = 1073741824; // number of bytes in one gigabytes

      if ($this->request->isSubmitted()) {
        $disk_settings_data = $this->request->post('disk_settings_data');

        // convert gigabytes to bytes
        $disk_space_limit = array_var($disk_settings_data, 'disk_space_limit', 0) * $one_gigabyte;
        $disk_settings_data['disk_space_limit'] = $disk_space_limit;
        $disk_settings_data['disk_space_email_notifications'] = isset($disk_settings_data['disk_space_email_notifications']) ? true : false;
      } else {
        $disk_settings_data = DiskSpace::getSettings();

        // convert bytes to gigabytes
        $disk_space_limit = array_var($disk_settings_data, 'disk_space_limit', 0) / $one_gigabyte;
        $disk_settings_data['disk_space_limit'] = $disk_space_limit;
      } // if

      $this->response->assign(array(
        'disk_usage_settings'     => $disk_settings_data,
        'disk_settings_url'       => Router::assemble('disk_space_admin_settings'),
        'can_modify_disk_limit'   => !(defined('LIMIT_DISK_SPACE_USAGE') && LIMIT_DISK_SPACE_USAGE) && !AngieApplication::isOnDemand()
      ));

      if ($this->request->isSubmitted()) {
        try {
          if ($disk_space_limit && $disk_space_limit < DiskSpace::getTotalUsage()) {
            throw new Error(lang('activeCollab is already using :amount. Limit must be larger than this value.', array('amount' => format_file_size(DiskSpace::getTotalUsage()))));
          } // if

          DiskSpace::setSettings($disk_settings_data);
          $this->response->respondWithData(DiskSpace::describe());
        } catch (Exception $e) {
          $this->response->exception($e);
        } // try
      } // if
    } // settings

    /**
     * Remove application cache
     */
    function remove_application_cache() {
      if (!$this->request->isSubmitted()) {
        $this->response->badRequest();
      } // if

      if (AngieApplication::isOnDemand()) {
        $this->response->badRequest();
      } // if

      try {
        AngieApplication::cache()->clear();
        Router::cleanUpCache(true);
        $this->response->respondWithData(DiskSpace::describe());
      } catch (Exception $e) {
        $this->response->exception($e);
      } // try
    } // remove_application_cache

    /**
     * Remove application logs
     */
    function remove_logs() {
      if (!$this->request->isSubmitted()) {
        $this->response->badRequest();
      } // if

      if (AngieApplication::isOnDemand()) {
        $this->response->badRequest();
      } // if

      try {
        AngieApplication::removeLogs();
        $this->response->respondWithData(DiskSpace::describe());
      } catch (Exception $e) {
        $this->response->exception($e);
      } // try
    } // remove_application_logs

    /**
     * Remove old application versions
     */
    function remove_old_application_versions() {
      if (!$this->request->isSubmitted()) {
        $this->response->badRequest();
      } // if

      if (AngieApplication::isOnDemand()) {
        $this->response->badRequest();
      } // if

      try {
        DiskSpace::removeOldVersions();
        $this->response->respondWithData(DiskSpace::describe());
      } catch (Exception $e) {
        $this->response->exception($e);
      } // try
    } // remove_old_application_versions

    /**
     * Remove orphan files
     */
    function remove_orphan_files() {
      if (!$this->request->isSubmitted()) {
        $this->response->badRequest();
      } // if

      if (AngieApplication::isOnDemand()) {
        $this->response->badRequest();
      } // if

      try {
        DiskSpace::removeOrphanFiles();
        $this->response->respondWithData(DiskSpace::describe());
      } catch (Exception $e) {
        $this->response->exception($e);
      } // try
    } // remove_orphan_files

  }