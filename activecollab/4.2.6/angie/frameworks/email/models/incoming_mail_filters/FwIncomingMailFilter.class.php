<?php

  /**
   * Framework level incoming mail filter
   *
   * @package angie.frameworks.email
   * @subpackage models
   */
  abstract class FwIncomingMailFilter extends BaseIncomingMailFilter implements IRoutingContext {
   
    const FILTER_ENABLED = 1;
    const FILTER_DISABLED = 0;
    
    /**
     * Incoming mail
     * 
     * @var IncomingMail
     */
    protected $email;
    
    //rule constants
    const IM_FILTER_IS = "IS";
    const IM_FILTER_IS_NOT = "IS NOT";
    const IM_FILTER_HAS = "HAS";
    const IM_FILTER_HAS_NOT = "HAS NOT";
    const IM_FILTER_STARTS_WITH = "STARTS WITH";
    const IM_FILTER_ENDS_WITH = "ENDS WITH";
    const IM_FILTER_ANY = "Any";
    const IM_FILTER_ONLY_REGISTERED = "Only Registered";
    const IM_FILTER_ONLY_NOT_REGISTERED = "Only Not Registered";
    const IM_FILTER_IMPORTANT = "Important";
    const IM_FILTER_HAS_ATTACHMENTS = "Has Attachments";
    const IM_FILTER_HAS_NOT_ATTACHMENTS = "Doesn't Have Attachments";
    
    //action constants
    const ALLOW_FOR_PEOPLE_WHO_CAN = "allow_for_people_who_can";
    const ALLOW_FOR_EVERYONE = "allow_for_everyone";
    
    const CREATE_AS_SENDER = 'sender';
    const CREATE_AS_SPECIFIC_USER = 'specific_user';
    
    const USE_MESSAGE_PRIORITY = "use_message_priority";
    const USE_CUSTOM_PRIORITY = "use_custom_priority";

    const DUE_ON_EMPTY = 'Leave Empty';
    const DUE_ON_MESSAGE_RECEIVED = 'For the day when message is received';
    const DUE_ON_NEXT_BUSSINESS_DAY = 'For the next business day';

 
    /**
     * Check to see if incoming mail match this filter rules
     * 
     * @param $email
     */
    function match($email) {
      $this->email = $email;
      return $this->matchAttachments() && $this->matchBody() && $this->matchMailbox() && $this->matchPriority() && $this->matchSender() && $this->matchSubject() && $this->matchToEmail(); 
     } //if
    
    /**
     * Apply action to incoming mail
     * 
     * @params $email
     * @return true on success, Error object at failure
     */
    function apply() {
      $action_object = $this->getActionObject();
      return $action_object->doActions($this->email,$this->getActionParameters()); 
    }//apply
    
    /**
     * Try to match with subject
     */
    protected function matchSubject() {
      if($this->getSubject()) {
        return $this->matchField($this->email->getSubject(),$this->getSubjectType(),$this->getSubjectText());
      } //if
      return true;
    }//matchSubject
    
    /**
     * Try to match with body
     */
    protected function matchBody() {
      if($this->getBody()) {
        return $this->matchField($this->email->getBody(),$this->getBodyType(),$this->getBodyText());
      }//if
      return true;
    }//matchBody
    
    /**
     * Try to match with priority
     */
    protected function matchPriority() {
      if($this->getPriority()) {
        return $this->matchField($this->email->getPriority(),$this->getPriority());
      }//if
      return true;
    }//matchPriority
    
    /**
     * Try to match with attachment
     */
    protected function matchAttachments() {
      if($this->getAttachments()){
        return $this->matchField($this->email->getAttachments(),$this->getAttachments());
      }//if
      return true;
    }//matchAttachments

    /**
     * Try to match with sender
     */
    protected function matchSender() {
      if($this->getSender()) {
        $text = explode(",",$this->getSenderText());
        if(is_foreachable($text)) {
          foreach($text as $sender) {
            $tmp[] = trim($sender);
          }//foreach
        }//if
        $text = $tmp;
        return $this->matchField($this->email->getCreatedByEmail(),$this->getSenderType(),$text);
      }//if
      return true;
    }//matchSender
    
    /**
     * Try to match with to email
     */
    protected function matchToEmail() {
      if($this->getToEmail()) {
        $text = explode(",",$this->getToEmailText());
        if(is_foreachable($text)) {
          foreach($text as $to) {
            $tmp[] = trim($to);
          }//foreach
        }//if
        $text = $tmp;
        $to = $this->email->getTo();
        $to_email = $to['email'];
        return $this->matchField($to_email,$this->getToEmailType(),$text);
      }//if
      
      return true;
    }//matchToEmail
    
    
    /**
     * Try to match mailbox id
     */
    protected function matchMailbox() {
      if($this->getMailboxId()) {
        return in_array($this->email->getIncomingMailboxId(),$this->getMailboxId());
      }//if
      return true;
    }//matchMailbox
    
    /**
     * Match filed
     * 
     * @param string $email_field
     * @param string $type
     * @param string $text
     */
    protected function matchField($email_field, $type, $text = false) {
      $email_field = is_foreachable($email_field) ? $email_field : strtolower_utf($email_field);
      $text = is_foreachable($text) ? $text : strtolower_utf($text);
      $ret = false;
     
      switch ($type) {
        case self::IM_FILTER_IS:
          if(!is_array($text)) {
            $ret = $email_field == $text;
          } else {
            //if sender_text is array
            $ret = in_array($email_field,$text);
          } //if
          break;
        case self::IM_FILTER_IS_NOT:
          if(!is_array($text)) {
            $ret = $email_field != $text;
          } else {
            //if sender_text is array
            $ret = !in_array($email_field,$text);
          } //if
          break;
        case self::IM_FILTER_HAS:
          if(is_array($text)) {
            //sender case
            foreach ($text as $item) {
              if(strpos_utf($email_field, $item) !== false) {
                $ret = true;
                break;
              }//if
              $ret = false;
            } //foreach
          } else {
            $ret = strpos_utf($email_field, $text) !== false;
          }//if
          break;
        case self::IM_FILTER_HAS_NOT:
          $ret = !(strpos_utf($email_field, $text) !== false);
          break;
        case self::IM_FILTER_STARTS_WITH:
          if(is_array($text)) {
            //sender case
            foreach ($text as $item) {
              if(str_starts_with($email_field,$item)) {
                $ret = true;
                break;
              }//if
              $ret = false;
            } //foreach
          } else {
            $ret = boolval(str_starts_with($email_field,$text));
          }//if
          break;
        case self::IM_FILTER_ENDS_WITH:
          if(is_array($text)) {
            //sender case
            foreach ($text as $item) {
              if(str_ends_with($email_field,$item)) {
                $ret = true;
                break;
              }//if
              $ret = false;
            } //foreach
          } else {
            $ret = boolval(str_ends_with($email_field,$text));
          }//if
          break;
        case self::IM_FILTER_ANY:
          $ret = true;
          break;
        case self::IM_FILTER_IMPORTANT:
          $ret = $email_field == $type;
          break;
        case self::IM_FILTER_ONLY_REGISTERED:
          $ret = Users::findByEmail($email_field, true) instanceof IUser ? true : false;
          break;
        case self::IM_FILTER_ONLY_NOT_REGISTERED:
          $ret = Users::findByEmail($email_field, true) instanceof IUser ? false : true;
          break;
        case self::IM_FILTER_HAS_ATTACHMENTS:
          $ret = $email_field ? true : false;
          break;
        case self::IM_FILTER_HAS_NOT_ATTACHMENTS:
          $ret = $email_field ? false : true;
          break;
     } //switch
     
       return boolval($ret);
    
    }//matchField
    
    /**
     * Return action object 
     */
    function getActionObject() {
      $object_name = strval($this->getActionName());
      return new $object_name();
    }//getActionObject
    
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

      $result['description'] = $this->getDescription();
      $result['is_enabled'] = $this->getIsEnabled();

      $result['urls']['enable'] = $this->getEnableUrl();
      $result['urls']['disable'] = $this->getDisableUrl();

      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false, $for_interface = false) {
      throw new NotImplementedError(__METHOD__);
    } // describe

    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'incoming_email_admin_filter';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('filter_id' => $this->getId());
    } // getRoutingContextParams
    
    
    /**
     * Return add filter URL
     *
     * @return string
     */
    function getAddUrl() {
      return Router::assemble('incoming_email_admin_filter_add');
    } // getAddUrl
    
     /**
     * Return view filter URL
     *
     * @return string
     */
    function getViewUrl() {
      return Router::assemble('incoming_email_admin_filter_view', array(
        'filter_id' => $this->getId()
      ));
    } // getViewUrl

    /**
     * Return edit filter URL
     *
     * @return string
     */
    function getEditUrl() {
      return Router::assemble('incoming_email_admin_filter_edit', array(
        'filter_id' => $this->getId()
      ));
    } // getEditUrl
    
    /**
     * Return delete filter URL
     *
     * @return string
     */
    function getDeleteUrl() {
      return Router::assemble('incoming_email_admin_filter_delete', array(
        'filter_id' => $this->getId()
      ));      
    } // getTrashUrl
    
    /**
     * Return change enabled/disabled URL
     * 
     * @return string
     */
    function getChangeStatusUrl() {
      return Router::assemble('incoming_email_admin_filter_change_status', array(
        'filter_id' => $this->getId()
      ));
    } //getChangeStatusUrl
    
     /**
     * Return enable filter URL
     * 
     * @return string
     */
    function getEnableUrl() {
      return Router::assemble('incoming_email_admin_filter_enable', array(
        'filter_id' => $this->getId()
      ));
    } // getEnableUrl
    
    /**
     * Return enable filter URL
     * 
     * @return string
     */
    function getDisableUrl() {
      return Router::assemble('incoming_email_admin_filter_disable', array(
        'filter_id' => $this->getId()
      ));
    } // getDisableUrl
    
    /**
     * Return reorder url
     * 
     * @return string
     */
    function getReorderUrl() {
      return Router::assemble('incoming_email_filter_reorder');
    } //getChangeStatusUrl
    
    /**
     * Serialize data
     *
     * @param array
     */
    function serializeData(&$data) {
      $data_to_be_serailazed = array('subject','body','sender','to_email','mailbox_id','action_parameters');
      foreach($data as $key => $value) {
        if(in_array($key,$data_to_be_serailazed)) {
          $data[$key] = serialize($value);
        } //if
      }//foreach
    }//serializeData
   
    /**
     * Set values which aren't posted (are disabled) to null 
     */
    function disableNotPostedData(&$data) {
      $data_to_be_serailazed = array('subject','body', 'priority', 'attachments', 'mailbox_id', 'sender','to_email','action_parameters');   
      foreach($data_to_be_serailazed as $key) {
        if(!$data[$key]) {
          $data[$key] = NULL;
        } //if
      }//foreach
    }//disableNotPostedData
    
    /**
     * Return body type
     */
    function getBodyType() {
      $value = unserialize($this->getBody());
      return $value['type'];
    } //getBodyType
    
    /**
     * Return body type
     */
    function getBodyText() {
      $value = unserialize($this->getBody());
      return $value['text'];
    } //getBodyType
    
    /**
     * Return subject type
     */
    function getSubjectType() {
      $value = unserialize($this->getSubject());
      return $value['type'];
    } //getSubjectType
    
    /**
     * Return subject type
     */
    function getSubjectText() {
      $value = unserialize($this->getSubject());
      return $value['text'];
    } //getSubjectText
    
    /**
     * Return sender type
     */
    function getSenderType() {
      $value = unserialize($this->getSender());
      return $value['type'];
    } //getSenderType
    
    /**
     * Return sender type
     */
    function getSenderText() {
      $value = unserialize($this->getSender());
      return $value['text'];
    } //getSenderText
    
    /**
     * Return to_email type
     */
    function getToEmailType() {
      $value = unserialize($this->getToEmail());
      return $value['type'];
    } //getSenderType
    
    /**
     * Return sender type
     */
    function getToEmailText() {
      $value = unserialize($this->getToEmail());
      return $value['text'];
    } //getSenderText
    
    /**
     * Return action parameters
     */
    function getActionParameters() {
      return $value = unserialize(parent::getActionParameters());
     } //getActionParameters
     
     /**
     * Return action parameter
     */
    function getActionParameter($param) {
      $action_params = $this->getActionParameters();
      return array_var($action_params,$param,null,true);
     } //getActionParameters
  
    /**
     * Return mailbox id
     */ 
    function getMailboxId() {
      return $value = unserialize(parent::getMailboxId());
    } //getMailboxId
  }