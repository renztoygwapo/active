<?php

  class IncomingMessageAutoRespondActivityLog extends IncomingMailingActivityLog {
    
    /**
     * Return name of this log entry
     * 
     * @return string
     */
    function getName() {
      return lang("Auto respond message ':subject' from ':mailbox_name' mailbox deleted.", array('mailbox_name' => $this->getAdditionalProperty('mailbox_name'), 'subject' => $this->getAdditionalProperty('subject')));
    } // getName
    
    
    /**
     * Log activity and save it to database
     * 
     * @param MailboxManagerEmail $email
     * @param IncomingMailbox $mailbox
     * @param string $error_message
     * @param boolean $save
     */
    function log(MailboxManagerEmail $email, IncomingMailbox $mailbox = null, $error_message = null, $save = true) {
      $from = $email->getAddress('from');
      $from_email = $from['email'];
      $from_name = $from['name'] ? $from['name'] : $from['email'];
      
      if($from_email) {
        $from_user = Users::findByEmail($from_email, true);
        if(!$from_user instanceof IUser) {
          if (!is_valid_email($from_email)) {
            $from_email = substr_replace($from_email, "uknown-domain.com", strpos($from_email, "@")+1);;
          } // if

          $from_user = new AnonymousUser($from_name, $from_email);
        }//if
      }//if
      
      $to = $email->getAddress('to');
      $to_email = $to['email'];
      
      if($to_email) {
        $to_user = Users::findByEmail($to_email, true);
        if(!$to_user instanceof IUser) {
          $to_user = new AnonymousUser($to_email, $to_email);
        }//if
      }//if
      
      parent::log($from_user, $to_user, array(
        'mailbox_name' => $mailbox instanceof IncomingMailbox ? $mailbox->getDisplayName() : 'CLI',
        'error_message' => $error_message,
        'subject' => $email->getSubject(),
        'mailbox_id' => $mailbox instanceof IncomingMailbox ? $mailbox->getId() : null,
      ), $save);
    } // log
    
    /**
     * We have details to show to the user about this particular mailing log
     * 
     * @return boolean
     */
    function hasDetails() {
      return true;
    } // hasDetails
    
    /**
     * Render log entry details for view in flyout
     * 
     * @param Smarty $smarty
     * @return string
     */
    function renderDetails(Smarty $smarty) {
      return parent::renderDetails($smarty, get_view_path('incoming_auto_respond', 'activity_log_details', EMAIL_FRAMEWORK));
    } // renderDetails
    
  }