<?php

  /**
   * Touch icons helper implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Return URL of a specific touch icon
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_touch($params, &$smarty) {
  	$icon_type = ActiveCollab::getBrandingRemoved() ? 'generic' : 'default';
  	
	  switch($params['what']) {
	    case 'icon':
	      $size = isset($params['size']) && $params['size'] ? '-' . $params['size'] : '';
	      return AngieApplication::getImageUrl("icons/$icon_type/apple-touch-icon$size-precomposed.png", ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface());
	    case 'startup':
	      return AngieApplication::getImageUrl("icons/$icon_type/startup.png", ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface());
	    case 'login-logo':
	      return AngieApplication::getBrandImageUrl('login-page-logo.png');
	  } // switch
  } // smarty_function_touch