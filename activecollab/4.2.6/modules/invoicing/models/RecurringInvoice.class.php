<?php

  /**
   * Create invoices from recurring profiles
   * 
   * @package current.modules.invoicing
   * @subpackage models
   */
  class RecurringInvoice {
  
    /**
     * Go throught all profiles for today and create invoices
     */
    static function createFromRecurringProfile() {
      $recurring_profiles = RecurringProfiles::findMatchingForDay();
    
      //create invoice
      if(is_foreachable($recurring_profiles)) {
        foreach ($recurring_profiles as $key => $recurring_profile) {
          //create invoice
          self::createInvoice($recurring_profile);
        }//foreach
      }//if
    }//createFromRecurringProfile

    /**
     * Create invoice from recurring profile
     *
     * @param RecurringProfile $recurring_profile
     * @return bool|void
     */
    static function createInvoice(RecurringProfile $recurring_profile) {
      $new_invoice = new Invoice();
      $invoice_data = array(
        'based_on_type'  => get_class($recurring_profile),
        'based_on_id'	=> $recurring_profile->getId(),      
        'company_id' => $recurring_profile->getCompanyId(),
        'project_id' => $recurring_profile->getProjectId(),
        'note' => $recurring_profile->getNote(),
        'language_id'	=> $recurring_profile->getLanguageId(),
        'currency_id'	=> $recurring_profile->getCurrencyId(),
        'company_address' => $recurring_profile->getCompanyAddress(),
        'private_note' => $recurring_profile->getPrivateNote(),
        'created_on' => $recurring_profile->getNextTriggerOn(),
        'allow_payments' => $recurring_profile->getAllowPayments(),
        'purchase_order_number' => $recurring_profile->getPurchaseOrderNumber()

      );
      
      
      if(is_foreachable($recurring_profile->getItems())) {
        $invoice_data['items'] = array();
        foreach($recurring_profile->getItems() as $item) {
          $invoice_data['items'][] = array(
            'description' => $item->getDescription(),
            'unit_cost' => $item->getUnitCost(),
            'quantity' => $item->getQuantity(),
            'first_tax_rate_id' => $item->getFirstTaxRateId(),
            'second_tax_rate_id' => $item->getSecondTaxRateId(),
            'position' => $item->getPosition(),
            'total' => $item->getTotal(),
            'subtotal' => $item->getSubtotal()
          );
        } // foreach
      }//if
      
      $new_invoice->setAttributes($invoice_data);
      $new_invoice->setState(STATE_VISIBLE);
      
      if($recurring_profile->getAutoIssue()) {
        $new_invoice->setNumber($new_invoice->generateInvoiceId());
        Invoices::incrementDateInvoiceCounters();  
        $new_invoice->setStatus(INVOICE_STATUS_ISSUED, $recurring_profile->getCreatedBy());
        $new_invoice->setIssuedToId($recurring_profile->getRecipientId());
        
        if($recurring_profile->getInvoiceDueAfter() || $recurring_profile->getInvoiceDueAfter() === 0) {
          $add_days = '+' . $recurring_profile->getInvoiceDueAfter() . ' days';
          $new_invoice->setDueOn(new DateValue($add_days));
        } else {
          $new_invoice->setDueOn(new DateValue('+7 days'));
        }//if
        
      } else {
        $new_invoice->setStatus(INVOICE_STATUS_DRAFT, $recurring_profile->getCreatedBy());
      }//if
      
      $new_invoice->save();
      
      $new_invoice->setItems($invoice_data['items']);

      $new_invoice->recalculate(true);
      
      if($recurring_profile->getAutoIssue()) {
        $send_to = array();
        $recipient = $recurring_profile->getRecipient();
        if($recipient instanceof IUser) {
           $send_to[] = $recipient; // Notify recipient
        }//if
        
        if(is_foreachable($send_to)) {
          AngieApplication::notifications()
            ->notifyAbout('invoicing/invoice_issued', $new_invoice)
            ->sendToUsers($send_to, true);
        } // if
        
        // Send email notification to the financial managers
        AngieApplication::notifications()
          ->notifyAbout('invoicing/invoice_generated_via_recurring_profile', $new_invoice)
          ->setProfile($recurring_profile)
          ->sendToFinancialManagers(true);
      } else {
        AngieApplication::notifications()
          ->notifyAbout('invoicing/draft_invoice_created_via_recurring_profile', $new_invoice)
          ->setProfile($recurring_profile)
          ->sendToFinancialManagers(true);
      } // if
      
      //increase triggered number value, set next trigger date and arhive this profile if is finished
      $recurring_profile->increaseTriggeredNumber();
      $recurring_profile->setLastTriggeredOn(new DateValue());
     
      $recurring_profile->setNextTriggerOnDate($recurring_profile->getFrequency());
     
      
      if($recurring_profile->getTriggeredNumber() == $recurring_profile->getOccurrences()) {
        $recurring_profile->setState(STATE_ARCHIVED);
        
        // Notify financial managers that recurring profile is archived
        AngieApplication::notifications()
          ->notifyAbout('invoicing/recurring_profile_archived', $recurring_profile)
          ->setProfile($recurring_profile)
          ->sendToFinancialManagers(true);
      }//if
      
      return $recurring_profile->save();
    }//createInvoice

    /**
     * Get language
     *
     * @return Language
     */
    function getLanguage() {
      if($this->language === false) {
        $this->language = Languages::findById($this->getLanguageId());
        if (!($this->language instanceof Language)) {
          $this->language = Languages::getBuiltIn();
        } // if
      } // if
      return $this->language;
    } // getLanguage

    
  }