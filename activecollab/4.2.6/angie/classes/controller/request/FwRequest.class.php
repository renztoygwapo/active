<?php

  /**
   * Request details
   *
   * This class is used for request handling - extraction of request parameters, 
   * input filtering and cleaning, work with data from $_SERVER variable etc
   * 
   * @package angie.library.controller
   * @subpackage request
   */
  abstract class FwRequest {
    
    /**
     * Name of the route that produced this request object
     *
     * @var string
     */
    private $matched_route;
  
    /**
     * Array of parameters extracted from URL
     *
     * @var array
     */
    private $url_params;

    /**
     * Construct request object
     *
     * @param string $matched_route
     * @param array $url_params
     */
    function __construct($matched_route, $url_params) {
      $this->matched_route = $matched_route;
      $this->url_params = $url_params;
      
      $_GET = array();
      if(is_foreachable($url_params)) {
        foreach($url_params as $k => $v) {
          if($k != 'controller' && $k != 'action') {
            $_GET[$k] = $v;
          } // if
        } // foreach
      } // if
    } // __construct
    
    /**
     * Return name of the module that needs to serve this request
     *
     * @return string
     */
    function getModule() {
      return array_var($this->url_params, 'module', DEFAULT_MODULE);
    } // getModule
    
    /**
     * Return requested controller
     *
     * @return string
     */
    function getController() {
      return array_var($this->url_params, 'controller', DEFAULT_CONTROLLER);
    } // getController
    
    /**
     * Prepared action name
     *
     * @var string
     */
    private $action = false;
    
    /**
     * Return requested action
     *
     * @return string
     */
    function getAction() {
      if($this->action === false) {
        $this->action = Inflector::underscore(trim(array_var($this->url_params, 'action', DEFAULT_ACTION)));
      } // if
      
      return $this->action;
    } // getAction
    
    /**
     * Requested response format
     *
     * @var string
     */
    private $format = false;
    
    /**
     * Return requested output format
     *
     * @return string
     */
    function getFormat() {
      if($this->format === false) {
        if(isset($this->url_params['format'])) {
          $this->format = $this->url_params['format'];
        } else {
          $accept = strtolower(array_var($_SERVER, 'HTTP_ACCEPT'));
          if($accept == 'application/json') {
            $this->format = FORMAT_JSON;
          } elseif($accept == 'application/xml') {
            $this->format = FORMAT_XML;
          } // if
        } // if
        
        if(empty($this->format)) {
          $this->format = defined('ANGIE_API_CALL') && ANGIE_API_CALL ? FORMAT_XML : DEFAULT_FORMAT; // Force API call?
        } // if
      } // if
      
      return $this->format;
    } // getFormat
    
    /**
     * Explicitly set requested output format
     *
     * @param string $format
     */
    function setFormat($format) {
      $this->format = $format;
    } // setFormat
    
    /**
     * Cached URL value
     *
     * @var string
     */
    private $url = false;
    
    /**
     * Return URL of original request
     * 
     * @return string
     */
    function getUrl() {
      if($this->url === false) {
        $this->url = Router::assemble($this->matched_route, $this->url_params);
      } // if
      
      return $this->url;
    } // getUrl
    
    /**
     * Hashtag URL
     *
     * @var string
     */
    private $hashtag_url = false;
    
    /**
     * Return hashtag URL
     * 
     * @return string
     */
    function getHashtagUrl() {
      if($this->hashtag_url === false) {
        if($this->getUrl()) {
          $this->hashtag_url = URL_BASE . '#!' . substr($this->getUrl(), strlen(URL_BASE) + 1);
        } else {
          $this->hashtag_url = URL_BASE;
        } // if
      } // if
      
      return $this->hashtag_url;
    } // getHashtagUrl
    
    // ---------------------------------------------------
    //  Variable access
    // ---------------------------------------------------
    
    /**
     * Return variable from GET
     * 
     * If $var is NULL, entire GET array will be returned
     *
     * @param string $var
     * @param mixed $default
     * @return mixed
     */
    function get($var = null, $default = null) {
      if($var) {
        switch($var) {
          case 'module':
            return $this->getModule();
          case 'controller':
            return $this->getController();
          case 'action':
            return $this->getAction();
          default:
            return isset($_GET[$var]) ? $_GET[$var] : $default;
        } // switch
      } else {
        return $_GET;
      } // if
    } // get
    
    /**
     * Return ID
     * 
     * This function will extract ID value from request. If $from is NULL get 
     * will be used, else it will be extracted from $from. Default value is 
     * returned if ID is missing
     *
     * @param string $name
     * @param array $from
     * @param mixed $default
     * @return integer
     */
    function getId($name = 'id', $from = null, $default = null) {
      if($from === null) {
        return (integer) $this->get($name, $default);
      } else {
        return (integer) array_var($from, $name, $default);
      } // if
    } // getId
    
    /**
     * Return page number
     *
     * @param string $variable_name
     * @return integer
     */
    function getPage($variable_name = 'page') {
      $page = (integer) $this->get($variable_name);
      return $page < 1 ? 1 : $page;
    } // getPage
    
    /**
     * Return POST variable
     * 
     * If $var is NULL, entire POST array will be returned
     *
     * @param string $var
     * @param mixed $default
     * @return mixed
     */
    function post($var = null, $default = null) {
      if($var) {
        return isset($_POST[$var]) ? $_POST[$var] : $default;
      } else {
        return $_POST;
      } // if
    } // post
    
    // ---------------------------------------------------
    //  Indicators
    // ---------------------------------------------------
    
    /**
     * Cached is index page
     *
     * @var boolean
     */
    protected $is_index = null;
    
    /**
     * Returns true if this requests is pointing to index page, or to one of the 
     * subpages
     * 
     * @return boolean
     */
    function isIndex() {
      if($this->is_index === null) {
        if(count($_GET)) {
          $this->is_index = true;
          
          foreach($_GET as $k => $v) {
            if($k != 'module' && $k != 'controller' && $k != 'action') {
              $this->is_index = false;
              break;
            } // if
          } // foreach
        } else {
          $this->is_index = true;
        } // if
      } // if
      
      return $this->is_index;
    } // isIndex
    
    /**
     * Returns true if this request is submitted through POST and submitted 
     * variable is set to submitted
     *
     * @param boolean $csfr_check
     * @param Response $response
     * @return boolean
     */
    function isSubmitted($csfr_check = false, $response = null) {
      if($this->post('submitted') == 'submitted') {
        if($csfr_check && !$this->isValidCsfrCode()) {
          if($response instanceof Response) {
            $response->badRequest();
          } // if
          
          return false;
        } // if
        
        return true;
      } // if
      
      return false;
    } // isSubmitted
    
    /**
     * Validate CSFR protection
     * 
     * @return boolean
     */
    function isValidCsfrCode() {
      if(defined('ANGIE_API_CALL') && ANGIE_API_CALL) {
        return true; // API calls made through api.php are signed with api token
      } else {
        $code = $this->post('csfr_code');
        
        if($code && $code == AngieApplication::getCsfrProtectionCode()) {
          AngieApplication::resetCsfrProtectionCode();
          
          return true;
        } else {
          return false;
        } // if
      } // if
    } // isValidCsfrCode
    
    /**
     * Returns true if user is requesting a web page to be served
     *
     * @return boolean
     */
    function isPageCall() {
      return !($this->isApiCall() || $this->isAsyncCall());
    } // isPageCall
    
    /**
     * Returns true if this request is API call
     *
     * @return boolean
     */
    function isApiCall() {
      return (defined('ANGIE_API_CALL') && ANGIE_API_CALL) || ($this->getFormat() != FORMAT_HTML);
    } // isApiCall
    
    /**
     * Returns true if this request is marked as async call
     *
     * @return boolean
     */
    function isAsyncCall() {
      return (boolean) $this->get('async');
    } // isAsyncCall
    
    /**
     * Returns true if this request should be served in already loaded wireframe
     *
     * @return boolean
     */
    function isInlineCall() {
      return $this->get('inline', false);
    } // isInlineCall
    
    /**
     * Returns true if this request should be served as single element of object 
     * list
     *
     * @return boolean
     */
    function isSingleCall() {
      return $this->get('single', false);
    } // isSingleCall

    /**
     * Returns true if request should be served for quick view
     *
     * @return string
     */
    function isQuickViewCall() {
      return $this->get('quick_view', false);
    } // isQuickViewCall
    
    /**
     * Returns true if this is request for print
     *
     * @return boolean
     */
    function isPrintCall() {
      return $this->get('print', false);
    } // isPrintCall
    
    // ---------------------------------------------------
    //  Device indicators
    // ---------------------------------------------------
    
    /**
     * Cached is web browser value
     *
     * @var boolean
     */
    private $is_web_browser = null;
    
    /**
     * Returns ture if device accessing the system is web browser
     * 
     * @return boolean
     */
    function isWebBrowser() {
    	if($this->is_web_browser === null) {
    	  if($this->isApiCall() || $this->isPrintCall()) {
    	    $this->is_web_browser = false;
    	  } else {
    	    $this->is_web_browser = AngieApplication::getPreferedInterface() == AngieApplication::INTERFACE_DEFAULT;
    	  } // if
    	} // if
    	
    	return $this->is_web_browser;
    } // isWebBrowser
    
    /**
     * Returns true if device accessing the system is mobile device
     * 
     * @return boolean
     */
    function isMobileDevice() {
    	return $this->isPhone() || $this->isTablet();
    } // isMobileDevice
    
    /**
     * Cached isPhone indicator value
     *
     * @var boolean
     */
    private $is_phone = null;
    
    /**
     * Returns true if device accessing the system is a phone
     * 
     * @return boolean
     */
    function isPhone() {
    	if($this->is_phone === null) {
    	  if($this->isApiCall() || $this->isPrintCall()) {
    	    $this->is_phone = false;
    	  } else {
    	    $this->is_phone = AngieApplication::getPreferedInterface() == AngieApplication::INTERFACE_PHONE;
    	  } // if
    	} // if
    	
    	return $this->is_phone;
    } // isPhone
    
    /**
     * Cached is tablet indicator value
     *
     * @var boolean
     */
    private $is_tablet = null;
    
    /**
     * Returns true if devices accessing the system is a table device
     * 
     * @return boolean
     */
    function isTablet() {
    	if($this->is_tablet === null) {
    	  if($this->isApiCall() || $this->isPrintCall()) {
    	    $this->is_tablet = false;
    	  } else {
    	    $this->is_tablet = AngieApplication::getPreferedInterface() == AngieApplication::INTERFACE_TABLET;
    	  } // if
    	} // if
    	
    	return $this->is_tablet;
    } // isTablet

    /**
     * Get event scope
     *
     * @return string
     */
    public function getEventScope() {
      if ($this->isQuickViewCall()) {
        return $this->isQuickViewCall();
      } else if ($this->isSingleCall()) {
        return 'single';
      } else if ($this->isInlineCall()) {
        return 'content';
      } // if

      return '';
    } // getEventScope
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Returns the matched route
     * 
     * @return string
     */
    function getMatchedRoute() {
    	return $this->matched_route;
    } // getMatchedRoute
    
    /**
     * Get url_params
     *
     * @return array
     */
    function getUrlParams() {
      return $this->url_params;
    } // getUrlParams
  
  }