<?php
  
  /**
   * Route definition class
   * 
   * @package angie.library.router
   */
  class Route {
    
    /**
     * Name of the route
     *
     * @var string
     */
    private $name;
    
    /**
     * Input route string that is parsed into parts on construction
     *
     * @var string
     */
    private $route_string;
    
    /**
     * Route string parsed into associative array of param name => regular 
     * expression
     *
     * @var array
     */
    private $parts;
    
    /**
     * Default values for specific params
     *
     * @var array
     */
    private $defaults = array();
    
    /**
     * Regular expressions that force specific expressions for specific params
     *
     * @var array
     */
    private $requirements = array();
    
    /**
     * Cached array of variables
     *
     * @var array
     */
    private $variables = array();

    /**
     * Construct route
     * 
     * This function will parse route string and populate $this->parts with rules 
     * that need to be matched
     *
     * @param string $name
     * @param string $route
     * @param array $defaults
     * @param array $requirements
     */
    function __construct($name, $route, $defaults = array(), $requirements = array()) {
      $this->route_string = $route; // original string
      
      $route = trim($route, '/');
      
      $this->name = $name;
      $this->defaults = (array) $defaults;
      $this->requirements = (array) $requirements;

      foreach(explode('/', $route) as $pos => $part) {
        if(substr($part, 0, 1) == Router::URL_VARIABLE) {
          $name = substr($part, 1);
          $regex = (isset($requirements[$name]) ? '(' . $requirements[$name] . ')' : Router::MATCH_SLUG);
          $this->parts[$pos] = array(
            'name'  => $name, 
            'regex' => $regex
          ); // array
          
          $this->variables[] = $name;
        } else {
          $this->parts[$pos] = array(
            'raw' => $part,
            'regex' => str_replace('\-', '-', preg_quote($part, Router::REGEX_DELIMITER)), // Unescape \-
          ); // array
        } // if
      } // foreach
    } // __construct
    
    // ---------------------------------------------------
    //  Utility methods
    // ---------------------------------------------------
    
    /**
     * Cached regular expression that will match this route
     *
     * @var string
     */
    private $regex = false;
    
    /**
     * Return regular expresion that will match path part of the URL
     * 
     * @return string
     */
    function getRegularExpression() {
      if($this->regex === false) {
        $this->regex = array();
        foreach($this->parts as $part) {
          $this->regex[] = $part['regex'];
        } // foreach
        
        $this->regex = '/^' . implode('\/', $this->regex) . '$/';
      } // if
      
      return $this->regex;
    } // getRegularExpression
    
    /**
     * Return named parameters
     * 
     * @return array
     */
    function getNamedParameters() {
      $parameters = array();
      foreach($this->parts as $part) {
        if(isset($part['name'])) {
          $parameters[] = $part['name'];
        } // if
      } // foreach
      
      return $parameters;
    } // getNamedParameters
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Get name
     *
     * @return string
     */
    function getName() {
      return $this->name;
    } // getName
    
    /**
     * Set name value
     *
     * @param string $value
     */
    function setName($value) {
      $this->name = $value;
    } // setName
    
    /**
     * Get route_string
     *
     * @return string
     */
    function getRouteString() {
      return $this->route_string;
    } // getRouteString
    
    /**
     * Set route_string value
     *
     * @param string $value
     */
    function setRouteString($value) {
      $this->route_string = $value;
    } // setRouteString
    
    /**
     * Return defaults value
     *
     * @return array
     */
    function getDefaults() {
      return $this->defaults;
    } // getDefaults
    
    /**
     * Return requirements value
     *
     * @return array
     */
    function getRequirements() {
      return $this->requirements;
    } // getRequirements
    
    /**
     * Returns true if this route has a variable with a given name
     * 
     * @param string $part_name
     * @return boolean
     */
    function hasVariable($variable_name) {
      return in_array($variable_name, $this->variables);
    } // hasVariable
  
  }