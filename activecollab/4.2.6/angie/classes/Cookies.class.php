<?php

  /**
   * Cookie service class
   *
   * Cookie service maps $_COOKIE and prvides simple methods for setting and 
   * getting cookie data. If adds prefixes to variable name to provide just to 
   * make sure that we are using correct data
   *
   *  @package angie.library
   */
  final class Cookies {
    
    /**
     * Cookie path
     *
     * @var string
     */
    static private $path;
    
    /**
     * Cookie domain
     *
     * @var string
     */
    static private $domain;
    
    /**
     * User HTTPS if available for cookies    
     *
     * @var boolean
     */
    static private $secure = false;
  
    /**
     * Cookie prefix
     *
     * @var string
     */
    static private $prefix;
    
    /**
     * Expiration time, in seconds
     *
     * @var integer
     */
    static private $expirationTime = 1209600;
    
    /**
     * Init cookie service
     *
     * @param string $prefix Variable prefix
     * @param string $path Cookie path
     * @param string $domain Cookie domain
     * @param boolean $secure Use HTTPS for cookies if available
     * @param integer $exp_time Expiration time
     */
    static function init($prefix, $path, $domain, $secure, $exp_time = null) {
      self::setPrefix($prefix);
      self::setPath($path);
      self::setDomain($domain);
      self::setSecure($secure);
      
      if($exp_time !== null) {
        self::setExpirationTime($exp_time);
      } // if
    } // init
    
    /**
     * Return variable value from cookie
     *
     * @param string $name Variable name
     * @return mixed
     */
    static function getVariable($name) {
      $var_name = self::getVariableName($name);
      return isset($_COOKIE[$var_name]) ? $_COOKIE[$var_name] : null;
    } // getVariable
    
    /**
     * Set cookie variable
     *
     * @param string $name Variable name, without prefix
     * @param mixed $value Value that need to be set
     * @param integer $expiration_time Expiration time, in seconds
     * @param boolean $http_only
     * @return mixed
     */
    static function setVariable($name, $value, $expiration_time = null, $http_only = true) {
      $name = self::getVariableName($name);
      $secure = self::getSecure() ? 1 : 0;
      
      if(is_null($expiration_time) || ((integer) $expiration_time < 1)) {
        $exp_time = time() + self::getExpirationTime();
      } else {
        $exp_time = time() + (integer) $expiration_time;
      } // if
      
      return setcookie($name, $value, $exp_time, self::getPath(), self::getDomain(), $secure, $http_only);
    } // setVariable
    
    /**
     * Unset cookie variable
     *
     * @param string $name Cookie name
     */
    static function unsetVariable($name) {
      $var_name = self::getVariableName($name);
      
      self::setVariable($name, null);
      $_COOKIE[$var_name] = null;
    } // unsetVariable
    
    // ---------------------------------------------------
    //  Utils
    // ---------------------------------------------------
    
    /**
     * Put prefix in front of variable name if available
     *
     * @param string $name Original name
     * @return string
     */
    static public function getVariableName($name) {
      $prefix = self::getPrefix();
      
      if($prefix) {
        $prefix .= '_';
      } // if
      
      return "$prefix$name";
    } // getVariableName
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Return cookie path
     *
     * @return string
     */
    static function getPath() {
      return self::$path;
    } // getPath
    
    /**
     * Set cookie path
     *
     * @param string $value New value
     */
    static function setPath($value) {
      self::$path = trim($value);
    } // setPath
    
    /**
     * Return cookie domain
     *
     * @return string
     */
    static function getDomain() {
      return self::$domain;
    } // getDomain
    
    /**
     * Set cookie domain
     *
     * @param string $value New value
     */
    static function setDomain($value) {
      self::$domain = trim($value);
    } // setDomain
    
    /**
     * Return secure value
     *
     * @return boolean
     */
    static function getSecure() {
      return self::$secure;
    } // getSecure
    
    /**
     * Set cookie secure flag
     *
     * @param boolean $value New value
     */
    static function setSecure($value) {
      self::$secure = (boolean) $value;
    } // setSecure
    
    /**
     * Return cookie prefix
     *
     * @return string
     */
    static function getPrefix() {
      return self::$prefix;
    } // getPrefix
    
    /**
     * Set cookie prefix
     *
     * @param string $value New value
     */
    static function setPrefix($value) {
      self::$prefix = trim($value);
    } // setPrefix
    
    /**
     * Return cookie expiration time, in seconds
     *
     * @return integer
     */
    static function getExpirationTime() {
      return self::$expirationTime;
    } // getExpirationTime
    
    /**
     * Set cookie expiration time
     *
     * @param integer $value Number of seconds
     */
    static function setExpirationTime($value) {
      if((integer) $value > 0) {
        self::$expirationTime = (integer) $value;
      } // if
    } // setExpirationTime
    
  }