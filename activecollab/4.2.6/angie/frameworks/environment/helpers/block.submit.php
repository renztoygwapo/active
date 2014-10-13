<?php

  /**
   * Render submit button
   *
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
   */
  function smarty_block_submit($params, $content, &$smarty, &$repeat) {
    if($repeat) {
      return;
    } // if
    
    $params['type'] = 'submit';
    
    if(empty($params['accesskey'])) {
      $params['accesskey'] = 's';
    } // if
    
    if(empty($params['class'])) {
      $params['class'] = 'default';
    } else {
      $params['class'] .= ' default';
    } // if
    
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    
    if($interface == AngieApplication::INTERFACE_PHONE || $interface == AngieApplication::INTERFACE_TABLET) {
    	$theme = array_var($params, 'theme', null, true);
    	
    	if(is_null($theme)) {
        $params['data-theme'] = 'i';
        $params['data-icon'] = 'check';
      } else {
        $params['data-theme'] = $theme;
      } // if
    } // if
    
    $caption = array_var($params, 'lang', true, true) ? lang($content) : $content;
    
    if($params['accesskey']) {
      $first = null;
      $first_pos = null;
      
      $to_highlight = array(strtolower($params['accesskey']), strtoupper($params['accesskey']));
      foreach($to_highlight as $accesskey_to_highlight) {
        if(($pos = strpos($caption, $accesskey_to_highlight)) === false) {
          continue;
        } // if
        
        if(($first_pos === null) || ($pos < $first_pos)) {
          $first = $accesskey_to_highlight;
          $first_pos = $pos;
        } // if
      } // foreach
      
      if($first !== null) {
        $caption = str_replace_first($first, "<u>$first</u>", $caption);
      } // if
    } // if
    
    return HTML::openTag('button', $params) . $caption . '</button>';
  } // smarty_block_submit