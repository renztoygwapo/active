<?php

  /**
   * Framework level data source mapping management implementation
   *
   * @package angie.frameworks.data_sources
   * @subpackage models
   */
  class FwDataSourceMappings extends BaseDataSourceMappings {
  
    // Put custom methods here
    const BASECAMP_EXTERNAL_TYPE_USER = 'bc_people';
    const BASECAMP_EXTERNAL_TYPE_PROJECT = 'bc_project';
    const BASECAMP_EXTERNAL_TYPE_TODO_LIST = 'bc_todo_list';
    const BASECAMP_EXTERNAL_TYPE_TODO = 'bc_todo';
    const BASECAMP_EXTERNAL_TYPE_DISCUSSION = 'bc_discussion';
    const BASECAMP_EXTERNAL_TYPE_FILE = 'bc_file';
    const BASECAMP_EXTERNAL_TYPE_TEXT_DOCUMENT = 'bc_text_document';
    const BASECAMP_EXTERNAL_TYPE_COMMENT = 'bc_comment';

    /**
     * Add or edit mapping table
     *
     * @param $source
     * @param ApplicationObject $created_object
     * @param Project $project
     * @param null $external_id
     * @param null $external_type
     * @return bool
     */
    static function add($source, ApplicationObject $created_object, $project = null, $external_id = null, $external_type = null) {
      $mapping = new DataSourceMapping();

      $params = array(
        'source_type' => get_class($source),
        'source_id' => method_exists($source, 'getId') ? $source->getId() : 0,
        'parent_id' => $created_object->getId(),
        'parent_type' => get_class($created_object),
        'project_id' => $project instanceof Project ? $project->getId() : null,
        'external_id' => $external_id ? $external_id : 0,
        'external_type' => $external_type ? $external_type : null
      );
      $mapping->setAttributes($params);
      return $mapping->save();
    } //add

    /**
     * Return map by parent_id
     *
     * @param ApplicationObject $parent
     * @return DataSourceMapping
     */
    static function findByParent(ApplicationObject $parent) {
      return DataSourceMappings::find(array(
        'conditions' => array('parent_type = ? AND parent_id = ?', $parent->getId(), $parent->getBaseTypeName())
      ));
    } //findByParentId

    /**
     * Return map by source and external object
     *
     * @param $external_type
     * @param $external_id
     * @param $source
     * @return DBResult
     */
    static function findObjectByExternalAndSource($external_type, $external_id, $source) {
      $map =  DataSourceMappings::find(array(
        'conditions' => array('external_type = ? AND external_id = ? AND source_type >= ? AND source_id >= ?', $external_type, ($external_id ? $external_id : 0), get_class($source), (method_exists($source, 'getId') ? $source->getId() : 0)),
        'one' => true
      ));

      if($map instanceof DataSourceMapping) {
        return $map->getParent();
      } //if
      return null;
    } //findByParentId

  }