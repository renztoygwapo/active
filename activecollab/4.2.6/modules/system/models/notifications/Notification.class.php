<?php

  /**
   * Notification class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class Notification extends FwNotification {

    /**
     * Notify notification to financial managers
     *
     * @param boolean $skip_sending_queue
     * @param mixed $exclude_user
     * @throws NotImplementedError
     */
    function sendToFinancialManagers($skip_sending_queue = false, $exclude_user = null) {
      if(AngieApplication::isModuleLoaded('invoicing')) {
        $notify_people = null;

        if($this instanceof InvoicePaidNotification) {
          $notify_managers = ConfigOptions::getValue('invoice_notify_financial_managers'); //only for InvoicePaidNotification

          if($notify_managers == Invoice::INVOICE_NOTIFY_FINANCIAL_MANAGERS_ALL) {
            $notify_people = InvoiceObjects::findFinancialManagers($exclude_user);

            //$this->sendToUsers(InvoiceObjects::findFinancialManagers($exclude_user), $skip_sending_queue);
          } elseif ($notify_managers == Invoice::INVOICE_NOTIFY_FINANCIAL_MANAGERS_SELECTED) {
            $notify_manager_ids = ConfigOptions::getValue('invoice_notify_financial_manager_ids');
            if(is_foreachable($notify_manager_ids)) {
              $notify_people = array(); //check is user still financial manager

              foreach($notify_manager_ids as $user_id) {
                $user = DataObjectPool::get('User', $user_id);
                if($user instanceof User && $user->isFinancialManager()) {
                  if($exclude_user instanceof User && $exclude_user->getId() == $user->getId()) {
                    continue; // skip if user is exclude user
                  } // if

                  $notify_people[] = $user;
                } // if
              } // foreach
            } // if
          } // if
        } else {
          $notify_people = InvoiceObjects::findFinancialManagers();
        } // if

        if ($notify_people) {
          $this->sendToUsers($notify_people, $skip_sending_queue);
        } // if
      } else {
        throw new NotImplementedError(__METHOD__, 'Invoicing module is not installed');
      } // if
    } // sendToFinancialManagers

  }