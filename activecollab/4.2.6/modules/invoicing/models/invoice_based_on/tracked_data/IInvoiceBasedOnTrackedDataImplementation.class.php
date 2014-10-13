<?php

  /**
   * Invoice based on tracked data
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  abstract class IInvoiceBasedOnTrackedDataImplementation extends IInvoiceBasedOnImplementation {

    /**
     * Query tracking records
     *
     * This function returns three elements: array of time records, array of expenses and project
     *
     * @param IUser $user
     * @return array
     */
    abstract function queryRecords(IUser $user = null);

    /**
     * Create new invoice instance based on parent object
     *
     * @param Company $client
     * @param string $client_address
     * @param array|null $additional
     * @param IUser $user
     * @return Invoice
     * @throws Error
     */
    function create(Company $client, $client_address = null, $additional = null, IUser $user = null) {
      $additional = $additional ? (array) $additional : array();

      list($time_records, $expenses, $project) = $this->queryRecords($user);

//      if($project) {
//        $additional['project_id'] = $project instanceof Project ? $project->getId() : $project;
//      } // if

      $invoice = parent::createInvoiceFromPropeties($client, $client_address, $additional);

      $sum_by = array_var($additional, 'sum_by');
      if(empty($sum_by)) {
        $sum_by = ConfigOptions::getValue('on_invoice_based_on');
      } // if

      $items = $this->prepareItemsForInvoice($time_records, $expenses, $sum_by, array_var($additional, 'first_tax_rate_id'), array_var($additional, 'second_tax_rate_id'), $user);

      if(is_foreachable($items)) {
        return $this->commitInvoiceItems($items, $invoice);
      } else {
        throw new Error(lang('Invoice must have at least one item. Make sure that selected records are not already pending payment in another invoice.'));
      } // if
    } // create

    /**
     * Return preview array of items
     *
     * @param array $settings
     * @param IUser $user
     * @return array
     */
    function previewItems($settings = null, IUser $user = null) {
      list($time_records, $expenses, $project) = $this->queryRecords($user);

      return $this->prepareItemsForInvoice($time_records, $expenses, $settings['sum_by'], $settings['first_tax_rate_id'], $settings['second_tax_rate_id'], $user);
    } // previewItems

    /**
     * Create invoice items for object
     *
     * @param TimeRecord[] $time_records
     * @param Expense[] $expenses
     * @param string $sum_by
     * @param TaxRate $first_tax_rate
     * @param TaxRate $second_tax_rate
     * @param IUser $user
     * @return array
     * @throws InvalidParamError
     */
    protected function prepareItemsForInvoice($time_records, $expenses, $sum_by, $first_tax_rate, $second_tax_rate, IUser $user) {
      if($time_records || $expenses) {
        switch($sum_by) {
          case Invoice::INVOICE_SETTINGS_SUM_ALL:
            return $this->sumAllRecords($time_records, $expenses, $first_tax_rate, $second_tax_rate);
          case Invoice::INVOICE_SETTINGS_SUM_ALL_BY_TASK:
            return $this->sumGroupedByTask($time_records, $expenses, $first_tax_rate, $second_tax_rate);
          case Invoice::INVOICE_SETTINGS_SUM_ALL_BY_PROJECT:
            return $this->sumGroupedByProject($time_records, $expenses, $first_tax_rate, $second_tax_rate);
          case Invoice::INVOICE_SETTINGS_SUM_ALL_BY_JOB_TYPE:
            return $this->sumGroupedByJobType($time_records, $expenses, $first_tax_rate, $second_tax_rate);
          case Invoice::INVOICE_SETTINGS_KEEP_AS_SEPARATE:
            return $this->keepSeparated($time_records, $expenses, $first_tax_rate, $second_tax_rate);
          default:
            throw new InvalidParamError('sum_by', $sum_by);
        } // switch
      } else {
        return null;
      } // if
    } // prepareItemsForInvoice

    /**
     * Sum all records as a single line
     *
     * @param TimeRecord[] $timerecords
     * @param Expense[] $expenses
     * @param TaxRate $first_tax_rate
     * @param TaxRate $second_tax_rate
     * @return array
     */
    private function sumAllRecords($timerecords, $expenses, $first_tax_rate, $second_tax_rate) {
      $items = $timerecord_ids = $expenses_ids = array();
      $total_time = $total_expense = 0;

      $is_identical = $unit_cost = TimeRecords::isIdenticalJobRate($timerecords);

      foreach ($timerecords as $timerecord) {
        $job_type = $timerecord->getJobType();
        $timerecord_ids[] = $timerecord->getId();
        if ($timerecord->getValue() > 0 && $job_type instanceof JobType) {
          if($is_identical) {
            $total_time += $timerecord->getValue();
          } else {
            $time_record_project = $timerecord->getProject();

            $total_time = 1;
            $unit_cost += $job_type->getHourlyRateFor($time_record_project) * $timerecord->getValue();
          } // if
        } // if
      } // foreach

      if($total_time > 0) {
        if($this->object instanceof TrackingReport) {
          if($is_identical) {
            $description = lang('Total of :hours logged', array('hours' => $total_time));
          } else {
            $description = lang('Total time logged');
          }//if
        } else {
          $description = $this->object->getVerboseType() . ':' . $this->object->getName();
        } // if

        $items[] = array(
          'description' => $description,
          'unit_cost' => $unit_cost,
          'quantity' => $total_time,
          'subtotal' => $unit_cost * $total_time,
          'total' => $unit_cost * $total_time,
          'first_tax_rate_id' =>  $first_tax_rate instanceof TaxRate ? $first_tax_rate->getId() : $first_tax_rate,
          'second_tax_rate_id' => $second_tax_rate instanceof TaxRate ? $second_tax_rate->getId() : $second_tax_rate,
          'time_record_ids' => $timerecord_ids,
        );
      } // if

      if(is_foreachable($expenses)) {
        foreach ($expenses as $expense) {
          if ($expense->getValue() > 0) {
            $total_expense += $expense->getValue();
            $expenses_ids[] = $expense->getId();
          } // if
        } // foreach
      } // if

      if($total_expense > 0) {
        $items[] = array(
          'description' => lang('Other expenses'),
          'unit_cost' => $total_expense,
          'quantity' => 1,
          'subtotal' => $total_expense,
          'total' => $total_expense,
          'first_tax_rate_id' => $first_tax_rate instanceof TaxRate ? $first_tax_rate->getId() : $first_tax_rate,
          'second_tax_rate_id' => $second_tax_rate instanceof TaxRate ? $second_tax_rate->getId() : $second_tax_rate,
          'expenses_ids' => $expenses_ids,
        );
      } // if

      return $items;
    } // sumAllRecords

    /**
     * Sum all records by task
     *
     * @param TimeRecord[] $time_records
     * @param Expense[] $expenses
     * @param TaxRate $first_tax_rate
     * @param TaxRate $second_tax_rate
     * @return array
     */
    private function sumGroupedByTask($time_records, $expenses, $first_tax_rate, $second_tax_rate) {
      $items = array();

      list($tasks, $projects) = $this->getParentDataMapsForRecords($time_records);

      if($time_records && is_foreachable($time_records)) {
        $grouped_records = array();

        $job_types = JobTypes::getIdNameMap(null, JOB_TYPE_INACTIVE);

        foreach($time_records as $time_record) {
          $parent_type = $time_record->getParentType();
          $parent_id = $time_record->getParentId();

          $parent_key = "{$parent_type}-{$parent_id}";

          if(!isset($grouped_records[$parent_key])) {
            if($time_record->getParentType() == 'Task') {
              $description = Invoices::generateTaskDescription(array(
                'job_type' => isset($job_types[$time_record->getJobTypeId()]) ? $job_types[$time_record->getJobTypeId()] : '',
                'task_id' => $tasks[$parent_id]['task_id'],
                'task_summary' => $tasks[$parent_id]['name'],
                'project_name' => $tasks[$parent_id]['project_name'],
              ));
            } elseif($time_record->getParentType() == 'Project') {
              $description = Invoices::generateProjectDescription(array(
                'name' => $projects[$parent_id]['name'],
              ));
            } else {
              $description = $time_record->getParent() instanceof ApplicationObject ? $time_record->getParent()->getName() : lang('Unknown record');
            } // if

            $grouped_records[$parent_key] = array(
              'description' => $description,
              'time_records' => array(),
            );
          } // if

          $grouped_records[$parent_key]['time_records'][] = $time_record;
        } // foreach

        // Prepare items based on grouped records
        foreach($grouped_records as $group) {
          $this->sumGroupedTimeRecords($items, $group['description'], $group['time_records'], $first_tax_rate, $second_tax_rate);
        } // foreach
      } // if

      $this->sumExpenses($items, $expenses, $first_tax_rate, $second_tax_rate);

      return $items;
    } // sumGroupedByTask

    /**
     * Get detail maps that we need in order to properly format descriptions
     *
     * @param TimeRecord[] $time_records
     * @return array
     */
    private function getParentDataMapsForRecords($time_records) {
      $project_ids = $task_ids = array();

      foreach($time_records as $time_record) {
        if($time_record->getParentType() == 'Task') {
          $task_ids[] = $time_record->getParentId();
        } else {
          $project_ids[] = $time_record->getParentId();
        } // if
      } // foreach

      $task_ids = array_unique($task_ids);

      $tasks = array();

      if(count($task_ids)) {
        $task_rows = DB::execute('SELECT id, project_id, name, integer_field_1 FROM ' . TABLE_PREFIX . 'project_objects WHERE id IN (?) AND type = ? AND state >= ?', $task_ids, 'Task', STATE_ARCHIVED);

        if($task_rows) {
          $task_rows->setCasting(array(
            'id' => DBResult::CAST_INT,
          ));

          $task_project_ids = array();

          foreach($task_rows as $task_row) {
            $tasks[$task_row['id']] = array(
              'name' => $task_row['name'],
              'task_id' => $task_row['integer_field_1'],
              'project_id' => $task_row['project_id'],
            );

            $task_project_ids[] = $task_row['project_id'];
          } // if
        } // if

        $task_project_ids = DB::executeFirstColumn('SELECT DISTINCT(project_id) FROM ' . TABLE_PREFIX . 'project_objects WHERE id IN (?) AND type = ?', $task_ids, 'Task');
        if($task_project_ids && is_foreachable($task_project_ids)) {
          $project_ids = array_merge($project_ids, $task_project_ids);
        } // if
      } // if

      $project_ids = array_unique($project_ids);

      $projects = array();

      if($project_ids) {
        $project_rows = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'projects WHERE id IN (?) AND state >= ?', $project_ids, STATE_ARCHIVED);
        if($project_rows) {
          foreach($project_rows as $project_row) {
            $projects[(integer) $project_row['id']] = array(
              'name' => $project_row['name'],
            );
          } // foreach
        } // if
      } // if

      foreach($tasks as $task_id => $task_details) {
        $project_id = $task_details['project_id'];

        $tasks[$task_id]['project_name'] = isset($projects[$project_id]) ? $projects[$project_id]['name'] : lang('Unknown');
      } // foreach

      return array($tasks, $projects, JobTypes::getIdNameMap());
    } // getParentDataMapsForRecords

    /**
     * Sum all records by project
     *
     * @param TimeRecord[] $time_records
     * @param Expense[] $expenses
     * @param TaxRate $first_tax_rate
     * @param TaxRate $second_tax_rate
     * @return array
     */
    private function sumGroupedByProject($time_records, $expenses, $first_tax_rate, $second_tax_rate) {
      $items = array();

      if($time_records && is_foreachable($time_records)) {
        $grouped_records = array();

        $category_names = Categories::getIdNameMap(null, 'ProjectCategory');
        $client_names = Companies::getIdNameMap(null, STATE_ARCHIVED);

        foreach($time_records as $time_record) {
          $time_record_parent = $time_record->getParent();

          if($time_record_parent instanceof Project || $time_record_parent instanceof Task) {
            $time_record_project = $time_record_parent instanceof Task ? $time_record_parent->getProject() : $time_record_parent;

            if($time_record_project instanceof Project) {
              $project_id = $time_record_project->getId();

              if(!isset($grouped_records[$project_id])) {
                $grouped_records[$project_id] = array(
                  'description' => Invoices::generateProjectDescription(array(
                    'name' => $time_record_project->getName(),
                    'category' => $time_record_project->getCategoryId() && isset($category_names[$time_record_project->getCategoryId()]) ? $category_names[$time_record_project->getCategoryId()] : '',
                    'client' => $time_record_project->getCompanyId() && isset($client_names[$time_record_project->getCompanyId()]) ? $client_names[$time_record_project->getCompanyId()] : '',
                  )),
                  'time_records' => array(),
                );
              } // if

              $grouped_records[$project_id]['time_records'][] = $time_record;
            } // if
          } // if
        } // foreach

        foreach($grouped_records as $group) {
          $this->sumGroupedTimeRecords($items, $group['description'], $group['time_records'], $first_tax_rate, $second_tax_rate);
        } // foreach
      } // if

      $this->sumExpenses($items, $expenses, $first_tax_rate, $second_tax_rate);

      return $items;
    } // sumGroupedByProject

    /**
     * Sum all records by job type
     *
     * @param TimeRecord[] $time_records
     * @param Expense[] $expenses
     * @param TaxRate $first_tax_rate
     * @param TaxRate $second_tax_rate
     * @return array
     */
    function sumGroupedByJobType($time_records, $expenses, $first_tax_rate, $second_tax_rate) {
      $items = array();

      $grouped = TimeRecords::groupByJobType($time_records);

      foreach ($grouped as $job_type_name => $records) {
        $total_time = 0;
        $time_record_ids = array();
        $is_identical = $unit_cost = TimeRecords::isIdenticalJobRate($records);

        foreach($records as $record) {
          if ($record->getValue() > 0) {
            if($is_identical) {
              $total_time += $record->getValue();
            } else {
              $job_type = $record->getJobType();

              $total_time = 1;
              $unit_cost += $job_type->getHourlyRateFor($record->getProject()) * $record->getValue();
            } // if

            $time_record_ids[] = $record->getId();
          } // if
        } // foreach

        if($total_time > 0) {
          $items[] = array(
            'description' => Invoices::generateJobTypeDescription(array(
              'job_type' => $job_type_name,
            )),
            'unit_cost' => $unit_cost,
            'quantity' => $total_time,
            'subtotal' => $unit_cost * $total_time,
            'total' => $unit_cost * $total_time,
            'first_tax_rate_id' => $first_tax_rate instanceof TaxRate ? $first_tax_rate->getId() : $first_tax_rate,
            'second_tax_rate_id' => $second_tax_rate instanceof TaxRate ? $second_tax_rate->getId() : $second_tax_rate,
            'time_record_ids' => $time_record_ids,
          );
        } // if
      } // foreach

      $this->sumExpenses($items, $expenses, $first_tax_rate, $second_tax_rate);

      return $items;
    } // sumGroupedByJobType

    /**
     * Keep all records at a single line
     *
     * @param TimeRecord[] $timerecords
     * @param Expense[] $expenses
     * @param TaxRate $first_tax_rate
     * @param TaxRate $second_tax_rate
     * @return array
     */
    function keepSeparated($timerecords, $expenses, $first_tax_rate, $second_tax_rate) {
      $items = array();
      $total_time = 0;

      foreach($timerecords as $timerecord) {
        if ($timerecord->getValue() > 0) {
          $job_type = $timerecord->getJobType();

          $time_record_parent = $timerecord->getParent();

          if($time_record_parent instanceof Project) {
            $time_record_project = $time_record_parent;
          } else {
            $time_record_project = $timerecord->getProject();
          } // if

          $items[] = array(
            'description' => Invoices::generateIndividualDescription(array(
              'job_type_or_category' => $job_type->getName(),
              'record_summary' => $timerecord->getSummary(),
              'record_date' => $timerecord->getRecordDate()->formatDateForUser(null, 0),
              'parent_task_or_project' => $time_record_parent instanceof Task ? '#' . $time_record_parent->getTaskId() . ': ' . $time_record_parent->getName() : $time_record_parent->getName(),
              'project_name' => $time_record_project->getName()
            )),
            'unit_cost' => $job_type->getHourlyRateFor($time_record_project),
            'quantity' => $timerecord->getValue(),
            'subtotal' => $job_type->getHourlyRateFor($time_record_project) * $timerecord->getValue(),
            'total' => $job_type->getHourlyRateFor($time_record_project) * $total_time,
            'first_tax_rate_id' => $first_tax_rate instanceof TaxRate ? $first_tax_rate->getId() : $first_tax_rate,
            'second_tax_rate_id' => $second_tax_rate instanceof TaxRate ? $second_tax_rate->getId() : $second_tax_rate,
            'time_record_ids' => array($timerecord->getId()),
          );
        }//if
      } // foreach

      //loop throught expenses
      foreach ($expenses as $expense) {
        if ($expense->getValue() > 0) {
          $expense_parent = $expense->getParent();

          $items[] = array(
            'description' => Invoices::generateIndividualDescription(array(
              'job_type_or_category' => $expense->getCategoryName(),
              'record_summary' => $expense->getSummary(),
              'record_date' => $expense->getRecordDate()->formatDateForUser(null, 0),
              'parent_task_or_project' => $expense_parent instanceof Task ? '#' . $expense_parent->getTaskId() . ': ' . $expense_parent->getName() : $expense_parent->getName(),
              'project_name' => $expense->getProject()->getName()
            )),
            'unit_cost' => $expense->getValue(),
            'quantity' => 1,
            'subtotal' => $expense->getValue(),
            'total' => $expense->getValue(),
            'first_tax_rate_id' => $first_tax_rate instanceof TaxRate ? $first_tax_rate->getId() : $first_tax_rate,
            'second_tax_rate_id' => $second_tax_rate instanceof TaxRate ? $second_tax_rate->getId() : $second_tax_rate,
            'expenses_ids' => array($expense->getId()),
          );
        }//if
      }//foreach

      return $items;
    } // keepSeparated

    /**
     * Sum expenses and add them as a single item
     *
     * @param array $items
     * @param string $group_description
     * @param TimeRecord[] $time_records
     * @param TaxRate $first_tax_rate
     * @param TaxRate $second_tax_rate
     */
    function sumGroupedTimeRecords(&$items, $group_description, $time_records, $first_tax_rate, $second_tax_rate) {
      $total_time = 0;
      $time_record_ids = array();

      // Get identical cost, or FALSE if time records have different hourly rate
      $unit_cost = TimeRecords::isIdenticalJobRate($time_records);

      if($unit_cost === false) {
        $total_time = 1;

        $unit_cost = 0;

        foreach($time_records as $time_record) {
          $unit_cost += $time_record->calculateExpense();
          $time_record_ids[] = $time_record->getId();
        } // foreach
      } else {
        foreach($time_records as $time_record) {
          $total_time += $time_record->getValue();
          $time_record_ids[] = $time_record->getId();
        } // foreach
      } // if

      if($total_time > 0) {
        $items[] = array(
          'description' => $group_description,
          'unit_cost' => $unit_cost,
          'quantity' => $total_time,
          'subtotal' => $unit_cost * $total_time,
          'total' => $unit_cost * $total_time,
          'first_tax_rate_id' => $first_tax_rate instanceof TaxRate ? $first_tax_rate->getId() : $first_tax_rate,
          'second_tax_rate_id' => $second_tax_rate instanceof TaxRate ? $second_tax_rate->getId() : $second_tax_rate,
          'time_record_ids' => $time_record_ids,
        );
      } // if
    } // sumGroupedTimeRecords

    /**
     * Sum expenses and add them as a single item
     *
     * @param array $items
     * @param Expense[] $expenses
     * @param TaxRate $first_tax_rate
     * @param TaxRate $second_tax_rate
     */
    function sumExpenses(&$items, $expenses, $first_tax_rate, $second_tax_rate) {
      if($expenses && is_foreachable($expenses)) {
        $total_expense = 0;
        $expenses_ids = array();

        foreach ($expenses as $expense) {
          if($expense->getValue() > 0) {
            $total_expense += $expense->getValue();
            $expenses_ids[] = $expense->getId();
          } // if
        } // foreach

        if($total_expense > 0) {
          $items[] = array(
            'description' => lang('Other expenses'),
            'unit_cost' => $total_expense,
            'quantity' => 1,
            'subtotal' => $total_expense,
            'total' => $total_expense,
            'first_tax_rate_id' => $first_tax_rate instanceof TaxRate ? $first_tax_rate->getId() : $first_tax_rate,
            'second_tax_rate_id' => $second_tax_rate instanceof TaxRate ? $second_tax_rate->getId() : $second_tax_rate,
            'expenses_ids' => $expenses_ids,
          );
        } // if
      } // if
    } // sumExpenses

  }