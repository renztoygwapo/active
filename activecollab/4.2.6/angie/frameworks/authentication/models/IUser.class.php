<?php

  /**
   * Interface that all user instances need to implement
   *
   * @package angie.frameworks.authentication
   * @subpackage models
   */
  interface IUser {

    /**
     * Return name of this user
     *
     * @return string
     */
    function getName();
    
    /**
     * Return email address of a given user
     *
     * @return string
     */
    function getEmail();
    
    /**
     * Return display name of this user
     *
     * @param boolean $short
     */
    function getDisplayName($short = false);
    
    /**
     * Return first name of this user
     *
     * @return string
     */
    function getFirstName();
    
    /**
     * Return user group ID
     * 
     * @return mixed
     */
    function getGroupId();
    
    /**
     * Return name of the group to which this user belongs to (company, group etc)
     *
     * @return string
     */
    function getGroupName();
    
    /**
     * Return language instance
     * 
     * In case user is using default language, system will return NULL
     *
     * @return Language
     */
    function getLanguage();
    
    /**
     * Return date format
     *
     * @return string
     */
    function getDateFormat();
    
    /**
     * Return time format
     *
     * @return string
     */
    function getTimeFormat();
    
    /**
     * Return date time format
     *
     * @return string
     */
    function getDateTimeFormat();
    
    /**
     * Return prefered mailing method for this user
     * 
     * @return string
     */
    function getMailingMethod();
    
    /**
     * Return date time of user's last visit
     * 
     * @return DateTimeValue
     */
    function getLastVisitOn();
    
    /**
     * Returns true if this user can see $object
     *
     * @param IVisibility $object
     */
    function canSee(IVisibility $object);
    
    /**
     * Return user's min visibility
     *
     * @return boolean
     */
    function getMinVisibility();
    
    /**
     * Returns true if this user has access to reports section
     * 
     * @return boolean
     */
    function canUseReports();
    
    /**
     * Returns true if this particular account is active
     *
     * @return boolean
     */
    function isActive();

    /**
     * Return true if this instance is member
     *
     * @param bool $strict
     * @return bool
     */
    function isMember($strict = false);
    
    /**
     * Returns true only if this person has administration permissions
     *
     * @return boolean
     */
    function isAdministrator();
    
    /**
     * Check if this user is the last administrator
     *
     * @return boolean
     */
    function isLastAdministrator();
    
    /**
     * Return true if this user is financial manager
     * 
     * @return boolean
     */
    function isFinancialManager();
    
  }