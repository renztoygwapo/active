<?php

  /**
   * user_link helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Display user name with a link to users profile
   * 
   * - user - User - We create link for this User
   * - short - boolean - Use short display name
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_user_link($params, &$smarty) {
    static $cache = array();

    $user = null;
    if (isset($params['user'])) {
      $user = $params['user'];
      unset($params['user']);
    } // if

    $short = false;
    if (isset($params['short'])) {
      $short = (boolean) $params['short'];
      unset($params['short']);
    } // if
    
    $default_label = array_var($params, 'default_label', lang('Unknown User'));
    
    // User instance
    if($user instanceof User) {
      if(!isset($cache[$short][$user->getId()])) {
        $params['href'] = $user->getViewUrl();
        if(isset($params['class'])) {
          $params['class'] .= ' user_link quick_view_item';
        } else {
          $params['class'] = 'user_link quick_view_item';
        } // if
        
        $cache[$short][$user->getId()] = HTML::openTag('a', $params) . clean($user->getDisplayName($short)) . '</a>';
      } // if
      
      return $cache[$short][$user->getId()];
      
    // AnonymousUser instance
    } elseif($user instanceof AnonymousUser && is_valid_email($user->getEmail())) {
      $params['href'] = 'mailto:' . $user->getEmail();
      if(isset($params['class'])) {
        $params['class'] .= ' anonymous_user_link';
      } else {
        $params['class'] = 'anonymous_user_link';
      } // if
      
      $name = $user->getDisplayName($short);
      if(empty($name)) {
        $name = $user->getFirstName(true);
      } // if
      
      return HTML::openTag('a', $params) . clean($name) . '</a>';
      
    // Unknown user
    } else {
      return '<span class="unknown_user_link unknown_object_link">' . clean($default_label) . '</span>';
    } // if
  } // smarty_function_user_link