<?php

  // We need admin controller
  AngieApplication::useController('admin');

  /**
   * Invoice overdue reminders admin controller
   *
   * @package activeCollab.modules.invoicing
   * @subpackage controllers
   */
  class InvoiceOverdueRemindersAdminController extends AdminController {
    
    /**
     * Set invoice overdue reminders
     * 
     * @param void
     * @return void
     */
    function index() {
      if($this->request->isAsyncCall()) {
        $reminders_data = $this->request->post('reminders', ConfigOptions::getValue(array(
          'invoice_overdue_reminders_enabled',
          'invoice_overdue_reminders_send_first',
          'invoice_overdue_reminders_send_every',
          'invoice_overdue_reminders_first_message',
          'invoice_overdue_reminders_escalation_enabled',
          'invoice_overdue_reminders_escalation_messages',
          'invoice_overdue_reminders_dont_send_to'
        )));

        $this->smarty->assign('reminders_data', $reminders_data);

        if($this->request->isSubmitted()) {
          try {
            $send_first_after = (integer) array_var($reminders_data, 'invoice_overdue_reminders_send_first');
            $send_every_after = (integer) array_var($reminders_data, 'invoice_overdue_reminders_send_every');

            // Validate when to send overdue reminders
            if(!$send_first_after || !$send_every_after) {
              throw new Error(lang('When to send overdue reminder values must be 1 or more days.'));
            } // if

            $escalation_messages_enabled = (boolean) array_var($reminders_data, 'invoice_overdue_reminders_escalation_enabled');
            $escalation_messages = array_var($reminders_data, 'invoice_overdue_reminders_escalation_messages');

            // Validate escalations
            if($escalation_messages_enabled) {
              if(is_foreachable($escalation_messages)) {
                $minimum_overdue = $send_first_after;

                foreach($escalation_messages as $escalation) {
                  $send_escalated_after = (integer) array_var($escalation, 'send_escalated');

                  if($minimum_overdue >= $send_escalated_after) {
                    throw new Error(lang('Each escalation should have higher overdue value then previous defined overdue value.'));
                  } // if

                  $minimum_overdue = $send_escalated_after;
                } // foreach
              } // if
            } // if

            ConfigOptions::setValue(array(
              'invoice_overdue_reminders_enabled' => (boolean) array_var($reminders_data, 'invoice_overdue_reminders_enabled'),
              'invoice_overdue_reminders_send_first' => $send_first_after,
              'invoice_overdue_reminders_send_every' => $send_every_after,
              'invoice_overdue_reminders_first_message' => array_var($reminders_data, 'invoice_overdue_reminders_first_message'),
              'invoice_overdue_reminders_escalation_enabled' => $escalation_messages_enabled,
              'invoice_overdue_reminders_escalation_messages' => $escalation_messages,
              'invoice_overdue_reminders_dont_send_to' => array_var($reminders_data, 'invoice_overdue_reminders_dont_send_to')
            ));

            $this->response->ok();
          } catch(Exception $e) {
            $this->response->exception($e);
          } // try
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // index
    
  }