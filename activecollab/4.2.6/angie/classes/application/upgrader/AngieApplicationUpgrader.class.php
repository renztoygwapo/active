<?php

  /**
   * Angie application upgrade system
   *
   * @package angie.library.application
   * @subpackage upgrader
   */
  final class AngieApplicationUpgrader {
    
    /**
     * Upgrader adapter
     *
     * @var AngieApplicationUpgraderAdapter
     */
    private static $adapter;
    
    /**
     * Initialize upgrader
     */
    static function init() {
      $adapter_class = APPLICATION_NAME . 'UpgraderAdapter';

      $adapter_class_path = ROOT . '/' . static::getVersionNumberFromUpgraderClassPath(__FILE__) . "/resources/$adapter_class.class.php";
      if(is_file($adapter_class_path)) {
        require_once $adapter_class_path;
        
        if(class_exists($adapter_class)) {
          $adapter = new $adapter_class();
          
          if($adapter instanceof AngieApplicationUpgraderAdapter) {
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
     * Return version number for path of upgrader class
     *
     * @param string $path
     * @return mixed
     */
    static function getVersionNumberFromUpgraderClassPath($path) {
      return first(explode('/', trim(substr(str_replace('\\', '/', $path), strlen(ROOT) + 1))));
    } // getVersionNumberFromUpgraderClassPath
    
    /**
     * Render installer dialog
     */
    static function render() {
      if(is_file(ANGIE_PATH . '/frameworks/environment/assets/foundation/javascript/jquery.plugins/jquery.form.js')) {
        $form_widget_path = ANGIE_PATH . '/frameworks/environment/assets/foundation/javascript/jquery.plugins/jquery.form.js';
      } else {
        $form_widget_path = ANGIE_PATH . '/frameworks/environment/widgets/form/widget.form.js';
      } // if

      print '<!DOCTYPE html>';
      print '<html><head><meta http-equiv="content-type" content="text/html; charset=utf-8"><title>' . AngieApplication::getName() . ' Upgrade Script</title>';
      print '<script type="text/javascript">' . file_get_contents(ANGIE_PATH . '/frameworks/environment/assets/foundation/javascript/jquery.official/jquery.js') . '</script>';
      print '<script type="text/javascript">' . file_get_contents($form_widget_path) . '</script>';
      print '<script type="text/javascript">' . file_get_contents(ANGIE_PATH . '/frameworks/environment/assets/upgrader/javascript/jquery.upgrader.js') . '</script>';
      print '<style type="text/css">' . file_get_contents(ANGIE_PATH . '/frameworks/environment/assets/foundation/stylesheets/reset.css') . '</style>';
      print '<style type="text/css">' . file_get_contents(ANGIE_PATH . '/frameworks/environment/assets/foundation/stylesheets/classes.css') . '</style>';
      print '<style type="text/css">' . file_get_contents(ANGIE_PATH . '/frameworks/environment/assets/upgrader/stylesheets/upgrader.css') . '</style>';
      print '</head>';
      
      print '<body><div id="application_upgrader">';
      
      $counter = 1;
      foreach(AngieApplicationUpgrader::getSections() as $section_name => $section_title) {
        print '<div class="upgrader_section" upgrader_section="' . $section_name . '">';
        print '<h1 class="head">' . $counter . '. <span>' . clean($section_title) . '</span></h1>';
        print '<div class="body">' . AngieApplicationUpgrader::getSectionContent($section_name) . '</div>';
        print '</div>';
        
        $counter++;
      } // foreach
      
      print '</div><p class="center">&copy;' . date('Y') . ' ' . AngieApplication::getVendor() . '. All rights reserved.</p><script type="text/javascript">$("#application_upgrader").upgrader({"name" : "' . AngieApplication::getName() . '"});</script>';
      print '</body></html>';
    } // render

    /**
     * Return grouped actions
     *
     * @return array
     */
    static function getGroupedActions() {
      $grouped_actions = array();
      $current_version = self::$adapter->currentVersion();
      $available_scripts = self::$adapter->availableScripts($current_version);

      if (!$available_scripts) {
        return null;
      } // if

      foreach ($available_scripts as $script) {
        if (!$script->getActions()) {
          continue;
        } // if

        $group = $script->getGroup();

        if (!array_key_exists($group, $grouped_actions)) {
          $grouped_actions[$group] = array();
        } // if

        foreach ($script->getActions() as $action => $description) {
          $grouped_actions[$group][$action] = $description;
        } // foreach
      } // foreach

      return $grouped_actions;
    } // getGroupedActions

    /**
     * List actions
     */
    static function listActions() {
      header("Content-Type: text/xml; charset=utf-8");

      print '<?xml version="1.0" encoding="UTF-8" ?>';
      print '<actions>';

      $grouped_actions = self::getGroupedActions();
      if (is_foreachable($grouped_actions)) {
        foreach($grouped_actions as $group => $group_actions) {
          foreach ($group_actions as $action => $description) {
            print '<action group="' . clean($group) . '" action="' . clean($action) . '">' . clean($description) . '</action>';
          } // foreach
        } // foreach
      } // if

      print '</actions>';

      die();
    } // listActions

    /**
     * Execute given step
     *
     * @param array $action
     */
    static function executeAction($action) {
      $email = isset($action['email']) && $action['email'] ? $action['email'] : '';
      $password = isset($action['password']) && $action['password'] ? $action['password'] : '';

      if($email && $password) {

        if (self::validateBeforeUpgrade(array('email' => $email, 'pass' => $password))) {
          $response = self::$adapter->executeAction($action['group'], $action['action']);

          if(is_string($response)) {
            AngieApplicationUpgrader::breakActionExecution($response);
          } else {
            header('HTTP/1.1 200 OK');
          } // if
        } else {
          $errors = array();

          foreach(self::getValidationLog() as $log_entry) {
            if($log_entry['status'] == AngieApplicationUpgraderAdapter::VALIDATION_ERROR) {
              $errors[] = $log_entry['message'];
            } // if
          } // foreach

          AngieApplicationUpgrader::breakActionExecution('Failed to execute upgrade. Errors: ' . implode(', ', $errors));
        } // if
      } else {
        AngieApplicationUpgrader::breakActionExecution('Invalid user credentials');
      } // if
    } // executeAction

    /**
     * Validate before upgrade
     *
     * @param array $params
     */
    static function validateBeforeUpgrade($params) {
      return self::$adapter->validateBeforeUpgrade($params);
    } // validateBeforeUpgrade

    /**
     * Get Validation log
     *
     * @return mixed
     */
    static function getValidationLog() {
      return self::$adapter->getValidationLog();
    } // getValidationLog

    /**
     * @param string $output
     * @return string
     */
    static function runUpgradeFromCli(&$output) {
      try {
        self::validateBeforeUpgrade(array('email' => true, 'pass' => true));
        self::$adapter->printValidationLog();

        $current_version = self::$adapter->currentVersion();

        $available_scripts = self::$adapter->availableScripts($current_version);
        if($available_scripts) {
          $response = "";
          foreach($available_scripts as $script) {
            $group = $script->getGroup();
            if($script->getActions()) {
              foreach($script->getActions() as $action => $description) {
                self::$adapter->executeAction($group, $action);
                $output .= $description . " succeeded\n";
              } // foreach
            } // if
          } // foreach
        } else {
          $output = "Database version is $current_version and it's up to date!";
        } // if
      } catch (Exception $e) {
        die($e->getMessage() . "\n\n" . $e->getTraceAsString());
        // @TODO: send e-mail
      } // try
    } // runUpgradeFromCli
    
    /**
     * Break action execution
     * 
     * @param string $message
     */
    static private function breakActionExecution($message) {
      header('HTTP/1.1 500 Internal Server Error');
      die($message);
    } // breakActionExecution
    
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
    
  }