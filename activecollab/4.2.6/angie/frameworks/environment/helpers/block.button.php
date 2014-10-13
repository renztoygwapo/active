<?php

  /**
   * Render button
   * 
   * Parameters:
   * 
   * - common button parameter
   * - href - when button is clicked this link is opened
   * - method - if POST that this button will be send POST request. Method works 
   *   only if href parameter is present
   * - confirm - enforces confirmation dialog
   * - not_lang - if true content will not be matched agains registered language 
   *   codes
   *
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
   */
  function smarty_block_button($params, $content, &$smarty, &$repeat) {
    if($repeat) {
      return null;
    } // if
    
    if(empty($params['type'])) {
      $params['type'] = 'button';
    } // if
    
    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('button');
    } // if
    
    $href = array_var($params, 'href', null, true);
    $mode = array_var($params, 'mode', null, true);
    
    $async = (boolean) array_var($params, 'async', true, true);
    $method = array_var($params, 'method', 'post', true);
    $confirmation = array_var($params, 'confirm', null, true);
    $success_message = array_var($params, 'success_message', null, true);
    $success_event = array_var($params, 'success_event', null, true);
    $error_event = array_var($params, 'error_event', null, true);
    $flyout_width = array_var($params, 'flyout_width', null, true);
    $flyout_title = array_var($params, 'flyout_title', null, true);

    $button = HTML::openTag('button', $params) . '<span><span>' . clean(array_var($params, 'lang', true, true) ? lang($content) : $content) . '</span></span></button>';
    
    // If we have href, we'll make a request on click
    if($href) {
      $js_options = array(
        'success_message' => $success_message, 
        'success_event' => $success_event, 
        'error_event' => $error_event, 
      );
      
      if($href) {
        $js_options['href'] = $href;
      } // if

      if($flyout_width) {
        $js_options['width'] = $flyout_width;
      } // if
      if($flyout_title) {
        $js_options['title'] = $flyout_title;
      } // if

      switch($mode) {
        case 'flyout':
          return $button . '<script type="text/javascript">$("#' . $params['id'] . '").flyout(' . JSON::encode($js_options) . ')</script>';
        case 'flyout_form':
          return $button . '<script type="text/javascript">$("#' . $params['id'] . '").flyoutForm(' . JSON::encode($js_options) . ')</script>';
        case 'new_window':
          return $button . '<script type="text/javascript">$("#' . $params['id'] . '").click(function() { window.open(' . var_export($href, true) . '); return false; });</script>';
        default:
          $js_options['async'] = $async;
          $js_options['method'] = $method;
          $js_options['confirmation'] = $confirmation;
          
          return $button . '<script type="text/javascript">$("#' . $params['id'] . '").asyncButton(' . JSON::encode($js_options) . ')</script>';
      } // switch
      
    // Plain button
    } else {
      return $button;
    } // if
  } // smarty_block_button