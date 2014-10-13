<?php

  /**
   * Project label implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ProjectLabel extends Label {
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'projects_admin_label';
    } // getRoutingContext

    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('label_id' => $this->getId());
    } // getRoutingContextParams

    /**
     * Return even names prefix
     *
     * @return string
     */
    function getEventNamesPrefix() {
      return 'project_label';
    } // getEventNamesPrefix

    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------

    /**
     * Remove this label from database
     *
     * @return boolean
     */
    function delete() {
      try {
        DB::beginWork('Deleting label @ ' . __CLASS__);

        parent::delete();
        DB::execute('UPDATE ' . TABLE_PREFIX . 'projects SET label_id = NULL WHERE label_id = ?', $this->getId());

        DB::commit('Label deleted @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to delete label @ ' . __CLASS__);
        throw $e;
      } // try

      return true;
    } // delete

  }