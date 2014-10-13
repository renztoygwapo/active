<?php

  /**
   * Framework level data filter implementation
   *
   * @package angie.frameworks.environment
   * @subpackage models
   */
  abstract class FwDataFilter extends BaseDataFilter implements IRoutingContext {

    // Result types
    const RESULT_RAW = 'raw';
    const RESULT_RENDERED = 'rendered';

    const EXPORT_CSV = 'csv';
    const EXPORT_EXCEL = 'xls';

    const EXPORT_ERROR_ALREADY_STARTED = 0;
    const EXPORT_ERROR_CANT_OPEN_HANDLE = 1;
    const EXPORT_ERROR_HANDLE_NOT_OPEN = 2;

    /**
     * Run the filter
     *
     * @param IUser $user
     * @param mixed $additional
     * @return array
     */
    abstract function run(IUser $user, $additional = null);

    /**
     * Run export and return file name where data is temporaly stored
     *
     * @param IUser $user
     * @param mixed $additional
     * @return string
     */
    abstract function runForExport(IUser $user, $additional = null);

    /**
     * Prepare this result to be used as map
     *
     * @param $result
     */
    function resultToMap(&$result) {

    } // resultToMap

    /**
     * Return name of CSV file export
     *
     * @return string
     */
    function getExportFileName() {
      return Inflector::underscore(get_class($this));
    } // getExportFileName

    /**
     * Add more print data, if needed for this report
     *
     * @param array $additional_print_data
     */
    function getAdditionalPrintData(&$additional_print_data) {

    } // getAdditionalPrintData

    // ---------------------------------------------------
    //  User Filter Utility Methods
    // ---------------------------------------------------

    const USER_FILTER_ANYBODY = 'anybody';
    const USER_FILTER_ANONYMOUS = 'anonymous';
    const USER_FILTER_LOGGED_USER = 'logged_user';
    const USER_FILTER_SELECTED = 'selected';

    /**
     * Returns true if $value is a valid and supported user filter
     *
     * @param string $value
     * @return bool
     */
    protected function isValidUserFilter($value) {
      return in_array($value, array(self::USER_FILTER_ANYBODY, self::USER_FILTER_ANONYMOUS, self::USER_FILTER_LOGGED_USER, self::USER_FILTER_SELECTED));
    } // isValidUserFilter

    /**
     * Return filter by user filer name
     *
     * @param string $user_filter_name
     * @return string
     */
    protected function getFilterByUserFilterName($user_filter_name) {
      return "{$user_filter_name}_by_filter";
    } // getFilterByUserFilterName

    /**
     * Return field name based on user filter, if $filter_name is empty
     *
     * @param string $field_name
     * @param string $user_filter_name
     * @return string
     */
    protected function getUserFilterFieldName($field_name, $user_filter_name) {
      return $field_name ? $field_name : "{$user_filter_name}_by_id";
    } // getUserFilterFieldName

    /**
     * Cached array of user filter getter
     *
     * @var array
     */
    private $user_filter_getters = array();

    /**
     * Return user filter getter method names
     *
     * @param string $user_filter_name
     * @return array
     */
    protected function getUserFilterGetters($user_filter_name) {
      if(!array_key_exists($user_filter_name, $this->user_filter_getters)) {
        $this->user_filter_getters[$user_filter_name] = $this->prepareUserFilterGetterNames($user_filter_name);
      } // if

      return $this->user_filter_getters[$user_filter_name];
    } // getUserFilterGetters

    /**
     * Cached array of user filter setters
     *
     * @var array
     */
    private $user_filter_setters = false;

    /**
     * Return user filter setter method names
     *
     * @param string $user_filter_name
     * @return array
     */
    protected function getUserFilterSetters($user_filter_name) {
      if(!array_key_exists($user_filter_name, $this->user_filter_setters)) {
        $this->user_filter_setters[$user_filter_name] = $this->prepareUserFilterSetterNames($user_filter_name);
      } // if

      return $this->user_filter_setters[$user_filter_name];
    } // getUserFilterSetters

    /**
     * Return user filter getter method names
     *
     * @param string $user_filter_name
     * @return array
     */
    protected function prepareUserFilterGetterNames($user_filter_name) {
      $user_filter_camelized = Inflector::camelize($user_filter_name);

      return array("get{$user_filter_camelized}ByFilter", "get{$user_filter_camelized}ByUsers");
    } // prepareUserFilterGetterNames

    /**
     * Prepare and return user filter setter method names
     *
     * @param string $user_filter_name
     * @return array
     */
    protected function prepareUserFilterSetterNames($user_filter_name) {
      $user_filter_camelized = Inflector::camelize($user_filter_name);
      $user_filter_prefix = lcfirst($user_filter_camelized);

      return array("set{$user_filter_camelized}ByFilter", "{$user_filter_prefix}ByUsers");
    } // prepareUserFilterSetterNames

    /**
     * Set user filter settings from attributes
     *
     * @param string $user_filter_name
     * @param array $attributes
     */
    protected function setUserFilterAttributes($user_filter_name, $attributes) {
      $user_filter = $this->getFilterByUserFilterName($user_filter_name);

      if(isset($attributes[$user_filter])) {
        list($filter_setter, $selected_users_setter) = $this->getUserFilterSetters($user_filter_name);

        switch($attributes[$user_filter]) {
          case self::USER_FILTER_SELECTED:
            $this->$selected_users_setter($attributes["{$user_filter_name}_by_user_ids"]);
            break;
          default:
            $this->$filter_setter($attributes[$user_filter]);
        } // switch
      } // if
    } // setUserFilterAttributes

    /**
     * Describe user filter
     *
     * @param string $user_filter_name
     * @param array $result
     */
    protected function describeUserFilter($user_filter_name, &$result) {
      list($filter_getter, $selected_users_getter) = $this->getUserFilterGetters($user_filter_name);

      $user_filter = $this->getFilterByUserFilterName($user_filter_name);

      $result[$user_filter] = $this->$filter_getter();

      if($result[$user_filter] == self::USER_FILTER_SELECTED) {
        $result["{$user_filter_name}_by_user_ids"] = $this->$selected_users_getter();
      } // if
    } // describeUserFilter

    /**
     * Prepare conditions for a particular user filter
     *
     * @param User $user
     * @param string $user_filter_name
     * @param string $table_name
     * @param array $conditions
     * @param string $field_name
     * @throws DataFilterConditionsError
     */
    protected function prepareUserFilterConditions(User $user, $user_filter_name, $table_name, &$conditions, $field_name = null) {
      list($filter_getter, $selected_users_getter) = $this->getUserFilterGetters($user_filter_name);

      $user_filter = $this->getFilterByUserFilterName($user_filter_name);

      if($this->isValidUserFilter($this->$filter_getter())) {
        $field_name = $this->getUserFilterFieldName($field_name, $user_filter_name);

        switch($this->$filter_getter()) {
          case self::USER_FILTER_ANYBODY:
            break;

          // Logged user
          case self::USER_FILTER_LOGGED_USER:
            $conditions[] = DB::prepare("($table_name.$field_name = ?)", $user->getId());
            break;

          // Selected users
          case self::USER_FILTER_SELECTED:
            $user_ids = $this->$selected_users_getter();

            if($user_ids) {
              $visible_user_ids = $user->visibleUserIds();

              if($visible_user_ids) {
                foreach($user_ids as $k => $v) {
                  if(!in_array($v, $visible_user_ids)) {
                    unset($user_ids[$k]);
                  } // if
                } // foreach

                if(count($user_ids)) {
                  $conditions[] = DB::prepare("($table_name.$field_name IN (?))", $user_ids);
                } else {
                  throw new DataFilterConditionsError($user_filter, self::USER_FILTER_SELECTED, $user_ids, 'Non of the selected users is visible');
                } // if
              } else {
                throw new DataFilterConditionsError($user_filter, self::USER_FILTER_SELECTED, $user_ids, "User can't see anyone else");
              } // if
            } else {
              throw new DataFilterConditionsError($user_filter, self::USER_FILTER_SELECTED, $user_ids, 'No users selected');
            } // if

            break;
        } // switch
      } else {
        throw new DataFilterConditionsError($user_filter, $this->$filter_getter(), 'mixed', 'Unknown user filter');
      } // if
    } // prepareUserFilterConditions

    // ---------------------------------------------------
    //  Date Filter Utility Methods
    // ---------------------------------------------------

    // Date filter
    const DATE_FILTER_ANY = 'any';
    const DATE_FILTER_IS_SET = 'is_set';
    const DATE_FILTER_IS_NOT_SET = 'is_not_set';
    const DATE_FILTER_LATE = 'late';
    const DATE_FILTER_LATE_OR_TODAY = 'late_or_today';
    const DATE_FILTER_YESTERDAY = 'yesterday';
    const DATE_FILTER_TODAY = 'today';
    const DATE_FILTER_TOMORROW = 'tomorrow';
    const DATE_FILTER_LAST_WEEK = 'last_week';
    const DATE_FILTER_THIS_WEEK = 'this_week';
    const DATE_FILTER_NEXT_WEEK = 'next_week';
    const DATE_FILTER_LAST_MONTH = 'last_month';
    const DATE_FILTER_THIS_MONTH = 'this_month';
    const DATE_FILTER_NEXT_MONTH = 'next_month';
    const DATE_FILTER_AGE_IS = 'age_is';
    const DATE_FILTER_AGE_IS_MORE_THAN = 'age_is_more_than';
    const DATE_FILTER_AGE_IS_LESS_THAN = 'age_is_less_than';
    const DATE_FILTER_BEFORE_SELECTED_DATE = 'before_selected_date';
    const DATE_FILTER_SELECTED_DATE = 'selected_date';
    const DATE_FILTER_AFTER_SELECTED_DATE = 'after_selected_date';
    const DATE_FILTER_SELECTED_RANGE = 'selected_range';

    /**
     * Return date filter getter method names
     *
     * @param string $date_filter_name
     * @return array
     */
    protected function getDateFilterGetters($date_filter_name) {
      $upper_case_date_filter_name = Inflector::camelize($date_filter_name);

      return array("get{$upper_case_date_filter_name}OnFilter", "get{$upper_case_date_filter_name}Age", "get{$upper_case_date_filter_name}OnDate", "get{$upper_case_date_filter_name}InRange");
    } // getDateFilterGetters

    /**
     * Return date filter setter method names
     *
     * @param string $date_filter_name
     * @return array
     */
    protected function getDateFilterSetters($date_filter_name) {
      return array('set' . Inflector::camelize($date_filter_name) . 'OnFilter', "{$date_filter_name}Age", "{$date_filter_name}OnDate", "{$date_filter_name}BeforeDate", "{$date_filter_name}AfterDate", "{$date_filter_name}InRange");
    } // getDateFilterSetters

    /**
     * Set date filter settings from attributes
     *
     * @param string $date_filter_name
     * @param array $attributes
     */
    protected function setDateFilterAttributes($date_filter_name, $attributes) {
      $date_filter = "{$date_filter_name}_on_filter";

      if(isset($attributes[$date_filter])) {
        list($filter_setter, $age_setter, $on_date_setter, $before_date_setter, $after_date_setter, $in_range_setter) = $this->getDateFilterSetters($date_filter_name);

        switch($attributes[$date_filter]) {
          case self::DATE_FILTER_AGE_IS:
          case self::DATE_FILTER_AGE_IS_LESS_THAN:
          case self::DATE_FILTER_AGE_IS_MORE_THAN:
            $this->$age_setter($attributes["{$date_filter_name}_age"], $attributes[$date_filter]);
            break;
          case self::DATE_FILTER_SELECTED_DATE:
            $this->$on_date_setter($attributes["{$date_filter_name}_on"]);
            break;
          case self::DATE_FILTER_BEFORE_SELECTED_DATE:
            $this->$before_date_setter($attributes["{$date_filter_name}_on"]);
            break;
          case self::DATE_FILTER_AFTER_SELECTED_DATE:
            $this->$after_date_setter($attributes["{$date_filter_name}_on"]);
            break;
          case self::DATE_FILTER_SELECTED_RANGE:
            $this->$in_range_setter($attributes["{$date_filter_name}_from"], $attributes["{$date_filter_name}_to"]);
            break;
          default:
            $this->$filter_setter($attributes[$date_filter]);
        } // switch
      } // if
    } // setDateFilterAttributes

    /**
     * Describe date filter
     *
     * @param string $date_filter_name
     * @param array $result
     */
    protected function describeDateFilter($date_filter_name, &$result) {
      list($filter_getter, $age_getter, $on_date_getter, $in_range_getter) = $this->getDateFilterGetters($date_filter_name);

      $date_filter = "{$date_filter_name}_on_filter";

      $result[$date_filter] = $this->$filter_getter();
      switch($this->$filter_getter()) {
        case self::DATE_FILTER_AGE_IS:
        case self::DATE_FILTER_AGE_IS_LESS_THAN:
        case self::DATE_FILTER_AGE_IS_MORE_THAN:
          $result["{$date_filter_name}_age"] = $this->$age_getter();
          break;
        case self::DATE_FILTER_SELECTED_DATE:
        case self::DATE_FILTER_BEFORE_SELECTED_DATE:
        case self::DATE_FILTER_AFTER_SELECTED_DATE:
          $result["{$date_filter_name}_on"] = $this->$on_date_getter();
          break;

        case self::DATE_FILTER_SELECTED_RANGE:
          list($from, $to) = $this->$in_range_getter();

          $result["{$date_filter_name}_from"] = $from;
          $result["{$date_filter_name}_to"] = $to;

          break;
      } // switch
    } // describeDateFilter

    /**
     * Prepare conditions for a particular date filter
     *
     * @param User $user
     * @param string $date_filter_name
     * @param string $table_name
     * @param array $conditions
     * @param string $field_name
     * @throws DataFilterConditionsError
     */
    protected function prepareDateFilterConditions(User $user, $date_filter_name, $table_name, &$conditions, $field_name = null) {
      list($filter_getter, $age_getter, $on_date_getter, $in_range_getter) = $this->getDateFilterGetters($date_filter_name);

      $date_filter = "{$date_filter_name}_on_filter";

      if(empty($field_name)) {
        $field_name = "{$date_filter_name}_on";
      } // if

      $user_gmt_offset = get_user_gmt_offset($user);


      $today = DateValue::now()->advance($user_gmt_offset, false);

      switch($this->$filter_getter()) {
        case self::DATE_FILTER_ANY:
          break;

        // List items where we have the value set
        case self::DATE_FILTER_IS_SET:
          $conditions[] = "($table_name.$field_name IS NOT NULL)"; break;

        // List items where we don't have date value set
        case self::DATE_FILTER_IS_NOT_SET:
          $conditions[] = "($table_name.$field_name IS NULL)"; break;

        // List late items
        case self::DATE_FILTER_LATE:
          $conditions[] = DB::prepare("($table_name.$field_name < ?)", $today); break;

        // List late or today items
        case self::DATE_FILTER_LATE_OR_TODAY:
          $conditions[] = DB::prepare("($table_name.$field_name <= ?)", $today); break;

        // List items that match yesterday
        case self::DATE_FILTER_YESTERDAY:
          if($user_gmt_offset != 0 && $this->calculateTimezoneWhenFilteringByDate($field_name)) {
            $yesterday = DateTimeValue::makeFromString('-1 day');

            $from = $yesterday->beginningOfDay()->advance($user_gmt_offset * -1, false); // Revert to user specific beginning of day
            $to = $yesterday->endOfDay()->advance($user_gmt_offset * -1, false); // Revert to user specific end of day

            $conditions[] = DB::prepare("($table_name.$field_name BETWEEN ? AND ?)", $from, $to);
          } else {
            $conditions[] = DB::prepare("(DATE($table_name.$field_name) = ?)", $today->advance(-86400, false));
          } // if

          break;

        // List items that match today
        case self::DATE_FILTER_TODAY:
          if($user_gmt_offset != 0 && $this->calculateTimezoneWhenFilteringByDate($field_name)) {
            $now = DateTimeValue::now();

            $from = $now->beginningOfDay()->advance($user_gmt_offset * -1, false); // Revert to user specific beginning of day
            $to = $now->endOfDay()->advance($user_gmt_offset * -1, false); // Revert to user specific end of day

            $conditions[] = DB::prepare("($table_name.$field_name BETWEEN ? AND ?)", $from, $to);
          } else {
            $conditions[] = DB::prepare("(DATE($table_name.$field_name) = ?)", $today);
          } // if

          break;

        // List items that match tomorrow
        case self::DATE_FILTER_TOMORROW:
          $conditions[] = DB::prepare("(DATE($table_name.$field_name) = ?)", $today->advance(86400, false)); break;

        // List items that match previous week
        case self::DATE_FILTER_LAST_WEEK:
          $first_week_day = ConfigOptions::getValueFor('time_first_week_day', $user);

          $seven_days_ago = $today->advance(-604800, false);

          $conditions[] = DB::prepare("($table_name.$field_name >= ? AND $table_name.$field_name <= ?)", $seven_days_ago->beginningOfWeek($first_week_day), $seven_days_ago->endOfWeek($first_week_day));

          break;

        // List items that match this week
        case self::DATE_FILTER_THIS_WEEK:
          $first_week_day = ConfigOptions::getValueFor('time_first_week_day', $user);

          $conditions[] = DB::prepare("($table_name.$field_name >= ? AND $table_name.$field_name <= ?)", $today->beginningOfWeek($first_week_day), $today->endOfWeek($first_week_day));
          break;

        // List items that match next week
        case self::DATE_FILTER_NEXT_WEEK:
          $first_week_day = ConfigOptions::getValueFor('time_first_week_day', $user);

          $next_week = $today->advance(604800, false);

          $conditions[] = DB::prepare("($table_name.$field_name >= ? AND $table_name.$field_name <= ?)", $next_week->beginningOfWeek($first_week_day), $next_week->endOfWeek($first_week_day));

          break;

        // List items that match this motnh
        case self::DATE_FILTER_LAST_MONTH:
          $month = $today->getMonth()-1;
          $year = $today->getYear();

          if($month == 0) {
            $month = 12;
            $year -= 1;
          } // if

          $conditions[] = DB::prepare("($table_name.$field_name >= ? AND $table_name.$field_name <= ?)", DateTimeValue::beginningOfMonth($month, $year), DateTimeValue::endOfMonth($month, $year));

          break;

        // List items that match this month
        case self::DATE_FILTER_THIS_MONTH:
          $conditions[] = DB::prepare("($table_name.$field_name >= ? AND $table_name.$field_name <= ?)", DateTimeValue::beginningOfMonth($today->getMonth(), $today->getYear()), DateTimeValue::endOfMonth($today->getMonth(), $today->getYear()));

          break;

        // List items that match the next month
        case self::DATE_FILTER_NEXT_MONTH:
          $month = $today->getMonth() + 1;
          $year = $today->getYear();

          if($month == 13) {
            $month = 1;
            $year += 1;
          } // if

          $conditions[] = DB::prepare("($table_name.$field_name >= ? AND $table_name.$field_name <= ?)", DateTimeValue::beginningOfMonth($month, $year), DateTimeValue::endOfMonth($month, $year));
          break;

        // Age is
        case self::DATE_FILTER_AGE_IS:
          $age = (integer) $this->$age_getter();

          $conditions[] = DB::prepare("(DATE({$table_name}.$field_name) = ?)", $today->advance(-1 * $age * 86400, false)); break;
          break;

        // Age is less than
        case self::DATE_FILTER_AGE_IS_LESS_THAN:
          $age = (integer) $this->$age_getter();

          //$conditions[] = DB::prepare("(DATE({$table_name}.$field_name) > ?)", DateValue::makeFromString("-{$age} days")); break;
          $conditions[] = DB::prepare("(DATE({$table_name}.$field_name) > ?)", $today->advance(-1 * $age * 86400, false)); break;
          break;

        // Age is more than
        case self::DATE_FILTER_AGE_IS_MORE_THAN:
          $age = (integer) $this->$age_getter();

          $conditions[] = DB::prepare("(DATE({$table_name}.$field_name) < ?)", $today->advance(-1 * $age * 86400, false)); break;
          break;

        // Specific date
        case self::DATE_FILTER_SELECTED_DATE:
          if($user_gmt_offset != 0 && $this->calculateTimezoneWhenFilteringByDate($field_name)) {
            $reverted_date = new DateTimeValue($this->$on_date_getter()->getTimestamp() - $user_gmt_offset); // Revert to GMT

            $conditions[] = DB::prepare("($table_name.$field_name BETWEEN ? AND ?)", $reverted_date->beginningOfDay(), $reverted_date->endOfDay());
          } else {
            $conditions[] = DB::prepare("(DATE($table_name.$field_name) = ?)", $this->$on_date_getter());
          } // if

          break;

        // Before specific date
        case self::DATE_FILTER_BEFORE_SELECTED_DATE:
          if($user_gmt_offset != 0 && $this->calculateTimezoneWhenFilteringByDate($field_name)) {
            $reverted_date = new DateTimeValue($this->$on_date_getter()->getTimestamp() - $user_gmt_offset); // Revert to GMT

            $conditions[] = DB::prepare("($table_name.$field_name < ?)", $reverted_date->beginningOfDay());
          } else {
            $conditions[] = DB::prepare("(DATE($table_name.$field_name) < ?)", $this->$on_date_getter());
          } // if

          break;

        // After specific date
        case self::DATE_FILTER_AFTER_SELECTED_DATE:
          if($user_gmt_offset != 0 && $this->calculateTimezoneWhenFilteringByDate($field_name)) {
            $reverted_date = new DateTimeValue($this->$on_date_getter()->getTimestamp() - $user_gmt_offset); // Revert to GMT

            $conditions[] = DB::prepare("($table_name.$field_name > ?)", $reverted_date->endOfDay());
          } else {
            $conditions[] = DB::prepare("(DATE($table_name.$field_name) > ?)", $this->$on_date_getter());
          } // if

          break;

        // Specific range
        case self::DATE_FILTER_SELECTED_RANGE:
          list($from, $to) = $this->$in_range_getter();

          if($user_gmt_offset != 0 && $this->calculateTimezoneWhenFilteringByDate($field_name)) {
            $reverted_from = new DateTimeValue($from->getTimestamp() - $user_gmt_offset); // Revert to GMT
            $reverted_to = new DateTimeValue($to->getTimestamp() - $user_gmt_offset); // Revert to GMT

            $conditions[] = DB::prepare("($table_name.$field_name >= ? AND $table_name.$field_name <= ?)", $reverted_from->beginningOfDay(), $reverted_to->endOfDay());
          } else {
            $conditions[] = DB::prepare("(DATE($table_name.$field_name) >= ? AND DATE($table_name.$field_name) <= ?)", $from, $to);
          } // if

          break;

        default:
          throw new DataFilterConditionsError($date_filter, $this->$filter_getter(), 'mixed', 'Unknown date filter');
      } // switch
    } // prepareDateFilterConditions

    /**
     * Return true if we should factore in time zone when we are filtering by a given date
     *
     * @param string $field_name
     * @return bool
     */
    protected function calculateTimezoneWhenFilteringByDate($field_name) {
      return $field_name == 'created_on';
    } // calculateTimezoneWhenFilteringByDate

    // ---------------------------------------------------
    //  CSV Export
    // ---------------------------------------------------

    /**
     * Path of the work CSV file
     *
     * @var string
     */
    private $csv_export_file_path;

    /**
     * Current Excel export row
     *
     * @var int
     */
    private $excel_export_current_row = 1;

    /**
     * Write handle on the CSV export
     *
     * @var resource|PHPExcel
     */
    private $export_handle = null;

    /**
     * Returns true if export has been started
     *
     * @return bool
     */
    private function isExportStarted() {
      return $this->export_handle !== null;
    } // isExportStarted

    /**
     * Return export handle
     *
     * @return PHPExcel|resource
     */
    private function getExportHandle() {
      return $this->export_handle;
    } // getExportHandle

    /**
     * Begin data export
     *
     * @param array $columns
     * @param string $format
     * @throws DataFilterExportError
     */
    protected function beginExport($columns, $format = null) {
      if($this->isExportStarted()) {
        throw new DataFilterExportError(DataFilter::EXPORT_ERROR_ALREADY_STARTED);
      } // if

      // Prpeare Excel export
      if($format === self::EXPORT_EXCEL) {
        require_once ANGIE_PATH . '/vendor/phpexcel/init.php';

        PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder()); // Automatically detect dates and date time values

        $this->export_handle = new PHPExcel();

        for($i = 0; $i < count($columns); $i++) {
          $this->export_handle->getActiveSheet()->setCellValueByColumnAndRow($i, $this->excel_export_current_row, $columns[$i]);
        } // for

        $last_column = $this->getColumnNameFromNumber(count($columns));

        $this->export_handle->getActiveSheet()->getStyle("A1:{$last_column}1")->applyFromArray(array(
          'borders' => array(
            'bottom' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN,
            ), ),
          'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
              'argb' => 'FFE8E8E8',
            ),
          ),
        ));

        $this->excel_export_current_row++;

      // Prepare CSV export
      } else {
        $this->csv_export_file_path = AngieApplication::getAvailableWorkFileName($this->getExportFileName(), 'csv');

        $this->export_handle = fopen($this->csv_export_file_path, 'w');
        if($this->export_handle) {
          fwrite($this->export_handle, array_to_csv_row($columns));
        } else {
          throw new DataFilterExportError(DataFilter::EXPORT_ERROR_CANT_OPEN_HANDLE);
        } // if
      } // if
    } // beginCsvExport

    /**
     * Return Excel compatible column name based on column number (indexed from 1!)
     *
     * @param integer $num
     * @return string
     */
    private function getColumnNameFromNumber($num) {
      $numeric = ($num - 1) % 26;
      $letter = chr(65 + $numeric);
      $num2 = intval(($num - 1) / 26);
      if ($num2 > 0) {
        return $this->getColumnNameFromNumber($num2) . $letter;
      } else {
        return $letter;
      } // if
    } // getColumnNameFromNumber

    /**
     * Write new line to CSV temp file
     *
     * @param array $elements
     * @throws DataFilterExportError
     */
    protected function exportWriteLine($elements) {
      $handle = $this->getExportHandle();

      if($handle instanceof PHPExcel) {
        for($i = 0; $i < count($elements); $i++) {
          $this->export_handle->getActiveSheet()->setCellValueByColumnAndRow($i, $this->excel_export_current_row, $elements[$i]);
        } // for

        $this->excel_export_current_row++;
      } elseif($handle) {
        fwrite($this->export_handle, array_to_csv_row($elements));
      } else {
        throw new DataFilterExportError(DataFilter::EXPORT_ERROR_HANDLE_NOT_OPEN);
      } // if
    } // exportWriteLine

    /**
     * Complete export process and return file path of the CSV file that we generated
     *
     * @return string
     * @throws DataFilterExportError
     */
    protected function completeExport() {
      $handle = $this->getExportHandle();

      // Finish Excel export
      if($handle instanceof PHPExcel) {
        $file_path = AngieApplication::getAvailableWorkFileName($this->getExportFileName(), 'xlsx');

        $writer = PHPExcel_IOFactory::createWriter($handle, 'Excel2007');
        $writer->save($file_path);

        $this->export_handle->disconnectWorksheets();
        unset($this->export_handle);

        $this->excel_export_current_row = 1;
        $this->export_handle = null;

      // Finish CSV export
      } elseif($handle) {
        $file_path = $this->csv_export_file_path;

        fclose($handle);

        $this->export_handle = null;
        $this->csv_export_file_path = null;

      // Export not started
      } else {
        throw new DataFilterExportError(DataFilter::EXPORT_ERROR_HANDLE_NOT_OPEN);
      } // if

      return $file_path;
    } // completeExport

    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------

    /**
     * Returns true if $user can update this filter
     *
     * @param User $user
     * @return bool
     */
    function canEdit(User $user) {
      return $this->isCreatedBy($user) || DataFilters::canManage(get_class($this), $user);
    } // canEdit

    /**
     * Return true if $user can delete this filter
     *
     * @param User $user
     * @return bool
     */
    function canDelete(User $user) {
      return $this->isCreatedBy($user) || DataFilters::canManage(get_class($this), $user);
    } // canDelete

    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------

    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if($this->validatePresenceOf('name')) {
        if(!$this->validateUniquenessOf('name', 'type')) {
          $errors->addError('Name needs to be unique', 'name');
        } // if
      } else {
        $errors->addError('Name is required', 'name');
      } // if
    } // validate

  }