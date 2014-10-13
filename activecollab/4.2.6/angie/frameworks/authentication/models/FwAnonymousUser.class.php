<?php

  /**
   * General anoymous user implementation
   *
   * @package angie.frameworks.authentication
   * @subpackage models
   */
  abstract class FwAnonymousUser implements IUser, IDescribe, IAvatar {
    
    /**
     * Users name
     * 
     * @var string
     */
    private $name;
    
    /**
     * Users email address
     * 
     * @var string
     */
    private $email;
    
    /**
     * Construct anonymous user instance
     *
     * @param string $name
     * @param string $email
     */
    function __construct($name, $email) {
      $this->setName($name);
      $this->setEmail($email);
    } // __construct
    
    /**
     * Anonymous User Avatar implementation instance for this object
     *
     * @var IUserAvatarImplementation
     */
  	private $avatar;
    
    /**
     * Return avatar implementation for this object
     *
     * @return IAnonymousUserAvatarImplementation
     */
    function avatar() {
      if(empty($this->avatar)) {
        $this->avatar = new IAnonymousUserAvatarImplementation($this);
      } // if
      
      return $this->avatar;
    } // avatar
    
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
      $result = array(
        'id' => $this->getId(),
        'first_name' => $this->getFirstName(),
        'last_name' => $this->getLastName(),
        'display_name' => $this->getDisplayName(), 
        'short_display_name' => $this->getDisplayName(true), 
        'email' => $this->getEmail(),
      	'urls' => array('view' => $this->getViewUrl()), 
        'permalink' => $this->getViewUrl(),
      );

      $this->avatar()->describe($user, $detailed, $for_interface, $result);

      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      $result = array(
        'id' => $this->getId(),
        'first_name' => $this->getFirstName(),
        'last_name' => $this->getLastName(),
        'display_name' => $this->getDisplayName(),
        'short_display_name' => $this->getDisplayName(true),
        'email' => $this->getEmail(),
        'urls' => array('view' => $this->getViewUrl()),
        'permalink' => $this->getViewUrl(),
      );

      $this->avatar()->describeForApi($user, $detailed, $result);

      return $result;
    } // describeForApi
    
    // ---------------------------------------------------
    //  IUser interface implementation
    // ---------------------------------------------------
    
    /**
     * Return ID
     *
     * @return integer
     */
    function getId() {
      return 0;
    } // getId
    
    /**
     * Return display name
     *
     * @param boolean $short
     * @return string
     */
    function getDisplayName($short = false) {
      return Users::getUserDisplayName(array(
        'full_name' => $this->getName(), 
        'email' => $this->getEmail(), 
      ), $short);
    } // getDisplayName
    
    /**
     * Return first name
     *
     * @return string
     */
    function getFirstName() {
      if($this->getName()) {
        return first(explode(' ', $this->getName()));
      } else {
        return substr_utf($this->getEmail(), 0, strpos_utf($this->getEmail(), '@'));
      } // if
    } // getFirstName
    
    /**
     * Return user's last name
     * 
     * @return string
     */
    function getLastName() {
    	$name = $this->getName();
    	
    	if($name) {
    		$pieces = explode(' ', $name);
    		
    		if(count($pieces) > 1) {
    			unset($pieces[0]); // Remove first name
    			
    			return implode(' ', $pieces);
    		} // if
    	} // if
    	
    	return '';
    } // getLastName
    
    /**
     * Return view URL
     *
     * @return string
     */
    function getViewUrl() {
      return 'mailto:' . $this->getEmail();
    } // getViewUrl
    
    /**
     * Return group ID
     * 
     * @return integer
     */
    function getGroupId() {
      return null;
    } // getGroupId
    
    /**
     * Return group name
     *
     * @return string
     */
    function getGroupName() {
      return lang('Anonymous Users');
    } // getGroupName
    
    /**
     * Cached langauge instance
     *
     * @var Language
     */
    private $language = false;
    
    /**
     * Return user's language
     *
     * @return Language
     */
    function getLanguage() {
      if($this->language === false) {
        $this->language = Languages::findDefault();
      } // if
      return $this->language;
    } // getLanguage
    
    /**
     * Return date format
     *
     * @return string
     */
    function getDateFormat() {
      return FORMAT_DATE;
    } // getDateFormat
    
    /**
     * Return time format
     *
     * @return string
     */
    function getTimeFormat() {
      return FORMAT_TIME;
    } // getTimeFormat
    
    /**
     * Return date time format
     *
     * @return string
     */
    function getDateTimeFormat() {
      return FORMAT_DATETIME;
    } // getDateTimeFormat
    
    /**
     * Return prefered mailing method for this user
     * 
     * @return string
     */
    function getMailingMethod() {
    	return AngieApplication::mailer()->getDefaultMailingMethod();
    } // getMailingMethod
    
    /**
     * Return user's last visit
     * 
     * @return DateTimeValue
     */
    function getLastVisitOn() {
      return new DateTimeValue('-30 days');
    } // getLastVisitOn
    
    /**
     * Returns true if this user can see $object
     *
     * @param IVisibility $object
     * @return boolean
     */
    function canSee(IVisibility $object) {
      return $object->getVisibility() >= VISIBILITY_NORMAL;
    } // canSee
    
    /**
     * Return user's min visibility
     *
     * @return integer
     */
    function getMinVisibility() {
    	return VISIBILITY_NORMAL;
    } // getMinVisibility
    
    /**
     * Returns true if this user has access to reports section
     * 
     * @return boolean
     */
    function canUseReports() {
      return false;
    } // canUseReports
    
    /**
     * Returns true if this account is active
     *
     * @return boolean
     */
    function isActive() {
      return true;
    } // isActive

    /**
     * Return true if this instance is a member
     *
     * @param bool $strict
     * @return bool
     */
    function isMember($strict = false) {
      return false;
    } // isMember
    
    /**
     * Returns true only if this person has administration permissions
     *
     * @return boolean
     */
    function isAdministrator() {
      return false;
    } // isAdministrator
    
    /**
     * Check if this user is the last administrator
     *
     * @return boolean
     */
    function isLastAdministrator() {
      return false;
    } // isLastAdministrator
    
    /**
     * Check if this user is the feed user
     * 
     * @return boolean
     */
    function isFeedUser() {
    	return false;
    } // if
    
    /**
     * Anonymous users can't be financial managers
     * 
     * @return boolean
     */
    function isFinancialManager() {
      return false;
    } // isFinancialManager
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Get value of name
     *
     * @return string
     */
    function getName() {
      return $this->name;
    } // getName
    
    /**
     * Set name of this anonymous user
     *
     * @param string $value
     */
    function setName($value) {
      $this->name = $value;
    } // setName
    
    /**
     * Get value of email
     *
     * @return string
     */
    function getEmail() {
      return $this->email;
    } // getEmail
    
    /**
     * Set email address
     *
     * @param string $value
     * @throws InvalidParamError
     */
    function setEmail($value) {
      if($value && is_valid_email($value)) {
        $this->email = $value;
      } else {
      	throw new InvalidParamError('value', $value, 'Value is required to be a valid email address');
      } // if
    } // setEmail
    
  }