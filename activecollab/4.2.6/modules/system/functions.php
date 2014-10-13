<?php

  /**
   * System module functions
   *
   * @package activeCollab.modules.system
   */

  /**
   * Return array of object types that can be completed
   *
   * Object that can be completed are counted and we use that data to see how
   * far project has gone (completed vs open tasks)
   *
   * @return array
   */
  function get_completable_project_object_types() {
    static $types = false;

    if($types === false) {
      $types = EventsManager::trigger('on_get_completable_project_object_types', array(), array());
      if(is_foreachable($types)) {
        sort($types);
      } // if
    } // if

    return $types;
  } // get_completable_project_object_types

  /**
   * Return day project object types
   *
   * Day project object types are important day events - like milestones or
   * events. They are shown in calendar without the day zoom
   *
   * @return array
   */
  function get_day_project_object_types() {
    static $types = false;

    if($types === false) {
      $types = EventsManager::trigger('on_get_day_project_object_types', array(), array());
      if(is_foreachable($types)) {
        sort($types);
      } // if
    } // if

    return $types;
  } // get_day_project_object_types

  /**
   * Return URL-s of project icons, large and small
   *
   * @param Project $project
   * @return array
   */
  function get_project_icon_urls($project) {
  	$project_id = $project instanceof Project ? $project->getId() : (integer) $project;
  	if($project_id) {
      return AngieApplication::cache()->getByObject(array('projects', $project_id), 'icons', function() use ($project, $project_id) {
        if($project instanceof Project) {
          $client_id = $project->getCompanyId();
        } else {
          $client_id = (integer) DB::executeFirstCell('SELECT company_id FROM ' . TABLE_PREFIX . 'projects WHERE id = ?', $project_id);
        } // if

        $icons = array();
        $sizes = array('40x40', '16x16');
        foreach($sizes as $size) {
          $supposed_project_icon_path = ENVIRONMENT_PATH . '/' . PUBLIC_FOLDER_NAME . "/projects_icons/$project_id.$size.png";
          $supposed_client_icon_path = ENVIRONMENT_PATH . '/' . PUBLIC_FOLDER_NAME . "/logos/$client_id.$size.png";

          if(is_file($supposed_project_icon_path)) {
            $icons[] = ROOT_URL."/projects_icons/$project_id.$size.png?updated_on=" . filemtime($supposed_project_icon_path);
          } elseif($client_id && is_file($supposed_client_icon_path)) {
            $icons[] = ROOT_URL . "/logos/$client_id.$size.png?updated_on=" . filemtime($supposed_client_icon_path);
          } else {
            $icons[] = ROOT_URL . "/projects_icons/default.$size.png";
          } // if
        } // foreach

        return $icons;
      });
  	} // if

  	return array('#', '#'); // no project?
  } // get_project_icon_urls

  // ---------------------------------------------------
  //  Custom
  // ---------------------------------------------------

  /**
   * Group objects by given date
   *
   * If $index_by_timestamp is set to TRUE, key will
   *
   * @param array $objects
   * @param User $user
   * @param string $getter
   * @param boolean $today_yesterday
   * @param boolean $index_by_timestamp
   * @param integer $offset
   * @return array
   */
  function group_by_date($objects, $user = null, $getter = 'getCreatedOn', $today_yesterday = true, $index_by_timestamp = false, $offset = null) {
    $result = array();
    if(is_foreachable($objects)) {
      AngieApplication::useHelper('date', GLOBALIZATION_FRAMEWORK, 'modifier');

      if($offset === null) {
        $offset = $user instanceof User ? get_user_gmt_offset($user) : 0;
      } // if

      foreach($objects as $object) {
        
        $gmt = is_object($object) ? $object->$getter() : $object[$getter];
        if(is_string($gmt) || is_integer($gmt)) {
          $gmt = new DateValue($gmt);
        } // if
        
        if($gmt instanceof DateValue) {
          $date = $gmt->advance($offset, false); // advance, but don't mutate
          
          if($index_by_timestamp) {
            $date_string = $gmt instanceof DateTimeValue ? $gmt->beginningOfDay()->getTimestamp() : $gmt->getTimestamp();
          } else {
            if($today_yesterday) {
              if($date->isToday($offset)) {
                $date_string = lang('Today');
              } elseif($date->isYesterday($offset)) {
                $date_string = lang('Yesterday');
              } else {
                $date_string = smarty_modifier_date($date, $offset);
              } // if
            } else {
              $date_string = smarty_modifier_date($date, $offset);
            } // if
          } // if

          if(!isset($result[$date_string])) {
            $result[$date_string] = array();
          } // if

          $result[$date_string][] = $object;
        } // if
      } // foreach
    } // if
    
    return $result;
  } // group_by_date

  /**
   * Group $objects by month they were created
   *
   * @param array $objects
   * @param string $getter
   * @return array
   */
  function group_by_month($objects, $getter = 'getCreatedOn') {
    $months = Globalization::getMonthNames();

    $result = array();
    if(is_foreachable($objects)) {
      foreach($objects as $object) {
        $date = $object->$getter();

        $month_name = $months[$date->getMonth()];

        if($date instanceof DateValue) {
          if(!isset($result[$date->getYear()])) {
            $result[$date->getYear()] = array();
          } // if

          if(!isset($result[$date->getYear()][$month_name])) {
            $result[$date->getYear()][$month_name] = array();
          } // if

          $result[$date->getYear()][$month_name][] = $object;
        } // if
      } // foreach
    } // if
    return $result;
  } // group_by_month
  
  /**
   * Group objects by the first letter
   *
   * @param DataObject[] $objects
   * @param string $getter
   * @return array
   */
  function group_by_first_letter($objects, $getter = 'getName') {
    $letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    
    $result = array();
    if(is_foreachable($objects)) {
      foreach($objects as $object) {
        $first_letter = substr_utf($object->$getter(), 0, 1);
        
        if(strpos($letters, $first_letter) === false) {
          $first_letter = '#';
        } else {
          $first_letter = strtoupper($first_letter);
        } // if
        
        if(!isset($result[$first_letter])) {
          $result[$first_letter] = array();
        } // if

        $result[$first_letter][] = $object;
      } // foreach
      
      ksort($result);
    } // if
    
    return $result;
  } // group_by_first_letter

  /**
   * Group objects by map array
   * 
   * @param $map
   * @param $objects
   * @param $getter
   */
  function group_by_mapped($map, $objects, $getter, $sort_by_name = true) {

    if($objects instanceof DBResult) {
      $objects = $objects->toArray();
    }//objects
    $user = Authentication::getLoggedUser();
    $results = array();

    if(is_foreachable($map) && $getter) {
      foreach ($map as $map_id => $map_name) {
        foreach($objects as $key => $object) {
          $refl = new ReflectionMethod(get_class($object), $getter);
          $object_value = $refl->getNumberOfParameters() > 0 ? $object->$getter($user) : $object->$getter();
          if($object_value == $map_id) {
            $results[$map_name][] = $object;
            unset($objects[$key]);
          }//if
        }//foreach
        if(!is_foreachable($results[$map_name])) {
          unset($results[$map_name]);
        } else {
          if($sort_by_name) usort($results[$map_name], "compare");
        }//if  
      }//foreach
      
    }//if
    
    if(is_foreachable($objects)) {
      if($sort_by_name) usort($objects,"compare");
      $results[' '] = $objects;
    }//if
      
    return $results;
    
  }//group_by_mapped
  
  /**
   * Compare two object by name
   * 
   * @param $a
   * @param $b
   */
  function compare($a, $b) {
    if ($a->getId() == $b->getId()) {
      return 0;
    } else {
      return strcmp(strtolower_utf($a->getName()), strtolower_utf($b->getName()));
    }//if
  }//compare
  
  
  /**
   * Render iCal data
   *
   * @param string $name iCalendar name
   * @param array $objects
   * @param boolean $include_project_name
   * @return void
   */
  function render_icalendar($name, $objects, $include_project_name = false) {
  	require_once ANGIE_PATH . '/classes/icalendar/iCalCreator.class.php';

    $calendar = new vcalendar();
    //$calendar->setProperty('VERSION', '1.0');
    $calendar->setProperty('X-WR-CALNAME', $name);
    $calendar->setProperty('METHOD', 'PUBLISH');

    foreach($objects as $object) {
	    if ($object instanceof Milestone) {
		    $start_on = $object->getStartOn();
		    $due_on   = $object->getDueOn();

		    $event = new vevent();

		    if ($start_on instanceof DateValue) {
			    $start_on_year = $start_on->getYear();
			    $start_on_month = $start_on->getMonth() < 10 ? '0' . $start_on->getMonth() : $start_on->getMonth();
			    $start_on_day = $start_on->getDay() < 10 ? '0' . $start_on->getDay() : $start_on->getDay();

			    $event->setProperty('dtstart', array($start_on_year, $start_on_month, $start_on_day), array('VALUE'=>'DATE'));
		    } else {
			    continue;
		    }//if

		    if ($due_on instanceof DateValue) {
			    $due_on->advance(24 * 60 * 60, true); // One day shift because iCal and Windows Calendar don't include last day

			    $due_on_year = $due_on->getYear();
			    $due_on_month = $due_on->getMonth() < 10 ? '0' . $due_on->getMonth() : $due_on->getMonth();
			    $due_on_day = $due_on->getDay() < 10 ? '0' . $due_on->getDay() : $due_on->getDay();

			    $event->setProperty('dtend', array($due_on_year, $due_on_month, $due_on_day), array('VALUE'=>'DATE'));
		    } else {
			    continue;
		    }//if

		    $summary = $include_project_name ? $object->getName() . ' | ' . $name : $object->getName();

		    $event->setProperty('dtstamp', date('Ymd'));
		    $event->setProperty('summary', $summary);

		    if($object->getBody()) {
			    $event->setProperty('description', $object->getBody() . "\n\n" . lang('Details: ') . $object->getViewUrl());
		    } else {
			    $event->setProperty('description', lang('Details') . ': ' . $object->getViewUrl());
		    } // if

		    switch($object->getPriority()) {
			    case PRIORITY_HIGHEST:
				    $event->setProperty('priority', 1);
				    break;
			    case PRIORITY_HIGH:
				    $event->setProperty('priority', 3);
				    break;
			    case PRIORITY_LOW:
				    $event->setProperty('priority', 7);
				    break;
			    case PRIORITY_LOWEST:
				    $event->setProperty('priority', 9);
				    break;
			    default:
				    $event->setProperty('priority', 5);
		    } // switch

		    $calendar->addComponent($event);
	    } else if ($object instanceof Task || $object instanceof TodoList) {
		    $start_on = $object->getDueOn();
		    $due_on   = $object->getDueOn();

		    $event = new vevent();

		    $summary = $include_project_name ? $object->getName() . ' | ' . $name : $object->getName();

		    $event->setProperty('summary', $summary);
		    $event->setProperty('description', $object->getName() . "\n\n" . lang('Details') . ': ' . $object->getViewUrl());

		    switch($object->getPriority()) {
			    case PRIORITY_HIGHEST:
				    $event->setProperty('priority', 1);
				    break;
			    case PRIORITY_HIGH:
				    $event->setProperty('priority', 3);
				    break;
			    case PRIORITY_LOW:
				    $event->setProperty('priority', 7);
				    break;
			    case PRIORITY_LOWEST:
				    $event->setProperty('priority', 9);
				    break;
			    default:
				    $event->setProperty('priority', 5);
		    } // switch

		    if($start_on instanceof DateValue) {

			    $start_on_year = $start_on->getYear();
			    $start_on_month = $start_on->getMonth() < 10 ? '0' . $start_on->getMonth() : $start_on->getMonth();
			    $start_on_day = $start_on->getDay() < 10 ? '0' . $start_on->getDay() : $start_on->getDay();

			    $event->setProperty('dtstart', array($start_on_year, $start_on_month, $start_on_day), array('VALUE'=>'DATE'));
		    } else {
			    continue;
		    }//if

		    if($due_on instanceof DateValue) {
			    $due_on->advance(24 * 60 * 60, true); // One day shift because iCal and Windows Calendar don't include last day

			    $due_on_year = $due_on->getYear();
			    $due_on_month = $due_on->getMonth() < 10 ? '0' . $due_on->getMonth() : $due_on->getMonth();
			    $due_on_day = $due_on->getDay() < 10 ? '0' . $due_on->getDay() : $due_on->getDay();

			    $event->setProperty('dtend', array($due_on_year, $due_on_month, $due_on_day), array('VALUE'=>'DATE'));
		    } else {
			    continue;
		    }//if

		    $event->setProperty('dtstamp', date('Ymd'));

		    $calendar->addComponent($event);
	    } else if ($object instanceof Subtask) {
		    $start_on = $object->getDueOn();
		    $due_on   = $object->getDueOn();

		    $event = new vevent();

		    $summary = $include_project_name ? $object->getBody() . ' | ' . $name : $object->getBody();

		    $event->setProperty('summary', $summary);
		    $event->setProperty('description', $object->getBody() . "\n\n" . lang('Details') . ': ' . $object->getViewUrl());

		    switch($object->getPriority()) {
			    case PRIORITY_HIGHEST:
				    $event->setProperty('priority', 1);
				    break;
			    case PRIORITY_HIGH:
				    $event->setProperty('priority', 3);
				    break;
			    case PRIORITY_LOW:
				    $event->setProperty('priority', 7);
				    break;
			    case PRIORITY_LOWEST:
				    $event->setProperty('priority', 9);
				    break;
			    default:
				    $event->setProperty('priority', 5);
		    } // switch

		    if($start_on instanceof DateValue) {

			    $start_on_year = $start_on->getYear();
			    $start_on_month = $start_on->getMonth() < 10 ? '0' . $start_on->getMonth() : $start_on->getMonth();
			    $start_on_day = $start_on->getDay() < 10 ? '0' . $start_on->getDay() : $start_on->getDay();

			    $event->setProperty('dtstart', array($start_on_year, $start_on_month, $start_on_day), array('VALUE'=>'DATE'));
		    } else {
			    continue;
		    }//if

		    if($due_on instanceof DateValue) {
			    $due_on->advance(24 * 60 * 60, true); // One day shift because iCal and Windows Calendar don't include last day

			    $due_on_year = $due_on->getYear();
			    $due_on_month = $due_on->getMonth() < 10 ? '0' . $due_on->getMonth() : $due_on->getMonth();
			    $due_on_day = $due_on->getDay() < 10 ? '0' . $due_on->getDay() : $due_on->getDay();

			    $event->setProperty('dtend', array($due_on_year, $due_on_month, $due_on_day), array('VALUE'=>'DATE'));
		    } else {
			    continue;
		    }//if

		    $event->setProperty('dtstamp', date('Ymd'));

		    $calendar->addComponent($event);
	    } // if
    } // foreach
		
    $cal = $calendar->createCalendar();
    
    header('Content-Type: text/calendar; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $name .'.ics"');
    header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Pragma: no-cache');

    print $cal;
    die();
  } // render_icalendar
  
  // ---------------------------------------------------
  //  Start page
  // ---------------------------------------------------
  
  /**
   * Returns logo of the company, if company has no logo, it returns the default logo
   *
   * @param integer $company_id
   * @param string $size
   * @return string
   */
  function get_company_logo_url($company_id, $size) {
  	$path = ENVIRONMENT_PATH . '/' . PUBLIC_FOLDER_NAME . "/logos/$company_id.$size.png";
  	
		if(is_file($path)) {
      return ROOT_URL . '/logos/' . $company_id . ".$size.png?updated_on=" . filemtime($path);
    } else {
      return ROOT_URL . "/logos/default.$size.png";
    } // if
  } // get_company_logo_url
  
  /**
   * Return project icon url if exists, if not, return the default one
   * 
   * @param integer $project_id
   * @param string $size
   * @return string
   */
  function get_project_icon_url($project_id, $size) {
  	$path = ENVIRONMENT_PATH . '/' . PUBLIC_FOLDER_NAME . "/projects_icons/$project_id.$size.png";
  	
		if(is_file($path)) {
      return ROOT_URL . '/projects_icons/' . $project_id . ".$size.png?updated_on=" . filemtime($path);
    } else {
      return ROOT_URL . "/projects_icons/default.$size.png";
    } // if
  } // get_project_icon_url
  
  /**
   * Prepare objects for timeline diagram
   * 
   * @param array $objects
   * @param User $user
   * @return array
   */
  function prepare_for_timeline_diagram(&$objects, &$user) {
		$prepared = array();
		foreach ($objects as $object) {
			$prepared[] = $object->describe(Authentication::getLoggedUser(), true);
		} // foreach
		return $prepared;
  } // prepare_for_timeline_diagram
  
  /**
   * Returns prepared milestone link based on $milestone value
   *
   * Reason why this function is global function is so it can be used as a
   * Smarty helper without the need to define it explicitely
   *
   * $milestone can be NULL
   *
   * @param Milestone $milestone
   * @return string
   */
  function milestone_link($milestone) {
    if($milestone instanceof Milestone) {
      $class = 'milestone';

      if($milestone->complete()->isCompleted()) {
        $class .= ' completed';
      } // if

      return '<a href="' . $milestone->getViewUrl() . '" class="' . $class . '">' . clean($milestone->getName()) . '</a>';
    } else {
      return lang('None');
    } // if
  } // milestone_link

  /**
   * Can user edit project object
   *
   * @param array $task
   * @param User $user
   * @param Project $project
   * @param Boolean $can_manage_tasks
   * @param array $all_assignees
   * @return bool
   */
  function can_edit_project_object($task, $user, $project, $can_manage_tasks, $all_assignees = null) {
    $visibility     = $task['visibility'];
    $created_by_id  = $task['created_by_id'];
    $assignee_id    = $task['assignee_id'];

    if (!($project instanceof Project)) {
      return false;
    } // if

    if ($user->isAdministrator() || $user->isProjectManager() || $project->isLeader($user)) {
      return true; // administrators and project managers have all permissions
    } // if

    if (($visibility < VISIBILITY_NORMAL) && !$user->canSeePrivate()) {
      return false;
    } // if

    if ($can_manage_tasks) {
      return true;
    } // if

    if ($created_by_id == $user->getId()) {
      return true;
    } // if

    if ($assignee_id == $user->getId() || in_array($user->getId(), $all_assignees)) {
      return true;
    } // if

    return false;
  } // can_edit_project_object

  /**
   * Can we trash this project_object
   *
   * @param Task $task
   * @param User $user
   * @param Project $project
   * @param boolean $can_manage_tasks
   * @param mixed $all_assignees
   * @return bool
   */
  function can_trash_project_object($task, $user, $project, $can_manage_tasks, $all_assignees = null) {
    $state = $task['state'];
    $visibility = $task['visibility'];
    $created_by_id  = $task['created_by_id'];

    if (!($project instanceof Project)) {
      return false;
    } // if

    if ($user->isAdministrator() || $user->isProjectManager() || $project->isLeader($user)) {
      return true; // administrators and project managers have all permissions
    } // if

    if ($state == STATE_TRASHED) {
      return false;
    } // if

    if (($visibility < VISIBILITY_NORMAL) && !$user->canSeePrivate()) {
      return false;
    } // if

    if ($can_manage_tasks) {
      return true;
    } // if

    if ($created_by_id == $user->getId()) {
      return true;
    } // if

    return false;
  } // can_trash_project_object
