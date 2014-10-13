<?php

  /**
   * object_subscription helper implementation
   * 
   * @package angie.frameworks.subscriptions
   * @subpackage helpers
   */

  /**
   * Render object subscriptions icon and link
   * 
   * Params:
   * 
   * - object - Object user needs to be subscribed to
   * - user - User who is subscribed, if not set logged user is used
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_object_subscription($params, &$smarty) {
    $object = array_required_var($params, 'object', true, 'ISubscriptions');
    $user = array_required_var($params, 'user', true, 'IUser');
    
    $id = isset($params['id']) && $params['id'] ? $params['id'] : HTML::uniqueId('object_subscription');
    
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    
    if ($interface == AngieApplication::INTERFACE_PRINTER) {
    	if ($object->subscriptions()->isSubscribed($user)) {
    		return '<img src="' . AngieApplication::getImageUrl('icons/12x12/object-subscription-active.png', SUBSCRIPTIONS_FRAMEWORK) . '" alt="subscribed" />';
    	} else {
    		return '<img src="' . AngieApplication::getImageUrl('icons/12x12/object-subscription-inactive.png', SUBSCRIPTIONS_FRAMEWORK) . '" alt="not subscribed" />';
    	} // if
    } else {
	    return open_html_tag('a', array(
	      'href' => '#', 
		    'id' => $id, 
		    'class' => 'object_subscription', 
	    	'is_on' => $object->subscriptions()->isSubscribed($user) ? 1 : 0, 
	    )) . '</a><script type="text/javascript">$("#' . $id . '").asyncToggler({ 
	      "content_when_on" : ' . var_export(open_html_tag('img', array('src' => array_var($params, 'subscribed_icon_url', AngieApplication::getImageUrl('icons/12x12/object-subscription-active.png', SUBSCRIPTIONS_FRAMEWORK))), true), true) . ', 
	      "content_when_off" : ' . var_export(open_html_tag('img', array('src' => array_var($params, 'unsubscribed_icon_url', AngieApplication::getImageUrl('icons/12x12/object-subscription-inactive.png', SUBSCRIPTIONS_FRAMEWORK))), true), true) . ', 
	      "title_when_on" : "' . lang('Click to Unsubscribe') . '", 
	      "title_when_off" : "' . lang('Click to Subscribe') . '" , 
	      "url_when_on" : ' . var_export($object->subscriptions()->getUnsubscribeUrl($user), true) . ' , 
	      "url_when_off" : ' . var_export($object->subscriptions()->getSubscribeUrl($user), true) . ', 
	      "success_event" : [ "' . $object->getUpdatedEventName() . '" ]
	    })</script>';
    } // if
  } // smarty_function_object_subscription