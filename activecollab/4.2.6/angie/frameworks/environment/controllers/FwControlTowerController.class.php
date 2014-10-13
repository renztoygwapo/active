<?php

  // Build on top of
  AngieApplication::useController('admin', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Framework level control tower controller
   *
   * @package angie.frameworks.environment
   * @subpackage controllers
   */
  class FwControlTowerController extends AdminController {

    /**
     * Popup action
     */
    function index() {
      $control_tower = new ControlTower($this->logged_user);
      $control_tower->load();
      $control_tower->loadBadgeValue();

      $this->response->assign('control_tower', $control_tower);
    } // index

    /**
     * Show control towser settings page
     */
    function settings() {
      $control_tower = new ControlTower($this->logged_user);
      $control_tower_settings = $control_tower->getSettings();

      $this->response->assign('control_tower_settings', $control_tower_settings);

      if($this->request->isSubmitted()) {
        try {
          DB::beginWork('Updating control tower settings @ ' . __CLASS__);

          foreach($control_tower_settings as $group => $settings) {
            foreach($settings as $setting_name => $setting) {
              ConfigOptions::setValue($setting_name, (boolean) $this->request->post($setting_name));
            } // foreach
          } // foreach

          DB::commit('Control tower settings updated @ ' . __CLASS__);

          $this->response->ok();
        } catch(Exception $e) {
          DB::rollback('Failed to update control tower settings @ ' . __CLASS__);
          $this->response->exception($e);
        } // if
      } // if
    } // settings

    /**
     * Render index page
     */
    function performance_checklist() {

      // ---------------------------------------------------
      //  PHP environment
      // ---------------------------------------------------

      $php_version = PHP_VERSION;

      if(version_compare($php_version, '5.4.0', '>=')) {
        $php_steps = array(
          array(
            'text' => lang('You are running PHP :version', array('version' => PHP_VERSION)),
            'is_ok' => true,
          ),
        );

        if(ini_get('opcache.enable')) {
          $php_steps[] = array(
            'text' => lang('Opcache extension is installed and enabled'),
            'is_ok' => true,
          );

          $shared_memory_size = (integer) ini_get('opcache.memory_consumption');
          $min_shared_memory_size = 64; // 648M;

          $php_steps[] = array(
            'text' => lang('Shared memory storage size (:option_name) is set to :size (you should have at least :min_size)', array(
              'option_name' => 'opcache.memory_consumption',
              'size' => $shared_memory_size . 'MB',
              'min_size' => $min_shared_memory_size . 'MB',
            )),
            'is_ok' => $shared_memory_size >= $min_shared_memory_size,
          );
        } elseif(extension_loaded('apc')) {
          if(ini_get('apc.enabled')) {
            $php_steps[] = array(
              'text' => lang('APC extension is installed and enabled'),
              'is_ok' => true,
            );

            $shared_memory_size = php_config_value_to_bytes(ini_get('apc.shm_size'));

            if(AngieApplication::cache()->getBackendType() == AngieCacheDelegate::APC_BACKEND) {
              $min_apc_cache_size = 134217728; // 128M
              $min_apc_cache_size_desc = lang('APC is used as opcode and data cache and therfore you need to allocate more memory to it');
            } else {
              $min_apc_cache_size = 67108864; // 64M
              $min_apc_cache_size_desc = '';
            } // if

            $php_steps[] = array(
              'text' => lang('Shared memory storage size (:option_name) is set to :size (you should have at least :min_size)', array(
                'option_name' => 'apc.shm_size',
                'size' => format_file_size($shared_memory_size),
                'min_size' => format_file_size($min_apc_cache_size),
              )),
              'description' => $min_apc_cache_size_desc,
              'is_ok' => $shared_memory_size >= $min_apc_cache_size,
            );
          } else {
            $php_steps[] = array(
              'text' => lang('APC extension is installed, but not enabled. Please enable APC caching in your php.ini'),
              'is_ok' => false,
            );
          } // if
        } elseif(extension_loaded('wincache')) {
          if(ini_get('wincache.ocenabled')) {
            $php_steps[] = array(
              'text' => lang('WinCache is installed and opcode caching is enabled'),
              'is_ok' => true,
            );

            $shared_memory_size = (integer) ini_get('wincache.ocachesize');

            $php_steps[] = array(
              'text' => lang('Shared memory storage size (:option_name) is set to :size (you should have at least :min_size)', array(
                'option_name' => 'wincache.ocachesize',
                'size' => $shared_memory_size . 'MB',
                'min_size' => '64MB',
              )),
              'is_ok' => $shared_memory_size >= 64,
            );
          } else {
            $php_steps[] = array(
              'text' => lang('WinCache is installed, but opcode caching is not enabled. Please set wincache.ocenabled to "1" in your php.ini'),
              'is_ok' => false,
            );
          } // if
        } else {
          $php_steps[] = array(
            'text' => lang("Opcode cache extension (Opcache, APC or WinCache) is not installed. Usage of opcode caching is curcial for good PHP performance"),
            'is_ok' => false,
          );
        } // if
      } else {
        $php_steps = array(
          array(
            'text' => lang('You are running PHP :version', array('version' => $php_version)) . '. ' . lang('You should use PHP 5.4.0 or later'),
            'is_ok' => false,
          ),
        );
      } // if

      $php_environment_all_ok = true;

      foreach($php_steps as $php_step) {
        if(empty($php_step['is_ok'])) {
          $php_environment_all_ok = false;
          break;
        } // if
      } // foreach

      // ---------------------------------------------------
      //  Cache
      // ---------------------------------------------------

      $cache_next_step = null;

      switch(AngieApplication::cache()->getBackendType()) {
        case AngieCacheDelegate::APC_BACKEND:
          $cache_steps = array(
            array(
              'text' => lang('You are using memory cache for data caching (:backend_name backend)', array(
              'backend_name' => 'APC'
            )),
            'is_ok' => true,
            ),
          );

          break;
        case AngieCacheDelegate::MEMCACHED_BACKEND:
          $cache_steps = array(
          array(
            'text' => lang('You are using memory cache for data caching (:backend_name backend)', array(
              'backend_name' => 'memcached'
            )),
            'is_ok' => true,
            ),
          );

          break;
        case AngieCacheDelegate::FILESYSTEM_BACKEND:
          $cache_steps = array(
            array(
              'text' => lang('You are using the file system for data caching. This is the slowest cache backend'),
              'is_ok' => false,
            ),
          );

          $cache_next_step = array(
            'text' => lang('Configure Data Cache'),
            'mode' => 'new_window',
            'url' => 'https://www.activecollab.com/help/books/self-hosted-edition/data-cache.html',
          );

          break;
        default:
          $cache_steps = array(
            array(
              'text' => lang('Data cache is not enabled'),
              'is_ok' => false,
            ),
          );

          $cache_next_step = array(
            'text' => lang('Configure Data Cache'),
            'mode' => 'new_window',
            'url' => 'https://www.activecollab.com/help/books/self-hosted-edition/data-cache.html',
          );
      } // switch

      // ---------------------------------------------------
      //  Old system versions
      // ---------------------------------------------------

      $old_versions = array();
      $folders = get_folders(ROOT);

      if(is_foreachable($folders)) {
        foreach($folders as $folder) {
          $folder_name = basename($folder);

          if(AngieApplication::isValidVersionNumber($folder_name) && $folder_name != APPLICATION_VERSION) {
            $old_versions[] = $folder_name;
          } // if
        } // foreach
      } // if

      if(count($old_versions) > 1) {
        $old_versions_steps = array(
        array(
          'text' => lang('You have :num old application versions', array(
          'num' => count($old_versions),
          )),
            'description' => lang("In some cases code from old versions can significantly decrease system performance. We recommend that you clean up old versions regularly"),
            'is_ok' => false,
          )
        );

        $old_versions_next_action = array(
          'text' => lang('Remove Old Versions'),
          'url' => Router::assemble('disk_space_remove_old_application_versions'),
          'success_event' => 'old_application_versions_removed',
        );
      } else {
        $old_versions_steps = array(
          array(
            'text' => count($old_versions) ? lang('You have the current and the previous application version backed up') : lang('You have the current application version'),
            'description' => lang("In some cases code from old versions can significantly decrease system performance. We recommend that you clean up old versions regularly"),
            'is_ok' => true,
          )
        );

        $old_versions_next_action = null;
      } // if

      $sections = array(

        // PHP Environment (version and opcode cache)
        array(
          'title' => 'PHP Environment',
          'steps' => $php_steps,
          'next_step' => null,
          'all_ok' => $php_environment_all_ok,
        ),

        array(
          'title' => lang('Data Cache'),
          'steps' => $cache_steps,
          'next_step' => $cache_next_step,
          'all_ok' => $cache_steps[0]['is_ok'],
        ),

        array(
          'title' => lang('Old Application Versions'),
          'steps' => $old_versions_steps,
          'next_step' => $old_versions_next_action,
          'all_ok' => $old_versions_steps[0]['is_ok'],
        ),

      );

      if(version_compare(APPLICATION_VERSION, '5.0.0', '>=') && DB::getConnection() instanceof MySQLDBConnection) {
        if(DB::getConnection()->hasInnoDBSupport()) {
          $sections[] = array(
            'title' => lang('MySQL InnoDB Support'),
            'steps' => array(
              array(
                'text' => lang('InnoDB support is enabled'),
                'is_ok' => true,
              )
            ),
            'all_ok' => true,
          );
        } else {
          $sections[] = array(
            'title' => lang('MySQL InnoDB Support'),
            'steps' => array(
              array(
                'text' => lang('InnoDB support is not enabled'),
                'is_ok' => false,
              )
            ),
            'next_step' => array(
            'text' => lang('Enable InnoDB'),
            'mode' => 'new_window',
              'url' => 'http://stackoverflow.com/questions/4757589/how-to-enable-innodb-in-mysql',
            ),
            'all_ok' => false,
          );
        } // if
      } // if

      $this->response->assign('sections', $sections);
    } // performance_checklist

    /**
     * Empty cache
     */
    function empty_cache() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        Router::cleanUpCache(true);
        AngieApplication::cache()->clear();

        $this->response->ok();
      } else {
        $this->response->badRequest();
      } // if
    } // empty_cache

    /**
     * Delete compiled templates
     */
    function delete_compiled_templates() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        AngieApplication::clearCompiledScripts();

        $this->response->ok();
      } else {
        $this->response->badRequest();
      } // if
    } // delete_compiled_templates

    /**
     * Rebuild images
     */
    function rebuild_images() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        $protect_assets = defined('PROTECT_ASSETS_FOLDER') && PROTECT_ASSETS_FOLDER;

        if(empty($protect_assets)) {
          AngieApplication::rebuildAssets();
        } // if

        $this->response->ok();
      } else {
        $this->response->badRequest();
      } // if
    } // rebuild_images

    /**
     * Rebuild lozalization
     */
    function rebuild_localization() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        AngieApplication::rebuildLocalization();

        $this->response->ok();
      } else {
        $this->response->badRequest();
      } // if
    } // rebuild_localization

  }