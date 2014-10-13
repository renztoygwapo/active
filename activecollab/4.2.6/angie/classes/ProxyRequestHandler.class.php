<?php

  /**
   * Base for all proxy.php request handlers
   * 
   * @package angie.library
   */
  abstract class ProxyRequestHandler {
    
    /**
     * Construct proxy request handler
     * 
     * @param array $params
     */
    function __construct($params = null) {
      
    } // __construct

    /**
     * Handle request based on provided data
     */
    abstract function execute();
    
    /**
     * Send not found HTTP header (404)
     */
    function notFound() {
      header("HTTP/1.1 404 HTTP/1.1 404 Not Found");
      die('<h1>HTTP/1.1 404 Not Found</h1>');
    } // notFound
    
    /**
     * Send bad request HTTP header (400)
     */
    function badRequest() {
      header("HTTP/1.1 400 HTTP/1.1 400 Bad Request");
      die('<h1>HTTP/1.1 400 Bad Request</h1>');    	
    } // badRequest
    
    /**
     * Send bad request HTTP header (500)
     */
    function operationFailed() {
      header("HTTP/1.1 500 HTTP/1.1 500 Internal Server Error");
      die('<h1>HTTP/1.1 500 Internal Server Error</h1>');    	
    } // badRequest
    
  }