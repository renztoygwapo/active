<?php

  /**
   * Class description
   *
   * @package
   * @subpackage
   */
  class PasswordPolicy {

    // Password hashing mechanism
    const HASHED_WITH_SHA1 = 'sha1';
    const HASHED_WITH_PBKDF2 = 'pbkdf2';

    // Password checker responses
    const PASSWORD_TOO_SHORT = 1;
    const PASSWORD_HAS_NO_NUMBERS = 2;
    const PASSWORD_USES_SAME_CASE = 3;
    const PASSWORD_HAS_NO_SYMBOLS = 4;

    /**
     * Validate password
     *
     * @param string $password
     * @param ValidationErrors $errors
     */
    function validateUserPassword($password, &$errors) {
      if(empty($password)) {
        $errors->addError(lang('Password value is required'), 'password');
      } // if

      $check = $this->checkPassword($password);

      if($check !== true) {
        switch($this->checkPassword($password)) {
          case PasswordPolicy::PASSWORD_TOO_SHORT:
            $errors->addError(lang('Password is too short'));
            break;
          case PasswordPolicy::PASSWORD_HAS_NO_NUMBERS:
            $errors->addError(lang('Password requires at least one number'));
            break;
          case PasswordPolicy::PASSWORD_USES_SAME_CASE:
            $errors->addError(lang('Password requires at least one lower case and at least one upper case letter'));
            break;
          case PasswordPolicy::PASSWORD_HAS_NO_SYMBOLS:
            $errors->addError(lang('Password requires that you use at least one symbol'));
            break;
        } // if
      } // if
    } // validateUserPassword

    /**
     * Check if $password is OK
     *
     * @param string $password
     * @return boolean
     */
    function checkPassword($password) {
      $password = trim($password);

      if($password) {
        if($this->getMinLength() !== null && strlen($password) < $this->getMinLength()) {
          return PasswordPolicy::PASSWORD_TOO_SHORT;
        } // if

        if($this->requireNumbers() && !preg_match('#\d#', $password)) {
          return PasswordPolicy::PASSWORD_HAS_NO_NUMBERS;
        } // if

        if($this->requireMixedCase() && !(preg_match('/[A-Z]+/', $password) && preg_match('/[a-z]+/', $password))) {
          return PasswordPolicy::PASSWORD_USES_SAME_CASE;
        } // if

        if($this->requireSymbols() && !preg_match('/[,.;:!$%^&]+/', $password)) {
          return PasswordPolicy::PASSWORD_HAS_NO_SYMBOLS;
        } // if

        return true;
      } // if

      return false;
    } // checkPassword

    /**
     * Generate a new password
     *
     * @param integer $length
     * @return string
     * @throws InvalidParamError
     */
    function generatePassword($length = null) {
      $length = empty($lenght) ? 20 : (integer) $length;

      if($length < 10) {
        throw new InvalidParamError('length', $length, 'Minimum password lenght can not be less than 10 letters');
      } // if

      return make_string($length, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890,.;:!$%^&');
    } // generatePassword

    // ---------------------------------------------------
    //  Hashing
    // ---------------------------------------------------

    /**
     * Returns true if $password password matches hased password that we stored somewhere
     *
     * @param string $raw
     * @param string $hashed
     * @param string $hashed_with
     * @return boolean
     */
    function isCurrentPassword($password, $hashed, $hashed_with = PasswordPolicy::HASHED_WITH_PBKDF2) {
      if($hashed_with == PasswordPolicy::HASHED_WITH_SHA1 || $hashed_with == PasswordPolicy::HASHED_WITH_PBKDF2) {
        return $this->hashPassword($password, $hashed_with) == $hashed;
      } else {
        throw new InvalidParamError('hashed_with', $hashed_with, 'Hashing mechanism can be SHA1 or PBKDF2');
      } // if
    } // isCurrentPassword

    /**
     * Return hashed password
     *
     * @param string $password
     * @param string $has_with
     * @return array
     */
    function hashPassword($password, $hash_with = PasswordPolicy::HASHED_WITH_PBKDF2) {
      if($hash_with == PasswordPolicy::HASHED_WITH_SHA1) {
        return sha1(APPLICATION_UNIQUE_KEY . $password);
      } elseif($hash_with == PasswordPolicy::HASHED_WITH_PBKDF2) {
        return base64_encode(pbkdf2($password, APPLICATION_UNIQUE_KEY, 1000, 40));
      } else {
        throw new InvalidParamError('hash_with', $hash_with, 'Hashing mechanism can be SHA1 or PBKDF2');
      } // if
    } // hashPassword
    
    // ---------------------------------------------------
    //  Configuration
    // ---------------------------------------------------

    /**
     * Return min password length. If this function returns null, system will not check password length
     *
     * @return mixed
     */
    function getMinLength() {
      return null;
    } // getMinLength

    /**
     * Returns true if system requires that passwords contain numbers
     *
     * @return bool
     */
    function requireNumbers() {
      return false;
    } // requireNumbers

    /**
     * Returns true if system requires that passwords contain numbers
     *
     * @return bool
     */
    function requireMixedCase() {
      return false;
    } // requireMixedCase

    /**
     * Returns true if system requires that passwords contain numbers
     *
     * @return bool
     */
    function requireSymbols() {
      return false;
    } // requireSymbols

    /**
     * Return number of months that password is valid
     *
     * @return int
     */
    function getAutoExpire() {
      return 0;
    } // getAutoExpire

  }