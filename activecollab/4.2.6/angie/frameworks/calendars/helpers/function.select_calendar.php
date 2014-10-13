<?php

  /**
   * select_calendar helper
   *
   * @package angie.frameworks.calendars
   * @subpackage helpers
   */

  /**
   * Render select calendar picker
   *
   * @param $params
   * @param $smarty
   * @return string
   */
  function smarty_function_select_calendar($params, &$smarty) {
    $name = array_required_var($params, 'name', true);
    $user = array_required_var($params, 'user', true, 'User');
    $value = array_var($params, 'value', true, null);
	  $label = array_var($params, 'label', null, true);

	  $groups = Calendars::findGroupedByUserId($user, !$user->isAdministrator());

	  $options = array();
	  if (is_foreachable($groups)) {
		  foreach($groups as $user_id => $calendars) {
			  $pooled_user = DataObjectPool::get('User', $user_id);
			  if ($pooled_user instanceof User) {
				  $tmp_options = array();
				  if (is_foreachable($calendars)) {
					  foreach ($calendars as $calendar) {
						  $calendar_name = array_var($calendar, 'name', null);
						  $calendar_id = array_var($calendar, 'id', null);
						  if ($calendar_name && $calendar_id) {
							  $tmp_options[] = HTML::optionForSelect($calendar_name, $calendar_id, $calendar_id == $value);
						  } // if
					  } // foreach
				  } // if

				  $opt_attributes = $pooled_user->getId() == $user->getId() ? array('class' => 'mine') : null;
				  $option_name = $pooled_user->getId() == $user->getId() ? lang('My Calendars') : $pooled_user->getName();
				  $options[] = HTML::optionGroup($option_name, $tmp_options, $opt_attributes);
			  } // if
		  } // foreach

	  } // if

	  if(isset($params['class'])) {
		  $params['class'] .= ' select_calendar';
	  } else {
		  $params['class'] = 'select_calendar';
	  } // if

	  $result = HTML::openTag('div', $params);

	  if($label) {
		  $result .= HTML::label($label, null, array_var($params, 'required'), array('class' => 'main_label'));
	  } // if

	  $result .= HTML::select($name, $options, $params);

	  if (Calendars::canAdd($user)) {
		  $calendar_add_url = Router::assemble('calendars_add');
		  $result .= '<div class="add_button_wrapper"><a href="' . $calendar_add_url . '" class="add_new_calendar button_add">' . lang('Add Calendar') . '</a></div>';
	  } // if

	  return $result.'</div>';
  } // smarty_function_select_calendar