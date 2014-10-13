<?php

  /**
   * Angie application installer
   * 
   * @package angie.library.application
   */
  final class AngieApplicationInstaller {
  
    /**
     * Installer adapter
     *
     * @var AngieApplicationInstallerAdapter
     */
    private static $adapter;
    
    /**
     * Initialize installer
     *
     * @param string $adapter_class
     * @param string $adapter_class_path
     * @throws InvalidInstanceError
     * @throws ClassNotImplementedError
     * @throws FileDnxError
     */
    static function init($adapter_class = null, $adapter_class_path = null) {
      if(empty($adapter_class)) {
        $adapter_class = APPLICATION_NAME . 'InstallerAdapter';
      } // if

      if(empty($adapter_class_path)) {
        $adapter_class_path = APPLICATION_PATH . "/resources/$adapter_class.class.php";
      } // if

      if(is_file($adapter_class_path)) {
        require_once $adapter_class_path;
        
        if(class_exists($adapter_class)) {
          $adapter = new $adapter_class();
          
          if($adapter instanceof AngieApplicationInstallerAdapter) {
            self::$adapter = $adapter;
          } else {
            throw new InvalidInstanceError('adapter', $adapter, $adapter_class);
          } // if
        } else {
          throw new ClassNotImplementedError($adapter_class, $adapter_class_path);
        } // if
      } else {
        throw new FileDnxError($adapter_class_path);
      } // if
    } // init
    
    /**
     * Render installer dialog
     */
    static function render() {
      print '<!DOCTYPE html>';
      print '<html><head><meta http-equiv="content-type" content="text/html; charset=utf-8"><title>' . AngieApplication::getName() . ' Installer</title>';
      print '<script type="text/javascript">' . file_get_contents(ANGIE_PATH . '/frameworks/environment/assets/foundation/javascript/jquery.official/jquery.js') . '</script>';
      print '<script type="text/javascript">' . file_get_contents(ANGIE_PATH . '/frameworks/environment/widgets/form/widget.form.js') . '</script>';
      print '<script type="text/javascript">' . file_get_contents(ANGIE_PATH . '/frameworks/environment/assets/installer/javascript/jquery.installer.js') . '</script>';
      print '<style type="text/css">' . file_get_contents(ANGIE_PATH . '/frameworks/environment/assets/foundation/stylesheets/reset.css') . '</style>';
      print '<style type="text/css">' . file_get_contents(ANGIE_PATH . '/frameworks/environment/assets/foundation/stylesheets/classes.css') . '</style>';
      print '<style type="text/css">' . file_get_contents(ANGIE_PATH . '/frameworks/environment/assets/installer/stylesheets/installer.css') . '</style>';
      print '</head>';
      
      print '<body><div id="application_installer">';
      
      $counter = 1;
      foreach(AngieApplicationInstaller::getSections() as $section_name => $section_title) {
        print '<div class="installer_section" installer_section="' . $section_name . '">';
        print '<h1 class="head">' . $counter . '. <span>' . clean($section_title) . '</span></h1>';
        print '<div class="body">' . AngieApplicationInstaller::getSectionContent($section_name) . '</div>';
        print '</div>';
        
        $counter++;
      } // foreach
      
      print '</div><p class="center">&copy;' . date('Y') . ' ' . AngieApplication::getVendor() . '. All rights reserved.</p><script type="text/javascript">$("#application_installer").installer({"name" : "' . AngieApplication::getName() . '"});</script>';
      print '</body></html>';
    } // render
    
    // ---------------------------------------------------
    //  Sections
    // ---------------------------------------------------
    
    /**
     * Return all installer sections
     * 
     * @return array
     */
    static function getSections() {
      return self::$adapter->getSections();
    } // getSections
    
    /**
     * Return initial content for a given section
     * 
     * @param string $name
     * @return string
     */
    static function getSectionContent($name) {
      return self::$adapter->getSectionContent($name);
    } // getSectionContent
    
    /**
     * Secuted section submission
     * 
     * @param string $name
     * @param mixed $data
     * @return boolean
     */
    static function executeSection($name, $data = null) {
      $response = '';
      
      if(self::$adapter->executeSection($name, $data, $response)) {
        header("HTTP/1.0 200 OK");
      } else {
        header("HTTP/1.0 409 Conflict");
      } // if
      
      print $response;
    } // executeSection

    /**
     * Run installation from CLI
     *
     * @param array $database_params
     * @param array $admin_params
     * @param array $license_params
     * @param array $additional_params
     * @param mixed $log
     * @return bool
     * @throws NotImplementedError
     */
    static function runInstallationFromCli($database_params, $admin_params, $license_params, $additional_params, &$log) {
      if(php_sapi_name() == 'cli') {
        self::$adapter->validateInstallation($database_params, $admin_params, $license_params, $additional_params);
        $log = self::$adapter->printValidationLog(false);
        return self::$adapter->everythingValid();
      } else {
        throw new NotImplementedError(__METHOD__, 'This method is available only for CLI calls');
      } // if
    } // runInstallation

    /**
     * Run uninstallation from CLI
     *
     * @param string $unique_key
     * @param mixed $log
     * @return bool
     * @throws NotImplementedError
     * @throws InvalidParamError
     */
    static function runUninstallationFromCli($unique_key, &$log) {
      if(php_sapi_name() !== 'cli') {
        throw new NotImplementedError(__METHOD__, 'This method is available only for CLI calls');
      } // if

      if(defined('APPLICATION_UNIQUE_KEY') && APPLICATION_UNIQUE_KEY && APPLICATION_UNIQUE_KEY == $unique_key) {
        self::$adapter->uninstall();
        $log = self::$adapter->printValidationLog(false);
        return self::$adapter->everythingValid();
      } else {
        throw new InvalidParamError('unique_key', $unique_key, 'Invalid key');
      } // if
    } // runUninstallationFromCli
    
  }