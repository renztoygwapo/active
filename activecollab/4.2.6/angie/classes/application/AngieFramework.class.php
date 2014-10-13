<?php

  /**
   * Foundation that all angie framework definitions extend
   *
   * @package angie.library.application
   */
  abstract class AngieFramework {
    
    /**
     * Short name of the framework
     *
     * @var string
     */
    protected $name;
    
    /**
     * Framework's version
     *
     * @var string
     */
    protected $version = '1.0';
    
    /**
     * Define framework routes
     */
    function defineRoutes() {
      
    } // defineRoutes
    
    /**
     * Define framework handlers
     */
    function defineHandlers() {
      
    } // defineHandlers
    
    // ---------------------------------------------------
    //  Paths
    // ---------------------------------------------------
    
    /**
     * Return framework name
     *
     * @return string
     */
    function getName() {
      return $this->name;
    } // getName
    
    /**
     * Return framework version
     *
     * @return string
     */
    function getVersion() {
      return $this->version;
    } // getVersion
    
    /**
     * Return full framework path
     *
     * @return string
     */
    function getPath() {
      return ANGIE_PATH . '/frameworks/' . $this->name;
    } // getPath
    
    // ---------------------------------------------------
    //  Model and installation
    // ---------------------------------------------------
    
    /**
     * Cached model instance
     *
     * @var AngieFrameworkModel
     */
    private $model = false;
    
    /**
     * Return model definition for this framework / module
     *
     * @return AngieFrameworkModel
     */
    function &getModel() {
      if($this->model === false) {
        $model_class = get_class($this) . 'Model';
        $model_file = $this->getPath() . "/resources/$model_class.class.php";

        // Load file and create instance
        if(is_file($model_file)) {
          require_once $model_file;
          $this->model = new $model_class($this);
        } // if
        
        if(!($this->model instanceof AngieFrameworkModel)) {
          $this->model = null;
        } // if
      } // if
      
      return $this->model;
    } // getModel
    
    /**
     * Install this framework
     */
    function install() {
      if($this->getModel() instanceof AngieFrameworkModel) {
        $this->getModel()->createTables();
        $this->getModel()->loadInitialData();
      } // if
    } // install
    
    /**
     * Uninstall this framework
     */
    function uninstall() {
      if($this->getModel() instanceof AngieFrameworkModel) {
        $this->getModel()->dropTables();
      } // if
    } // install
    
    // ---------------------------------------------------
    //  Path resolution and loading
    // ---------------------------------------------------
    
    /**
     * Load controller class
     *
     * @param string $controller_name
     * @return string
     * @throws FileDnxError
     */
    function useController($controller_name) {
      $controller_class = Inflector::camelize($controller_name) . 'Controller';
      if(!class_exists($controller_class, false)) {
        $controller_file = $this->getPath() . "/controllers/$controller_class.class.php";
      
        if(is_file($controller_file)) {
          include_once $controller_file;
        } else {
          throw new FileDnxError($controller_file, "Controller $this->name::$controller_name does not exist (expected location '$controller_file')");
        } // if
      } // if

      return $controller_class;
    } // useController
    
    /**
     * Use specific helper
     *
     * @param string $helper_name
     * @param string $helper_type
     * @return string
     * @throws FileDnxError
     */
    function useHelper($helper_name, $helper_type = 'function') {
      if(!function_exists("smarty_{$helper_type}_{$helper_name}")) {
        $helper_file = $this->getPath() . "/helpers/$helper_type.$helper_name.php";
        
        if(is_file($helper_file)) {
          include_once $helper_file;
        } else {
          throw new FileDnxError($helper_file, "Helper $this->name::$helper_name does not exist (expected location '$helper_file')");
        } // if
      } // if

      return "smarty_{$helper_type}_{$helper_name}";
    } // useHelper
    
    /**
     * Use specific model
     * 
     * $model_names can be single model name or array of model names
     *
     * @param string $model_names
     */
    function useModel($model_names) {
      $model_names = (array) $model_names;
      foreach($model_names as $model_name) {
        $object_class = Inflector::camelize(Inflector::singularize($model_name));
        $manager_class = Inflector::camelize($model_name);
        
        AngieApplication::setForAutoload(array(
          "Base$object_class"  => $this->getPath() . "/models/$model_name/Base$object_class.class.php",
          $object_class        => $this->getPath() . "/models/$model_name/$object_class.class.php", 
          "Base$manager_class" => $this->getPath() . "/models/$model_name/Base$manager_class.class.php", 
          $manager_class       => $this->getPath() . "/models/$model_name/$manager_class.class.php",
        ));
      } // foreach
    } // useModel
    
    /**
     * Return path for a given template in this framework / module
     *
     * @param string $view
     * @param string $controller_name
     * @param string $interface
     * @return string
     */
    function getViewPath($view, $controller_name = null, $interface = null) {
      if(empty($interface)) {
        $interface = AngieApplication::getPreferedInterface();
      } // if
      
      if($controller_name) {
        return $this->getPath() . "/views/$interface/$controller_name/$view.tpl";
      } else {
        return $this->getPath() . "/views/$interface/$view.tpl";
      } // if
    } // getViewPath
    
    /**
     * Return layout path
     *
     * @param string $layout
     * @return string
     */
    function getLayoutPath($layout) {
      return $this->getPath() . "/layouts/$layout.tpl";
    } // getLayoutPath

    /**
     * Return widget path
     *
     * @param string $widget
     * @return string
     */
    function getWidgetPath($widget) {
      return $this->getPath() . "/widgets/$widget";
    } // getWidgetPath
    
    /**
     * Return proxy URL
     * 
     * @param string $proxy
     * @param mixed $params
     * @return string
     */
    function getProxyUrl($proxy, $params = null) {
      if(empty($params)) {
        $url_params = array(
          'proxy' => $proxy, 
          'module' => $this->getName(),
        	'v' => AngieApplication::getVersion(), 
          'b' => AngieApplication::getBuild(),
        );
      } else {
        $url_params = array_merge(array(
          'proxy' => $proxy, 
          'module' => $this->getName(),
        	'v' => AngieApplication::getVersion(), 
        	'b' => AngieApplication::getBuild(), 
        ), $params);
      } // if
      
      return ROOT_URL . '/proxy.php?' . (version_compare(PHP_VERSION, '5.1.2', '>=') ? http_build_query($url_params, '', '&') : http_build_query($url_params, ''));
    } // getProxyUrl
    
    /**
     * Return email template path
     *
     * @param string $template
     * @return string
     */
    function getEmailTemplatePath($template) {
      return $this->getPath() . "/email/$template.tpl";
    } // getEmailTemplatePath
    
    /**
     * Return path of file where specific event handler is defined
     *
     * @param string $callback_name
     * @return string
     */
    function getEventHandlerPath($callback_name) {
      return $this->getPath() . "/handlers/$callback_name.php";
    } // getEventHandlerPath
    
  }