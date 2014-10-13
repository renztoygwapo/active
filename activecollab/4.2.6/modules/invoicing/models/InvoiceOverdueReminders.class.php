<?php

  /**
   * Send invoice overdue reminders
   * 
   * @package current.modules.invoicing
   * @subpackage models
   */
  final class InvoiceOverdueReminders {
  
    /**
     * Find all overdue invoices and send reminders
     */
    public static function send() {
      if(ConfigOptions::getValue('invoice_overdue_reminders_enabled')) {
        $exclude_company_ids = ConfigOptions::getValue('invoice_overdue_reminders_dont_send_to');

        $overdue_invoices = self::findOverdueInvoices($exclude_company_ids);
        if(is_foreachable($overdue_invoices)) {
          foreach($overdue_invoices as $overdue_invoice) {

            // Send first reminder
            if(!($overdue_invoice->getReminderSentOn() instanceof DateTimeValue)) {
              $send_first_after = ConfigOptions::getValue('invoice_overdue_reminders_send_first');
              $message = ConfigOptions::getValue('invoice_overdue_reminders_first_message');

              if(strtotime("+$send_first_after day", $overdue_invoice->getDueOn()->getTimestamp()) <= strtotime(DateValue::now()->toMySQL())) {
                self::sendReminder($overdue_invoice, $message);
              } // if

            // Re-send reminder
            } else {
              $send_every = ConfigOptions::getValue('invoice_overdue_reminders_send_every');
              $message = ConfigOptions::getValue('invoice_overdue_reminders_first_message');

              if(strtotime("+$send_every day", $overdue_invoice->getReminderSentOn()->getTimestamp()) <= strtotime(DateValue::now()->toMySQL())) {
                self::sendReminder($overdue_invoice, $message);
              } // if

              // Send escalation reminder
              if(ConfigOptions::getValue('invoice_overdue_reminders_escalation_enabled')) {
                $escalations = ConfigOptions::getValue('invoice_overdue_reminders_escalation_messages');
                if(is_foreachable($escalations)) {
                  foreach($escalations as $escalation) {
                    $send_escalated_after = array_var($escalation, 'send_escalated');
                    $message = array_var($escalation, 'escalated_message');

                    if(strtotime("+$send_escalated_after day", $overdue_invoice->getDueOn()->getTimestamp()) == strtotime(DateValue::now()->toMySQL())) {
                      self::sendReminder($overdue_invoice, $message);
                    } // if
                  } // if
                } // if
              } // if
            } // if
          } // foreach
        } // if
      } // if
    } // send

    /**
     * Find overdue invoices
     *
     * @param array $exclude_company_ids
     * @return array
     */
    private static function findOverdueInvoices($exclude_company_ids) {
      $today = new DateTimeValue();
      if(is_foreachable($exclude_company_ids)) {
        return Invoices::find(array(
          'conditions' => array('status = ? AND date_field_1 < ? AND company_id NOT IN (?)', INVOICE_STATUS_ISSUED, $today, implode(',', $exclude_company_ids)),
          'order' => 'date_field_1 DESC',
        ));
      } else {
        return Invoices::find(array(
          'conditions' => array('status = ? AND date_field_1 < ?', INVOICE_STATUS_ISSUED, $today),
          'order' => 'date_field_1 DESC',
        ));
      } // if
    } // findOverdueInvoices

    /**
     * Send overdue invoice reminder
     *
     * @param Invoice $overdue_invoice
     * @param string $message
     * @return null
     */
    private static function sendReminder(Invoice $overdue_invoice, $message) {
      try {
        DB::beginWork('Send invoice overdue reminder @ ' . __CLASS__);

        $overdue_invoice->setReminderSentOn(new DateTimeValue());
        $overdue_invoice->save();

        $recipient_id = $overdue_invoice->getIssuedToId();
        if($recipient_id) {
          $recipient = Users::findById($recipient_id);
          if($recipient instanceof User) {
            AngieApplication::notifications()
              ->notifyAbout('invoicing/invoice_reminder', $overdue_invoice)
              ->setReminderMessage($message)
              ->sendToUsers($recipient);
          } // if
        } // if

        DB::commit('Invoice overdue reminder sent @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to send invoice overdue reminder @ ' . __CLASS__);
        throw $e;
      } // try
    } // sendReminder
    
  }