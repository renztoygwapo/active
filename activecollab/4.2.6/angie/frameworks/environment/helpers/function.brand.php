<?php

  /**
   * Brand helper implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Return URL of a specific brand element
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_brand($params, &$smarty) {
	  switch($params['what']) {
	    case 'logo':
	      $size = isset($params['size']) && $params['size'] ? $params['size'] : '80x80';
	      return AngieApplication::getBrandImageUrl("logo.$size.png");
	    case 'favicon':
	      return AngieApplication::getBrandImageUrl('favicon.ico');
	  } // switch
  } // smarty_function_brand