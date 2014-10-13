<?php

  // Build on top of collect files proxy
  require_once ANGIE_PATH . '/frameworks/environment/proxies/CollectFilesProxy.class.php';

  /**
   * Collect JavaScript files for given modules and context
   * 
   * @package angie.frameworks.environment
   * @subpackage proxies
   */
  abstract class FwCollectJavaScriptProxy extends CollectFilesProxy {
    
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
     * Targeted context
     *
     * @var string
     */
    protected $context;
    
    /**
     * Load only files from context
     * 
     * @var Boolean
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
    } // __construct
  
    /**
     * Return content type of the data that handler will forward to the browser
     * 
     * @return string
     */
    protected function getContentType() {
      return 'text/javascript; charset: UTF-8';
    } // getContentType
    
    /**
     * Return array of files that need to be forwarded to the browser
     * 
     * @return array
     */
    protected function getFiles() {
    	
    	$files = array();
      
      // Load base files      
     
     if (!$this->only_context) {
	      $files[] = ANGIE_PATH . '/frameworks/environment/assets/foundation/javascript/jquery.official/jquery.js';
	      $files[] = ANGIE_PATH . '/frameworks/environment/assets/foundation/javascript/jquery.official/jquery.migrate.js';

	      if($this->interface == 'default') {
	        $files[] = ANGIE_PATH . '/frameworks/environment/assets/foundation/javascript/jquery.official/jquery.ui.js';
	        $files[] = ANGIE_PATH . '/frameworks/environment/assets/foundation/javascript/jquery.official/jquery.ui.draggable.js';
	        $files[] = ANGIE_PATH . '/frameworks/environment/assets/default/javascript/backend/history.js';
	        $files[] = ANGIE_PATH . '/frameworks/environment/assets/default/javascript/backend/history.adapter.jquery.js';
	      } elseif($this->interface == 'phone' || $this->interface == 'tablet') {
	        $files[] = ANGIE_PATH . '/frameworks/environment/assets/foundation/javascript/jquery.official/jquery.mobile.config.js';
	        $files[] = ANGIE_PATH . '/frameworks/environment/assets/foundation/javascript/jquery.official/jquery.mobile.js';
	      } // if

       $files[] = ANGIE_PATH . '/frameworks/environment/assets/foundation/javascript/app.js';
	
	      // Load plugins and foundation
	      $this->collectFilesFromDir($files, ANGIE_PATH . '/frameworks/environment/assets/foundation/javascript/jquery.plugins');
				
	      $this->collectFilesFromDir($files, ANGIE_PATH . '/frameworks/environment/assets/foundation/javascript', array(
          'map.js', // Map implementation
	      	'date.js', // Improved date management, used by date and date time pickers
          'moment.js', // Date and time management library
	        'json2.js',
	        'jstorage.js',
	      	'wireframe.js',
	      	'inspector.js'
	      ));
     } else {
        if ($this->context == 'ie') {
          $files[] = ANGIE_PATH . '/frameworks/environment/assets/default/javascript/ie/history.html4.js';
          $files[] = ANGIE_PATH . '/frameworks/environment/assets/default/javascript/ie/history.html4.hack.js';
        } // if
     } // if
	      
			$frameworks = explode(',', APPLICATION_FRAMEWORKS);
    	foreach($frameworks as $framework) {
    		if (!$this->only_context) {
	        if($framework != 'environment') {
	          $this->collectFilesFromDir($files, ANGIE_PATH . "/frameworks/$framework/assets/foundation/javascript", array('main.js'));
	        } // if
	        
	        $this->collectFilesFromDir($files, ANGIE_PATH . "/frameworks/$framework/assets/$this->interface/javascript", array('wireframe.js', 'main.js'));
    		} // if
        
        if($this->context) {
          $this->collectFilesFromDir($files, ANGIE_PATH . "/frameworks/$framework/assets/$this->interface/javascript/$this->context", array('wireframe.js', 'main.js'));
        } // if
      } // foreach
	      
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
	          $this->collectFilesFromDir($files, "$base_folder/assets/foundation/javascript", array('main.js'));
	          $this->collectFilesFromDir($files, "$base_folder/assets/$this->interface/javascript", array('wireframe.js', 'main.js'));
	          
	          if($this->context) {
	            $this->collectFilesFromDir($files, "$base_folder/assets/$this->interface/javascript/$this->context", array('wireframe.js', 'main.js'));
	          } // if
        	} // if
        } // foreach
      } // if
      
      return $files;
    } // getFiles
    
  }