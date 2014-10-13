<?php

/**
 * Update activeCollab 3.0.5 to activeCollab 3.0.6
 *
 * @package activeCollab.upgrade
 * @subpackage scripts
 */
class Upgrade_0029 extends AngieApplicationUpgradeScript {

  /**
   * Initial system version
   *
   * @var string
   */
  protected $from_version = '3.0.5';

  /**
   * Final system version
   *
   * @var string
   */
  protected $to_version = '3.0.6';

  /**
   * Return script actions
   *
   * @return array
   */
  function getActions() {
    return array(
      'updateConfigOptions' => 'Update configuration options',
      'updateSubtaskTimeRecords' => 'Update subtask time records',
      'cleanSubtaskEstimates' => 'Clean up subtask estimates',
      'updateCurrenciesTable' => 'Update currencies table',
      'dropNotebookPageVisibility' => 'Clean up notebook page model',
    );
  } // getActions

  /**
   * Update configuration options
   *
   * @return boolean
   */
  function updateConfigOptions() {
    try {
      DB::beginWork('Updating configuration options @ ' . __CLASS__);

      DB::execute('INSERT INTO ' . TABLE_PREFIX . 'config_options (name, module, value) VALUES (?, ?, ?)', 'notifications_from_force', 'system', serialize(true));
      DB::execute('DELETE FROM ' . TABLE_PREFIX . 'config_options WHERE name = ?', 'mailing_empty_return_path');

      DB::commit('Updated configuration options @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::rollback('Failed to update configuration options @ ' . __CLASS__);
      return $e->getMessage();
    } // try

    return true;
  } // updateConfigOptions

  /**
   * Update subtask time records
   *
   * @return boolean
   */
  function updateSubtaskTimeRecords() {

    $time_records_table = TABLE_PREFIX . 'time_records';
    $subtasks_table = TABLE_PREFIX . 'subtasks';
    $project_objects_table = TABLE_PREFIX . 'project_objects';

    try {
      DB::beginWork('Upgrading subtask time records @ ' . __CLASS__);

      if(DB::executeFirstCell("SELECT COUNT(*) FROM " . TABLE_PREFIX . "modules WHERE name = 'tracking'")) {
        $subtask_ids = DB::executeFirstColumn("SELECT DISTINCT parent_id FROM $time_records_table WHERE parent_type = 'ProjectObjectSubtask'");

        if($subtask_ids) {
          $rows = DB::execute("SELECT $subtasks_table.id, $subtasks_table.parent_type, $subtasks_table.parent_id, $subtasks_table.body AS 'subtask_body', $project_objects_table.project_id, $project_objects_table.name AS 'parent_name' FROM $subtasks_table, $project_objects_table WHERE $subtasks_table.parent_type = $project_objects_table.type AND $subtasks_table.parent_id = $project_objects_table.id AND $subtasks_table.id IN (?)", $subtask_ids);
          if($rows) {
            foreach($rows as $row) {
              if($row['parent_type'] == 'TodoList') {
                $summary = 'TodoList Title: '.$row['parent_name'].'; Subtask Title: '.$row['subtask_body'].'; Summary: ';
                DB::execute("UPDATE $time_records_table SET parent_type = 'Project', parent_id = ?, summary = CONCAT_WS(' ',?,summary) WHERE parent_type = 'ProjectObjectSubtask' AND parent_id = ?", $row['project_id'], $summary, $row['id']);
              } else {
                $summary = 'Task Title: '.$row['parent_name'].'; Subtask Title: '.$row['subtask_body'].'; Summary: ';
                DB::execute("UPDATE $time_records_table SET parent_type = 'Task', parent_id = ?, summary = CONCAT_WS(' ',?,summary) WHERE parent_type = 'ProjectObjectSubtask' AND parent_id = ?", $row['parent_id'], $summary, $row['id']);
              } // if
            } // foreach
          } // if
        } // if
      } // if

      DB::commit('Subtask time records upgraded @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::commit('Failed to upgrade subtask time records @ ' . __CLASS__);
      return $e->getMessage();
    } // try

    return true;
  } // updateSubtaskTimeRecords

  /**
   * Update subtask estimates
   *
   * @return boolean
   */
  function cleanSubtaskEstimates() {
    try {
      if(DB::executeFirstCell("SELECT COUNT(*) FROM " . TABLE_PREFIX . "modules WHERE name = 'tracking'")) {
        DB::execute('DELETE FROM ' . TABLE_PREFIX . 'estimates WHERE parent_type = ?', 'ProjectObjectSubtask');
      } // if
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // cleanSubtaskEstimates

  /**
   * Update currencies table
   *
   * @return boolean
   */
  function updateCurrenciesTable() {
    try {
      DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'currencies ADD UNIQUE INDEX name (name)');
      DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'currencies ADD UNIQUE INDEX code (code)');
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // updateCurrenciesTable

  /**
   * Remove visibility fields from notebook page model
   *
   * @return boolean
   */
  function dropNotebookPageVisibility() {
    try {
      if(DB::executeFirstCell("SELECT COUNT(*) FROM " . TABLE_PREFIX . "modules WHERE name = 'notebooks'")) {
        DB::execute("ALTER TABLE " . TABLE_PREFIX . "notebook_pages DROP visibility");
        DB::execute("ALTER TABLE " . TABLE_PREFIX . "notebook_pages DROP original_visibility");
      } // if
    } catch(Exception $e) {
      return $e->getMessage();
    } // try

    return true;
  } // dropNotebookPageVisibility

}