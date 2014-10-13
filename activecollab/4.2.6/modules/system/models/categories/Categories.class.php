<?php

  /**
   * Categories class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class Categories extends FwCategories {

    /**
     * Return unique category names in given projects
     *
     * @param array $project_ids
     * @param array $types
     */
    static function getUniqueNamesInProjects($project_ids, $types) {
      return DB::executeFirstColumn('SELECT DISTINCT name FROM ' . TABLE_PREFIX . 'categories WHERE parent_type = ? AND parent_id IN (?) AND type IN (?) ORDER BY name', 'Project', $project_ids, $types);
    } // getUniqueNamesInProjects

  }