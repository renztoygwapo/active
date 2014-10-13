<?php

  // Build on top of administration controller
  AngieApplication::useController('admin', GLOBALIZATION_FRAMEWORK_INJECT_INTO);

  /**
   * Globalization administration controller
   *
   * @package angie.frameworks.globalization
   * @subpackage controllers
   */
  class FwGlobalizationAdminController extends AdminController {
    
    /**
     * Show and process date and time settings page
     */
    function date_time() {
      if($this->request->isAsyncCall()) {
        $date_time_data = $this->request->post('date_time');
        if(!is_array($date_time_data)) {
          $date_time_data = ConfigOptions::getValue(array(
            'time_timezone',
            'time_dst',
            'format_date',
            'format_time',
          ));
        } // if
        $this->smarty->assign(array(
          'date_time_data' => $date_time_data,
          'days_off' => DayOffs::find(),
        ));

        if($this->request->isSubmitted()) {
          try {
            if(is_array($date_time_data['time_workdays'])) {
              foreach($date_time_data['time_workdays'] as $k => $v) {
                $date_time_data['time_workdays'][$k] = (integer) $v;
              } // foreach
            } // if

            $data = array(
              'time_timezone' => (integer) $date_time_data['time_timezone'],
              'time_dst' => (integer) $date_time_data['time_dst'],
              'format_date' => $date_time_data['format_date'],
              'format_time' => $date_time_data['format_time'],
            );

            ConfigOptions::setValue($data);

            if(AngieApplication::isOnDemand()) {
              $settings = array(
                'morning_paper' => array(
                  'time_timezone' => $data['time_timezone'],
                  'time_dst' => $data['time_dst'])
              );
              $options = array(
                'i' => ON_DEMAND_INSTANCE_NAME,
                's' => serialize($settings)
              );
              OnDemand::executeViaCLI('instance_settings', $options);
            } //if

            AngieApplication::cache()->clear();

            $this->response->ok();
          } catch(Exception $e) {
            $this->response->exception($e);
          } // try
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // date_time
    
    /**
     * Workweek settings
     */
    function workweek() {
      if($this->request->isAsyncCall()) {
        $days_off = DayOffs::find();
      
        $workweek_data = $this->request->post('workweek');
        if(!is_array($workweek_data)) {
          $workweek_data = ConfigOptions::getValue(array(
            'time_first_week_day',
            'time_workdays',
            'effective_work_hours'
          ));

          $workweek_data['days_off'] = array();
          if(is_foreachable($days_off)) {
            foreach($days_off as $day_off) {
              $workweek_data['days_off']['existing_' . $day_off->getId()] = array(
                'name' => $day_off->getName(),
                'date' => $day_off->getEventDate(),
                'repeat_yearly' => $day_off->getRepeatYearly(),
              );
            } // foreach
          } // if
        } // if

        $this->smarty->assign('workweek_data', $workweek_data);

        if($this->request->isSubmitted()) {
          try {
            DB::beginWork('Updating workweek settings @ ' . __CLASS__);

            if(!is_array($workweek_data['days_off'])) {
              $workweek_data['days_off'] = array(); // Just in case...
            } // if

            ConfigOptions::setValue(array(
              'time_first_week_day' => (integer) $workweek_data['time_first_week_day'],
              'time_workdays' => $workweek_data['time_workdays'],
              'effective_work_hours' => (integer) $workweek_data['effective_work_hours']
            ));

            $clear_days_off_cache = false;

            // Update or remove existing
            if(is_foreachable($days_off)) {
              $clear_days_off_cache = true;
              foreach($days_off as $day_off) {
                $existing_key = 'existing_' . $day_off->getId();
                if(isset($workweek_data['days_off'][$existing_key])) {
                  $day_off->setAttributes($workweek_data['days_off'][$existing_key]);
                  $day_off->save();
                } else {
                  $day_off->delete();
                } // if
              } // foreach
            } // if

            // Add new
            foreach($workweek_data['days_off'] as $k => $day_off_data) {
              if(!str_starts_with($k, 'existing_')) {
                $day_off = new DayOff();
                $day_off->setAttributes($day_off_data);
                $day_off->save();

                $clear_days_off_cache = true;
              } // if
            } // foreach

            DB::commit('Workweek settings updated @ ' . __CLASS__);

            if ($clear_days_off_cache) {
              AngieApplication::cache()->remove("days_off");
            } // if

            $this->response->ok();
          } catch(Exception $e) {
            DB::rollback('Failed to update workweek settings @ ' . __CLASS__);
            $this->response->exception($e);
          } // try
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // workweek
    
    /**
     * List all available languages
     */
    function languages() {
      
    } // languages
    
  }