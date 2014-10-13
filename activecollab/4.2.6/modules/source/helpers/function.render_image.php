<?php

/**
 * Render_image helper
 *
 * @package activeCollab.modules.source
 * @subpackage helpers
 */


/**
 * Render a image from resource
 *
 * @param array $params
 * @param Smarty $smarty
 * @return rendered image
 */
function smarty_function_render_image($params, &$smarty) {
    
  switch ($params['file_extension']) {
  	case 'gif':
  	  return imagegif($params['source']);
  	break;
  	
  	default:
  		;
  	break;
  }
  return 'null'; 
} // smarty_function_select_repository