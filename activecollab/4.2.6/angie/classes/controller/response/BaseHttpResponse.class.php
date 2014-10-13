<?php

  /**
   * Framework level HTTP response implementation
   * 
   * This class was originally called HttpResponse, but it was renamed to avoid 
   * conflict with HttpResponse class of HTTP PHP extension:
   * 
   * <http://www.php.net/manual/en/class.httpresponse.php>
   * 
   * @package angie.library.controller
   */
  class BaseHttpResponse extends Response {
    
    // Frequenlty used HTTP statuses
    const OK = 200;
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const INVALID_PROPERTIES = 400;
    const CONFLICT = 409;
    const OPERATION_FAILED = 500;
    const UNAVAILABLE = 503;
    
    // Frequently used content types
    const HTML = 'text/html';
    const JAVASCRIPT = 'application/javascript';
    const CSS = 'text/css';
    const CSV = 'text/csv';
    const EXCEL = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    const JSON = 'application/json';
    const XML = 'application/xml';
    const PLAIN = 'text/plain';
    
    /**
     * Content type of response
     *
     * @var string
     */
    private $content_type = 'text/html';
    
    /**
     * Response charset
     *
     * @var string
     */
    private $charset = 'utf-8';
    
    /**
     * Describe for interface (false by default)
     *
     * @var boolean
     */
    protected $describe_for_interface = false;
    
    /**
     * HTTP statuses
     *
     * @var array
     */
    private $http_statuses = array(
      100 => "Continue",
      101 => "Switching Protocols",
      200 => "OK",
      201 => "Created",
      202 => "Accepted",
      203 => "Non-Authoritative Information",
      204 => "No Content",
      205 => "Reset Content",
      206 => "Partial Content",
      300 => "Multiple Choices",
      301 => "Moved Permanently",
      302 => "Found",
      303 => "See Other",
      304 => "Not Modified",
      305 => "Use Proxy",
      307 => "Temporary Redirect",
      400 => "Bad Request",
      401 => "Unauthorized",
      402 => "Payment Required",
      403 => "Forbidden",
      404 => "Not Found",
      405 => "Method Not Allowed",
      406 => "Not Acceptable",
      407 => "Proxy Authentication Required",
      408 => "Request Time-out",
      409 => "Conflict",
      410 => "Gone",
      411 => "Length Required",
      412 => "Precondition Failed",
      413 => "Request Entity Too Large",
      414 => "Request-URI Too Large",
      415 => "Unsupported Media Type",
      416 => "Requested range not satisfiable",
      417 => "Expectation Failed",
      500 => "Internal Server Error",
      501 => "Not Implemented",
      502 => "Bad Gateway",
      503 => "Service Unavailable",
      504 => "Gateway Time-out" 
    );

    /**
     * Return description of the http status
     *
     * @param $status
     * @return null
     */
    function getStatusDescription($status) {
      $descriptions = array(
        404 => lang("The requested page or object does not exist"),
        403 => lang("You don't have privilege to access this page or object")
      );

      if (isset($descriptions[$status])) {
        return $descriptions[$status];
      } // if

      return null;
    } // getStatusDescription

    /**
     * Notify user that request was executed successfully
     * 
     * @param mixed $settings
     */
    function ok($settings = null) {
      $this->respondWithStatus(self::OK, array_var($settings, 'message'), array_var($settings, 'only_headers'));
    } // ok
    
    /**
     * Require client to authenticate before proceeding
     * 
     * @param mixed $settings
     */
    function requireAuthentication($settings = null) {
      $this->respondWithStatus(self::UNAUTHORIZED, array_var($settings, 'message'), array_var($settings, 'only_headers'));
    } // requireAuthentication
    
    /**
     * Notify client that user has been successfully authenticated
     * 
     * @param User $user
     * @param mixed $settings
     */
    function authenticated(User $user, $settings = null) {
      $this->redirectToReferer();
    } // authenticated
    
    /**
     * Send 402 (Bad Request) error
     * 
     * @param mixed $settings
     */
    function badRequest($settings = null) {
      $this->respondWithStatus(self::BAD_REQUEST, array_var($settings, 'message'), array_var($settings, 'only_headers'));
    } // badRequest
    
    /**
     * Send 403 (Forbidden) error
     * 
     * @param mixed $settings
     */
    function forbidden($settings = null) {
      $this->respondWithStatus(self::FORBIDDEN, array_var($settings, 'message'), array_var($settings, 'only_headers'));
    } // forbidden
    
    /**
     * Send 404 (Not Found) error
     * 
     * @param mixed $settings
     */
    function notFound($settings = null) {
      $this->respondWithStatus(self::NOT_FOUND, array_var($settings, 'message'), array_var($settings, 'only_headers'));
    } // notFound
    
    /**
     * Send 407 (Operation Failed) error
     * 
     * @param mixed $settings
     */
    function operationFailed($settings = null) {
      $this->respondWithStatus(self::OPERATION_FAILED, array_var($settings, 'message'), array_var($settings, 'only_headers'));
    } // operationFailed
    
    /**
     * Send 503 notification
     * 
     * @param mixed $settings
     */
    function unavailable($settings = null) {
      $this->respondWithStatus(self::UNAVAILABLE, array_var($settings, 'message'), array_var($settings, 'only_headers'));
    } // unavailable
    
    // ---------------------------------------------------
    //  Redirects
    // ---------------------------------------------------
    
    /**
     * Redirect to URL
     *
     * @param string $url
     */
    function redirectToUrl($url) {
      header('Location: ' . undo_htmlspecialchars($url));
      die();
    } // redirectToUrl
    
    // ---------------------------------------------------
    //  Responses
    // ---------------------------------------------------
    
    /**
     * Assign variable to template
     *
     * @param mixed $var
     * @param mixed $value
     */
    function assign($var, $value = null) {
      // black hole
    } // assign
    
    /**
     * Default response
     * 
     * Default API response is OK. Use export() when needed.
     * 
     * @param mixed $settings
     */
    function respond($settings = null) {
      $this->ok();
    } // respond

    /**
     * Respond with content
     *
     * This function accepts following settings:
     *
     * - die: boolean (true by default)
     *
     * @param string $content
     * @param mixed $settings
     */
    function respondWithContent($content, $settings = null) {
      $this->sendContent($content, array_var($settings, 'die', true));
    } // respondWithContent
    
    /**
     * Send simple, status response
     * 
     * @param integer $status
     * @param string $message
     * @param boolean $only_headers
     */
    function respondWithStatus($status, $message = null, $only_headers = false) {
      if(empty($message)) {
        $message = $this->getStatusMessage($status);
      } // if
      
      header("HTTP/1.1 $status $message");
      $this->sendContentType();
      
      if($this->request->isPageCall() && !$only_headers) {
        $smarty = SmartyForAngie::getInstance();
        $smarty->assign(array(
          'page_title'  => "HTTP/1.1 $status $message",
          'message'     => $this->getStatusDescription($status)
        ));
        $this->sendContent($smarty->fetch(AngieApplication::getLayoutPath('error', ENVIRONMENT_FRAMEWORK)));
      } // if
      
      die();
    } // respondWithStatus
    
    /**
     * Export data (used for responses to machines)
     * 
     * Available settings:
     * 
     * - as - Name of the root XML node
     * - format - XML or JSON, by default system will use format from request
     * - detailed - should response be detailed data set, or just brief info 
     *   (brief by default)
     * - for_interface - should response be tailored for web interface, or gor 
     *   general data sharing (API)
     * 
     * @param mixed $data
     * @param mixed $settings
     */
    function respondWithData($data, $settings = null) {
      $format = $settings && isset($settings['format']) ? $settings['format'] : $this->request->getFormat();
      $detailed = $settings && isset($settings['detailed']) ? $settings['detailed'] : $this->request->get('detailed', false);
      $for_interface = $settings && isset($settings['for_interface']) && $settings['for_interface'] ? (boolean) $settings['for_interface'] : ($this->describe_for_interface ? $this->describe_for_interface : $this->request->get('for_interface', false));

      if(empty($for_interface)) {
        $for_interface = $this->request->isApiCall() ? AngieApplication::INTERFACE_API : AngieApplication::INTERFACE_DEFAULT;
      } // if
      
      // Serve JSON or JSONP
      if($format == FORMAT_JSON || $this->request->isAsyncCall()) {
        if(ALLOW_JSONP && $this->request->isApiCall() && $this->request->get('jsonp_callback_name')) {
          $this->setContentType(self::JAVASCRIPT);
          $this->sendContent($this->request->get('jsonp_callback_name') . '(' . JSON::encode($data, null, $detailed, $for_interface) . ');', true);
        } else {
          $this->setContentType(self::JSON);
          $this->sendContent(JSON::encode($data, null, $detailed, $for_interface), true);
        } // if
        
      // Serve XML
      } else {
        if($settings && isset($settings['as'])) {
          $as = $settings['as'];
        } else {
          $as = is_foreachable($data) ? 'items' : 'item';
        } // if
        
        $this->setContentType(self::XML);
        $this->sendContent(XmlEncoder::encode($data, $as, null, $detailed, $for_interface), true);
      } // if
    } // respondWithData
    
    /**
     * Respond with file download
     * 
     * @param string $file_path
     * @param string $type
     * @param string $name
     * @param boolean $force
     */
    function respondWithFileDownload($file_path, $type = 'application/octet-stream', $name = null, $force = false) {
      download_file($file_path, $type, $name, $force, true);
    } // respondWithFileDownload
    
    /**
     * Forward content to browser as a file download
     * 
     * @param mixed $content
     * @param string $type
     * @param string $name
     * @param boolean $force
     */
    function respondWithContentDownload($content, $type = 'application/octet-stream', $name, $force = true) {
      download_contents($content, $type, $name, $force, true);
    } // respondWithContentDownload
    
    /**
     * Notify client on system exception
     *
     * @param Exception $data
     * @param mixed $settings
     */
    function exception($data, $settings = null) {
      if($this->request->isAsyncCall() || $this->request->isApiCall()) {
        header('HTTP/1.1 500 Internal Server Error');
        
        if($settings) {
          if(!isset($settings['as'])) {
            $settings['as'] = 'exception';
          } // if
        } else {
          $settings = array('as' => 'exception');
        } // if
        
        $this->respondWithData($data, $settings);
      } else {
        $this->respondWithStatus(500);
      } // if
    } // exception
    
    // ---------------------------------------------------
    //  HTTP statuses
    // ---------------------------------------------------

    /**
     * Cached use GZIP value, so we don't need to check it every time
     *
     * @var boolean
     */
    private $use_gzip = null;

    /**
     * Return true if we need to GZIP responses
     *
     * @return bool
     */
    function useGzip() {
      if($this->use_gzip === null) {
        $this->use_gzip = AngieApplication::clientAcceptsGzip() && defined('COMPRESS_HTTP_RESPONSES') && COMPRESS_HTTP_RESPONSES && extension_loaded('zlib') && !((boolean) ini_get('zlib.output_compression'));
      } // if

      return $this->use_gzip;
    } // useGzip
    
    /**
     * Return message for a given status
     * 
     * @param integer $status
     * @return string
     * @throws InvalidParamError
     */
    protected function getStatusMessage($status) {
      if(isset($this->http_statuses[$status])) {
        return $this->http_statuses[$status];
      } else {
        throw new InvalidParamError('status', $status, "Unknown HTTP status: '$status'");
      } // if
    } // getStatusMessage
    
    /**
     * Indicator whether content type has already been sent or not
     *
     * @var boolean
     */
    protected $content_type_sent = false;
    
    /**
     * Send content type header
     */
    protected function sendContentType() {
      if(!$this->content_type_sent) {
        if($this->useGzip()) {
          @ob_start('ob_gzhandler');
        } else {
          ob_start();
        } // if

        header("Content-Type: $this->content_type; charset=$this->charset");
        $this->content_type_sent = true;
      } // if
    } // sendContentType
    
    /**
     * Send content (full content or content chunk)
     * 
     * @param string $content
     * @param boolean $die
     */
    function sendContent($content, $die = false) {
      if(!$this->content_type_sent) {
        $this->sendContentType();
      } // if
      
      print $content;
      
      if($die) {
        die();
      } // if
    } // sendContent
    
    // ---------------------------------------------------
    //  Getters and Setters
    // ---------------------------------------------------
    
    /**
     * Return response content type
     * 
     * @return string
     */
    function getContentType() {
      return $this->content_type;
    } // getContentType
    
    /**
     * Set content type
     * 
     * @param string $value
     * @throws InvalidParamError
     */
    function setContentType($value) {
      if(preg_match('/^[a-z]+\w*\/[a-z]+[\w.;= -]*$/', $value)) {
        $this->content_type = $value;
      } else {
        throw new InvalidParamError('value', "'$value' is not a valid content type");
      } // if
    } // setContentType
    
    /**
     * Return charset
     * 
     * @return string
     */
    function getCharset() {
      return $this->charset;
    } // getCharset
    
    /**
     * Set response charset
     * 
     * @param string $value
     */
    function setCharset($value) {
      $this->charset = $value;
    } // setCharset
    
  }