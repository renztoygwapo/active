<?php

  /**
   * select_subscribers helper implementation
   *
   * @package angie.frameworks.subscriptions
   * @subpackage helpers
   */

  /**
   * Render select subscribers
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_subscribers($params, &$smarty) {
    $object = array_required_var($params, 'object', true, 'ISubscriptions');
    $user = array_required_var($params, 'user', false, 'IUser');
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    
    if (empty($params['id'])) {
      $params['id'] = HTML::uniqueId('select_subscribers');
    } // if
    
    if(isset($params['class'])) {
      $params['class'] .= ' select_subscribers';
    } else {
      $params['class'] = 'select_subscribers';
    } // if

    $exclude_ids = array_var($params, 'exclude', null, true);
    
    $params['inline'] = true;
    $params['users'] = $object->subscriptions()->getAvailableUsersForSelect($user, $exclude_ids);
    
    $user_ids = null;
    if (is_foreachable($params['users'])) {
    	foreach ($params['users'] as $company_users) {
    		if (is_foreachable($company_users)) {
    			foreach ($company_users as $user_id => $user_name) {
    				$user_ids[] = $user_id;
    			} // foreach
    		} // if
    	} // foreach
    } // if
    
    $options = array(
    	'can_see_private' => Users::whoCanSeePrivate($user_ids)
    );

    AngieApplication::useWidget('select_subscribers', SUBSCRIPTIONS_FRAMEWORK);
    AngieApplication::useHelper('select_users', AUTHENTICATION_FRAMEWORK);
    
    return smarty_function_select_users($params, $smarty) . '<script type="text/javascript">$("#' . $params['id'] . '").selectSubscribers(' . JSON::encode($options) . ')</script>';
  } // smarty_function_select_subscribers