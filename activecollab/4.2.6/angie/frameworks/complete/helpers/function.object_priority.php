<?php

  /**
   * object_priority helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Return object priority icon
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_object_priority($params, &$smarty) {
    $object = array_required_var($params, 'object', true, 'IComplete');
    $user = array_required_var($params, 'user', true, 'IUser');
    $show_normal = array_var($params, 'show_normal', false, true);
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    
    $priority = $object->getPriority();
    
    if($priority == 0 && !$show_normal) {
      return '';
    } // if
      
    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('object_priority');
    } // if
    
    if(isset($params['class'])) {
      $params['class'] .= " object_priority_widget";
    } else {
      $params['class'] = "object_priority_widget";
    } // if
    
    // verbose labels
    $verbose = array(
    	'-2'		=> lang('Lowest'),
    	'-1'		=> lang('Low'),
    	'0'			=> lang('Normal'),
    	'1'			=> lang('High'),
    	'2'			=> lang('Highest')
    );
    
    $priority_label = array_var($verbose, $priority, $verbose[0]);
    
    // Default interface
    if($interface == AngieApplication::INTERFACE_DEFAULT) {
      $options = array('value' => $priority );
      
      if($object->canEdit($user)) {
        $options['base_type_name'] = $object->getBaseTypeName();
        $options['edit_url'] = $object->getEditUrl();
        $options['success_event'] = $object->getUpdatedEventName();
      } // if

      AngieApplication::useWidget('object_priority', COMPLETE_FRAMEWORK);
      return HTML::openTag('span', $params) . '</span><script type="text/javascript">$("#' . $params['id'] . '").objectPriority(' . JSON::encode($options, $user) . ');</script>';
    } else if ($interface == AngieApplication::INTERFACE_PRINTER) {
    	
      switch($priority) {
        case -2:
          $image = 'priority-lowest.png';
          break;
        case -1:
          $image = 'priority-low.png';
          break;
        case 1:
          $image = 'priority-high.png';
          break;
        case 2:
          $image = 'priority-highest.png';
          break;
        default:
          $image = 'priority-normal.png';
      } // switch
      
      if($priority > 0) {
        $priority_label = '<strong>' . $priority_label . '</strong>';
      }//if
      
      if ($priority != 0) {
    		return '<img src="' . AngieApplication::getImageUrl('priority-widget/' . $image, COMPLETE_FRAMEWORK) . '" /> ' . $priority_label;
      } // if
      
      return null;
    	      
    // Movile devices
    } else {
      switch($priority) {
        case -2:
          $params['class'] .= 'lowest_priority';
          break;
        case -1:
          $params['class'] .= 'low_priority';
          break;
        case 1:
          $params['class'] .= 'high_priority';
          break;
        case 2:
          $params['class'] .= 'highest_priority';
          break;
        default:
          $params['class'] .= 'normal_priority';
      } // switch
      
      return HTML::openTag('span', $params) . clean($priority_label) . '</span>';
    } // if
  } // smarty_function_object_priority