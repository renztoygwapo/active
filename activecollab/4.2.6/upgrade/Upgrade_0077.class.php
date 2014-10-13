<?php

  /**
   * Update activeCollab 3.3.13 to activeCollab 3.3.14
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0077 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.3.13';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.3.14';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'updateUserAgentColumn' => 'Update user agent column',
        'deleteCategoriesWithoutParent' => 'Delete categories without a parent',
      );
    } // getActions

    /**
     * Update sue agent column
     *
     * @return bool|string
     */
    function updateUserAgentColumn() {
      if (!$this->isModuleInstalled('invoicing')) {
        return true;
      } // if

      try {
        DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'invoice_item_templates CHANGE `description` `description` TEXT NULL');
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // updateUserAgentColumn

    /**
     * Delete categories without a parent
     *
     * @return bool|string
     */
    function deleteCategoriesWithoutParent() {
      $categories_table = TABLE_PREFIX . 'categories';
      $projects_table = TABLE_PREFIX . 'projects';
      $category_ids_for_delete = array();

      try {
        // find all the categories which Project parent has state = 0
        $categories_with_soft_deleted_parent = DB::execute("SELECT $categories_table.id FROM $categories_table, $projects_table WHERE $categories_table.parent_type = 'Project' AND $categories_table.parent_id = $projects_table.id AND $projects_table.state = 0");
        if($categories_with_soft_deleted_parent instanceof DBResult) {
          foreach ($categories_with_soft_deleted_parent as $category_id) {
            $category_ids_for_delete[] = $category_id['id'];
          } //foreach
        } //if

        // find all the categories which Project parent does not exist
        $categories_with_deleted_parent = DB::execute("SELECT $categories_table.id FROM $categories_table LEFT JOIN $projects_table ON $categories_table.parent_id = $projects_table.id WHERE $categories_table.parent_type = 'Project' AND $projects_table.id IS NULL");
        if($categories_with_deleted_parent instanceof DBResult) {
          foreach ($categories_with_deleted_parent as $category_id) {
            $category_ids_for_delete[] = $category_id['id'];
          } //foreach
        } //if

        if (!empty($category_ids_for_delete)) {
          DB::execute("DELETE FROM $categories_table WHERE id IN (?)", $category_ids_for_delete);
        } //if

      } catch(Exception $e) {
        return $e->getMessage();
      } //try

      return true;
    } // deleteCategoriesWithoutParent

  }