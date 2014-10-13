<?php

  /**
   * object_subtasks helper implementation
   *
   * @package angie.frameworks.subtasks
   * @subpackage helpers
   */
  
  /**
   * Render object tasks section
   * 
   * Parameters:
   * 
   * - object - Selected project object
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_object_subtasks($params, &$smarty) {
    $object = array_required_var($params, 'object', true, 'ISubtasks');
    $described_object = array_var($params, 'described_object', false);
    $user = array_required_var($params, 'user', true, 'User');
    
    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('object_subtasks');
    } // if
    
    if(isset($params['class'])) {
      $params['class'] .= ' object_subtasks';
    } else {
      $params['class'] = 'object_subtasks';
    } // if
    
    AngieApplication::useHelper('date', GLOBALIZATION_FRAMEWORK, 'modifier');
    
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    
    // Default interface
    if($interface == AngieApplication::INTERFACE_DEFAULT) {
      
      $can_add = $object->subtasks()->canAdd($user);
      $can_manage = $object->subtasks()->canManage($user);
      $can_edit = $object->canEdit($user);
      
      $options = array(
      	'subtasks' => array(),
        'can_add' => $can_add,
        'can_edit' => $can_edit,
        'can_manage' => $can_manage
     	);
     	
			$options['subtasks'] = Subtasks::findForWidget($object, $user);
			$options['object'] = array(
      	'id' => $object->getId(),
      	'class' => get_class($object),
      	'is_completed' => $object instanceof IComplete && $object->getCompletedOn() instanceof DateValue,
      	'event_names' => array('updated' => $object->getUpdatedEventName(), 'deleted' => $object->getDeletedEventName())
			);

      $current_request = $smarty->getVariable('request')->value;
      $options['event_scope'] = $current_request->getEventScope();

      // Open wrapper
      $result = HTML::openTag('div', $params);
      
      // Prepare table
      $result .= '<table class="subtasks_table view_mode" cellspacing="0"> 
        <tbody> 
          <tr class="empty_row"> 
            <td class="task_reorder"></td> 
            <td class="task_meta"></td> 
            <td class="task_content">' . lang('No subtasks yet') . '</td> 
            <td class="task_options"></td>
          </tr>';
       
      if($can_add || $can_manage) {
        $view = $smarty->createTemplate(get_view_path('_object_subtask_form_row', null, SUBTASKS_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT));
        $view->assign(array(
        	'subtask_parent' => $object, 
          'subtask' => $object->subtasks()->newSubtask(), 
          'subtasks_id' => $params['id'], 
          'user' => $user,
          'logged_user' => Authentication::getLoggedUser(),
          'subtask_data' => array(
            'body' => null,
            'assignee_id' => null,
            'priority' => null,
            'label_id' => null,
            'due_on' => null
          )
        ));
          
        $result .= $view->fetch();
          
        $result .= '<tr class="add_and_manage">
          <td class="task_reorder"></td>
          <td class="task_meta"></td>
          <td class="task_content">';
          
        if($can_add) {
          $result .= '<a href="' . $object->subtasks()->getAddUrl() . '" class="add_subtask">' . lang('New Subtask') . '</a>';
        } // if
          
        if($can_manage) {
          $result .= '<a href="' . $object->subtasks()->getReorderUrl() . '" class="reorder_subtasks">' . lang('Reorder') . '</a>';
        } // if
          
        $result .= '</td>
          <td class="task_options"></td>
        </tr>';
      } // if

      $result .= '<tr class="show_old_completed" style="display: none"> 
            <td class="task_reorder"></td> 
            <td class="task_meta"></td> 
            <td class="task_content"><a href="#">' . lang('Show All Completed') . '</td> 
            <td class="task_options"></td> 
          </tr>
        </tbody>
      </table>';

      AngieApplication::useWidget('object_subtasks', SUBTASKS_FRAMEWORK);
      return "$result</div>" . '<script type="text/javascript">$("#' . $params['id'] . '").objectSubtasks("init", ' . JSON::encode($options, $user) . ')</script>';
      
    // Phone interface
    } elseif($interface == AngieApplication::INTERFACE_PHONE) {
    	$options = array('subtasks' => array());
    	
      if($object->subtasks()->get($user)) {
        foreach($object->subtasks()->get($user, 'open') as $subtask) {
	        $options['subtasks'][] = $subtask->describe($user, array(
	          'detailed' => true, 
	        ));
	      } // if
      } // if
      
      // Open wrapper
      $result = HTML::openTag('div', $params);
      
      AngieApplication::useHelper('image_url', ENVIRONMENT_FRAMEWORK);
    	
    	$result .= '<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
      	<li data-role="list-divider"><img src="' . smarty_function_image_url(array(
					'name' => 'icons/listviews/navigate.png',
					'module' => SUBTASKS_FRAMEWORK,
					'interface' => AngieApplication::INTERFACE_PHONE
				), $smarty) . '" class="divider_icon" alt="">' . lang('Subtasks') . '</li>';
      
      if(is_foreachable($options['subtasks'])) {
	      foreach($options['subtasks'] as $subtask) {
	      	$formatted_subtask = '';
	      	
	      	$description = '<p class="ui-li-desc">';
	      	if($subtask['assignee'] != null) {
	      		$description .= clean($subtask['assignee']['name']);
	      		$description .= $subtask['due_on'] instanceof DateValue ? ', ' : '';
	      	} // if
	      	
	      	if($subtask['due_on'] instanceof DateValue) {
	      		$description .= smarty_modifier_date($subtask['due_on']);
	      	} // if
	      	$description .= '</p>';
	      	
	      	$formatted_subtask .= '<li><a href="' . $subtask['permalink'] . '">';
	        if(is_null($subtask['assignee']) && !($subtask['due_on'] instanceof DateValue)) {
	        	$formatted_subtask .= clean($subtask['name']);
	        } else {
	        	$formatted_subtask .= '<h3 class="ui-li-heading">' . clean($subtask['name']) . '</h3>' . $description;
	        } // if
	        $formatted_subtask .= '</a></li>';
	        
	        $result .= $formatted_subtask;
	      } // foreach
      } else {
      	$result .= '<li>'.lang('No active subtasks').'</li>';
      } // if
      
      $result .= '</ul>';
      
      $result .= '<div class="archived_objects">
				<a href="' . $object->subtasks()->getArchiveUrl() . '" data-role="button" data-theme="k">' . lang('Completed Subtasks') . '</a>
			</div>';
      
      return "$result</div>";
      
    // Printer interface
    } if ($interface == AngieApplication::INTERFACE_PRINTER) {
    	
    	$subtasks = $object->subtasks()->get($user);
    	if (!is_foreachable($subtasks)) {
    		return '<p class="empty_page">' . lang('No subtasks yet') . '</p>';
    	} // if
    	    	
      // Open wrapper
      $result = HTML::openTag('div', $params);
      
      $result .= '<h2>' . lang('Subtasks') . '</h2>';
      // Prepare table
      $result .= '<table class="subtasks_table view_mode common" cellspacing="0"> 
        <tbody>';
      
      $open_subtasks = '';
      $completed_subtasks = '';

      foreach ($subtasks as $subtask) {
      	if ($subtask->getCompletedOn() instanceof DateValue) {
      		$destination = &$completed_subtasks;
      		$checkbox = '<input type="checkbox" checked="checked" />';
      	} else {
      		$destination = &$open_subtasks;
					$checkbox = '<input type="checkbox" />';
      	} // if
      	
      	$destination .= '<tr>';      	
      	$destination .= 	'<td class="label">';
	      if ($subtask->label()->get() instanceof Label) {
	      	$destination .= clean($subtask->label()->get()->getName());
	      } // if
      	$destination .= 	'</td>';
      	
      	$destination .= 	'<td class="checkbox">' . $checkbox . '</td>';
      	
      	$destination .= 	'<td>';
      	$assignee = $subtask->assignees()->getAssignee();
      	if ($assignee instanceof IUser) {
      		$destination .= 	'<span class="subtask_assignee">' . clean($assignee->getName(true)) . '</span>';
      	} // if
      	
      	$destination .= 		'<span class="task_content_text">' . clean($subtask->getName()) . '</span>';
      	$destination .=		'</td><td class="due_on">';
      	
      	if ($subtask->getDueOn() instanceof DateValue) {
		      $destination .= '<span class="subtask_due_on">' . smarty_modifier_date($subtask->getDueOn(), 0) . '</span>';
      	} // if
      	$destination .= '</td></tr>';
      } // foreach
      
      $result .= ($open_subtasks . $completed_subtasks);

      $result .= '<tr class="show_old_completed" style="display: none">
      			<td class="label"></td> 
            <td class="task_reorder"></td> 
            <td class="task_meta"></td> 
            <td class="task_content"><a href="#">' . lang('Show All Completed') . '</td> 
            <td class="task_options"></td> 
          </tr>
        </tbody>
      </table>';
      
      return "$result</div>";
    	
   	// Other interfaces
    } else {
      $options = array('subtasks' => array());
      
      if($object->subtasks()->get($user)) {
        foreach($object->subtasks()->get($user) as $subtask) {
          $options['subtasks'][] = $subtask->describe($user, array(
            'detailed' => true, 
          ));
        } // if
      } // if
      
      return HTML::openTag('div', $params) . '</div><script type="text/javascript">$("#' . $params['id'] . '").objectSubtasks(' . JSON::encode($options, $user) . ')</script>';
    } // if
  } // smarty_function_object_subtasks