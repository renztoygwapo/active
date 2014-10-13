<?php

  /**
   * Update activeCollab 3.2.3 to activeCollab 3.2.4
   *
   * @package activeCollab.upgrade
   * @subpackage scripts
   */
  class Upgrade_0052 extends AngieApplicationUpgradeScript {

    /**
     * Initial system version
     *
     * @var string
     */
    protected $from_version = '3.2.3';

    /**
     * Final system version
     *
     * @var string
     */
    protected $to_version = '3.2.4';

    /**
     * Return script actions
     *
     * @return array
     */
    function getActions() {
      return array(
        'fixCodeSnippets' => 'Rebuild code snippet parent values',
      );
    } // getActions

    /**
     * Rebuild code snippet parent values
     *
     * @return bool|string
     */
    function fixCodeSnippets() {
      try {
        $code_snippets_table = TABLE_PREFIX . 'code_snippets';

        DB::execute("ALTER TABLE $code_snippets_table CHANGE parent_type parent_type VARCHAR(50) NULL DEFAULT NULL");

        $rows = DB::execute("SELECT id, parent_type, parent_id FROM $code_snippets_table WHERE parent_type = '0' OR parent_type IS NULL AND parent_id > '0'");
        if($rows) {
          $rows->setCasting(array(
            'id' => DBResult::CAST_INT,
            'parent_id' => DBResult::CAST_INT,
          ));

          try {
            DB::beginWork('Updating parent type of code snippets @ ' . __CLASS__);

            foreach($rows as $row) {
              $parent_id = DB::escape($row['parent_id']);
              $look_for = DB::escape('%placeholder-type="code" placeholder-object-id="' . $row['id'] . '"%');

              if(DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . "projects WHERE id = $parent_id AND overview LIKE $look_for")) {
                $parent_type = 'Project';
              } elseif(DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . "project_requests WHERE id = $parent_id AND body LIKE $look_for")) {
                $parent_type = 'ProjectRequest';
              } elseif($this->isModuleInstalled('notebooks') && DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . "notebook_pages WHERE id = $parent_id AND body LIKE $look_for")) {
                $parent_type = 'NotebookPage';
              } elseif($queried_type = DB::executeFirstCell('SELECT type FROM ' . TABLE_PREFIX . "project_objects WHERE id = $parent_id AND body LIKE $look_for")) {
                $parent_type = $queried_type;
              } elseif($queried_type = DB::executeFirstCell('SELECT type FROM ' . TABLE_PREFIX . "comments WHERE id = $parent_id AND body LIKE $look_for")) {
                $parent_type = $queried_type;
              } elseif($this->isModuleInstalled('documents') && DB::executeFirstCell('SELECT COUNT(id) FROM ' . TABLE_PREFIX . "documents WHERE id = $parent_id AND body LIKE $look_for")) {
                $parent_type = 'Document';
              } else {
                $parent_type = null;
              } // if

              if($parent_type) {
                DB::execute("UPDATE $code_snippets_table SET parent_type = ? WHERE id = ?", $parent_type, $row['id']);
              } // if
            } // foreach

            DB::commit('Parent type for code snippets has been updated @ ' . __CLASS__);
          } catch(Exception $e) {
            DB::rollback('Faield to update parent type for code snippets @ ' . __CLASS__);
            throw $e;
          } // try
        } // if
      } catch(Exception $e) {
        return $e->getMessage();
      } // try

      return true;
    } // fixCodeSnippets

  }