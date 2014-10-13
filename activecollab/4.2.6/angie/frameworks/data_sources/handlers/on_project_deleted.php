<?php

  /**
   * Data Sources module on_project_deleted event handler
   *
   * @package angie.frameworks.data_sources
   * @subpackage handlers
   */

  /**
   * on_project_deleted handler implemenation
   *
   * @param Object $object
   * @return null
   */
  function data_sources_handle_on_project_deleted($object) {
    if($object instanceof Project) {
      $data_source_mappings_tbl = TABLE_PREFIX . 'data_source_mappings';
      if(DB::tableExists($data_source_mappings_tbl)) {
        DB::execute("DELETE FROM $data_source_mappings_tbl WHERE project_id = ?", $object->getId());
      } //if
    } // if
  } // data_sources_handle_on_project_deleted
