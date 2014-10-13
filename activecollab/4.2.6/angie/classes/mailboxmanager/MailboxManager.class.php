<?php

  /**
   * Base class for mailboxes
   * 
   * @package angie.classes.mailboxmanager
   */
  abstract class MailboxManager {
    
    /**
     * Mailbox connection
     * 
     * @var mixed
     */
    var $connection;
    
    /**
     * Tells if manager is connected
     * 
     * @var boolean
     */
    var $connected = false;
    
    /**
     * Mailbox parameters
     * 
     * @var array
     */
    var $mailbox_parameters;
    
    /**
     * Path to mailbox
     * 
     * @var string
     */
    var $mailbox_file = false;
    
    /**
     * User credentials
     *
     * @var array
     */
    var $mailbox_user;
    
    /**
     * Errors
     * 
     * @var array
     */
    var $errors;
    
    /**
     * Construct MailboxManager class
     *
     * @param string $server_address
     * @param string $server_type
     * @param string $server_security
     * @param string $server_port
     * @param string $mailbox_name
     * @param string $username
     * @param string $password
     */
    function __construct($server_address=null, $server_type=null, $server_security=null, $server_port=null, $mailbox_name=null, $username=null, $password=null) {
    	if (is_file($server_address)) {
    		$this->mailbox_file = $server_address;
    	} else {
	      $this->mailbox_parameters = array(
	        'server_address' => $server_address,
	        'server_type' => $server_type,
	        'server_security' => $server_security ? $server_security : MM_SECURITY_NONE,
	        'server_port' => $server_port ? $server_port : mm_get_default_port($server_security),
	        'mailbox_name' => $mailbox_name
	      );
	      
	      $this->mailbox_user = array(
	        'username' => $username,
	        'password' => $password
	      );
    	} // if    	
    } // __construct
    
    /**
     * Connect method
     * 
     * @return boolean
     */
    public function connect() {
    	if ($this->mailbox_file) {
    		return $this->connectToFile();
    	} else {
    		return $this->connectToServer();
    	} // if
    } // connect
    
    /**
     * Connect to mail server
     * 
     * @return boolean
     */
    protected function connectToServer() {
      if (!$this->getServerAddress()) {
        throw new Error(lang('Server address is not defined'));
      } // if
      
      $port = $this->getServerPort();
      if ($port<1 || $port>65535 || !trim($port)) {
        throw new Error(lang('Server port needs to be in range 1-65535'));
      } // if
      
      return false;    	
    } // connectToServer
    
    /**
     * Connect to file
     * 
     * @return boolean
     */
    protected function connectToFile() {
    	return false;
    } // connectToFile
        
    /**
     * Disconnect from server
     * 
     * @return boolean
     */
    public function disconnect() {
      $this->connected = false;
      return true;
    } // disconnect
    
    /**
     * Test connection. If it's ok then it returns true, otherwise returns connection error
     * 
     * @return boolean
     */
    public function testConnection() {
    	return false;
    } // testConnection
    
   /**
    * Assembles pop3/imap connection string
    *
    * @param boolean $novalidate_cert
    * @return string
    */
    public function getConnectionString($novalidate_cert = true) {
      $connection_str = '{' . $this->getServerAddress() . ':' . $this->getServerPort() . '/' . $this->getServerType();
      
      if ($this->getServerSecurity() != MM_SECURITY_NONE) {
        $connection_str.= '/'.$this->getServerSecurity();
        if ($novalidate_cert) {
          $connection_str.= '/novalidate-cert';
        } else {
        	$connection_str.= '/validate-cert';
        }
      } else {
        $connection_str.= '/notls';
      } // if
    
      $connection_str.= '}'.$this->getMailboxName();
      return $connection_str;
    } // getConnectionString
      
    /**
     * Return connection
     * 
     * @return mixed
     */
    public function getConnection() {
      return $this->connection;
    } // getConnection
    
    /**
     * Check if connected
     *
     * @return boolean
     */
    public function isConnected() {
      return (boolean) $this->connection;
    } // isConnected
    
    /**
     * If is connected returns true if not, raises exception
     * 
     * @return boolean
     */
    public function requireConnection() {
      if (!$this->isConnected()) {
        throw new Error(lang("You are not connected, can't execute a command"), true);
      };
      return true;
    }
    
    /**
     * Get Number of messages in mailbox
     * 
     * @return integer
     */
    public function countMessages() {
      $this->requireConnection();
      return 0;
    } // countMessages
    
    /**
     * Get Number of unread messages
     * 
     * @return integer
     */
    public function countUnreadMessages() {
      $this->requireConnection();
      return 0;
    } // countUnreadMessages
    
    /**
     * Retrieves message headers
     *
     * @param mixed $message_id
     * @return mixed
     */
    public function getMessageHeaders($message_id) {
      $this->requireConnection();
      return false;
    } // getMessageHeaders
    
    /**
     * Delete messsage
     *
     * @param mixed $message_id
     */
    public function deleteMessage($message_id) {
      $this->requireConnection();
      return false;
    } // deleteMessage

    /**
     * Retrieves headers for multiple emails
     *
     * @param array $message_ids
     */
    public function getHeaders($message_ids) {
      $headers = array();
      if (is_foreachable($message_ids)) {
        foreach ($message_ids as $message_id) {
        	$headers[] = $this->getMessageHeaders($message_id);
        } // foreach
      } // if
      return $headers;
    } // getHeaders
    
    /**
     * Return message with id $message_id
     *
     * @param int $message_id
     * @return MailboxManagerEmail
     */
    public function getMessage($message_id) {
      return null;  
    } // getMessage
    
    // ---------------------------------------------------
    //  Getters
    // ---------------------------------------------------
    
    /**
     * Returns server address
     * 
     * @return string
     */
    public function getServerAddress() {
      return array_var($this->mailbox_parameters,'server_address', null);
    } // getServerAddress
    
    /**
     * Returns server type
     * 
     * @return string
     */
    public function getServerType() {
      return array_var($this->mailbox_parameters,'server_type', null);
    } // getServerAddress
    
    /**
     * Returns server security
     * 
     * @return string
     */
    public function getServerSecurity() {
      return array_var($this->mailbox_parameters,'server_security', null);
    } // getServerSecurity
    
    /**
     * Returns server port
     * 
     * @return string
     */
    public function getServerPort() {
      return array_var($this->mailbox_parameters,'server_port', null);
    } // getServerPort
    
    /**
     * Returns mailbox name
     * 
     * @return string
     */
    public function getMailboxName() {
      return array_var($this->mailbox_parameters,'mailbox_name', null);
    } // getMailboxName
    
    /**
     * Returns mailbox username
     *
     * @return string
     */
    public function getMailboxUsername() {
      return array_var($this->mailbox_user, 'username', null);
    } // getMailboxUsername
    
    /**
     * Returns mailbox password
     *
     * @return string
     */
    public function getMailboxPassword() {
      return array_var($this->mailbox_user, 'password', null);
    } // getMailboxPassword
  
  }

?>