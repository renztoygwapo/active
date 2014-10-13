<?php

  /**
   * on_mass_edit event handler implementation
   * 
   * @package activeCollab.modules.discussions
   * @subpackage handlers
   */

  /**
   * Handle on mass edit event
   *
   * @param mixed $objects
   * @param User $logged_user
   * @param array $actions
   * @param array $data
   * @param array $response
   * @param Smarty $smarty
   */
  function discussions_handle_on_mass_edit(&$objects, &$logged_user=null, $actions=null, $data=null, &$response=null, &$smarty) {
    if ($actions === null && $data === null) {
      if ($objects instanceof Discussion) {
        $response['change_discussion_pinned_state'] = array(
          'position' => 0,
          'title'    => lang('Pin / Unpin Discussion'),
          'controls' => '<select name="pin_unpin_discussion"><option value="0">' . lang('Pin') . '</option><option value="1">' . lang('Unpin') . '</option></select>'
        );
      } // if
    } else {
      // find actions which this handler needs to perfroem
      $perform_actions = array_intersect($actions, array('change_discussion_pinned_state'));
            
      if (is_foreachable($perform_actions)) {
        foreach ($objects as $object) {
        	foreach ($perform_actions as $action) {
        	  switch ($action) {
        	    case 'change_discussion_pinned_state':        	      
              if ($object->canPinUnpin($logged_user)) {
              	$pin = (integer) array_var($data, 'pin_unpin_discussion');
                if (!$pin) {
                 $object->setIsPinned(true);
                } else {
                 $object->setIsPinned(false);
                } // if
              } // if
      	      break;
        	  } // switch
        	} // foreach
        	
        	// if object is modified, save it and put it in response
        	if ($object->isModified()) {
						$object->save();
        		$response[$object->getId()] = get_class($object);
        	} // if
        	
        } // foreach
      } // if
    }
  } // discussions_handle_on_admin_sections