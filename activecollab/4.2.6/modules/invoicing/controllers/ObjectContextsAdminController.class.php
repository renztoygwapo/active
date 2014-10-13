<?php

  // Build on top of framework controller
  AngieApplication::useController('fw_object_contexts_admin', ENVIRONMENT_FRAMEWORK);

  /**
   * Object contexts controller
   * 
   * @package activeCollab.modules.discussions
   * @subpackage controllers
   */
  class ObjectContextsAdminController extends FwObjectContextsAdminController {
    
    /**
     * Rebuild task entries
     */
    function rebuild_invoicing() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        try {
          DB::beginWork('Updating invoicing contexts @ ' . __CLASS__);

          $invoice_objects = DB::execute('SELECT id, type FROM  ' . TABLE_PREFIX . 'invoice_objects');
          if (is_foreachable($invoice_objects)) {
            $batch = DB::batchInsert(TABLE_PREFIX . 'object_contexts', array('parent_type', 'parent_id', 'context'));
            foreach ($invoice_objects as $invoice_object) {
              $lowercase_type = strtolower($invoice_object['type']);

              switch ($lowercase_type) {
                case 'invoice':
                  $batch->insert('Invoice', $invoice_object['id'], "invoices:invoices/$invoice_object[id]");
                  break;
                case 'quote':
                  $batch->insert('Quote', $invoice_object['id'], "quotes:quotes/$invoice_object[id]");
                  break;
                case 'recurringprofile':
                  $batch->insert('RecurringProfile', $invoice_object['id'], "recurring_profiles:recurring_profiles/$invoice_object[id]");
                  break;
              } // switch
            } // foreach
            $batch->done();
          } // if
          
          DB::commit('Invoicing contexts updated @ ' . __CLASS__);
          
          $this->response->ok();
        } catch(Exception $e) {
          DB::rollback('Failed to update invoicing contexts @ ' . __CLASS__);
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // rebuild_invoicing
    
  }