<?php
  // We need MailboxManager
  require_once(MAILBOX_MANAGER_LIB_PATH.'/MailboxManager.class.php');
  
  /**
   * PHPImapMailboxManager
   * 
   * @package angie.classes.mailboxmanager
   */
  class PHPImapMailboxManager extends MailboxManager {    
    /**
     * Tries to connect to server. If it succedes it returns true, if not, it returns error object
     * 
     * @return true
     */
    public function connect() {
      if(!extension_loaded('imap')) {
        throw new Error('IMAP extension not loaded');
      } // if
     
      return parent::connect();
    } // connect
    
    /**
     * Connects to server
     * 
     * @return mixed
     */
    protected function connectToServer() {
    	parent::connectToServer();
    	
      $this->connection = imap_open($this->getConnectionString(), $this->getMailboxUsername(), $this->getMailboxPassword(), OP_SILENT);

      // we are not connected
      if (!is_resource($this->connection)) {
        $this->connected = false;

        $errors = imap_errors();
        if ($errors) {
          throw new Error(implode("\n", $errors));
        } else {
          throw new Error(lang('Unknown error'));
        } // if
      } // if
      
      $this->connected = true;
      return $this->connection;
    } // connectToServer
    
    /**
     * Connect to file
     * 
     * @return mixed
     */
    protected function connectToFile() {
    	parent::connectToFile();
    	
    	$this->connection = imap_open($this->mailbox_file, '', '');
    	
      $errors = imap_last_error();
      if ($errors) {
        $this->connected = false;
        throw new Error(imap_last_error());
      } // if
      
      $this->connected = true;
      return $this->connection;
    } // connectToFile
    
    /**
     * Test connection. If it's ok then it returns true, otherwise returns connection error
     * 
     * @return boolean
     */
    public function testConnection() {
    	if (!extension_loaded('imap')) {
    		throw new PhpExtensionDnxError('imap');
    	} // if
    	
			if (imap_open($this->getConnectionString(), $this->getMailboxUsername(), $this->getMailboxPassword())) {
        return true;
      } else {
        throw new Error(implode(",",imap_errors()));
      } // if
    } // testConnection
    
    /**
     * Disconnect from pop3/imap server
     *
     * @return boolean
     */
    public function disconnect() {
      if(!extension_loaded('imap')) {
        throw new Error('IMAP extension not loaded');
      } // if
      
      $this->expunge();
      
      if (imap_close($this->connection)) {
        return parent::disconnect();
      } else {
        return false;
      } // if
    } // disconnect
   
    /**
     * Do queued mailbox tasks (delete messages etc...)
     */
    public function expunge() {
      if(!extension_loaded('imap')) {
        throw new Error('IMAP extension not loaded');
      } // if
      
      return imap_expunge($this->getConnection());
    } // expunge
    
    /**
     * Return number of messages in mailbox
     *
     * @return integer
     */
    public function countMessages() {
      parent::countMessages(); 
      return (int) imap_num_msg($this->getConnection());
    } // countMessages
    
    /**
     * Count unread messages in mailbox
     *
     * @return integer
     */
    public function countUnreadMessages() {
      parent::countUnreadMessages();
      $status = imap_status($this->getConnection(),$this->getConnectionString(),SA_UNSEEN);
      return $status->unseen;
    } // countUnreadMessages
    
    /**
     * Retrieves message headers
     *
     * @param integer $message_id
     * @return array
     */
    public function getMessageHeaders($message_id) {
      parent::getMessageHeaders($message_id);
      $header = imap_headerinfo($this->getConnection(), $message_id);
      return $header;
    } // getMessageHeaders()
       
    /**
     * Delete message from server
     * 
     * @param integer $message_id - unique message id
     * @param boolean $instantly - if true, message is removed instantly, and if false, message is removed on disconnection or expunge method
     * @return boolean
     */
    public function deleteMessage($message_id, $instantly = false) {
      parent::deleteMessage($message_id);
      // empty error stack
      imap_errors();
      
      $delete = imap_delete($this->getConnection(), $message_id);
      if ($delete) {
        if ($instantly) {
          $this->expunge();
        } // if
        return true;
      } // if
      throw new Error(imap_errors());
    } // deleteMessage
    
    /**
     * List messages in mailbox
     *
     * @param integer $start
     * @param integer $count
     */
    public function listMessagesHeaders($start, $count) {
      $end = $start + $count - 1;
      $headers = imap_fetch_overview($this->getConnection(),"$start:$end", 0);
      if (count($headers) > 0) {
        for ($x = 0; $x < count($headers); $x++) {
          $headers[$x]->subject = imap_utf8_alt($headers[$x]->subject);
          $headers[$x]->from = imap_utf8_alt($headers[$x]->from);
        } // for
      } // if
      return $headers;
    } // listMessagesHeaders
    
    // ---------------------------------------------------
    //  DOWNLOADING AND PARSING MESSAGE
    // ---------------------------------------------------
    
    /**
     * Downloads message
     *
     * @param integer $message_id
     * @return MailboxManagerEmail
     */
    public function getMessage($message_id, $attachments_folder = null) {
      $email = new MailboxManagerEmail();
      
      $result = $this->parseMessageHeaders($message_id, $email);
      
      if (is_error($result)) {
        return $result;        
      } // if
             
      $result = $this->parseMessageBody($message_id, $email, $attachments_folder);
    
      if (is_error($result)) {
        return $result;
      } // if
      
      return $email;
    } // getMessage
        
    /**
     * Returns string representation of main content type
     *
     * @param integer $main_type_id
     * @return string
     */
    protected function getMainContentType($main_type_id) {
      switch ($main_type_id) {
        case 0: $main_type = 'text'; break;
        case 1: $main_type = 'multipart'; break;
        case 2: $main_type = 'message'; break;
        case 3: $main_type = 'application'; break;
        case 4: $main_type = 'audio'; break;
        case 5: $main_type = 'image'; break;
        case 6: $main_type = 'video'; break;
        case 7: $main_type = 'model'; break;
        default: $main_type = 'x-unknown'; break;
      }
      return $main_type;
    } // getMainContentType
    
    /**
     * Process subcontent type
     *
     * @param string $content_type
     * @return string
     */
    protected function getSubContentType($content_type) {
      return strtolower($content_type);
    } // getSubContentType
    
    /**
     * Return string representation of provided encoding
     *
     * @param integer $encoding_id
     * @return string
     */
    protected function getBodyEncodingString($encoding_id) {
      switch ($encoding_id) {
      	case '0': $encoding = '7bit'; break;
      	case '1': $encoding = '8bit'; break;
      	case '2': $encoding = 'binary'; break;
      	case '3': $encoding = 'base64'; break;
      	case '4': $encoding = 'quoted-printable'; break;
      	default: $encoding = 'other'; break;
      } // switch
      return $encoding;
    } // getBodyEncodingString
    
    /**
     * Returns content-type for specified part
     *
     * @param stdClass $part
     * @return string
     */
    protected function getContentType(& $part) {
      if (!is_object($part)) {
        return false;
      } // if
      
      $type = $this->getMainContentType($part->type);
      if ($part->ifsubtype) {
        $type.='/'.strtolower($part->subtype) ;
      } // if
      
      return $type;
    } // getPartContentType
    
    /**
     * Return specified parameter for part
     *
     * @param stdObject $part
     * @param string $parameter_name
     * @return string
     */
    protected function getPartParameter(&$part, $parameter_name) {
      $parameter_name = strtoupper($parameter_name);
      if ($part->ifparameters && is_foreachable($part->parameters)) {
        foreach ($part->parameters as $parameter) {
        	if ($parameter_name == strtoupper($parameter->attribute)) {
        	  return $parameter->value;
        	} // if
        } // foreach
      } // if
      return false;
    } // getPartParameter
    
    /**
     * Return specified disposition parameter
     *
     * @param stdObject $part
     * @param string $parameter_name
     * @return string
     */
    protected function getDispositionParameter(&$part, $parameter_name) {
      $parameter_name = strtoupper($parameter_name);
      if ($part->ifdparameters && is_foreachable($part->dparameters)) {
        foreach ($part->dparameters as $parameter) {
        	if ($parameter_name == strtoupper($parameter->attribute)) {
        	  return $parameter->value;
        	} // if
        } // foreach
      } // if
      return false;
    } // getDispositionParameter
    
    /**
     * Parse message headers
     *
     * @param integer $message_id
     * @param MailboxManagerEmail $email
     * @return MailboxManagerEmail
     */
    protected function parseMessageHeaders($message_id, &$email) {
      if (!($email instanceof MailboxManagerEmail)) {
        $email = new MailboxManagerEmail();
      } // if
      
      $headers = $this->getMessageHeaders($message_id);
      if (!is_object($headers)) {
        throw new Error(lang('Could not retrieve headers for that messsage. Does message with id #:message_id exists?', array('message_id' => $message_id)));
      } // if
      
      
      $email->setId($message_id);
      $email->setSubject(imap_utf8_alt($headers->Subject));
      $email->setDate($headers->Date);
      $email->setSize($headers->Size);
      
      // fetch and set headers
      $raw_headers = trim(imap_fetchheader($this->getConnection(), $message_id));
      $email->setHeaders($raw_headers);
      
      //$raw_headers .= "\nAuto-Submitted: auto-generated\nPrecedence: bulk\n";
      
      // extract extra headers from email, starting with X-
      $extra_headers = array();
      $raw_headers = explode("\n", $raw_headers);
      if (is_foreachable($raw_headers)) {
      	foreach ($raw_headers as $raw_header) {
      		$raw_header = trim($raw_header);
		      
      		//priority
      		if (preg_match('/^(X[^:]*): (.*)/is', $raw_header, $results)) {
		      	$header_name = trim(isset($results[1]) ? $results[1] : null);
		      	$header_value = trim(isset($results[2]) ? $results[2] : null);
		      	if ($header_name) {
		      		$extra_headers[$header_name] = $header_value;
		      	} // if
		      } // if
		      
		      //Auto-Submitted
		      if (preg_match('/^(Auto-Submitted[^:]*): (.*)/is', $raw_header, $results)) {
		      	$header_name = trim(isset($results[1]) ? $results[1] : null);
		      	$header_value = trim(isset($results[2]) ? $results[2] : null);
		      	if ($header_name) {
		      		$extra_headers[$header_name] = $header_value;
		      	} // if
		      } // if
		      
		      //Reply-Path
		      if (preg_match('/^(Return-Path[^:]*): (.*)/is', $raw_header, $results)) {
		      	$header_name = trim(isset($results[1]) ? $results[1] : null);
		      	$header_value = trim(isset($results[2]) ? $results[2] : null);
		      	if ($header_name) {
		      		$extra_headers[$header_name] = $header_value;
		      	} // if
		      } // if
		      
		      //Precedence
		      if (preg_match('/^(Precedence[^:]*): (.*)/is', $raw_header, $results)) {
		      	$header_name = trim(isset($results[1]) ? $results[1] : null);
		      	$header_value = trim(isset($results[2]) ? $results[2] : null);
		      	if ($header_name) {
		      		$extra_headers[$header_name] = $header_value;
		      	} // if
		      } // if
		      
		      
		    } // foreach
      } // if
      
      //check to see if email is auto responder or failed delivery
      $auto_submitted_response = array(
        'auto-generated',
        'auto-replied',
        'auto-notified'
      );

      $extra_header_auto_submitted = array_var($extra_headers, 'Auto-Submitted');
      if($extra_header_auto_submitted && in_array($extra_header_auto_submitted, $auto_submitted_response)) {
        $email->setIsAutoRespond(true);
      }//if

      $extra_header_return_path = array_var($extra_headers, 'Return-Path');
      if($extra_header_return_path && $extra_header_return_path == '<>') {
        $email->setIsAutoRespond(true);
      }//if

      $extra_header_precedence = array_var($extra_headers, 'Precedence');
      if($extra_header_precedence && ($extra_header_precedence == 'bulk' || $extra_header_precedence == 'junk')) {
        $email->setIsAutoRespond(true);
      }//if

      $extra_header_x_failed_recipients = array_var($extra_headers, 'X-Failed-Recipients');
      if($extra_header_x_failed_recipients) {
        $email->setIsAutoRespond(true);
      }//if
      
      $priority = isset($extra_headers['X-Priority']) && $extra_headers['X-Priority'] ? intval($extra_headers['X-Priority']) : 0;
      $email->setPriority($priority);
     
      if (property_exists($headers, 'from') && is_foreachable($headers->from)) {
        foreach ($headers->from as $from) {
          if (property_exists($from, 'personal')) {
            $display_name = imap_utf8_alt($from->personal);
          } else {
            $display_name = $from->mailbox.'@'.$from->host;
          } // if

          $email->addAddress($from->mailbox.'@'.$from->host, $display_name, 'from');
        } // foreach
      } // if
      
      if (property_exists($headers, 'to') && is_foreachable($headers->to)) {
        foreach ($headers->to as $to) {
          
          if (property_exists($to, 'personal')) {
            $display_name = imap_utf8_alt($to->personal);
          } else {
            $display_name = $to->mailbox.'@'.$to->host;
          } // if
          $email->addAddress($to->mailbox.'@'.$to->host, $display_name, 'to');
        } // foreach
      } // if
     
      if (property_exists($headers, 'reply_to') && is_foreachable($headers->reply_to)) {
        foreach ($headers->reply_to as $reply_to) {
          if (property_exists($reply_to, 'personal')) {
            $display_name = imap_utf8_alt($reply_to->personal);
          } else {
            $display_name = $reply_to->mailbox.'@'.$reply_to->host;
          } // if

          $email->addAddress($reply_to->mailbox.'@'.$reply_to->host, $display_name, 'reply_to');
        } // foreach
      } // if
      
      if (property_exists($headers, 'cc') && is_foreachable($headers->cc)) {
        foreach ($headers->cc as $cc) {
          if (property_exists($cc, 'personal')) {
            $display_name = imap_utf8_alt($cc->personal);
          } else {
            $display_name = $cc->mailbox.'@'.$cc->host;
          } // if

          $email->addAddress($cc->mailbox.'@'.$cc->host, $display_name, 'cc');
        } // foreach
      } // if
      
      if (property_exists($headers, 'bcc') && is_foreachable($headers->bcc)) {
        foreach ($headers->bcc as $bcc) {
          if (property_exists($bcc, 'personal')) {
            $display_name = imap_utf8_alt($bcc->personal);
          } else {
            $display_name = $bcc->mailbox.'@'.$bcc->host;
          } // if

          $email->addAddress($bcc->mailbox.'@'.$bcc->host, $display_name, 'bcc');
        } // foreach
      } // if
      
      
      return $email;
    } // parseMessageHeaders
    
    /**
     * Parse message body
     * 
     * @param integer $message_id
     * @param MailboxManagerEmail $email
     * @param string $attachments_folder
     * @return MailboxManagerEmail
     */
    protected function parseMessageBody($message_id, &$email, $attachments_folder = null) {     
      $structure = imap_fetchstructure($this->getConnection(), $message_id);
       
      if (!$structure) {
        throw new Error(lang('Cannot fetch body structure'));
      } // if
      
      if (!($email instanceof MailboxManagerEmail)) {
        $email = new MailboxManagerEmail();
      } // if
       
      if (isset($structure->parts) && is_foreachable($structure->parts)) {
        $this->parseMessageBodyPart($message_id, $structure, $email, $attachments_folder, null);
      } else {
        $this->parseMessageSinglepartBody($message_id, $structure, $email);
      } // if
      return $email;
    } //parseMessageBody
    
    /**
     * parse message body part
     *
     * @param integer $message_id
     * @param stdObject $structure
     * @param MailboxManagerEmail $results
     * @param string $attachments_folder
     * @param string $part_id
     * @return array
     */
    protected function parseMessageBodyPart($message_id, &$structure, &$results, $attachments_folder, $part_id) {
      $content_type = $this->getContentType($structure);
      $type = $this->getMainContentType($structure->type);
      $encoding = $this->getBodyEncodingString($structure->encoding);
      $charset = $this->getPartParameter($structure, 'charset');
      $is_attachment = (boolean) $this->getPartParameter($structure, 'name');

      // determine file name if it's attachment
      $file_name = 'unknown_file_' . $results->countAttachments();
      if ($this->getPartParameter($structure, 'name')) {
        $file_name = imap_utf8_alt($this->getPartParameter($structure, 'name'));
      } else if ($this->getDispositionParameter($structure, 'filename')) {
        $file_name = imap_utf8_alt($this->getDispositionParameter($structure, 'filename'));
      } // if

      switch ($type) {
        // if we have multipart bodies the do the recursion
        case 'multipart':
          for ($x=0; $x < count($structure->parts); $x++) {
            $new_part_id = !$part_id ? $new_part_id = (string) ($x + 1) : (string) $part_id . '.' . ($x+1);
            $this->parseMessageBodyPart($message_id, $structure->parts[$x], $results, $attachments_folder, $new_part_id);
          } // for
        break;

        // if we have text then either we have attachment or text body
        case 'text':
          if ($is_attachment) {
            do {
              $path = $attachments_folder . '/' . make_string(40);
            } while (is_file($path));

            if ($this->getBodyPart($message_id, $part_id, $encoding, $path)) {
              $results->addAttachment($part_id, $content_type, $file_name, $path, filesize($path));
            } // if
          } else {
            $content = $this->getBodyPart($message_id, $part_id, $encoding);
            $results->addBody($part_id, $content_type, $charset == 'UTF-8' ?  $content : convert_to_utf8($content, $charset));
          } // if
        break;

        // if none
        default:
          // it's a attachment
          do {
            $path = $attachments_folder . '/' . make_string(40);
          } while (is_file($path));

          if ($this->getBodyPart($message_id, $part_id, $encoding, $path)) {
            $results->addAttachment($part_id, $content_type, $file_name, $path, filesize($path));
          } // if
        break;
      } // switch
    } // parseMessageBodyPart;
    
    /**
     * Parse singlepart message
     *
     * @param integer $message_id
     * @param stdObj $structure
     * @param MailboxManagerEmail $email
     * @return MailboxManagerEmail
     */
    protected function parseMessageSinglepartBody($message_id, &$structure, &$email) {
      $content_type = $this->getContentType($structure);
  		$charset = strtoupper($this->getPartParameter($structure, 'charset'));
  		$encoding = $this->getBodyEncodingString($structure->encoding);
  		
  		$content = imap_body($this->getConnection(), $message_id);
      switch ($encoding) {
        case 'base64': 
          $content = imap_base64($content);
          break;
          
        case 'quoted-printable':
          $content = imap_qprint($content);
          break;
      } // switch
      $content = $charset == 'UTF-8' ? $content : convert_to_utf8($content,$charset);
  		$email->addBody('0', $content_type, $content);
  		return $email;
    } // parseMessageSinglepartBody
    
    /**
     * Retrieve body part, and if it's file write it to filesystem
     *
     * @param integer $message_id
     * @param string $body_part
     * @param string $file_path
     * @return mixed
     */
    protected function getBodyPart($message_id, $body_part, $encoding, $file_path = false) {
      if ($file_path) {        
        
        if (!MM_CAN_DOWNLOAD_LARGE_ATTACHMENTS) {
          $structure = imap_bodystruct($this->getConnection(), $message_id, $body_part);
          if(!($structure instanceof stdClass)) {
            return false;
          } // if
          // if attachment is larger than FAIL_SAFE_IMAP_ATTACHMENT_SIZE_MAX, don't download it
          if ($structure->bytes > FAIL_SAFE_IMAP_ATTACHMENT_SIZE_MAX) {
            return false;
          } // if
        } // if

        // use failsafe functions always if we are connected to mailbox file instead of real server
        if ($this->mailbox_file) {
          $savebody_result = imap_failsafe_savebody($this->getConnection(), $file_path, $message_id, $body_part);
        } else {
          $savebody_result = imap_savebody_alt($this->getConnection(), $file_path, $message_id, $body_part);
        } // if

        // if we could not import the mail, skip it
        if (!$savebody_result) {
          return false;
        } // if
        
        $temporary_file = $file_path.'_temp';
        switch ($encoding) {
          case 'base64':
            $decoding_result = base64_decode_file($file_path, $temporary_file);
            if ($decoding_result) {
              @unlink($file_path);
              rename($temporary_file, $file_path);
              return true;
            } else {
              @unlink($file_path);
              @unlink($temporary_file);
              return false;
            }
            break;
            
          case 'quoted-printable':
            $decoding_result = quoted_printable_decode_file($file_path, $temporary_file);
            if ($decoding_result) {
              @unlink($file_path);
              rename($temporary_file, $file_path);
              return true;
            } else {
              @unlink($file_path);
              @unlink($temporary_file);
              return false;
            }
            break;
        } // switch
        
        return true;
      } else {
        $result = imap_fetchbody($this->getConnection(), $message_id, $body_part);

        switch ($encoding) {
          case 'base64': 
            return imap_base64($result);
            break;
            
          case 'quoted-printable':
            return imap_qprint($result);
            break;
            
          default:
            return $result;
            break;
        } // switch
      } // if
    } // getBodyPart
    
  }