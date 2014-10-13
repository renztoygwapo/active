<?php
  
  /**
   * Incoming Message Imoprt Error Activity log
   * 
   * @package angie.framework.email
   * @subpackage model
   *
   */
  class IncomingMessageImportErrorActivityLog extends IncomingMailingActivityLog {
    
    const ERROR_ANONYMOUS_NOT_ALLOWED = "Anonymous not allowed";
    const ERROR_USER_CANNOT_CREATE_OBJECT = "User cant create object";
    const ERROR_PARENT_NOT_EXISTS = "Parent not exist";
    const ERROR_USER_CANNOT_CREATE_COMMENT = "User cant create comment";
    const ERROR_SYSTEM_CANNOT_CREATE_OBJECT = "System cant create object";
    const ERROR_PROJECT_DOES_NOT_EXISTS = "Project does not exist";
    const ERROR_PARENT_NOT_ACCEPTING_COMMENTS = "Parent not accepting comments";
    const ERROR_IMPORTING_FILE = "Email attachment error";
    const ERROR_IMPORTING_FILE_DNX_EXIST = "Email doesn't have attachments";
    const ERROR_NO_FILTER_APPLIED = "no filter applied";
    const ERROR_DISK_QUOTA_REACHED = "Disk Quota Reached. Attachments can't be imported.";
       
    /**
  	 * Return name of this log entry
  	 * 
  	 * @return string
  	 */
  	function getName() {
  	  return lang('Message ":subject" - import error', array('subject' => $this->getAdditionalProperty('subject')));
  	} // getName

    /**
     * Log entry into the database
     *
     * @param null $mailbox
     * @param null $incoming_email
     * @param null $status_code
     * @param null $applied
     * @param null $target
     * @param bool $save
     */
    function log($mailbox = null, $incoming_email = null, $status_code = null, $applied = null, $target = null, $save = true) {
  	  parent::log(
  	    $incoming_email instanceof IncomingMail ? $incoming_email->getCreatedBy() : null, 
  	    $incoming_email instanceof IncomingMail ? $incoming_email->getToUser() : null,
  	    array(

    		'filter_id' => $applied instanceof IncomingMailFilter ? $applied->getId() : null,
        'filter_name' => $applied instanceof IncomingMailFilter ? $applied->getName() : null,
        'action_name' => $applied instanceof IncomingMailFilter ? $applied->getActionName() : null,
        'action_description' => $applied instanceof IncomingMailFilter ? $applied->getActionObject()->getDescription() : null,

        'interceptor_class_name' => $applied instanceof IncomingMailInterceptor ? get_class($applied) : null,
        'interceptor_name' => $applied instanceof IncomingMailInterceptor ? $applied->getName() : null,
        'interceptor_action' => $applied instanceof IncomingMailInterceptor ? $applied->getMessage() : null,

        'target_type' => $target instanceof ApplicationObject ? get_class($target) : null,
    		'target_id' => $target instanceof ApplicationObject ? $target->getId() : null,
  	    'mailbox_id' => $mailbox instanceof IncomingMailbox ? $mailbox->getId() : null,
  	    'incoming_email_id' => $incoming_email instanceof IncomingMail ? $incoming_email->getId() : null,
  	    'status_code' => $status_code ? $status_code : null,
        'subject' => $incoming_email instanceof IncomingMail ? $incoming_email->getSubject() : null,
        'body' =>  $incoming_email instanceof IncomingMail ? $incoming_email->getBody() : null,
        'from_email_original' => $incoming_email instanceof IncomingMail ? $incoming_email->getOriginalFromEmail() : null,
  	    ), 
  	    $save);
  	    
  	    if($incoming_email instanceof IncomingMail) { 
  	      $incoming_email->setStatus($this->getStatusDescription());
          $incoming_email->save();
        }//if
        
  	} // log
  	
  	/**
     * Return status message for status code
     * 
     * @param $status_code
     */
    function getStatusDescription() {
      switch ($this->getAdditionalProperty('status_code')) {
       case IncomingMessageImportErrorActivityLog::ERROR_ANONYMOUS_NOT_ALLOWED:
          $message = lang('Mailbox does not accept emails from unregistered users');
          break;
        case IncomingMessageImportErrorActivityLog::ERROR_USER_CANNOT_CREATE_OBJECT:
          $message = lang('User does not have permission to create object in selected project');
          break;
        case IncomingMessageImportErrorActivityLog::ERROR_PARENT_NOT_EXISTS:
          $message = lang('Requested parent object does not exist');
          break;
        case IncomingMessageImportErrorActivityLog::ERROR_USER_CANNOT_CREATE_COMMENT:
          $message = lang('User does not have permission to create comment in selected object');
          break;
        case IncomingMessageImportErrorActivityLog::ERROR_SYSTEM_CANNOT_CREATE_OBJECT:
          $message = lang('Object cannot be saved, possibly because of validation errors');
          break;
        case IncomingMessageImportErrorActivityLog::ERROR_PROJECT_DOES_NOT_EXISTS:
          $message = lang('Project does not exist or it was deleted');
          break;
        case IncomingMessageImportErrorActivityLog::ERROR_PARENT_NOT_ACCEPTING_COMMENTS:
          $message =  lang('Object does not accept comments. Either it is locked for comments or it does not support comments');
          break;
        case IncomingMessageImportErrorActivityLog::ERROR_NO_FILTER_APPLIED:
          $message =  lang('No filter applied over incoming mail. No match.');
          break;
        case IncomingMessageImportErrorActivityLog::ERROR_DISK_QUOTA_REACHED:
          $message =  lang('Disk Quota Reached. Please consult your system administrator.');
          break;

      }//switch
      return $message ? $message : $this->getAdditionalProperty('status_code');
    }//getStatusDescription
  	
  	/** We have details to show to the user about this particular mailing log
  	 * 
  	 * @return boolean
  	 */
  	function hasDetails() {
  		return true;
  	} // hasDetails
  	
  	 /**
     * Return log entry icon URL
     * 
     * @return string
     */
    function getIconUrl() {
    	return AngieApplication::getImageUrl('icons/16x16/mail-info.png', ENVIRONMENT_FRAMEWORK);
    } // getIconUrl
    
  	
  	/**
  	 * Return import url to use in mailing activity log list
  	 * 
  	 */
//  	function getViewUrl() {
//  	  return $this->getIncomingMail() instanceof IncomingMail ? $this->getIncomingMail()->getImportUrl() : parent::getViewUrl();
//  	}//getViewUrl
  	
  	
  	/**
  	 * Render log entry details for view in flyout
  	 * 
  	 * @param Smarty $smarty
  	 * @return string
  	 */
  	function renderDetails(Smarty $smarty) {
  	  return parent::renderDetails($smarty, get_view_path('incoming_import_error', 'activity_log_details', EMAIL_FRAMEWORK));
  	} // renderDetails
      	
  }