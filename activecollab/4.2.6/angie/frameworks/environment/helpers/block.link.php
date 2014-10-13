<?php

  /**
   * Render button
   * 
   * Parameters:
   * 
   * - common anchor parameter
   * 
   * - method - if POST that this button will be send POST request. Method works 
   *   only if href parameter is present
   * - confirm - enforces confirmation dialog
   *   codes
   * - not_lang - Don't translate the content
   * - language - Instance of language that needs to be used in lang() call
   *
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
   */
  function smarty_block_link($params, $content, &$smarty, &$repeat) {
    if($repeat) {
      return;
    } // if

    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('button');
    } // if
    
    $mode = array_var($params, 'mode', null);
    $href = array_var($params, 'href', null);
    
    if (str_starts_with($href, '?')) {
    	$href = Router::assembleFromString($params['href']);
    	$params['href'] = $href;
    } // if
    
    $js_options = array();
    
    if(isset($params['success_message']) && $params['success_message']) {
      $js_options['success_message'] = $params['success_message'];
    } // if
    
    if(isset($params['success_event']) && $params['success_event']) {
      $js_options['success_event'] = $params['success_event'];
    } // if
    
    if(isset($params['error_event']) && $params['error_event']) {
      $js_options['error_event'] = $params['error_event'];
    } // if
    
    if(isset($params['title']) && $params['title']) {
      $js_options['title'] = lang($params['title']);
    } // if

    if(isset($params['flyout_width']) && $params['flyout_width']) {
      $js_options['width'] = $params['flyout_width'];
    } // if

    if (!$mode) {
      $confirmation = array_var($params, 'confirm', null, true);
      
	    if ((isset($params['method']) && $params['method'] == 'post') || $confirmation) {
	      $execution = $mode == 'post' ? 'App.postLink(' . var_export($href, true) . ')' : 'location.href = ' . var_export($href, true);
	      if($confirmation) {
	        $params['onclick'] = "if(confirm(" . var_export($confirmation, true) . ")) { $execution; } return false;";
	      } else {
	        $params['onclick'] = "$execution; return false;";
	      } // if
	    } // if
    } // if
    
		$link = HTML::openTag('a', $params) . (array_var($params, 'not_lang', false, true) ? $content : lang($content, null, true, array_var($params, 'language'))) . '</a>';
    
		switch($mode) {
    	case 'flyout':
      	return $link . '<script type="text/javascript">$("#' . $params['id'] . '").flyout(' . JSON::encode($js_options) . ')</script>';
      	
      case 'flyout_form':
        return $link . '<script type="text/javascript">$("#' . $params['id'] . '").flyoutForm(' . JSON::encode($js_options) . ')</script>';
        
      default:        	
      	return $link;
    } // switch
  } // smarty_block_link