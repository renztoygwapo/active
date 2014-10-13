<?php
  
  /**
   * on_daily event handler
   * 
   * @package angie.frameworks.email
   * @subpackage handlers
   */

  /**
   * Handle on_daily event
   */
  function email_handle_on_daily() {
  	AngieApplication::mailer()->sendDigest(OutgoingMessages::findByMethod(AngieMailerDelegate::SEND_DAILY));
    MailingActivityLogs::cleanUp();
    
    //if there is conflicts and send_on_daily is configured
    if(ConfigOptions::getValue('conflict_notifications_delivery') == IncomingMail::CONFLICT_NOTIFY_ON_DAILY && IncomingMails::countConflicts() > 0) {
      AngieApplication::notifications()
        ->notifyAbout('email/conflict_notify_on_daily', null)
        ->setConflictsNum(IncomingMails::countConflicts())
        ->setConflictPageUrl(Router::assemble('incoming_email_admin_conflict'))
        ->sendToAdministrators(true);
    }//if
    
  } // email_handle_on_daily