<?php

  /**
   * Framework level HTTP response implementation
   *
   * @package angie.library.controller
   * @subpackage response
   */
  abstract class FwResponse {
    
    // Format constants
    const FORMAT_JSON = 'json';
    const FORMAT_XML = 'xml';
    
    /**
     * Parnet controller, that created this response object
     *
     * @var Controller
     */
    protected $controller;
    
    /**
     * Input request object
     *
     * @var Request
     */
    protected $request;
    
    /**
     * Construct response object
     * 
     * @param Controller $controller
     * @param Request $request
     */
    function __construct(Controller &$controller, Request &$request) {
      $this->controller = $controller;
      $this->request = $request;
    } // __construct
    
    // ---------------------------------------------------
    //  Statuses
    // ---------------------------------------------------
    
    /**
     * Notify user that request was executed successfully
     * 
     * @param mixed $settings
     */
    abstract function ok($settings = null);
    
    /**
     * Require client to authenticate before proceeding
     * 
     * @param array $settings
     */
    abstract function requireAuthentication($settings = null);
    
    /**
     * Notify client that user has been successfully authenticated
     * 
     * @param User $user
     * @param array|null $settings
     */
    abstract function authenticated(User $user, $settings = null);
    
    /**
     * Notify client that submitted request is not valid
     * 
     * @param array $settings
     */
    abstract function badRequest($settings = null);
    
    /**
     * Notify client that access to this action is forbidden
     * 
     * @param array $settings
     */
    abstract function forbidden($settings = null);
    
    /**
     * Notify client that requested resource is not found
     * 
     * @param array $settings
     */
    abstract function notFound($settings = null);
    
    /**
     * Notify client that operation failed
     * 
     * @param array $settings
     */
    abstract function operationFailed($settings = null);
    
    /**
     * Notify client that system is temporaly unavailable
     * 
     * @param array $settings
     */
    abstract function unavailable($settings = null);
    
    // ---------------------------------------------------
    //  Redirects
    // ---------------------------------------------------
    
    /**
     * Redirect to URL
     *
     * @param string $url
     */
    abstract function redirectToUrl($url);
    
    /**
     * Redirect to specific route
     * 
     * Params of this function will be used to assemble URL
     *
     * @param string $route_name
     * @param array $params
     * @param array $options
     */
    function redirectTo($route_name, $params = null, $options = null) {
      $this->redirectToUrl(Router::assemble($route_name, $params, $options));
    } // redirectTo
    
    /**
     * Redirect to referer
     *
     * @param string $alternative
     */
    function redirectToReferer($alternative = '') {
      if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']) {
        $this->redirectToUrl($_SERVER['HTTP_REFERER']);
      } else {
        $this->redirectToUrl($alternative);
      } // if
    } // redirectToReferer
    
    // ---------------------------------------------------
    //  Responses
    // ---------------------------------------------------
    
    /**
     * Assign variable to template
     *
     * @param mixed $var
     * @param mixed $value
     */
    abstract function assign($var, $value = null);
    
    /**
     * Default response based on request
     * 
     * This method is called if action does not provide any alternative response
     * 
     * @param mixed $settings
     */
    abstract function respond($settings = null);

    /**
     * Respond with content
     *
     * @param string $content
     * @param mixed $settings
     */
    abstract function respondWithContent($content, $settings = null);
    
    /**
     * Respond with data
     * 
     * @param mixed $data
     * @param mixed $settings
     */
    abstract function respondWithData($data, $settings = null);

    /**
     * Shortcut method for easy map encoding
     *
     * @param mixed $data
     * @param mixed $settings
     */
    function respondWithMap($data, $settings = null) {
      $this->respondWithData(JSON::valueToMap($data), $settings);
    } // respondWithMap
    
    /**
     * Notify client on system exception
     * 
     * @param Exception $data
     * @param mixed $settings
     */
    abstract function exception($data, $settings = null);
    
  }