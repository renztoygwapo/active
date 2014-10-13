<?php

/**
 * Notebooks state implementation
 *
 * @package activeCollab.modules.notebooks
 * @subpackage models
 */
class INotebookStateImplementation extends IProjectObjectStateImplementation {

  /**
   * Move object to trash
   *
   * @param boolean $trash_already_trashed
   */
  function trash($trash_already_trashed = false) {
    try {
      DB::beginWork('Moving notebook to trash @ ' . __CLASS__);

      parent::trash($trash_already_trashed);

      $first_level_notebook_pages = NotebookPages::findByNotebook($this->object, STATE_ARCHIVED);
      if (is_foreachable($first_level_notebook_pages)) {
        foreach ($first_level_notebook_pages as $first_level_notebook_page) {
          if ($first_level_notebook_page instanceof IActivityLogs) {
            $first_level_notebook_page->activityLogs()->gag();
          } // if
          $first_level_notebook_page->state()->trash(true);
        } // foreach
      } // if

      DB::commit('Notebook moved to trash @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::rollback('Failed to move notebook to trash @ ' . __CLASS__);

      throw $e;
    } // try
  } // trash

  /**
   * Restore object from trash
   */
  function untrash() {
    try {
      DB::beginWork('Restoring notebook from a trash @ ' . __CLASS__);

      parent::untrash();

      $first_level_notebook_pages = NotebookPages::findByNotebook($this->object, STATE_TRASHED);
      if (is_foreachable($first_level_notebook_pages)) {
        foreach ($first_level_notebook_pages as $first_level_notebook_page) {
          if ($first_level_notebook_page->getState() == STATE_TRASHED) {
            if ($first_level_notebook_page instanceof IActivityLogs) {
              $first_level_notebook_page->activityLogs()->gag();
            } // if
            $first_level_notebook_page->state()->untrash();
          } // if
        } // foreach
      } // if

      DB::commit('Notebook restored from a trash @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::rollback('Failed to restore notebook from trash @ ' . __CLASS__);

      throw $e;
    } // try
  } // untrash

  /**
   * Mark object as deleted
   */
  function delete() {
    try {
      DB::beginWork('Deleting notebook @ ' . __CLASS__);

      parent::delete();

      $first_level_notebook_pages = NotebookPages::findByNotebook($this->object, STATE_TRASHED);
      if (is_foreachable($first_level_notebook_pages)) {
        foreach ($first_level_notebook_pages as $first_level_notebook_page) {
          $first_level_notebook_page->state()->delete();
        } // foreach
      } // if

      DB::commit('Notebook deleted @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::rollback('Failed to delete notebook @ ' . __CLASS__);

      throw $e;
    } // try
  } // delete

  /**
   * Mark object as archived
   */
  function archive() {
    try {
      DB::beginWork('Archive notebook @ ' . __CLASS__);

      parent::archive();

      $first_level_notebook_pages = NotebookPages::findByNotebook($this->object, STATE_VISIBLE);
      if (is_foreachable($first_level_notebook_pages)) {
        foreach ($first_level_notebook_pages as $first_level_notebook_page) {
          if ($first_level_notebook_page instanceof IActivityLogs) {
            $first_level_notebook_page->activityLogs()->gag();
          } // if
          $first_level_notebook_page->state()->archive();
        } // foreach
      } // if

      DB::commit('Notebook archived @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::rollback('Failed to archive notebook @ ' . __CLASS__);

      throw $e;
    } // try
  } // archive

  /**
   * Mark object as archived
   */
  function unarchive() {
    try {
      DB::beginWork('Unarchive notebook @ ' . __CLASS__);

      parent::unarchive();

      $first_level_notebook_pages = NotebookPages::findByNotebook($this->object, STATE_ARCHIVED);
      if (is_foreachable($first_level_notebook_pages)) {
        foreach ($first_level_notebook_pages as $first_level_notebook_page) {
          if ($first_level_notebook_page->getState() == STATE_ARCHIVED) {
            if ($first_level_notebook_page instanceof IActivityLogs) {
              $first_level_notebook_page->activityLogs()->gag();
            } // if
            $first_level_notebook_page->state()->unarchive();
          } // if
        } // foreach
      } // if

      DB::commit('Notebook unarchived @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::rollback('Failed to unarchive notebook @ ' . __CLASS__);

      throw $e;
    } // try
  } // unarchive
}