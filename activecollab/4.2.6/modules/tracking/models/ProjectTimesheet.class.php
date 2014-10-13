<?php

  /**
   * Project timesheet implementation
   *
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  final class ProjectTimesheet {
    
    /**
     * User who is accessing the timesheet
     *
     * @var User
     */
    private $logged_user;
    
    /**
     * Selected project
     *
     * @var Project
     */
    private $project;
    
    /**
     * Construct project timesheet instance
     *
     * @param Project $project
     * @param User $user
     */
    function __construct(User $user, Project $project) {
      $this->logged_user = $user;
      $this->project = $project;
    } // __construct
    
    /**
     * Start date for the table
     *
     * @var DateValue
     */
    private $start_date;
    
    /**
     * End date for the table
     *
     * @var DateValue
     */
    private $end_date;
    
    /**
     * Array of days that need to be rendered
     *
     * @var unknown_type
     */
    private $days = array();
    
    /**
     * List of timesheet users
     * 
     * All project users + everyone else for whom we have time tracked, even if 
     * they are no longer involved with the project or they are deleted
     *
     * @var array
     */
    private $users;
    
    /**
     * Render timesheet table
     *
     * @return string
     */
    function render() {
      $this->loadRange();
      $this->loadUsers();

      $result = '<div class="timesheet" day_details_url="' . Router::assemble('project_tracking_timesheet_day', array('project_slug' => $this->project->getSlug())) . '">';
      $result.=   '<div class="timesheet_canvas"></div>';
      $result.=   '<div class="timesheet_users">' . $this->renderUserList() . '</div>';
      $result.=   '<div class="timesheet_date_scale">' . $this->renderDateScale() . '</div>';
      $result.=   '<div class="timesheet_records_wrapper">' . $this->renderTimesheetEntries() . '</div>';
      $result.=   '<div class="timesheet_vertical_scrollbar scrollbar"><div class="scrollbar_handle"></div></div>';
      $result.=   '<div class="timesheet_horizontal_scrollbar scrollbar"><div class="scrollbar_handle"></div></div>';
      $result.= '</div>';
      
      return $result;
    } // render
    
    /**
     * Render head rows
     * 
     * @return string
     */
    private function renderDateScale() {
      $month_names = Globalization::getShortMonthNames();
      
      $structure = array();
      
      foreach($this->days as $day) {
        if(!isset($structure[$day->getYear()])) {
          $structure[$day->getYear()] = array();
        } // if
        
        if(!isset($structure[$day->getYear()][$day->getMonth()])) {
          $structure[$day->getYear()][$day->getMonth()] = array();
        } // if
        
        $structure[$day->getYear()][$day->getMonth()][] = $day->getDay();
      } // foreach
      
      // First row, month names
      $result = '<table cellspacing="0">';
      $result.= '<tr class="months">';
      foreach($structure as $year => $months) {
        foreach($months as $month => $days) {
          $result .= '<td colspan="' . count($days) . '" month="' . $year . '-' . ($month < 10 ? '0' . $month : $month) . '"><span>' . $month_names[$month] . ' ' . $year . '</span></td>';
        } // foreach
      } // foreach
      $result .= '</tr>';
      
      // Second row, days
      $result .= '<tr class="days">';
      foreach($structure as $year => $months) {
        foreach($months as $month => $days) {
          foreach($days as $day) {
            $result .= '<td day="' . $year . '-' . ($month < 10 ? '0' . $month : $month) . '-' . ($day < 10 ? '0' . $day : $day) . '"><span>' . $day . '</span></td>';
          } // foreach
        } // foreach
      } // foreach
      $result .= '</tr>';
      
      return "<thead>$result</thead></table>";
    } // renderDateScale
    
    /**
     * Renders the list of users
     * 
     * @param void
     * @return string
     */
    private function renderUserList() {
      $result = '<table cellspacing="0">';
      foreach($this->users as $user) {
      	$result .= '<tr id="user_' . $user->getId() . '" user_id="' . $user->getId() . '"><td class="name"><a href="' . clean($user->getViewUrl()) . '">' . clean($user->getDisplayName(true)) . '</a></td><td class="icon"><a href="' . clean($user->getViewUrl()) . '"><img src="' . $user->avatar()->getUrl(IUserAvatarImplementation::SIZE_SMALL) . '" alt="" /></a></td></tr>';
      } // foreach
      $result.= '</table>';
    	return $result;
    } // renderUserList
    
    /**
     * Render body rows
     *
     * @return string
     */
    private function renderTimesheetEntries() {
      $result= '<table cellspacing="0">';
      foreach($this->users as $user) {
        $result .= '<tr id="time_for_' . $user->getId() . '" user_id="' . $user->getId() . '" class="timerecords">';
        
        $map = TrackingObjects::sumUserTimeRecordsByDate($this->project, $user, $this->logged_user, STATE_VISIBLE);
        
        foreach($this->days as $day) {
          $formatted_day = $day->toMySQL();
          
          $formatted_month = explode('-', $formatted_day);
          unset($formatted_month[2]);
          $formatted_month = implode('-', $formatted_month);
          
          if($day->isWeekend()) {
            $day_class = 'weekend';
          } elseif($day->isWorkday()) {
            $day_class = 'workday';
          } else {
            $day_class = 'day_off';
          } // if
          
          $value = isset($map[$formatted_day]) ? $map[$formatted_day] : 0;
          
          if($value) {
            $result .= "<td class=\"day $day_class\" day=\"$formatted_day\" month=\"$formatted_month\"><span class='day'>$value</span></td>";
          } else {
            $result .= "<td class=\"day no_time $day_class\" day=\"$formatted_day\" month=\"$formatted_month\"><span class='day'></span></td>";
          } // if
        } // foreach
        
        $result .= '</tr>';
      } // foreach
      
      return "<tbody>$result</tbody></table>";
    } // renderDateScale
    
    /**
     * Render timesheet footer
     *
     * @return string
     */
    function renderFoot() {
      
    } // renderFoot
    
    /**
     * Load date range
     */
    private function loadRange() {
      $time_records_table = TABLE_PREFIX . 'time_records';

      $parent_conditions = TrackingObjects::prepareParentTypeFilter($this->logged_user, $this->project, true, STATE_VISIBLE, VISIBILITY_NORMAL);
      $min_date_value = DB::executeFirstCell("SELECT MIN(record_date) FROM $time_records_table WHERE $parent_conditions AND state >= ?", STATE_VISIBLE);
      $max_date_value = DB::executeFirstCell("SELECT MAX(record_date) FROM $time_records_table WHERE $parent_conditions AND state >= ?", STATE_VISIBLE);

      $this->start_date = $min_date_value ? new DateValue($min_date_value) : DateValue::now();
      $this->end_date = $max_date_value ? new DateValue($max_date_value) : DateValue::now();
      
      $this->start_date->advance(-86400 * 45, true);
      $this->end_date->advance(86400 * 45, true);
      
      for($i = $this->start_date->getTimestamp(); $i <= $this->end_date->getTimestamp(); $i += 86400) {
        $this->days[$i] = new DateValue($i);
      } // for
    } // loadRange
    
    /**
     * Load all the users that need to be displayed
     */
    private function loadUsers() {
      $time_records_table = TABLE_PREFIX . 'time_records';
      
      $parent_conditions = TrackingObjects::prepareParentTypeFilter($this->logged_user, $this->project, true, STATE_VISIBLE, VISIBILITY_NORMAL);
      $time_user_ids = DB::executeFirstColumn("SELECT DISTINCT user_id FROM $time_records_table WHERE $parent_conditions AND state >= ?", STATE_VISIBLE);
      
      $user_ids = $time_user_ids ? 
        array_unique(array_merge($this->project->users()->getIds($this->logged_user), $time_user_ids)) :  // Project users + time users
        $this->project->users()->getIds($this->logged_user); // Just project users
      
      $this->users = Users::findBySQL('SELECT * FROM ' . TABLE_PREFIX . 'users WHERE id IN (?) AND id IN (?) ORDER BY CONCAT(first_name, last_name, email)', $user_ids, Users::findVisibleUserIds($this->logged_user));
    } // loadUsers
    
  }