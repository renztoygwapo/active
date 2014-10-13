<?php

  // Build on top of collect files proxy
  require_once ANGIE_PATH . '/frameworks/environment/proxies/CollectFilesProxy.class.php';

  /**
   * Collect CSS files for given modules and context
   * 
   * @package angie.frameworks.environment
   * @subpackage proxies
   */
  abstract class FwCollectStylesheetsProxy extends CollectFilesProxy {
    
    /**
     * Targeted interface
     *
     * @var string
     */
    protected $interface;
    
    /**
     * Targeted device
     *
     * @var string
     */
    protected $device;

    /**
     * Selected theme
     *
     * @var string
     */
    protected $theme;
    
    /**
     * Targeted context
     *
     * @var string
     */
    protected $context;
    
    /**
     * Load only context files
     * 
     * @var boolean
     */
    protected $only_context;
    
    /**
     * Array of modules that need to be checked for JavaScript files
     *
     * @var array
     */
    protected $modules;
    
    /**
     * Construct proxy request handler
     * 
     * @param array $params
     */
    function __construct($params = null) {
      $this->interface = isset($params['interface']) && $params['interface'] ? $params['interface'] : 'default';
      $this->device = isset($params['device']) && $params['device'] ? $params['device'] : 'unknown';
      $this->context = isset($params['context']) && $params['context'] ? $params['context'] : null;
      $this->only_context = isset($params['only_context']) && $params['only_context'] ? $params['only_context'] : false;
      
      $modules = isset($_GET['modules']) && $_GET['modules'] ? explode(',', trim($_GET['modules'], ',')) : null;
      if($modules) {
        $this->modules = array();

        foreach($modules as $module_name_and_version) {
          list($module, $module_version) = explode('-', $module_name_and_version);

          if($module && preg_match('/\W/', $module) == 0) {
            $this->modules[] = $module;
          } // if
        } // if
      } // if

      $background_color = isset($params['background_color']) && $params['background_color'] ? $params['background_color'] : '#202329';
      $outer_color = isset($params['outer_color']) && $params['outer_color'] ? $params['outer_color'] : '#DEDEB6';
      $inner_color = isset($params['inner_color']) && $params['inner_color'] ? $params['inner_color'] : '#E9EADF';
      $link_color = isset($params['link_color']) && $params['link_color'] ? $params['link_color'] : '#950000';

      require_once ANGIE_PATH . '/vendor/less/init.php';
      require_once ANGIE_PATH . '/classes/color/ColorUtil.class.php';
      require_once ANGIE_PATH . '/classes/color/Color.class.php';
      require_once ANGIE_PATH . '/frameworks/environment/models/FwColorSchemes.class.php';
      require_once ROOT . '/' . APPLICATION_VERSION . '/modules/system/models/ColorSchemes.class.php';

      ColorSchemes::initializeForCompile($background_color, $outer_color, $inner_color, $link_color);

      $this->setPreProcessor(function($content, $file_name = null) {
        // if it's less perform replacement
        if($file_name && substr($file_name, strlen($file_name) - 9) == '.less.css') {
          $content = LessForAngie::compile($content);
        } // if

        // replace colors
        $content = ColorSchemes::compileCss($content);

        // add absolute assets path
        if(strpos($content, 'url(assets') !== false || strpos($content, 'url("assets') !== false || strpos($content, "url('assets") !== false) {
          $content = str_replace(array('url(assets', 'url("assets', "url('assets"), array('url(' . ASSETS_URL, 'url("' . ASSETS_URL, "url('" . ASSETS_URL), $content);
        } // if

        return $content;
      });
    } // __construct
  
    /**
     * Return content type of the data that handler will forward to the browser
     * 
     * @return string
     */
    protected function getContentType() {
      return 'text/css; charset: UTF-8';
    } // getContentType
    
    /**
     * Return array of files that need to be forwarded to the browser
     * 
     * @return array
     */
    protected function getFiles() {
    	$files = array();
    	
    	if (!$this->only_context) {
	      $files[] = ANGIE_PATH . '/frameworks/environment/assets/foundation/stylesheets/reset.css';
	      $files[] = ANGIE_PATH . '/frameworks/environment/assets/foundation/stylesheets/classes.css';
	    
	      if($this->interface == 'default') {
	        $this->collectFilesFromDir($files, ANGIE_PATH . "/frameworks/environment/assets/foundation/stylesheets/jquery.plugins");
	      } elseif($this->interface == 'phone' || $this->interface == 'tablet') {
	        $files[] = ANGIE_PATH . '/frameworks/environment/assets/foundation/stylesheets/jquery.official/jquery.mobile.css';
        } // if
    	} // if

      if ($this->interface == 'printer') {
        if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)) {
          $files[] = ANGIE_PATH . '/frameworks/environment/assets/printer/stylesheets/ie/print.css';
        } // if
      } // if
      
      // load framework css files
      $frameworks = explode(',', APPLICATION_FRAMEWORKS);
      foreach($frameworks as $framework) {
      	if (!$this->only_context) {
	        if($framework != 'environment') {
	          $this->collectFilesFromDir($files, ANGIE_PATH . "/frameworks/$framework/assets/foundation/stylesheets", array('main.css', 'wireframe.css'));
	        } // if
	        $this->collectFilesFromDir($files, ANGIE_PATH . "/frameworks/$framework/assets/$this->interface/stylesheets", array('main.css', 'wireframe.css'));
      	} // if
        
        if($this->context) {
          $this->collectFilesFromDir($files, ANGIE_PATH . "/frameworks/$framework/assets/$this->interface/stylesheets/$this->context", array('main.css', 'wireframe.css'));
        } // if
      } // foreach
      
      // load module css fies
      if($this->modules) {
        foreach($this->modules as $module) {
        	if (is_dir(APPLICATION_PATH . "/modules/$module")) {
        		$base_folder = APPLICATION_PATH . "/modules/$module";
        	} else if (is_dir(CUSTOM_PATH . "/modules/$module")) {
        		$base_folder = CUSTOM_PATH . "/modules/$module";
        	} else {
        		continue;
        	} // if
        	
        	if (!$this->only_context) {
	          $this->collectFilesFromDir($files, "$base_folder/assets/foundation/stylesheets", array('main.css', 'wireframe.css'));
	          $this->collectFilesFromDir($files, "$base_folder/assets/$this->interface/stylesheets", array('main.css', 'wireframe.css'));
        	} // if
          
          if($this->context) {
            $this->collectFilesFromDir($files, "$base_folder/assets/$this->interface/stylesheets/$this->context", array('main.css', 'wireframe.css'));
          } // if
        } // foreach
      } // if
      
      return $files;
    } // getFiles
    
  }