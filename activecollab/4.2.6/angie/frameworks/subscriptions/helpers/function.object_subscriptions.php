<?php

  /**
   * object_subscriptions helper
   *
   * @package activeCollab.modules.resources
   * @subpackage helpers
   */
  
  /**
   * Render object subscribers
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_object_subscriptions($params, &$smarty) {
    $object = array_required_var($params, 'object', true, 'ISubscriptions');
    $user = array_required_var($params, 'user', true, 'IUser');
    
    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('object_subscribers');
    } // if
    
    if(isset($params['class'])) {
      $params['class'] .= ' object_subscribers';
    } else {
      $params['class'] = 'object_subscribers';
    } // if
    
    $subscriber_names = $object->subscriptions()->getNames();
    
    switch(count($subscriber_names)) {
    	case 0:
    		$subscribers_string = lang('No subscribers');
    		break;
    	case 1:
    		$subscribers_string = lang(':subscriber is subscribed', array('subscriber' => $subscriber_names[0]));
    	  break;
    	default:
    	  AngieApplication::useHelper('join', ENVIRONMENT_FRAMEWORK);
        $subscribers_string = lang(':subscribers are subscribed', array('subscribers' => smarty_function_join(array('items' => $subscriber_names), $smarty)));
    } // if
    
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    
    // Regular interface
    if($interface == AngieApplication::INTERFACE_DEFAULT) {
      $subscription_event = $object->getUpdatedEventName();
      $unsubscription_event = $object->getUpdatedEventName();
      
      $result = HTML::openTag('div', $params) . '<span class="list_of_subscribers">' . $subscribers_string . '</span>';
      
      if($object->subscriptions()->canManage($user)) {
      	$result .= ' <span class="manage_subscribers">(<a href="' . $object->subscriptions()->getSubscriptionsUrl() . '" title="' . lang('Manage Subscriptions') . '">' . lang('Change') . '</a>)</span>';
      } // if
      
      $flyout_params = array(
      	'width'					=> 'narrow'
      );

      AngieApplication::useWidget('object_subscriptions', SUBSCRIPTIONS_FRAMEWORK);
      return $result . '</div><script type="text/javascript">$("#' . $params['id'] . ' span.manage_subscribers a:first").flyout(' . JSON::encode($flyout_params) . '); App.widgets.ObjectSubscriptions.init("' . $params['id'] . '", { "subscription_event" : "' . $subscription_event . '", "unsubscription_event" : "' . $unsubscription_event . '" });</script>';
    // Other devices
    } else if ($interface == AngieApplication::INTERFACE_PRINTER) {
			return HTML::openTag('div', $params) . $subscribers_string . '</div>';
    } else {
      return HTML::openTag('div', $params) . $subscribers_string . '</div><script type="text/javascript">$("#' . $params['id'] . '").objectSubscriptions();</script>';
    } // if
  } // smarty_function_object_subscriptions