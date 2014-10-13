<?php

  /**
   * Controller implementation
   *
   * @package angie.library.controller
   */
  abstract class Controller {
    
    /**
     * Request object
     *
     * @var Request
     */
    protected $request;
    
    /**
     * Request time
     *
     * @var DateTimeValue
     */
    protected $request_time;
    
    /**
     * Response instance
     *
     * @var Response|BaseHttpResponse
     */
    protected $response;
    
    /**
     * Smarty instance
     *
     * @var Smarty
     */
    protected $smarty;
    
    /**
     * Flash instance
     *
     * @var Flash
     */
    protected $flash;
    
    /**
     * Do not render the layout, render only content
     *
     * @var boolean
     */
    protected $skip_layout = null;
    
    /**
     * Automaticly render template / layout if action ends without exit
     *
     * @var boolean
     */
    protected $auto_render = true;
    
    /**
     * Array of method names that are available through API
     *
     * @var array
     */
    protected $api_actions = array();
    
    /**
     * Template name. If it is empty this controller will use action name.php
     *
     * @var string
     */
    private $view;
    
    /**
     * Layout name. If it is empty this controller will use its name.php
     *
     * @var string
     */
    private $layout;
  
    /**
     * Parent controller, if this controller is exectued as delegate
     *
     * @var Controller
     */
    protected $delegate_parent;
    
    /**
     * Parent context, if this controller is executed as delegate
     *
     * @var string
     */
    protected $delegate_context;
  
    /**
     * Construct controller
     * 
     * @param mixed $parent
     * @param mixed $context
     * @throws InvalidInstanceError
     */
    function __construct(&$parent, $context = null) {
      
      // First level controller
      if($parent instanceof Request) {
        $this->request_time = new DateTimeValue();
        $this->smarty =& SmartyForAngie::getInstance();
        $this->flash = new Flash();
        
        $this->request = $parent;
        $this->response = $this->getResponseInstance();
        
        $this->smarty->assign(array(
          'request_time' => $this->request_time, 
          'request' => $parent, 
          'response' => $this->response, 
          'flash' => $this->flash, 
        ));
        
      // Delegate
      } elseif($parent instanceof Controller) {
        $this->delegate_parent = $parent;
        $this->delegate_context = $context;
        
      // Invalid parent
      } else {
        throw new InvalidInstanceError('parent', $parent, array('Request', 'Controller'));
      } // if
    } // __construct
  
    /**
     * Function executed before action
     */
    function __before() {
      
    } // __before
    
    /**
     * Set internal properties
     *
     * @param array $properties
     */
    function __setProperties($properties) {
      foreach($properties as $k => &$v) {
        $this->$k = $v;
      } // foreach
    } // __setProperties
    
    /**
     * Function exectued after action
     */
    function __after() {
      if($this->getAutoRender()) {
        $this->render();
      } // if
      
      return true;
    } // __after
    
    /**
     * Execute action
     *
     * @param string $action
     * @return bool
     * @throws ControllerActionDnxError
     */
    function __execute($action) {
      foreach($this->delegates as &$delegate) {
        try {
          if($delegate->__execute($action)) {
            return true;
          } // if
        } catch(ControllerActionDnxError $e) {
          // Skip...
        } // try
      } // foreach
      unset($delegate);
      
      $real_action_name = $this->__realActionName($action);
    
      if(in_array($real_action_name, $this->__actions())) {
        $parents = array();
        $this->__delegateParents($parents);
        
        if(count($parents)) {
          $parents = array_reverse($parents);
          
          foreach($parents as &$parent) {
            $parent->__before();
          } // foreach
          unset ($parent);
        } // if
      
        $this->__before();
        $this->$real_action_name();
        
        return $this->__after();
      } else {
        if(!$this->__isDelegate()) {
          throw new ControllerActionDnxError($this->getControllerName(), $action);
        } // if
      } // if
    } // __execute
    
    /**
     * Forward to given action
     * 
     * @param string $action
     * @param string $view
     * @return mixed
     * @throws ControllerActionDnxError
     */
    function __forward($action, $view = null) {
      if(in_array($action, $this->__actions())) {
        if(empty($view)) {
          $view = $action;
        } // if
        
        $this->setView($view);
        return $this->$action();
      } else {
        throw new ControllerActionDnxError($this->getControllerName(), $action);
      } // if
    } // __forward
    
    /**
     * Cached array of actions
     *
     * @param array
     */
    private $actions = false;
    
    /**
     * Returns array of controller actions
     *
     */
    function __actions() {
      if($this->actions === false) {
        $this->actions = array();
        
        $this_class = get_class($this);
      
        $reflection = new ReflectionClass($this_class);
        foreach($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
        
          // Filter only methods that are defined in this class
          if(substr($method->getName(), 0, 2) != '__' && $method->getDeclaringClass()->getName() != 'Controller') {
            $this->actions[] = $method->getName();
          } // if
        } // foreach
      } // if
      
      return $this->actions;
    } // __actions
    
    /**
     * Returns real, routing context aware action name
     *
     * @param string $action
     * @return string
     */
    function __realActionName($action) {
      if($this->delegate_context && str_starts_with($action, $this->delegate_context)) {
        return substr($action, strlen($this->delegate_context) + 1);
      } // if
      
      return $action;
    } // __realActionName
    
    // ---------------------------------------------------
    //  Delegate related functions
    // ---------------------------------------------------
    
    /**
     * Controller delegates
     *
     * @param array
     */
    public $delegates = array();
    
    /**
     * Extend $controller with actions from this controller in given context
     *
     * @param string $controller_name
     * @param string $controller_module
     * @param string $context
     * @return Controller
     * @throws ControllerDnxError
     */
    function __delegate($controller_name, $controller_module, $context) {
      AngieApplication::useController($controller_name, $controller_module);
      
      $controller_class = Inflector::camelize($controller_name) . 'Controller';
      if(class_exists($controller_class, false)) {
       $controller = new $controller_class($this, $context);
        if($controller instanceof Controller) {
          $this->__prepareDelegate($controller);
          $this->delegates[] = $controller;
          return $controller;
        } // if
      } // if
      
      throw new ControllerDnxError($controller_class);
    } // __delegate
    
    /**
     * Returns true if this controller is delegate of another controller
     *
     * @return boolean
     */
    function __isDelegate() {
      return $this->delegate_parent instanceof Controller;
    } // __isDelegate
    
    /**
     * Return all delegate parents
     *
     * @param array $parents
     */
    function __delegateParents(&$parents) {
      if($this->delegate_parent instanceof Controller) {
        $parents[] = $this->delegate_parent;
        
        $this->delegate_parent->__delegateParents($parents);
      } // if
    } // __delegateParents
    
    /**
     * Prepare controller delegate instance
     *
     * @param Controller $delegate
     * @param array $additional
     */
    function __prepareDelegate(Controller &$delegate, $additional = null) {
      if($this->delegate_parent instanceof Controller) {
        $this->delegate_parent->__prepareDelegate($delegate, $additional);
      } else {
        $delegate->__setProperties(array(
          'request' => &$this->request, 
          'request_time' => &$this->request_time,
          'response' => &$this->response, 
          'smarty' => &$this->smarty, 
          'flash' => &$this->flash, 
        ));
        
        if($additional) {
          $delegate->__setProperties($additional);
        } // if
      } // if
    } // __prepareDelegate
    
    // ---------------------------------------------------
    //  Renderers
    // ---------------------------------------------------

    /**
     * Render page
     *
     * @param mixed $view
     * @param mixed $layout
     * @param boolean $die
     * @return bool
     */
    function render($view = null, $layout = null, $die = true) {
      if($view) {
        $this->setView($view);
      } // if

      if($this->getSkipLayout()) {
        $this->response->respondWithContent($this->fetchContent(), array(
          'die' => $die,
        ));
      } else {
        if($layout) {
          $this->setLayout($layout);
        } // if

        $this->renderLayout($this->getLayoutPath(), $this->fetchContent(), $die);
      } // if

      return true;
    } // render

    /**
     * Fetch content that needs to be rendered
     *
     * @return string
     */
    private function fetchContent() {
      $content = $this->smarty->fetch($this->getViewPath());

      $widgets = '';
      foreach(AngieApplication::getUsedWidgets() as $widget_name => $widget_path) {
        $widgets .= AngieApplication::renderWidget($widget_name, $widget_path);
      } // foreach

      if(empty($widgets)) {
        return "<!-- Begin Content -->\n{$content}\n";
      } else {
        return "<!-- Widgets -->\n{$widgets}\n\n<!-- Content -->\n{$content}\n";
      } // if
    } // fetchContent
    
    /**
     * Render just template content, without the template
     *
     * @param string $view
     * @param string $controller_name
     * @param string $module_name
     * @param string $interface
     * @param boolean $die
     * @throws FileDnxError
     */
    function renderView($view, $controller_name = null, $module_name = null, $interface = null, $die = true) {
      $path = AngieApplication::getViewpath($view, $controller_name, $module_name, $interface);
      if(is_file($path)) {
        $this->response->respondWithContent($this->smarty->fetch($path), array(
          'die' => $die,
        ));
      } else {
        throw new FileDnxError($path);
      } // if
    } // renderView
    
    /**
     * Assign content and render layout
     *
     * @param string $layout_path Path to the layout file
     * @param string $content Value that will be assigned to the $content_for_layout
     *   variable
     * @return boolean
     * @throws FileDnxError
     */
    protected function renderLayout($layout_path, $content = null, $die = false) {
      $this->smarty->assign('content_for_layout', $content);

      $this->response->respondWithContent($this->smarty->fetch($layout_path), array(
        'die' => $die,
      ));
    } // renderLayout
    
    /**
     * Shortcut method for printing text and setting auto_render option
     * 
     * If $render_layout is set to true, controller will render text inside of a 
     * layout. Default is false for simple and fast text rendering
     *
     * @param string $text Text that need to be rendered
     * @param boolean $render_layout
     * @param boolean $die
     * @throws FileDnxError
     */
    protected function renderText($text, $render_layout = false, $die = true) {
      if($render_layout) {
        $this->setAutoRender(false);
        $this->renderLayout($this->getLayoutPath(), $text, $die);
      } else {
        $this->response->respondWithContent($text, array(
          'die' => $die,
        ));
      } // if
    } // renderText
    
    // ---------------------------------------------------
    //  Naming convection
    // ---------------------------------------------------
    
    /**
     * Return module name
     *
     * @param string $declaration_path
     * @return string
     * @throws InvalidParamError
     */
    function getModuleName($declaration_path) {
      if(empty($declaration_path)) {
        throw new InvalidParamError('declaration_path', $declaration_path);
      } // if

      return AngieApplication::getModuleNameFromControllerPath($declaration_path);
    } // getModuleName
    
    /**
     * Return controller name based on controller class name
     *
     * @param string $controller_class_name
     * @return string
     */
    function getControllerName($controller_class_name = null) {
      if(empty($controller_class_name)) {
        $controller_class_name = get_class($this);
      } // if

      return AngieApplication::getControllerNameFromControllerClassName($controller_class_name);
    } // getControllerName
    
    // ---------------------------------------------------
    //  Paths
    // ---------------------------------------------------
    
    /**
     * Get view
     *
     * @return string
     */
    protected function getView() {
      return $this->view;
    } // getView
    
    /**
     * Set view value
     * 
     * $value can be string or associative array with following fields:
     * 
     * - template - template name, without extension
     * - controller - controller name
     * - module - module name
     *
     * @param string $value
     */
    protected function setView($value) {
      $this->view = $value;
    } // setView
    
    /**
     * Get layout
     *
     * @return string
     */
    protected function getLayout() {
      return $this->layout;
    } // getLayout
    
    /**
     * Set layout value
     *
     * @param string $value
     */
    protected function setLayout($value) {
      $this->layout = $value;
    } // setLayout
    
    /**
     * Get auto_render
     *
     * @return boolean
     */
    protected function getAutoRender() {
      return $this->auto_render;
    } // getAutoRender
    
    /**
     * Set auto_render value
     *
     * @param boolean $value
     */
    protected function setAutoRender($value) {
      $this->auto_render = (boolean) $value;
    } // setAutoRender
    
    /**
     * Return skip layout value
     *
     * @return boolean
     */
    function getSkipLayout() {
      return $this->skip_layout === null ? $this->request->isAsyncCall() || $this->request->get('skip_layout') : $this->skip_layout;
    } // getSkipLayout
    
    /**
     * Set skip layout value
     * 
     * NULL value is auto-detect (based on request information)
     *
     * @param mixed $value
     * @return boolean
     */
    function setSkipLayout($value) {
      $this->skip_layout = $value === null ? null : (boolean) $value;
      
      return $this->skip_layout;
    } // setSkipLayout
    
    /**
     * Return path of the template. If template dnx throw exception
     *
     * @return string
     * @throws FileDnxError
     */
    private function getViewPath() {
      
      if(is_array($this->getView())) {
        $path = get_view_path(
          array_var($this->getView(), 'view', $this->request->getAction()), 
          array_var($this->getView(), 'controller', $this->request->getController()), 
          array_var($this->getView(), 'module', $this->request->getModule())
        );
      } elseif(is_string($this->getView())) {
        $view = str_replace('\\', '/', $this->getView()); // Windows path to Unix path
        
        if(strpos($view, '/') === false) {
          $path = AngieApplication::getViewPath($view, $this->getControllerName(), $this->request->getModule());
          
          if(!is_file($path)) {
            $action_name = $this->__realActionName($this->request->getAction());
            
            $method = new ReflectionMethod(get_class($this), $action_name);
          
            $path = AngieApplication::getViewPath(
              $view, 
              $this->getControllerName($method->getDeclaringClass()->getName()), 
              $this->getModuleName($method->getDeclaringClass()->getFileName())
            );
          } // if
        } else {
          $path = $view;
        } // if
        
      // User did not set view
      } else {
        $action_name = $this->__realActionName($this->request->getAction());

      	// Assume that we have the template for this action, even if it's just inherited
        $path = AngieApplication::getViewPath($action_name, $this->request->getController(), $this->request->getModule());
        
        // Not found? Get path from controller where this action is declared
        if(!is_file($path)) {
          $method = new ReflectionMethod(get_class($this), $action_name);
          
          $path = AngieApplication::getViewPath(
            $action_name, 
            $this->getControllerName($method->getDeclaringClass()->getName()), 
            $this->getModuleName($method->getDeclaringClass()->getFileName())
          );
        } // if
      } // if
      
      if(is_file($path)) {
        return $path;
      } elseif($this->request->isPhone()) {
        return AngieApplication::getViewPath('_phone_view_not_found', null, ENVIRONMENT_FRAMEWORK, AngieApplication::INTERFACE_PHONE);
      } else {
        throw new FileDnxError($path);
      } // if
    } // getViewPath
    
    /**
     * Return path of the layout file. File dnx throw exception
     *
     * @return string
     * @throws FileDnxError
     */
    private function getLayoutPath() {
    	$layout = $this->getLayout();
    	
      if($this->request->isMobileDevice()) {
      	if(is_array($layout)) {
      		if($layout['module'] == ENVIRONMENT_FRAMEWORK && $layout['layout'] == 'auth') {
		        $path = AngieApplication::getLayoutPath($layout['layout'], $layout['module']);
		        if(is_file($path)) {
		          return $path;
		        } // if
					} // if
      	} // if
				
        $interface = $this->request->isPhone() ? 'phone' : 'tablet';
        $device_class = AngieApplication::getDeviceClass();
        
        // See if there's layout for given device class, in target module or in environment framework
        $path = AngieApplication::getLayoutPath("{$interface}_{$device_class}", ENVIRONMENT_FRAMEWORK_INJECT_INTO);
        if(is_file($path)) {
          return $path;
        } // if
        
        $path = AngieApplication::getLayoutPath("{$interface}_{$device_class}", ENVIRONMENT_FRAMEWORK);
        if(is_file($path)) {
          return $path;
        } // if
        
        // Check for interface targeted at specific device type, in target module or in environment framework
        $path = AngieApplication::getLayoutPath($interface, ENVIRONMENT_FRAMEWORK_INJECT_INTO);
        if(is_file($path)) {
          return $path;
        } // if
        
        $path = AngieApplication::getLayoutPath($interface, ENVIRONMENT_FRAMEWORK);
        if(is_file($path)) {
          return $path;
        } // if
      } else {
        if(is_array($layout)) {
          $path = AngieApplication::getLayoutPath($layout['layout'], $layout['module']);
        } elseif($layout) {
          if (strpos($layout, CUSTOM_PATH) === false) {
            $path = AngieApplication::getLayoutPath($layout, $this->request->getModule());
          } else {
            $path = $layout;
          } // if
        } else {
          if($this->__isDelegate()) {
            $path = $this->delegate_parent->getLayoutPath();
          } else {
            $path = AngieApplication::getLayoutPath($this->getControllerName(), $this->request->getModule());
          } // if
        } // if
        
        if(is_file($path)) {
          return $path;
        } // if
      } // if
      
      // Throw error with last assumed path
      throw new FileDnxError($path);
    } // getLayoutPath
  
  }