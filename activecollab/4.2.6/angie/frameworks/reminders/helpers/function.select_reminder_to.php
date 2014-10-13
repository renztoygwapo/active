<?php

  /**
   * select_reminder_to helper implementation
   * 
   * @package angie.frameworks.reminders
   * @subpackage helpers
   */

  /**
   * Render select reminder to widget
   * 
   * This widget is aware of object type, so it offers options appropriate to 
   * that type (for example, it will not include option to send reminder to 
   * people involved in a discussion if parent object does not support comments)
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_reminder_to($params, &$smarty) {
  	static $ids = array();
  	
  	$user = array_var($params, 'user');
  	if(!($user instanceof User)) {
  		throw new InvalidInstanceError('user', $user, 'User');
  	} // if

  	$object = array_var($params, 'object');
  	if(!($object instanceof IReminders)) {
  		throw new InvalidInstanceError('object', $object, 'IReminders');
  	} // if

  	$interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
  	
  	$options = array(Reminder::REMIND_SELF => lang('Me (:name)', array('name' => $user->getDisplayName(true))));
  	
  	if($object instanceof IAssignees) {
  		$options[Reminder::REMIND_ASSIGNEES] = lang('Assignees');
  	} // if
  	
  	if($object instanceof ISubscriptions) {
  		$options[Reminder::REMIND_SUBSCRIBERS] = lang('Subscribers');
  	} // if
  	
  	if($object instanceof IComments) {
  	  $options[Reminder::REMIND_COMMENTERS] = $interface == AngieApplication::INTERFACE_DEFAULT ? lang('Everyone Involved in a Discussion') : lang('Everyone Involved');
  	} // if
  	
  	$options[Reminder::REMIND_SELECTED] = lang('Selected User');
  	
  	// Exclude specific options
  	if(isset($params['exclude']) && $params['exclude']) {
  		$exclude = (array) array_var($params, 'exclude');
  		
  		foreach($options as $k => $v) {
  		  if(in_array($k, $exclude)) {
  		  	unset($options[$k]);
  		  } // if
  		} // foreach
  	} // if
  	
  	$id = array_var($params, 'id');
  	if(empty($id)) {
  		$counter = 1;
  		
  		do {
  			$id = 'select_reminder_to_' . $counter++;
  		} while(in_array($id, $ids));
  	} // if
  	
  	$name = array_var($params, 'name');
  	$value = array_var($params, 'value');
  	if(empty($value)) {
  		$value = Reminder::REMIND_SELF;
  	} // if

    // Default, web interface
    if($interface == AngieApplication::INTERFACE_DEFAULT) {
    	$result = '<div class="select_reminder_to" id="' . $id . '">';
	  	foreach($options as $option_name => $option_text) {
	  	  $option_id = $id . '_' . $option_name;
	  	  
	  	  $selected = $value == $option_name;
	  	  
	  	  $result .= '<div class="select_reminder_to_option">' . radio_field($name, $selected, array(
	  	    'value' => $option_name,
	  	    'id' => $option_id,
	  	    'class' => 'select_reminder_to_option_radio auto', 
	  	  )) . ' ' . label_tag($option_text, $option_id, false, array('class' => 'auto'), '');
	  	  
	  	  if($option_name == Reminder::REMIND_SELECTED) {
	  	  	require_once AUTHENTICATION_FRAMEWORK_PATH . '/helpers/function.select_user.php';
	  	  	
	  	  	$result .= '<div class="select_reminder_to_option_content" style="display: ' . ($selected ? 'block' : 'none') . '">' . smarty_function_select_user(array(
	  	  	  'user' => $user, 
	  	  	  'object' => $object->reminders()->getUsersContext(), 
	  	  	  'id' => $id . '_select_users', 
	  	  	  'value' => array_var($params, 'selected_users'),
	  	  	  'name' => 'reminder[selected_user_id]',
            'filter_only_users_with_private_visibility' => ($object instanceof IVisibility && $object->visibility()->isPrivate())
	  	  	), $smarty) . '</div>';
	  	  } // if
	  	  
	  	  $result .= '</div>';
	  	} // foreach

      AngieApplication::useWidget('select_reminder_to', REMINDERS_FRAMEWORK);
	  	
    // Phone interface
  	} elseif($interface == AngieApplication::INTERFACE_PHONE) {
  		$result = '<div class="select_reminder_to" id="' . $id . '">
  			<fieldset data-role="controlgroup" data-theme="j">';
  		
	  	foreach($options as $option_name => $option_text) {
	  	  $option_id = $id . '_' . $option_name;
	  	  
	  	  $selected = $value == $option_name;
	  	  
	  	  $result .= radio_field($name, $selected, array(
	  	    'value' => $option_name,
	  	    'id' => $option_id,
	  	    'class' => 'select_reminder_to_option_radio auto', 
	  	  )) . ' ' . label_tag($option_text, $option_id, false, array('class' => 'auto'), '');
	  	} // foreach
	  	
	  	$result .= '</fieldset>';
  	} // if


  	return $result . '</div><script type="text/javascript">App.widgets.SelectReminderTo.init("' . $id . '");</script>';
  } // smarty_function_select_reminder_to