<?php

  /**
   * Router
   * 
   * Router provides support for canonical, pretty URL-s out of box. Reuqest is 
   * matched with set of routes mapped by the user; when router finds first match 
   * it will use data collected from it and match process will be stoped. Routes 
   * are matched in reveresed order so make sure that general routes are on top 
   * of the map list
   * 
   * @package angie.library.router
   */
  final class Router {
    
    // Config
    const REGEX_DELIMITER = '#';
    const URL_VARIABLE = ':';
    
    // Common matches
    const MATCH_ID = '\d+';
    const MATCH_WORD = '\w+';
    const MATCH_SLUG = '([a-z0-9\-\._]+)';
    
    // Name of the compiled 
    const COMPILED_FILE_PREFIX = '_router';
    
    /**
     * Hash that's used to test whether we have new modules
     *
     * @var string
     */
    private static $control_hash;
    
    /**
     * Enable or desiable assembled routes caching
     *
     * @var boolean
     */
    private static $cache_assembled_routes = true;
    
    /**
     * Name of the compiled match function
     *
     * @var string
     */
    private static $compiled_match_function;
    
    /**
     * Name of the compiled assemble function
     *
     * @var string
     */
    private static $compiled_assemble_function;
    
    /**
     * Array of mapped routes
     *
     * @var array
     */
    private static $routes = array();
    
    /**
     * Current module
     * 
     * Used by loadByModules function to remember the name of current module. 
     * When this value is present, by no 'module' is defined in route 
     * definition, map() method will use this value
     *
     * @var string
     */
    private static $current_module = null;
    
    /**
     * Initialize route
     * 
     * @param array $frameworks
     * @param array $modules
     */
    static function init(&$frameworks, &$modules) {
      self::validateCaches(); // Make sure that caches are valid

      self::$control_hash = array();
      
      $max_mtime = 0;
      
      foreach($frameworks as $framework) {
        self::$control_hash[] = $framework->getName();
        
        $definition_mtime_file = filemtime($framework->getPath() . '/' . get_class($framework) . '.class.php');
        
        if($definition_mtime_file > $max_mtime) {
          $max_mtime = $definition_mtime_file;
        }  // if
      } // foreach
      
      foreach($modules as $module) {
        self::$control_hash[] = $module->getName();
        
        $definition_mtime_file = filemtime($module->getPath() . '/' . get_class($module) . '.class.php');
        
        if($definition_mtime_file > $max_mtime) {
          $max_mtime = $definition_mtime_file;
        }  // if
      } // foreach
      
      self::$control_hash = sha1(implode(',', self::$control_hash));
      
      // ---------------------------------------------------
      //  Check if we need to recompile
      // ---------------------------------------------------
      
      $match_file = self::getCompiledMatchFilePath();
      $assemble_file = self::getCompiledAssembleFilePath();
      
      if(is_file($match_file) && is_file($assemble_file)) {
        $match_file_mtime = filemtime($match_file);
        $assemble_file_mtime = filemtime($assemble_file);
        
        $recompile = $match_file_mtime < $max_mtime || $assemble_file_mtime < $max_mtime;
      } else {
        $recompile = true;
      } // if
      
      // ---------------------------------------------------
      //  Load definitions and recompile, if needed
      // ---------------------------------------------------
      
      if($recompile) {
        foreach($frameworks as $framework) {
          self::$current_module = $framework->getName();
          $framework->defineRoutes();
        } // foreach
        
        foreach($modules as $module) {
          self::$current_module = $module->getName();
          $module->defineRoutes();
        } // foreach
        
        self::$current_module = null;
      } // if
      
      if($recompile) {
        self::recompile();
      } // if
    } // init

    /**
     * Make sure that caches are valid
     */
    static private function validateCaches() {
      $root_url_from_cache = AngieApplication::cache()->get('root_url');

      // If ROOT_URL changed, clean up cached routes
      if($root_url_from_cache && $root_url_from_cache != ROOT_URL) {
        AngieApplication::cache()->clear();
        self::cleanUpCache(true);
      } // if

      AngieApplication::cache()->set('root_url', ROOT_URL);
    } // validateCaches
    
    /**
     * Regiter a new route
     * 
     * This function will create a new route based on route string, default 
     * values and additional requirements and save  it under specific name. Name 
     * is used so you can access the route when assembling URL based on a given 
     * route. Name needs to be unique (if route with a given name is already 
     * registered it will be overwriten).
     *
     * @param string $name
     * @param string $route
     * @param array $defaults
     * @param array $requirements
     * @return Route
     */
    static public function map($name, $route, $defaults = null, $requirements = null) {
      if($defaults) {
        if(!isset($defaults['module'])) {
          $defaults['module'] = self::$current_module;
        } // if
      } else {
        $defaults = array('module' => self::$current_module);
      } // if
      
      return self::$routes[$name] = new Route($name, $route, $defaults, $requirements);
    } // map
    
    /**
     * Match request string agains array of mapped routes
     * 
     * This function will loop request string agains array of mapped routes. As 
     * soon as request string is matched looping is stopped and result of route 
     * match method is returned (array of name => value pairs). In case that 
     * none of the mapped routes does not match request string RoutingError will 
     * be thrown
     *
     * @param string $str
     * @param string $query_string
     * @param boolean $use_cache
     * @return Request
     * @throws RoutingError
     * @throws InvalidInstanceError
     */
    static public function match($str, $query_string, $use_cache = true) {
      $str = trim($str, '/');
      
      if(AngieApplication::isInDevelopment() || AngieApplication::isInDebugMode()) {
        Logger::log("Routing string '$str'", Logger::INFO, 'routing');
      } // if
      
      $request = $use_cache ? self::getFromCache($str, $query_string) : null;
      
      if($request instanceof Request) {
        return $request;
      } else {
        self::requireMatchFunction();

        if(self::$compiled_match_function instanceof Closure) {
          $request = call_user_func(self::$compiled_match_function, $str, $query_string);

          if($request instanceof Request) {
            return $request;
          } else {
            throw new RoutingError($str);
          } // if
        } else {
          throw new InvalidInstanceError('compiled_match_function', self::$compiled_match_function, 'Closure');
        } // if
      } // if
    } // match
    
    /**
     * Does same thing as match, but as input parameter it uses $url which is parsed
     * 
     * @param string $url
     * @return Request
     * @throws RoutingError
     */
    static public function matchUrl($url) {
    	$parsed_url = self::parseUrl($url);
    	return self::match($parsed_url[0], $parsed_url[1]);
    } // matchUrl
    
    /**
     * Extracts path_info & query_string from some url
     * 
     * @param string $url
     * @return array
     * @throws Error
     */
    static public function parseUrl($url) {
			$path_info = '';
			$query_string = '';
			
			if (strpos($url, 'index.php/') !== false) {
				// http://afiveone.activecollab.net/public/index.php/reports/assignments?pero=sara
				$path_info_keyword = 'index.php/';				
				$question_mark_pos = strpos($url, '?');

				$path_info_start = strpos($url, $path_info_keyword) + strlen($path_info_keyword);
				
				if ($question_mark_pos !== false) {
					$path_info = substr($url, $path_info_start, $question_mark_pos - $path_info_start);
				} else {
					$path_info = substr($url, $path_info_start);
				} // if
				
				if ($question_mark_pos !== false) {
					$query_string = substr($url, $question_mark_pos + 1);
				} // if

			} else if (strpos($url, 'index.php?') !== false) {
				// http://afiveone.activecollab.net/public/index.php?path_info=reports/assignments&pero=sara
				parse_str(parse_url($url, PHP_URL_QUERY), $parsed_query_string);
				$path_info = isset($parsed_query_string['path_info']) ? $parsed_query_string['path_info'] : null;
				
				$question_mark_pos = strpos($url, '?');
				if ($question_mark_pos !== false) {
					$query_string = substr($url, $question_mark_pos + 1);
				} // if
				
			} else {
				// http://afiveone.activecollab.net/reports/assignments?pero=sara
				if (strpos($url, ROOT_URL) !== 0) {
					throw new Error('Url is invalid');
				} // if
				
				$useful_part = substr($url, strlen(ROOT_URL));
				if ($useful_part[0] == '/') {
					$useful_part = substr($useful_part, 1);
				} // if
				
				$question_mark_pos = strpos($useful_part, '?');
								
				if ($question_mark_pos !== false) {
					$path_info = substr($useful_part, 0, $question_mark_pos);
				} else {
					$path_info = $useful_part;
				} // if
				
				if ($path_info[strlen($path_info) - 1] == '/') {
					$path_info = substr($path_info, 0, -1);
				} // if
				
				if ($question_mark_pos !== false) {
					$query_string = substr($useful_part, $question_mark_pos + 1);
				} // if
			} // if
			
			return array($path_info, $query_string);
    } // parseUrl
    
    /**
     * Assemble URL
     * 
     * Supported options:
     * 
     * - url_base (string): base for URL-s, default is an empty string
     * - query_arg_separator (string): what to use to separate query string 
     *   arguments, default is '&'
     * - anchor (string): name of the URL anchor
     * 
     * @param string $name
     * @param array $data
     * @param array $options
     * @throws AssembleURLError
     * @throws InvalidInstanceError
     * @return string
     */
    static public function assemble($name, $data = null, $options = null) {
      if(!is_array($data)) {
        $data = empty($data) ? array() : array('id' => $data);
      } // if
      
      // Performance note: If we cache rotes with two parameters, we increase 
      // memory usage without any significant speed gain on top level pages, but 
      // pages that display a lot of project level data get small execution time 
      // increase (around 0.01 seconds) and significant memory usage reduction 
      // (down for 3-5MB)
      
      if(self::$cache_assembled_routes) {
        switch(count($data)) {
          case 0:
            $cache_name = 'simple_routes';
            $cache_key = $name;
            break;
          case 1:
            $cache_name = $name . '_routes';
            $cache_key = first($data);
            break;
          case 2:
            $cache_name = $name . '_routes';
            
            ksort($data);
            
            $first = true;
            foreach($data as $k => $v) {
              if($first) {
                $cache_name .= "_{$k}_{$v}";
              } else {
                $cache_key = "{$k}_{$v}";
              } // if
              
              $first = false;
            } // foreach
            
            break;
        } // if
      } // if
      
      if(isset($cache_name) && isset($cache_key)) {
        $cached_values = AngieApplication::cache()->get($cache_name);
        
        if(is_array($cached_values)) {
          if(isset($cached_values[$cache_key])) {
            return $cached_values[$cache_key];
          } // if
        } else {
          $cached_values = array();
        } // if
      } // if
      
      $url_base = $options && isset($options['url_base']) && $options['url_base'] ? $options['url_base'] : URL_BASE;
      $query_arg_separator = $options && isset($options['query_arg_separator']) && $options['query_arg_separator'] ? $options['query_arg_separator'] : '&';
      $anchor = $options && isset($options['anchor']) && $options['anchor'] ? $options['anchor'] : '';

      self::requireAssembleFunction();

      if(self::$compiled_assemble_function instanceof Closure) {
        $result = call_user_func(self::$compiled_assemble_function, $name, $data, $url_base, $query_arg_separator, $anchor);

        if(empty($result)) {
          if(AngieApplication::isInDevelopment() || AngieApplication::isInDebugMode()) {
            Logger::log("Failed to assemble URL based on '$name' route");
          } // if

          throw new AssembleURLError($name, $data);
        } // if

        if(isset($cache_name) && isset($cache_key)) {
          $cached_values[$cache_key] = $result;
          AngieApplication::cache()->set($cache_name, $cached_values);
        } // if

        return $result;
      } else {
        throw new InvalidInstanceError('compiled_assemble_function', self::$compiled_assemble_function, 'Closure');
      } // if
    } // assemble
    
    /**
     * Assemble route from query string
     *
     * @param string $string
     * @return string
     * @throws RouteNotDefinedError
     */
    static public function assembleFromString($string) {
      $params = parse_string(substr($string, 1));
      
      $route = isset($params['route']) ? $params['route'] : null;
      if(empty($route)) {
        throw new RouteNotDefinedError($route);
      } // if
      unset($params['route']);
      
      return self::assemble($route, $params);
    } // assembleFromString
    
    /**
     * Clean router, mostly used in tests
     */
    static public function cleanUp() {
      self::$routes = array();
    } // cleanUp
    
    /**
     * Clean up routing cache
     *
     * @param boolean $full
     */
    static public function cleanUpCache($full = false) {
      if($full) {
        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'routing_cache');
      } else {
        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'routing_cache WHERE last_accessed_on < ?', DateTimeValue::makeFromString('-30 days'));
      } // if
    } // cleanUpCache
    
    // ---------------------------------------------------
    //  Compiler
    // ---------------------------------------------------

    /**
     * Recompile match and and assemble routes
     */
    public static function recompile() {
      self::recompileMatch();
      self::recompileAssemble();

      self::$compiled_match_function = null;
      self::$compiled_assemble_function = null;
    } // recompile

    /**
     * Recompile match file
     */
    static private function recompileMatch() {
      if(AngieApplication::isInDevelopment() || AngieApplication::isInDebugMode()) {
        Logger::log('Compiling match file. Routes defined: ' . count(self::$routes), Logger::INFO, 'routing');
      } // if
      
      $match_file_path = self::getCompiledMatchFilePath();
      
      $handle = fopen($match_file_path, 'w');
      if($handle) {
        fwrite($handle, "<?php\n  \n");
        
        fwrite($handle, '  return function ($path, $query_string) {' . "\n");
        fwrite($handle, '    $matches = null;' . "\n");
        
        $routes = array_reverse(self::$routes);
    
        $counter = 0;
        foreach($routes as $route_name => $route) {
          $counter++;
          
          if($counter == 1) {
            fwrite($handle, '    if(preg_match(' . var_export($route->getRegularExpression(), true) . ', $path, $matches)) {' . "\n");
          } else {
            fwrite($handle, '    } elseif(preg_match(' . var_export($route->getRegularExpression(), true) . ', $path, $matches)) {' . "\n");
          } // if
          
          $name = var_export($route->getName(), true);
          $route_string = var_export($route->getRouteString(), true);
          
          $defaults = var_export($route->getDefaults(), true);
          if(strpos($defaults, "\n") !== false) {
            $defaults = explode("\n", $defaults);
            foreach($defaults as $k => $v) {
              $defaults[$k] = trim($v);
            } // foreach
            
            $defaults = implode(' ', $defaults);
          } // if
          
          $parameters = $route->getNamedParameters();
          
          if(count($parameters)) {
            $parameters = var_export($parameters, true);
          } else {
            $parameters = 'array()';
          } // if
          
          fwrite($handle, '      return Router::doMatch($path, ' . $name . ', ' . $route_string .  ', ' . $defaults . ', ' . $parameters . ', $matches, $query_string);' . "\n");
        } // foreach
        
        fwrite($handle, "    }\n\n");
        fwrite($handle, "    return array(null, null);\n");
        fwrite($handle, '  };');
        
        fclose($handle);
        
        if(AngieApplication::isInDevelopment() || AngieApplication::isInDebugMode()) {
          Logger::log("Compiled match file created at '$match_file_path'", Logger::INFO, 'routing');
        } // if
      } else {
        throw new FileCreateError($match_file_path);
      } // if
    } // recompileMatch
    
    /**
     * Recompile assemble file
     */
    static private function recompileAssemble() {
      if(AngieApplication::isInDevelopment() || AngieApplication::isInDebugMode()) {
        Logger::log('Compiling match file. Routes defined: ' . count(self::$routes), Logger::INFO, 'routing');
      } // if
      
      $assemble_file_path = self::getCompiledAssembleFilePath();
      
      $handle = fopen($assemble_file_path, 'w');
      if($handle) {
        fwrite($handle, "<?php\n  \n");
        
        fwrite($handle, '  return function ($name, $data, $url_base, $query_arg_separator, $anchor) {' . "\n");
        fwrite($handle, '    switch($name) {' . "\n");
        
        foreach(self::$routes as $route_name => $route) {
          fwrite($handle, "      case '$route_name':\n");
          
          $name = var_export($route->getName(), true);
          $route_string = var_export(trim($route->getRouteString(), '/'), true);
          
          $defaults = var_export($route->getDefaults(), true);
          if(strpos($defaults, "\n") !== false) {
            $defaults = explode("\n", $defaults);
            foreach($defaults as $k => $v) {
              $defaults[$k] = trim($v);
            } // foreach
            
            $defaults = implode(' ', $defaults);
          } // if
          
          $requiremetns = is_foreachable($route->getRequirements()) ? var_export($route->getRequirements(), true) : 'array()';
          
          fwrite($handle, '        return Router::doAssemble(' . $name . ', ' . $route_string . ', ' . $defaults . ', $data, $url_base, $query_arg_separator, $anchor);' . "\n"); 
        } // foreach
        
        fwrite($handle, "      default:\n");
        fwrite($handle, "        return '';\n");
        fwrite($handle, "    }\n");
        fwrite($handle, "  };\n");
        
        fclose($handle);
        
        if(AngieApplication::isInDevelopment() || AngieApplication::isInDebugMode()) {
          Logger::log("Compiled match file created at '$assemble_file_path'", Logger::INFO, 'routing');
        } // if
      } else {
        throw new FileCreateError($assemble_file_path);
      } // if
    } // recompileAssemble

    /**
     * Require compiled match function
     */
    static private function requireMatchFunction() {
      if(empty(self::$compiled_match_function)) {
        $compiled_match_file = self::getCompiledMatchFilePath();

        if(!is_file($compiled_match_file)) {
          Router::recompileMatch();
        } // if

        self::$compiled_match_function = include $compiled_match_file;
      } // if
    } // requireMatchFunction

    /**
     * Require assemble function
     */
    static private function requireAssembleFunction() {
      if(empty(self::$compiled_assemble_function)) {
        $compiled_assemble_file = self::getCompiledAssembleFilePath();

        if(!is_file($compiled_assemble_file)) {
          Router::recompileAssemble();
        } // if

        self::$compiled_assemble_function = include $compiled_assemble_file;
      } // if
    } // requireAssembleFunction
    
    /**
     * Return compiled match file path
     * 
     * @return string
     */
    static private function getCompiledMatchFilePath() {
      return COMPILE_PATH . '/' . self::COMPILED_FILE_PREFIX . '_match_' . self::$control_hash . '.php';
    } // getCompiledMatchFilePath
    
    /**
     * Return compiled assemble file path
     * 
     * @return string
     */
    static private function getCompiledAssembleFilePath() {
      return COMPILE_PATH . '/' . self::COMPILED_FILE_PREFIX . '_assemble_' . self::$control_hash . '.php';
    } // getCompiledAssembleFilePath
    
    /**
     * Return from cache
     * 
     * @param string $str
     * @param string $query_string
     * @return Request
     */
    static private function getFromCache($str, $query_string) {
      $row = DB::executeFirstRow('SELECT id, name, content FROM ' . TABLE_PREFIX . 'routing_cache WHERE path_info = ?', $str);
      
      if($row) {
        DB::execute('UPDATE ' . TABLE_PREFIX . 'routing_cache SET last_accessed_on = UTC_TIMESTAMP() WHERE id = ?', $row['id']);
        
        $values = $row['content'] ? unserialize($row['content']) : array();
        
        if($query_string) {
          self::doProcessQueryString($values, $query_string);
        } // if
        
        return new Request($row['name'], $values);
      } else {
        return null;
      } // if
    } // getFromCache
    
    /**
     * Do match route
     * 
     * Method used by compiled match script to extract the data when URL is 
     * matched
     * 
     * @param string $path_info
     * @param string $name
     * @param string $route
     * @param array $defaults
     * @param array $parameters
     * @param array $matches
     * @param string $query_string
     * @return Request
     */
    static function doMatch($path_info, $name, $route, $defaults, $parameters, $matches, $query_string) {
      $values = $defaults;
      
      // Match variables from path
      $index = 0; 
      foreach($parameters as $parameter_name) { 
        $index++; $values[$parameter_name] = $matches[$index]; 
      } // foreach
      
      DB::execute('REPLACE INTO ' . TABLE_PREFIX . 'routing_cache (path_info, name, content, last_accessed_on) VALUES (?, ?, ?, UTC_TIMESTAMP())', $path_info, $name, serialize($values));
      
      // Match variables from query string
      if($query_string) { 
        self::doProcessQueryString($values, $query_string);
      } // if
      
      return new Request($name, $values);
    } // doMatch
    
    /**
     * Process query string
     * 
     * @param array $values
     * @param string $query_string
     */
    static private function doProcessQueryString(&$values, $query_string) {
      $reserved = array("module", "controller", "action");
      
      $query_string_parameters = array(); 
      parse_str($query_string, $query_string_parameters); 
      
      if(is_foreachable($query_string_parameters)) { 
        foreach($query_string_parameters as $parameter_name => $parameter_value) { 
          if(isset($values[$parameter_name]) && in_array($values[$parameter_name], $reserved)) { 
            continue; 
          } // if 
          
          $values[$parameter_name] = $parameter_value; 
        }  // foreach
      }  // if
    } // doProcessQueryString
    
    /**
     * Do assemble route based on given parameters
     * 
     * This function is called by compiled assemble script
     * 
     * @param string $name
     * @param string $route
     * @param array $defaults
     * @param array $data
     * @param string $url_base
     * @param string $query_arg_separator
     * @param string $anchor
     * @return string
     * @throws AssembleURLError
     */
    static function doAssemble($name, $route, $defaults, $data, $url_base, $query_arg_separator, $anchor) {
      $path_parts = array();
      $query_parts = array(); 
      $part_names = array();
  
      // Prepare path param
      foreach(explode("/", $route) as $key => $part) { 
        if(substr($part, 0, 1) == ":") { 
          $part_name = substr($part, 1); 
          $part_names[] = $part_name; 
          
          if(isset($data[$part_name])) { 
            $path_parts[$key] = $data[$part_name] === false ? 0 : $data[$part_name]; 
          } elseif(isset($defaults[$part_name])) {
            $path_parts[$key] = $defaults[$part_name] === false ? 0 : $defaults[$part_name]; 
          } else { 
            throw new AssembleURLError($route, $data, $defaults, $part_name); } 
          } else { 
            $path_parts[$key] = $part; 
          } // if 
        } // if
  
      // Query string params
      foreach($data as $k => $v) { 
        if(!in_array($k, $part_names)) { 
          $query_parts[$k] = $v === false ? 0 : $v; 
        } // if  
      } // foreach
      
      // URL foundation
      if(PATH_INFO_THROUGH_QUERY_STRING) { 
        $url = $url_base; 
        $query_parts = array_merge(array("path_info" => implode("/", $path_parts)), $query_parts); 
      } else { 
        $url = with_slash($url_base) . trim(implode('/', $path_parts), '/'); 
      } // if
      
      // Query string
      if(count($query_parts)) { 
        $url .= version_compare(PHP_VERSION, "5.1.2", ">=") ? ("?" . http_build_query($query_parts, "", $query_arg_separator)) : ("?" . http_build_query($query_parts, "")); 
      } // if
      
      // Anchro
      return $anchor ? "$url#$anchor" : $url;
    } // doAssemble
  
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Returns array of mapped routes
     *
     * @return array
     */
    static function getRoutes() {
      return self::$routes;
    } // getRoutes
    
    /**
     * Return route by name
     *
     * @param string $name
     * @return Route
     */
    static function getRoute($name) {
      return isset(self::$routes[$name]) ? self::$routes[$name] : null;
    } // getRoute
  
  }