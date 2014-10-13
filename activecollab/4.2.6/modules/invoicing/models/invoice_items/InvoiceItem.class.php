<?php

  /**
   * InvoiceItem class
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class InvoiceItem extends InvoiceObjectItem {

    // ---------------------------------------------------
    //  GETTERS AND SETTERS
    // ---------------------------------------------------

    /**
     * Set ID-s of related time records
     *
     * @param array $ids
     * @return boolean
     */
    function setTimeRecordIds($ids) {
      DB::beginWork('Setting TimeRecords for Invoice start');

      $execute = DB::execute('DELETE FROM ' . TABLE_PREFIX . 'invoice_related_records WHERE invoice_id = ? && item_id = ? && parent_type=?', $this->getParentId(), $this->getId(), 'TimeRecord');
      if($execute && !is_error($execute)) {
        if(is_foreachable($ids)) {
          $to_insert = array();
          $invoice_id = $this->getParentId();
          $item_id = $this->getId();

          foreach($ids as $id) {
            $id = (integer) $id;
            if($id && !isset($to_insert[$id])) {
              $to_insert[$id] = "($invoice_id, $item_id, $id, 'TimeRecord')";
            } // if
          } // foreach

          if(is_foreachable($to_insert)) {
            $execute = DB::execute('INSERT INTO ' . TABLE_PREFIX . 'invoice_related_records (invoice_id, item_id, parent_id, parent_type) VALUES ' . implode(', ', $to_insert));
            if(!$execute || is_error($execute)) {
              DB::rollback();
              return $execute;
            } // if
          } // if
        } // if

        DB::commit('Setting TimeRecords for Invoice end');
        return true;
      } else {
        DB::rollback('Setting TimeRecords for Invoice failed');
        return $execute;
      } // if
    } // setTimeRecordIds

    /**
     * Retrieve TimeRecordIds
     *
     * @return array
     */
    function getTimeRecordIds() {
      $execute = DB::execute('SELECT `parent_id` FROM ' . TABLE_PREFIX . 'invoice_related_records WHERE invoice_id = ? && item_id = ? && parent_type=?', $this->getParentId(), $this->getId(), 'TimeRecord');
      if ($execute && !is_error($execute)) {
        if (is_foreachable($execute)) {
          $time_record_ids = array();
          foreach ($execute as $time_record) {
            $time_record_ids[] = $time_record['parent_id'];
          } // foreach
          return $time_record_ids;
        } // if
        return null;
      } else {
        return $execute;
      } // if
    } // getTimeRecordIds


    /**
     * Set ID-s of related expenses
     *
     * @param array $ids
     * @return boolean
     */
    function setExpensesIds($ids) {
      DB::beginWork();

      $execute = DB::execute('DELETE FROM ' . TABLE_PREFIX . 'invoice_related_records WHERE invoice_id = ? && item_id = ? && parent_type = ?', $this->getParentId(), $this->getId(),'Expense');
      if($execute && !is_error($execute)) {
        if(is_foreachable($ids)) {
          $to_insert = array();
          $invoice_id = $this->getParentId();
          $item_id = $this->getId();

          foreach($ids as $id) {
            $id = (integer) $id;
            if($id && !isset($to_insert[$id])) {
              $to_insert[$id] = "($invoice_id, $item_id, $id, 'Expense')";
            } // if
          } // foreach

          if(is_foreachable($to_insert)) {
            $execute = DB::execute('INSERT INTO ' . TABLE_PREFIX . 'invoice_related_records (invoice_id, item_id, parent_id, parent_type) VALUES ' . implode(', ', $to_insert));
            if(!$execute || is_error($execute)) {
              DB::rollback();
              return $execute;
            } // if
          } // if
        } // if

        DB::commit();
        return true;
      } else {
        DB::rollback();
        return $execute;
      } // if
    } // setExpensesIds

    /**
     * Retrieve ExpensesIds
     *
     * @return array
     */
    function getExpensesIds() {
      $execute = DB::execute('SELECT `parent_id` FROM ' . TABLE_PREFIX . 'invoice_related_records WHERE invoice_id = ? && item_id = ? && parent_type = ?', $this->getParentId(), $this->getId(), 'Expense');
      if ($execute && !is_error($execute)) {
        if (is_foreachable($execute)) {
          $expenses_ids = array();
          foreach ($execute as $expense) {
            $expenses_ids[] = $expense['parent_id'];
          } // foreach
          return $expenses_ids;
        } // if
        return null;
      } else {
        return $execute;
      } // if
    } // if

  }