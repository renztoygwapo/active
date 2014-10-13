<?php

  /**
   * Framework level backend web interface response
   * 
   * @package angie.frameworks.environment
   * @subpackage models
   */
  abstract class FwBackendWebInterfaceResponse extends WebInterfaceResponse {
    
    /**
     * Redirect to given URL
     *
     * When $refresh_page is set to TRUE, standard HTTP redeireection is initiated
     * 
     * @param string $url
     * @param boolean $refresh_page
     */
    function redirectToUrl($url, $refresh_page = false) {
      if($this->request->isInlineCall() && empty($refresh_page)) {
        $interface = AngieApplication::getPreferedInterface();

        if($interface != AngieApplication::INTERFACE_DEFAULT) {
          parent::redirectToUrl($url);
        } else {
          $this->assign('redirect_to_url', $url);
          $this->respondWithFragment('_backend_redirect', null, ENVIRONMENT_FRAMEWORK);
        } // if
      } else {
        parent::redirectToUrl($url);
      } // if
    } // redirectToUrl


    /**
     * Require user authentication in order to display the requested page
     * 
     * @param mixed $additional
     */
    function requireAuthentication($additional = null) {
      $interface = AngieApplication::getPreferedInterface();
      
      if($interface != AngieApplication::INTERFACE_DEFAULT) {
      	$params = array();
      	if(!in_array($this->request->getMatchedRoute(), array('login', 'logout', 'forgot_password', 'reset_password'))) {
	        $params['re_route'] = $this->request->getMatchedRoute();
          foreach($this->request->getUrlParams() as $k => $v) {
            if(($k == 'module') || ($k == 'controller') || ($k == 'action') || ($k == 'path_info')) {
              continue;
            } // if
            
            $params["re_$k"] = $v;
          } // foreach
	      } // if
      	
      	header("Location:" . Router::assemble('login', $params));
      } else {
	      if($this->request->isAsyncCall()) {
	        if($this->request->getMatchedRoute() == 'wireframe_update') {
	          $this->respondWithStatus(BaseHttpResponse::UNAUTHORIZED);
	        } else {
	          $this->tearDownBackend();
	        } // if
	      } else {
	        $this->buildLogin();
	      } // if
	      $this->sendContent('--Printing login form--');
      } // if
      
	    die();
    } // requireAuthentication
    
    /**
     * Initialize backend so login form is displayed
     */
    protected function buildLogin() {
      $this->initializeBackend(array(
        'logged_user' => null, 
      ));
    } // buildLogin
    
    /**
     * Initialize backend so actual backend page is displayed
     */
    protected function buildBackend() {
      $this->initializeBackend(array(
        'logged_user' => Authentication::getLoggedUser(), 
      ));
    } // buildBackend
    
    /**
     * Tear down backend in order to display login form
     * 
     * This situation happens if user gets logged out, but still has loaded 
     * backend interface
     */
    protected function tearDownBackend() {
      
    } // tearDownBackend
    
    /**
     * Initialize backend with given data
     * 
     * @param array $data
     */
    protected function initializeBackend($data) {
      $this->smarty->assign($data);
      $this->sendContent($this->smarty->fetch(AngieApplication::getLayoutPath('backend', ENVIRONMENT_FRAMEWORK_INJECT_INTO)), true);
    } // initializeBackend
  
  }