<?php

  /**
   * Notebook Page state implementation
   *
   * @package activeCollab.modules.notebooks
   * @subpackage models
   */
  class INotebookPageStateImplementation extends IStateImplementation {

    /**
     * Move object to trash
     *
     * @param boolean $trash_already_trashed
     */
    function trash($trash_already_trashed = false) {
      try {
        DB::beginWork('Moving notebook page to trash @ ' . __CLASS__);

        parent::trash($trash_already_trashed);

        $sub_pages = $this->object->getSubpages(STATE_TRASHED);
        if (is_foreachable($sub_pages)) {
          foreach ($sub_pages as $sub_page) {
            if ($sub_page instanceof IActivityLogs) {
              $sub_page->activityLogs()->gag();
            } // if
            $sub_page->state()->trash(true);
          } // foreach
        } // if

        DB::commit('Notebook page moved to trash @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to move notebook page to trash @ ' . __CLASS__);

        throw $e;
      } // try
    } // trash

    /**
     * Restore object from trash
     */
    function untrash() {
      try {
        DB::beginWork('Restoring notebook page from a trash @ ' . __CLASS__);

        parent::untrash();

        $sub_pages = $this->object->getSubpages(STATE_TRASHED);
        if (is_foreachable($sub_pages)) {
          foreach ($sub_pages as $sub_page) {
            if ($sub_page->getState() == STATE_TRASHED) {
              if ($sub_page instanceof IActivityLogs) {
                $sub_page->activityLogs()->gag();
              } // if
              $sub_page->state()->untrash();
            } // if
          } // foreach
        } // if

        DB::commit('Notebook page restored from a trash @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to restore notebook page from trash @ ' . __CLASS__);

        throw $e;
      } // try
    } // untrash

    /**
     * Mark object as deleted
     */
    function delete() {
      try {
        DB::beginWork('Deleting notebook page @ ' . __CLASS__);

        parent::delete();

        $sub_pages = $this->object->getSubpages(STATE_TRASHED);
        if (is_foreachable($sub_pages)) {
          foreach ($sub_pages as $sub_page) {
            $sub_page->state()->delete();
          } // foreach
        } // if

        DB::commit('Notebook page deleted @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to delete notebook page @ ' . __CLASS__);

        throw $e;
      } // try
    } // delete

    /**
     * Mark object as archived
     */
    function archive() {
      try {
        DB::beginWork('Archiving notebook page @ ' . __CLASS__);

        parent::archive();

        $sub_pages = $this->object->getSubpages(STATE_VISIBLE);
        if (is_foreachable($sub_pages)) {
          foreach ($sub_pages as $sub_page) {
            if ($sub_page->getState() == STATE_VISIBLE) {
              if ($sub_page instanceof IActivityLogs) {
                $sub_page->activityLogs()->gag();
              } // if
              $sub_page->state()->archive();
            } // if
          } // foreach
        } // if

        DB::commit('Notebook page archived @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to archive notebook page @ ' . __CLASS__);

        throw $e;
      } // try
    } // archive

    /**
     * Mark object as unarchived
     */
    function unarchive() {
      try {
        DB::beginWork('Unarchiving notebook page @ ' . __CLASS__);

        parent::unarchive();

        $sub_pages = $this->object->getSubpages(STATE_ARCHIVED);
        if (is_foreachable($sub_pages)) {
          foreach ($sub_pages as $sub_page) {
            if ($sub_page->getState() == STATE_ARCHIVED) {
              if ($sub_page instanceof IActivityLogs) {
                $sub_page->activityLogs()->gag();
              } // if
              $sub_page->state()->unarchive();
            } // if
          } // foreach
        } // if

        DB::commit('Notebook page unarchived @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to unarchive notebook page @ ' . __CLASS__);

        throw $e;
      } // try
    } // unarchive

  }