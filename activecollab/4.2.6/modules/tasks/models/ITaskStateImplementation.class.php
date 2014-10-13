<?php

/**
 * task state implementation
 *
 * @package activeCollab.modules.tasks
 * @subpackage models
 */
class ITaskStateImplementation extends IProjectObjectStateImplementation {

  /**
   * Move object to archive
   */
  function archive() {
    try {
      DB::beginWork('Moving task to archive @ ' . __CLASS__);

      parent::archive();

      if (AngieApplication::isModuleLoaded('tracking')) {
        Expenses::archiveByParent($this->object);
        TimeRecords::archiveByParent($this->object);
      } // if

      DB::commit('Task moved to archive @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::rollback('Failed to move task to archive @ ' . __CLASS__);

      throw $e;
    } // try
  } // archive

  /**
   * Move object to archive
   */
  function unarchive() {
    try {
      DB::beginWork('Restoring task from archive @ ' . __CLASS__);

      parent::unarchive();

      if (AngieApplication::isModuleLoaded('tracking')) {
        Expenses::unarchiveByParent($this->object);
        TimeRecords::unarchiveByParent($this->object);
      } // if

      DB::commit('Task restored rom archive @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::rollback('Failed to restor task from archive @ ' . __CLASS__);

      throw $e;
    } // try
  } // unarchive


  /**
   * Move object to trash
   *
   * @param boolean $trash_already_trashed
   */
  function trash($trash_already_trashed = false) {
    try {
      DB::beginWork('Moving task to trash @ ' . __CLASS__);

      parent::trash($trash_already_trashed);

      // trash expenses & time records
      if (AngieApplication::isModuleLoaded('tracking')) {
        Expenses::trashByParent($this->object);
        TimeRecords::trashByParent($this->object);
      } // if

      DB::commit('Task moved to trash @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::rollback('Failed to move task to trash @ ' . __CLASS__);

      throw $e;
    } // try
  } // trash

  /**
   * Restore object from trash
   */
  function untrash() {
    try {
      DB::beginWork('Restoring task from a trash @ ' . __CLASS__);

      parent::untrash();

      // untrash expenses & time records
      if (AngieApplication::isModuleLoaded('tracking')) {
        Expenses::untrashByParent($this->object);
        TimeRecords::untrashByParent($this->object);
      } // if

      DB::commit('Task restored from a trash @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::rollback('Failed to restore task from trash @ ' . __CLASS__);

      throw $e;
    } // try
  } // untrash

  /**
   * Mark object as deleted
   */
  function delete() {
    try {
      DB::beginWork('Deleting task @ ' . __CLASS__);

      parent::delete();

      // untrash expenses & time records
      if (AngieApplication::isModuleLoaded('tracking')) {
        Expenses::deleteByParent($this->object, true);
        TimeRecords::deleteByParent($this->object, true);
      } // if

      DB::commit('Task deleted @ ' . __CLASS__);
    } catch(Exception $e) {
      DB::rollback('Failed to delete task @ ' . __CLASS__);

      throw $e;
    } // try
  } // delete
}