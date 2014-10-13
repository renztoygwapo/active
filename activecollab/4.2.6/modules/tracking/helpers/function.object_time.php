<?php

  /**
   * object_time helper definition
   *
   * Reason why this helper needs to be here is because it is used across entire
   * system and other modules may required it without check if tracking module
   * is installed
   *
   * @package activeCollab.modules.resources
   * @subpackage helpers
   */

  /**
   * Render object time widget
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_object_time($params, &$smarty) {
    $object = array_required_var($params, 'object', true, 'ITracking');
    $user = array_required_var($params, 'user', true, 'IUser');

    $id = isset($params['id']) && $params['id'] ? $params['id'] : HTML::uniqueId('object_tracking_for_' . $object->getId());

    return HTML::openTag('span', array(
      'id' => $id,
      'class' => 'object_tracking',
      'data-estimated-time' => $object->tracking()->getEstimate() instanceof Estimate ? $object->tracking()->getEstimate()->getValue() : 0,
      'data-object-time' => $object->tracking()->sumTime($user),
      'data-object-expenses' => $object->tracking()->sumExpenses($user),
      'data-show-label' => array_var($params, 'show_label', true) ? 1 : 0,
      'data-interface' => array_var($params, 'interface', AngieApplication::getPreferedInterface(), true),
    )) . '<a href="' . clean($object->tracking()->getUrl()) . '"><img src="' . AngieApplication::getImageUrl('icons/12x12/object-time-inactive.png', TRACKING_MODULE, AngieApplication::INTERFACE_DEFAULT) . '"></a></span><script type="text/javascript">$("#' . $id . '").objectTime();</script>';
  } // smarty_function_object_time