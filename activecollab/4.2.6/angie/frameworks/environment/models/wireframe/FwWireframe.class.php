<?php

  /**
   * Basic wireframe class implementation
   *
   * @package angie.frameworks.environment
   * @subpackage models
   */
  abstract class FwWireframe {
  	
  	/**
  	 * Request
  	 * 
  	 * @var Request
  	 */
  	protected $request;
    
    /**
     * Object set as primary object for this page
     *
     * @var ApplicationObject
     */
    protected $page_object;
    
    /**
     * Construct this wireframe
     * 
     * @param Request $request
     * @return FwWireframe
     */
    function __construct(Request $request) {
			$this->request = $request;    	
    } // __construct
    
    /**
     * Return initialization parameters
     * 
     * @param IUser $user
     * @return string
     */
    function getInitParams($user = null) {
      $result = array(
        'request' => array('url' => $this->request->getUrl()), 
      );
        
      if($this->request->isSubmitted()) {
        $result['request']['post'] = $this->request->post();
      } // if
      
      if($user instanceof User) {
        $result['logged_user'] = $user;
        $result['default_content_url'] = Router::assemble('homepage');
        $result['logout_url'] = Router::assemble('logout');
      } // if

      return $result;
    } // getInitParams
    
    /**
     * Return page object instance
     * 
     * @return ApplicationObject
     */
    function getPageObject() {
      return $this->page_object;
    } // getPageObject
    
    /**
     * Set page object
     * 
     * @param ApplicationObject $object
     * @param IUser $user
     * @return ApplicationObject
     * @throws InvalidInstanceError
     */
    function setPageObject($object, IUser $user) {
      if($object === null || $object instanceof ApplicationObject || $object instanceof AngieModule) {
        if($user instanceof IUser) {
          $this->page_object = $object;
          
          if($this->getPageTitle('') == '') {
            $this->setPageTitle($object->getName());
          } // if
          
          foreach($this as $k => $v) {
            if($this->$k instanceof IWireframeElement) {
              $this->$k->onPageObjectSet($object, $user);
            } // if
          } // foreach
          
          return $this->page_object;
        } else {
          throw new InvalidInstanceError('user', $user, 'IUser');
        } // if
      } else {
        throw new InvalidInstanceError('object', $object, array('ApplicationObject', 'AngieModule'));
      } // if
    } // setPageObject
    
    // ---------------------------------------------------
    //  Page Construction
    // ---------------------------------------------------
    
    /**
     * Page title
     *
     * @var string
     */
    private $page_title;
    
    /**
     * Array of of link tags
     *
     * @var array
     */
    private $links = array();
    
    /**
     * Array of meta tags
     *
     * @var array
     */
    private $meta = array('<meta name="robots" content="noindex, nofollow">');
    
    /**
     * Array of style elements
     *
     * @var array
     */
    private $style = array();
    
    /**
     * Array of scripts that will be included
     *
     * @var array
     */
    private $scripts = array();
    
    /**
     * Add link to this document
     *
     * @param string $href
     * @param string $rel
     * @param array $attributes
     */
    function addLink($href, $rel = null, $attributes = null) {
      if(!is_array($attributes)) {
        $attributes = array();
      } // if
      $attributes['href'] = $href;
      
      if($rel !== null) {
        $attributes['rel'] = $rel;
      } // if
      
      $this->links[] = open_html_tag('link', $attributes, true);
    } // addLink
    
    /**
     * Add meta tag to this page
     *
     * @param string $name
     * @param string $content
     * @param boolean $http_equiv
     */
    function addMeta($name, $content, $http_equiv = false) {
      $this->meta[] = open_html_tag('meta', array(
        ($http_equiv ? 'http-equiv' : 'name') => $name, 
        'content' => $content
      ), true);
    } // addMeta
    
    /**
     * Add script to this page
     *
     * @param string $content
     * @param boolean $inline
     */
    function addScript($content, $inline = true) {
      if($inline) {
        $this->scripts[] = open_html_tag('script', array('type' => 'text/javascript')) . $content . '</script>';
      } else {
        $this->scripts[] = open_html_tag('script', array('type' => 'text/javascript', 'src' => $content)) . '</script>';
      } // if
    } // addScript
    
    /**
     * Add stylesheet
     *
     * @param string $href
     * @param string $media
     */
    function addStylesheet($href, $media = 'screen') {
      $this->addLink($href, 'stylesheet', array('type' => 'text/css', 'media' => $media));
    } // addStylesheet
    
    /**
     * Return all head tags as one array
     *
     * @return array
     */
    function getAllHeadTags() {
      $result = array();
      if(count($this->links)) {
        $result = array_merge($result, $this->links);
      } // if
      if(count($this->meta)) {
        $result = array_merge($result, $this->meta);
      } // if
      if(count($this->style)) {
        $result = array_merge($result, $this->style);
      } // if
      if(count($this->scripts)) {
        $result = array_merge($result, $this->scripts);
      } // if
      return count($result) ? $result : null;
    } // getAllHeadTags
    
    /**
     * Get page_title
     *
     * @param string|null $default
     * @return string
     */
    function getPageTitle($default = null) {
      return $this->page_title ? $this->page_title : $default;
    } // getPageTitle
    
    /**
     * Set page_title value
     *
     * @param string $value
     */
    function setPageTitle($value) {
      $this->page_title = $value;
    } // setPageTitle
    
    /**
     * Return all page links
     *
     * @return array
     */
    function getLinks() {
      return $this->links;
    } // getLinks
    
    /**
     * Return all meta tags
     *
     * @return array
     */
    function getMeta() {
      return $this->meta;
    } // getMeta
    
    /**
     * Return all style tags
     *
     * @return array
     */
    function getStyle() {
      return $this->style;
    } // getStyle
    
    /**
     * Return all scripts
     *
     * @return array
     */
    function getScripts() {
      return $this->scripts;
    } // getScripts
    
    // ---------------------------------------------------
    //  Body
    // ---------------------------------------------------
    
    /**
     * Prepare and return body classes
     * 
     * @return array
     */
    function getBodyClasses() {
      $classes = array('wireframe');

      if (ColorSchemes::isBackgroundColorLight()) {
        $classes[] = 'light_background_color';
      } else {
        $classes[] = 'dark_background_color';
      } // if

      if (ColorSchemes::isOuterColorLight()) {
        $classes[] = 'light_outer_color';
      } else {
        $classes[] = 'dark_outer_color';
      } // if
      
      foreach($this as $k => $v) {
        if($this->$k instanceof IWireframeElement) {
          $this->$k->onBodyClasses($classes);
        } // if
      } // foreach
      
      return $classes;
    } // getBodyClasses
    
    // ---------------------------------------------------
    //  Wireframe elements
    // ---------------------------------------------------
        
    /**
     * Array of rss feeds
     *
     * @var array
     */
    private $rss_feeds = array();
    
    /**
     * Print button flag
     * 
     * Possible values:
     * 
     * - true - Show the button and use style switcher
     * - false - Don't show the button
     * - URL - Show the button but use special print page
     *
     * @var mixed
     */
    private $print_button = true;
    
    /**
     * Javascript variables
     *
     * @var array
     */
    private $javascript_variables = array();
    
    /**
     * Return list of registered RSS feeds
     *
     * @return array
     */
    function getRssFeeds() {
      return $this->rss_feeds;
    } // getRssFeeds
    
    /**
     * Add RSS feeds to page
     *
     * @param string $title
     * @param string $url
     * @param string $feed_type
     */
    function addRssFeed($title, $url, $feed_type = 'application/rss+xml') {
      $this->rss_feeds[] = array(
        'title' => $title,
        'url' => $url,
        'feed_type' => $feed_type,
      );
    } // addRssFeed
    
    /**
     * Return JavaScript variables array
     *
     * @return array
     */
    function getJavascriptVariables() {
      return $this->javascript_variables;
    } // getJavascriptVariables
    
    /**
     * Assign JavaScript variable
     *
     * @param mixed $var
     * @param mixed $value
     * @return mixed
     */
    function javascriptAssign($var, $value = null) {
      if(is_array($var)) {
        foreach($var as $k => $v) {
          $this->javascript_variables[$k] = $v;
        } // foreach
      } else {
        $this->javascript_variables[$var] = $value;
      } // if
    } // javascriptAssign
    
    /**
     * Return print button value
     *
     * @return boolean
     */
    function getPrintButton() {
      return $this->print_button;
    } // getPrintButton
    
    /**
     * Show print button on a given page
     */
    function showPrintButton() {
      $this->print_button = true;
    } // showPrintButton
    
    /**
     * Don't show print button on a given page
     */
    function hidePrintButton() {
      $this->print_button = false;
    } // hidePrintButton
    
    // ---------------------------------------------------
    //  Assets
    // ---------------------------------------------------
    
    /**
     * Return assets context
     * 
     * @return string
     */
    function getAssetsContext() {
      return null;
    } // getAssetsContext

    /**
     * Return collected javascript URL
     *
     * @param string $interface
     * @param string $device
     * @param string $context
     * @param bool $only_context
     * @return string
     */
    function getCollectedJavaScriptUrl($interface = AngieApplication::INTERFACE_DEFAULT, $device = AngieApplication::CLIENT_UNKNOWN, $context = null, $only_context = false) {
      return $this->getCollectedAssetsUrl('java_script', $interface, $device, $context, $only_context);
    } // getCollectedJavaScriptUrl

    /**
     * Return collected stylesheets URL
     *
     * @param string $interface
     * @param string $device
     * @param string $context
     * @param bool $only_context
     * @return string
     */
    function getCollectedStylesheetsUrl($interface = AngieApplication::INTERFACE_DEFAULT, $device = AngieApplication::CLIENT_UNKNOWN, $context = null, $only_context = false) {
      return $this->getCollectedAssetsUrl('stylesheets', $interface, $device, $context);
    } // getCollectedStylesheetsUrl
    
    /**
     * Return collected print stylesheets URL
     *
     * @return string
     */
    function getCollectedPrintStylesheetsUrl() {
      return $this->getCollectedStylesheetsUrl(AngieApplication::INTERFACE_PRINTER);
    } // getCollectedPrintStylesheetsUrl
    
    /**
     * Return collected print javascript URL
     *
     * @return string
     */
    function getCollectedPrintJavaScriptUrl() {
      return $this->getCollectedJavaScriptUrl(AngieApplication::INTERFACE_PRINTER);
    } // getCollectedPrintStylesheetsUrl
    
    /**
     * Return collected assets URL
     * 
     * @param string $type
     * @param string $interface
     * @param string $device
     * @param string $context
     * @param bool $only_context - if true, only files from context will be loaded
     * @return string
     */
    protected function getCollectedAssetsUrl($type, $interface = AngieApplication::INTERFACE_DEFAULT, $device = AngieApplication::CLIENT_UNKNOWN, $context = null, $only_context = false) {
      $current_scheme = ColorSchemes::getCurrentScheme();

      return AngieApplication::getProxyUrl("collect_$type", ENVIRONMENT_FRAMEWORK_INJECT_INTO, array(
        'interface' => $interface, 
        'device' => $device,
        'context' => $context,
      	'only_context' => $only_context,
        'background_color' => array_var($current_scheme, 'background_color', '#202329'),
        'outer_color' => array_var($current_scheme, 'outer_color', '#DEDEB6'),
        'inner_color' => array_var($current_scheme, 'inner_color', '#E9EADF'),
        'link_color' => array_var($current_scheme, 'link_color', '#DEDEB6'),
        'modules' => implode(',', AngieApplication::getModuleNamesForCollectors()),
      ));
    } // getCollectedAssetsUrl
    
  }