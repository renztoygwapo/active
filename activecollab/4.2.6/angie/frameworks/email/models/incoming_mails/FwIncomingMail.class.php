<?php

  /**
   * Framework level incoming email message implementation
   *
   * @package angie.frameworks.email
   * @subpackage models
   */
  abstract class FwIncomingMail extends BaseIncomingMail implements IRoutingContext,IReadOnly {
    
    //email priority constants
    const IM_PRIORITY_HIGHEST = 1;
    const IM_PRIORITY_HIGH = 2;
    const IM_PRIORITY_NORMAL = 3;
    const IM_PRIORITY_LOW = 4;
    const IM_PRIORITY_LOWEST = 5;
        
    //incoming mail status codes
    const STATUS_OK = 1;
    const STATUS_ERROR = 0;

    //conflict notify
    const CONFLICT_NOTIFY_DO_NOT = 0; // "Don't Notify Administrators About New Conflicts";
    const CONFLICT_NOTIFY_INSTANTLY = 1; // "Instantly Notify Administrators About New Conflicts";
    const CONFLICT_NOTIFY_ON_DAILY = 2; // "Notify Administrators About New Conflicts Once a Day";

    //Automatically disable mailbox on successive failure attempts
    const AUTO_DISABLE_MAILBOX_OFF = 0;
    const AUTO_DISABLE_MAILBOX_ON = 1;

    /**
     * Return Delivered-To from header
     *
     * @return string
     */
    function getDeliveredToFromHeader() {
      $raw_headers = explode("\n", $this->getHeaders());
      if (is_foreachable($raw_headers)) {
        foreach ($raw_headers as $raw_header) {
          $raw_header = trim($raw_header);

          //Delivered-To
          if (preg_match('/^(Delivered-To[^:]*): (.*)/is', $raw_header, $results)) {
            $header_name = trim(isset($results[1]) ? $results[1] : null);
            $header_value = trim(isset($results[2]) ? $results[2] : null);
            if ($header_name) {
              return $header_value;
            } // if
          } // if
        }//foreach
      } //if
      return null;
    }//getDeliveredToFromHeader
    
    /**
     * Return true if "add new comment" action can be added to conflict resolution
     * 
     * @return boolean
     */
    function canAddCommentAction() {
      if($this->isReplyToNotification()) {
        $parent = $this->getParent();

        if($parent instanceof ApplicationObject) {
          if($parent instanceof IState && $parent->getState() <= STATE_TRASHED) {
            return false;
          } // if

          return $parent instanceof IComments;
        } // if
      } // if

      return false;
    } // canAddCommentAction
    
    /**
     * Return true if email is reply
     * 
     * @return boolean
     * 
     */
    function isReplyToNotification() {
      return $this->getIsReplayToNotification();
    }//isReplyToNotification
    
    
    /**
     * Return original email from raw additional values
     * 
     * @return string
     * 
     */
    function getOriginalFromEmail() {
      return $this->getAdditionalProperty('from_email_original');
    }//getOriginalEmail
    
    /**
     * Set original email in raw additional values
     * 
     * @return string
     * 
     */
    function setOriginalFromEmail($value) {
      return $this->setAdditionalProperty('from_email_original', $value);
    }//setOriginalFromEmail
    
    /**
     * Retrieve all attachments
     * 
     * @return array
     */
    function getAttachments() { 
      return IncomingMailAttachments::find(array(
        "conditions" => array('mail_id = ?', $this->getId()),
      ));
    } // getAttachments

    /**
     * Return attachment size
     *
     * @return int
     */
    function getAttachmentsSize() {
      $table_name = TABLE_PREFIX . 'incoming_mail_attachments';
      return (int) DB::executeFirstCell("SELECT sum(file_size) FROM " . $table_name . " WHERE mail_id=?", $this->getId());
    } //getAttachmentsSize
    
    /**
     * Return incoming mail status
     */
    function getStatus() {
      return parent::getStatus() ? parent::getStatus() : lang('Unknown conflict status');  
    }//getStatus
    
    
    /**
     * Return mailbox
     *
     * @return IncomingMailbox
     */
    function getMailbox() {
      return IncomingMailboxes::findById($this->getIncomingMailboxId());
    } //getMailbox
    
    /**
     * Delete object
     * 
     * @return boolean
     */
    function delete() {
      $attachments = $this->getAttachments();
      
      if (is_foreachable($attachments)) {
        foreach ($attachments as $attachment) {
        	$attachment->delete();
        } // foreach
      } // if
      
      return parent::delete();
    } // delete
    
    /**
     * Return User who receive email 
     * 
     *  @return Object
     */
    function getToUser() {
      $to_email = unserialize($this->getToEmail());
      if($to_email) {
        $user_name = $to_email['name'];
        $user_email = $to_email['email'];
        $user = Users::findByEmail($user_email, true);
        if(!$user instanceof IUser) {
          $user = new AnonymousUser($user_name,$user_email);
        }//if
      }//if
      return $user;
    } // getToUser
        
    /**
     * Return bcc as array
     */
    function getBccTo() {
      return unserialize(parent::getBccTo());
    } //getBccTo
    
    /**
     * Return cc as array
     */
    function getCcTo() {
      return unserialize(parent::getCcTo());
    } //getCcTo
    
    /**
     * Return reply_to as array
     */
    function getReplyTo() {
      return unserialize(parent::getReplyTo());
    } //getReplyTo
    
    /**
     * Return to as array
     */
    function getTo() {
      return unserialize(parent::getToEmail());
    } //getTo
    
    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
      $result = parent::describe($user, $detailed, $for_interface);

      $result['subject'] = $this->getSubject();
      $result['from'] = $this->getCreatedByEmail();
      $result['mailbox'] = $this->getMailbox() instanceof IncomingMailbox ? $this->getMailbox()->getDisplayName() : lang('Unknown');
      $result['status'] = $this->getStatus() ? $this->getStatus() : lang('Unknown conflict status');
      $result['urls']['import_url'] = $this->getImportUrl();
      if($detailed) {
        $result['conflicts'] = IncomingMails::countConflicts();
      }//is
      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false, $for_interface = false) {
      throw new NotImplementedError(__METHOD__);
    } // describeForApi
    
    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'incoming_mail';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('incoming_mail_id' => $this->getId());
    } // getRoutingContextParams
    
    
    /**
     * Return edit mail URL
     *
     * @param void
     * @return string
     */
    function getEditUrl() {
      return Router::assemble('incoming_email_edit_mail', array(
        'incoming_mail_id' => $this->getId()
      ));
    } // getEditUrl
    
    /**
     * Return delete mailbox URL
     *
     * @param void
     * @return string
     */
    function getDeleteUrl() {
      return Router::assemble('incoming_email_delete_mail', array(
        'incoming_mail_id' => $this->getId(),
      ));      
    } // getDeleteUrl
    
    /**
     * Return delete mailbox URL
     *
     * @param void
     * @return string
     */
    function getMassEditUrl() {
      return Router::assemble('incoming_mail_mass_conflict_resolution');      
    } // getMassEditUrl
    
    /**
     * Return import mailbox URL
     *
     * @param void
     * @return string
     */
    function getImportUrl() {
      return Router::assemble('incoming_email_import_mail', array(
        'incoming_mail_id' => $this->getId(),
      ));
    } // getImportUrl
    
  }
