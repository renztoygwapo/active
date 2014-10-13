<?php

  /**
   * Widget loader class
   *
   * @package angie.library.application
   */
  final class AngieWidgetLoader {

    /**
     * Name of the widget
     *
     * @var string
     */
    private $widget_name;

    /**
     * Widget directory
     *
     * @var string
     */
    private $widget_dir;

    /**
     * Scripts that need to be loaded
     *
     * @var array
     */
    private $scripts;

    /**
     * Stylesheets that need to be loaded
     *
     * @var array
     */
    private $stylesheets;

    /**
     * Names of widgets that particular widget depends on
     *
     * @var array
     */
    private $dependencies;

    /**
     * Construct new widget loader instance
     *
     * @param string $widget_dir
     * @param array $scripts
     * @param array $stylesheets
     * @param array $dependencies
     */
    function __construct($widget_dir, $scripts = null, $stylesheets = null, $dependencies = null) {
      $this->widget_name = basename($widget_dir);
      $this->widget_dir = $widget_dir;
      $this->scripts = $scripts && is_foreachable($scripts) ? $scripts : array();
      $this->stylesheets = $stylesheets && is_foreachable($stylesheets) ? $stylesheets : array();
      $this->dependencies = $dependencies && is_foreachable($dependencies) ? $dependencies : array();
    } // __construct

    /**
     * Render widget content
     *
     * @return string
     */
    function render() {
      $result = "<script type=\"text/javascript\">\n";

      $javascripts = '';

      // Scripts
      foreach($this->getScriptFiles() as $file) {
        $javascripts .= "/** File: " . substr($file, strlen($this->widget_dir) + 1) . " **/\n";
        $javascripts .= file_get_contents($file) . "\n\n";
      } // foreach

      if ($javascripts) {
        $result .= "App.Wireframe.Widgets.setWidgetJavaScript('$this->widget_name', " . JSON::encode($javascripts) . ");\n";
      } // if

      // Stylesheets
      $stylesheets = '';

      ColorSchemes::initializeForCompile(); // initialize for compile

      foreach($this->getStylesheetFiles() as $file) {
        $parsed_stylesheet = file_get_contents($file);

        if(str_ends_with($file, '.less.css')) {
          if(!class_exists('LessForAngie', false)) {
            require_once ANGIE_PATH . '/vendor/less/init.php';
          } // if

          $parsed_stylesheet = LessForAngie::compile($parsed_stylesheet);
        } // if

        $parsed_stylesheet = ColorSchemes::compileCss($parsed_stylesheet); // replace colors

        // add absolute assets path
        if(strpos($parsed_stylesheet, 'url(assets') !== false || strpos($parsed_stylesheet, 'url("assets') !== false || strpos($parsed_stylesheet, "url('assets") !== false) {
          $parsed_stylesheet = str_replace(array('url(assets', 'url("assets', "url('assets"), array('url(' . ASSETS_URL, 'url("' . ASSETS_URL, "url('" . ASSETS_URL), $parsed_stylesheet);
        } // if

        $stylesheets .= $parsed_stylesheet . "\n\n";
      } // foreach

      if($stylesheets) {
        $result .= "App.Wireframe.Widgets.setWidgetStylesheets('$this->widget_name', " . JSON::encode($stylesheets) . ");\n";
      } // if

      $result .= "App.Wireframe.Widgets.setAsLoaded('$this->widget_name');\n";

      return "$result</script>";
    } // render

    /**
     * Return script files
     *
     * @return array
     */
    function getScriptFiles() {
      if(empty($this->scripts)) {
        return file_exists("{$this->widget_dir}/widget.{$this->widget_name}.js") ? array("{$this->widget_dir}/widget.{$this->widget_name}.js") : array();
      } else {
        $load_the_rest = false;
        $result = array();

        foreach($this->scripts as $script) {
          if($script == '*') {
            $load_the_rest = true;
            continue;
          } // if

          $result[] = "{$this->widget_dir}/javascript/$script";
        } // foreach

        if($load_the_rest) {
          $all_files = get_files("{$this->widget_dir}/javascript", 'js');

          if($all_files) {
            foreach($all_files as $file) {
              $result[] = $file;
            } // foreach

            $result = array_unique($result);
          } // if
        } // if

        return $result;
      } // if
    } // getScriptFiles

    /**
     * Return stylesheet files
     *
     * @return array
     */
    function getStylesheetFiles() {
      if(empty($this->stylesheets)) {
        if(file_exists("{$this->widget_dir}/widget.{$this->widget_name}.css")) {
          return array("{$this->widget_dir}/widget.{$this->widget_name}.css");
        } elseif(file_exists("{$this->widget_dir}/widget.{$this->widget_name}.less.css")) {
          return array("{$this->widget_dir}/widget.{$this->widget_name}.less.css");
        } else {
          return array();
        } // if
      } else {
        $load_the_rest = false;

        $result = array();

        foreach($this->stylesheets as $stylesheet) {
          if($stylesheet == '*') {
            $load_the_rest = true;
            continue;
          } // if

          $result[] = "{$this->widget_dir}/stylesheets/$stylesheet";
        } // foreach

        if($load_the_rest) {
          $all_files = get_files("{$this->widget_dir}/stylesheets", 'css');

          if($all_files) {
            foreach($all_files as $file) {
              $result[] = $file;
            } // foreach

            $result = array_unique($result);
          } // if
        } // if

        return $result;
      } // if
    } // getStylesheetFiles

    /**
     * Return widget dependencies
     *
     * @return array|null
     */
    function getDependencies() {
      return $this->dependencies;
    } // getDependencies

  }