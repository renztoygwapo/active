<?php

  // Build on top of framework controller
  AngieApplication::useController('fw_activity_logs_admin', ACTIVITY_LOGS_FRAMEWORK);

  /**
   * Activity logs controller
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage controllers
   */
  class ActivityLogsAdminController extends FwActivityLogsAdminController {
    
    /**
     * Rebuild invoicing entries
     */
    function rebuild_invoicing() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        try {
          DB::beginWork('Updating invoicing activity logs @ ' . __CLASS__);
          
          $invoices = DB::execute('SELECT id, status, based_on_type, based_on_id, created_on, created_by_id, created_by_name, created_by_email, date_field_2 AS issued_on, integer_field_1 AS issued_by_id, varchar_field_3 AS issued_by_name, varchar_field_4 AS issued_by_email, closed_on, closed_by_id, closed_by_name, closed_by_email FROM ' . TABLE_PREFIX . 'invoice_objects WHERE type = ? ORDER BY created_on', 'Invoice');
          if($invoices) {
            $invoices->setCasting(array(
              'id' => DBResult::CAST_INT, 
              'status' => DBResult::CAST_INT, 
              'based_on_id' => DBResult::CAST_INT, 
              'created_by_id' => DBResult::CAST_INT, 
              'issued_by_id' => DBResult::CAST_INT, 
              'closed_by_id' => DBResult::CAST_INT, 
            ));
            
            $batch = DB::batchInsert(TABLE_PREFIX . 'activity_logs', array('subject_type', 'subject_id', 'subject_context', 'action', 'target_type', 'target_id', 'created_on', 'created_by_id', 'created_by_name', 'created_by_email'));
            
            $update_payments = array();
            
            foreach($invoices as $invoice) {
              $invoice_context = "invoices:invoices/$invoice[id]";
              
              $batch->insert('Invoice', $invoice['id'], $invoice_context, 'invoice/created', $invoice['based_on_type'], $invoice['based_on_id'], $invoice['created_on'], $invoice['created_by_id'], $invoice['created_by_name'], $invoice['created_by_email']);
              
              if($invoice['status'] > INVOICE_STATUS_DRAFT) {
                $batch->insert('Invoice', $invoice['id'], $invoice_context, 'invoice/issued', $invoice['based_on_type'], $invoice['based_on_id'], $invoice['issued_on'], $invoice['issued_by_id'], $invoice['issued_by_name'], $invoice['issued_by_email']);
                
                if($invoice['status'] == INVOICE_STATUS_CANCELED) {
                  $batch->insert('Invoice', $invoice['id'], $invoice_context, 'invoice/canceled', $invoice['based_on_type'], $invoice['based_on_id'], $invoice['closed_on'], $invoice['closed_by_id'], $invoice['closed_by_name'], $invoice['closed_by_email']);
                } elseif($invoice['status'] == INVOICE_STATUS_PAID) {
                  $batch->insert('Invoice', $invoice['id'], $invoice_context, 'invoice/paid', $invoice['based_on_type'], $invoice['based_on_id'], $invoice['closed_on'], $invoice['closed_by_id'], $invoice['closed_by_name'], $invoice['closed_by_email']);
                  
                  $update_payments[] = $invoice['id'];
                } // if
              } // if
            } // foreach
            
            if(count($update_payments)) {
              $payments = DB::execute('SELECT id, type, parent_id, created_on, created_by_id, created_by_name, created_by_email FROM ' . TABLE_PREFIX . 'payments WHERE parent_type = ? AND parent_id IN (?)', 'Invoice', $update_payments);
              
              if($payments) {
                $payments->setCasting(array(
                  'id' => DBResult::CAST_INT, 
                  'parent_id' => DBResult::CAST_INT, 
                  'created_by_id' => DBResult::CAST_INT, 
                ));
                
                foreach($payments as $payment) {
                  $batch->insert($payment['type'], $payment['id'], "invoices:invoices/$payment[parent_id]/payments/$payment[id]", 'payment/created', 'Invoice', $payment['parent_id'], $payment['created_on'], $payment['created_by_id'], $payment['created_by_name'], $payment['created_by_email']);
                } // foreach
              } // if
            } // if
            
            $batch->done();
          } // if
          
          DB::commit('Invoicing activity log update @ ' . __CLASS__);
          
          $this->response->ok();
        } catch(Exception $e) {
          DB::rollback('Failed to update invoicing activity log @ ' . __CLASS__);
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // rebuild_invoicing
    
  }