<?php 

	/**
	 * Retrieves the icon for the module
	 * 
   * @package angie.frameworks.environment
   * @subpackage proxies
	 * @author godza
	 */
	class ModuleIconProxy extends ProxyRequestHandler {
		
		/**
		 * Initial parameters
		 * 
		 * @var array
		 */
		protected $params;
		
		/**
		 * Constructor
		 * 
		 * @param array $params
		 */
		function __construct($params) {
			$this->params = $params;
			
			require_once ANGIE_PATH . '/functions/web.php';
			require_once ANGIE_PATH . '/functions/errors.php';
			require_once ANGIE_PATH . '/classes/Inflector.class.php';
			require_once ANGIE_PATH . '/classes/application/AngieApplication.class.php';
			require_once ANGIE_PATH . '/classes/application/AngieFramework.class.php';
			require_once ANGIE_PATH . '/classes/application/AngieModule.class.php';
		} // __construct
		
		/**
		 * Serve the module icon image
		 */
		function execute() {
    	$wanted_module = AngieApplication::getModule(isset($this->params['module_name']) ? $this->params['module_name'] : null, false);
    	
    	if ($wanted_module instanceof AngieModule) {
    		$icon_path = $wanted_module->getPath() . '/assets/default/images/module.png';
    		if (is_file($icon_path)) {
          @date_default_timezone_set('gmt');
    			download_file($icon_path, 'image/png', false, false);
    		} // if
    	} // if
    	
    	$this->notFound();
		}	// execute
			
	}