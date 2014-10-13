<?php

  /**
   * Framework level help element implementation
   *
   * @package angie.framework.help
   * @subpackage models
   */
  abstract class FwHelpElement implements ISearchItem {

    /**
     * Properties separator
     */
    const PROPERTIES_SEPARATOR = '================================================================';

    /**
     * Name of the module or framework that this book belongs to
     *
     * @var string
     */
    protected $module;

    /**
     * Book's path
     *
     * @var string
     */
    protected $path;

    /**
     * Load indicator
     *
     * @var bool
     */
    protected $is_loaded = false;

    /**
     * Book's short name
     *
     * @var string
     */
    protected $short_name;

    /**
     * List of properties
     *
     * @var string
     */
    protected $properties = array();

    /**
     * Body text
     *
     * @var string
     */
    protected $body;

    /**
     * Construct and load help element
     *
     * @param string $module
     * @param string $path
     * @param bool $load
     */
    function __construct($module, $path, $load = true) {
      $this->module = $module;
      $this->path = $path;

      if($load) {
        $this->load();
      } // if
    } // __construct

    /**
     * Get folder name
     *
     * @return string
     */
    function getFolderName() {
      return basename($this->path);
    } // getFolderName

    /**
     * Return module name
     *
     * @return string
     */
    function getModuleName() {
      return $this->module;
    } // getModuleName

    /**
     * Return true if $user can view this element
     *
     * @param User $user
     * @return bool
     */
    function canView(User $user) {
      $groups = AngieApplication::help()->getUserGroups($user);

      $show_to = $this->getProperty('show_to');

      if($show_to) {
        $show_to_groups = array_map('trim', explode(',', $show_to));

        foreach($show_to_groups as $show_to_group) {
          if(in_array($show_to_group, $groups)) {
            return true;
          } // if
        } // foreach

        return false; // Not visible to any of the Show To groups
      } // if

      $hide_from = $this->getProperty('hide_from');

      if($hide_from) {
        $hide_from_groups = array_map('trim', explode(',', $hide_from));

        foreach($hide_from_groups as $hide_from_group) {
          if(in_array($hide_from_group, $groups)) {
            return false;
          } // if
        } // foreach
      } // if

      return true;
    } // canView

    /**
     * Return book's short name
     *
     * @return string
     */
    function getShortName() {
      if($this->short_name === null) {
        $this->short_name = str_replace('_', '-', basename($this->path));
      } // if

      return $this->short_name;
    } // getShortName

    /**
     * Return property value
     *
     * @param string $name
     * @param mixed $default
     * @return string
     */
    function getProperty($name, $default = null) {
      return isset($this->properties[$name]) ? $this->properties[$name] : $default;
    } // getProperty

    /**
     * Return element body
     *
     * @return string
     */
    function getBody() {
      return $this->body;
    } // getBody

    /**
     * Return book URL
     *
     * @return string
     */
    function getUrl() {
      return AngieApplication::help()->getUrl($this);
    } // getUrl

    /**
     * Return true if we loaded element's definition
     *
     * @return bool
     */
    function isLoaded() {
      return $this->is_loaded;
    } // isLoaded

    /**
     * Get index file path
     *
     * @return string
     */
    function getIndexFilePath() {
      return is_dir($this->path) ? $this->path . '/index.md' : $this->path;
    } // getIndexFile

    /**
     * Load element's definition
     *
     * @throws FileDnxError
     */
    function load() {
      if(empty($this->is_loaded)) {
        $index_file = $this->getIndexFilePath();

        if(is_file($index_file)) {
          $this->body = file_get_contents($index_file);

          $separator_pos = strpos($this->body, self::PROPERTIES_SEPARATOR);

          if($separator_pos === false) {
            if(substr($this->body, 0, 1) == '*') {
              $properties_string = $this->body;
              $this->body = '';
            } else {
              $properties_string = '';
            } // if
          } else {
            $properties_string = trim(substr($this->body, 0, $separator_pos));
            $this->body = trim(substr($this->body, $separator_pos + strlen(self::PROPERTIES_SEPARATOR)));
          } // if

          if($properties_string) {
            $properties_lines = explode("\n", $properties_string);

            if(count($properties_lines)) {
              foreach($properties_lines as $properties_line) {
                $properties_line = trim(trim($properties_line, '*')); // Clean up

                if($properties_line) {
                  $colon_pos = strpos($properties_line, ':');

                  if($colon_pos !== false) {
                    $this->loadProperty(trim(substr($properties_line, 0, $colon_pos)), trim(substr($properties_line, $colon_pos + 1)));
                  } // if
                } // if
              } // foreach
            } // if
          } // if

          $this->body = trim($this->body);
        } else {
          throw new FileDnxError($index_file);
        } // if

        $this->is_loaded = true;
      } // if
    } // load

    /**
     * Load property value
     *
     * @param string $name
     * @param string $value
     */
    private function loadProperty($name, $value) {
      $this->properties[Inflector::underscore(str_replace(' ', '', $name))] = $value;
    } // loadProperty

    // ---------------------------------------------------
    //  Interfaces
    // ---------------------------------------------------

    /**
     * Cached search item helper instance
     *
     * @var IHelpElementSearchItemImplementation
     */
    private $search = false;

    /**
     * Return search helper instance
     *
     * @return IHelpElementSearchItemImplementation
     */
    function &search() {
      if($this->search === false) {
        $this->search = new IHelpElementSearchItemImplementation($this);
      } // if

      return $this->search;
    } // search

  }