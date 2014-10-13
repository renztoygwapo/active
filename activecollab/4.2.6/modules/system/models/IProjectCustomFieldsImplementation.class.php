<?php

  /**
   * Project custom fields helper implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class IProjectCustomFieldsImplementation extends ICustomFieldsImplementation {

    /**
     * Return value map for given field
     *
     * @param $field_name
     */
    function getValueMap($field_name) {
      $map = array();

      $projects_table = TABLE_PREFIX . 'projects';
      $field_name = DB::escapeFieldName($field_name);

      $rows = DB::execute("SELECT DISTINCT $field_name AS 'value' FROM $projects_table WHERE state >= ? ORDER BY $field_name", STATE_ARCHIVED);

      if($rows) {
        foreach($rows as $row) {
          if($row['value']) {
            $map[$row['value']] = $row['value'];
          } // if
        } // foreach
      } // if

      return $map;
    } // getValueMap

    /**
     * Return list of values that we can use to aid the user (offered for auto completion)
     *
     * @param string $field_name
     * @return array
     */
    function getValueAid($field_name) {
      $aid = array();

      $projects_table = TABLE_PREFIX . 'projects';
      $field_name = DB::escapeFieldName($field_name);

      $rows = DB::execute("SELECT DISTINCT $field_name AS 'value' FROM $projects_table WHERE state >= ? ORDER BY $field_name", STATE_ARCHIVED);

      if($rows) {
        foreach($rows as $row) {
          if($row['value'] && trim($row['value'])) {
            $aid[] = trim($row['value']);
          } // if
        } // foreach
      } // if

      return count($aid) ? $aid : null;
    } // getValueAid

  }