<?php

  /**
   * RecurringProfile class
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class RecurringProfile extends InvoiceObject implements IState, IHistory {

    // Frequency
    const FREQUENCY_DAILY = 'daily';
    const FREQUENCY_WEEKLY = 'weekly';
    const FREQUENCY_TWO_WEEKS = '2 weeks';
    const FREQUENCY_MONTHLY = 'monthly';
    const FREQUENCY_TWO_MONTHS = '2 months';
    const FREQUENCY_THREE_MONTHS = '3 months';
    const FREQUENCY_SIX_MONTHS = '6 months';
    const FREQUENCY_YEARLY = 'yearly';
    const FREQUENCY_BIANNUAL = 'biannual';

    /**
     * Define fields used by this invoice object
     *
     * @var array
     */
    protected $fields = array(
      'id', 'type',
      'project_id', 'currency_id', 'language_id',
      'company_id', 'company_address',
      'name', 'private_note', 'note',
      'date_field_3', // start_on
      'varchar_field_1', 'varchar_field_2','varchar_field_3', 'integer_field_1', // frequency, occurrences, auto_issue
      'date_field_3', 'integer_field_2', // invoice_due_after
      'integer_field_3', 'date_field_1', 'date_field_2', // triggered_number, last_triggered_on, next_trigger_on
      'subtotal', 'tax', 'total', 'balance_due', 'paid_amount',
      'allow_payments',
      'second_tax_is_enabled', 'second_tax_is_compound',
      'state', 'original_state',
      'created_on', 'created_by_id', 'created_by_name', 'created_by_email',
      'recipient_id', 'recipient_name', 'recipient_email'
    );

    /**
     * Field map
     *
     * @var array
     */
    var $field_map = array(
      'last_triggered_on' => 'date_field_1',
      'next_trigger_on'   => 'date_field_2',
      'start_on'          => 'date_field_3',
      'frequency'         => 'varchar_field_1',
      'occurrences'       => 'varchar_field_2',
      'purchase_order_number' => 'varchar_field_3',
      'auto_issue'        => 'integer_field_1',
      'invoice_due_after' => 'integer_field_2',
      'triggered_number'    => 'integer_field_3'
    );

    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------

    /**
     * Get purchase order number
     *
     * @return string
     */
    function getPurchaseOrderNumber() {
      return $this->getVarcharField3();
    } // getPurchaseOrderNumber

    /**
     * Set the purchase order number
     *
     * @param string $purchase_order_number
     * @return string
     */
    function setPurchaseOrderNumber($purchase_order_number) {
      return $this->setVarcharField3($purchase_order_number);
    } // setPurchaseOrderNumber


    /**
     * Get Last Triggered On
     *
     * @return DateValue
     */
    function getLastTriggeredOn() {
      return $this->getDateField1();
    } // getLastTriggeredOn

    /**
     * Set Last Triggered On
     *
     * @param DateValue $date
     */
    function setLastTriggeredOn($date) {
      return $this->setDateField1($date);
    } // setLastTriggeredOn

    /**
     * Get Next Trigger On
     *
     * @return DateValue
     */
    function getNextTriggerOn() {
      return $this->getDateField2();
    } // getNextTriggerOn

    /**
     * Set Next Trigger On
     *
     * @param DateValue $date
     */
    function setNextTriggerOn($date) {
      return $this->setDateField2($date);
    } // setNextTriggerOn

    /**
     * Get Start On
     *
     * @return DateValue
     */
    function getStartOn() {
      return $this->getDateField3();
    } // getStartOn

    /**
     * Set Start On
     *
     * @param DateValue $date
     */
    function setStartOn($date) {
      return $this->setDateField3($date);
    } // setStartOn

    /**
     * Get Frequency
     *
     * @return string
     */
    function getFrequency() {
      return $this->getVarcharField1();
    } // getFrequency

    /**
     * Set Frequency
     *
     * @param string $frequency
     */
    function setFrequency($frequency) {
      return $this->setVarcharField1($frequency);
    } // setFrequency

    /**
     * Get Occurrences
     *
     * @return string
     */
    function getOccurrences() {
      return $this->getVarcharField2();
    } // getOccurrences

    /**
     * Set Occurrences
     *
     * @param string $occurrence
     */
    function setOccurrences($occurrences) {
      return $this->setVarcharField2($occurrences);
    } // setOccurrences

    /**
     * Get Auto Issue
     *
     * @return int
     */
    function getAutoIssue() {
      return $this->getIntegerField1();
    } // getAutoIssue

    /**
     * Set Auto Issue
     *
     * @param int $auto_issue
     */
    function setAutoIssue($auto_issue) {
      return $this->setIntegerField1($auto_issue);
    } // setAutoIssue

    /**
     * Get Invoice Due After
     *
     * @return int
     */
    function getInvoiceDueAfter() {
      return $this->getIntegerField2();
    } // getInvoiceDueAfter

    /**
     * Set Invoice Due After
     *
     * @param int $due_after
     */
    function setInvoiceDueAfter($due_after) {
      return $this->setIntegerField2($due_after);
    } // setInvoiceDueAfter

    /**
     * Get Trigger Number
     *
     * @return int
     */
    function getTriggeredNumber() {
      return $this->getIntegerField3();
    } // getTriggerNumber

    /**
     * Set Trigger Number
     *
     * @param $triggered_number
     */
    function setTriggeredNumber($triggered_number) {
      return $this->setIntegerField3($triggered_number);
    } // setTriggerNumber

    /**
     * Return invoice due after text
     */
    function getInvoiceDueAfterText() {
      switch ($this->getInvoiceDueAfter()) {
        case 0:
          return lang('Due Upon Receipt');
          break;
        case 10:
          return lang('10 Days After Issue (NET 10)');
          break;
        case 15:
          return lang('15 Days After Issue (NET 15)');
          break;
        case 30:
          return lang('30 Days After Issue (NET 30)');
          break;
        case 60:
          return lang('60 Days After Issue (NET 60)');
          break;
      } //switch
    } // getInvoiceDueAfterText

    /**
     * Return proper type name in user's language
     *
     * @param boolean $lowercase
     * @param Language $language
     * @return string
     */
    function getVerboseType() {
      return lang('Recurring Profile');
    } // getVerboseType

    /**
     * Cached Recipient
     *
     * @var User
     */
    var $recipient = false;

    /**
     * Return recipient
     *
     * @return User
     */
    function getRecipient() {
      if ($this->recipient === false) {
        if ($this->getRecipientId()) {
          $this->recipient = Users::findByid($this->getRecipientId());
          if (!$this->recipient instanceof User) {
            $this->recipient = new AnonymousUser($this->getRecipientName(),$this->getRecipientEmail());
          } // if
        } // if
      } // if

      return $this->recipient;
    }//getRecipient

    /**
     * Ser recipient
     *
     * @param $recipient - can be instance of IUser or id of user
     */
    function setRecipient($recipient) {
      if(!$recipient instanceof IUser) {
        $recipient = Users::findById($recipient);
      } // if

      if($recipient instanceof IUser) {
        $this->setRecipientEmail($recipient->getEmail());
        $this->setRecipientId($recipient->getId());
        $this->setRecipientName($recipient->getName());
      } // if
    } // setRecipient

    /**
     * Return true if profile started already
     *
     * @return Boolean
     */
    function isStarted() {
      $today = new DateValue();
      return strtotime($today) >= strtotime($this->getStartOn());
    } // isStarted

    /**
     * Set Next Trigger On Date
     *
     * @param string $frequency
     * @param bool $start_on
     */
    function setNextTriggerOnDate($frequency, $start_on = false) {
      if($start_on) {
        $last_triggered_on = $start_on;
      } else {
        $last_triggered_on = date("Y-m-d");
      }//if

      switch ($frequency) {
        case self::FREQUENCY_DAILY:
          $next_trigger_on = date("Y-m-d",strtotime($last_triggered_on . " +1 day"));
          break;
        case self::FREQUENCY_WEEKLY:
          $next_trigger_on = date("Y-m-d",strtotime($last_triggered_on . " +1 week"));
          break;
        case self::FREQUENCY_TWO_WEEKS:
          $next_trigger_on = date("Y-m-d",strtotime($last_triggered_on . " +2 week"));
          break;
        case self::FREQUENCY_MONTHLY:
          $next_trigger_on = date("Y-m-d",strtotime($last_triggered_on . " +1 month"));
          break;
        case self::FREQUENCY_TWO_MONTHS:
          $next_trigger_on = date("Y-m-d",strtotime($last_triggered_on . " +2 month"));
          break;
        case self::FREQUENCY_THREE_MONTHS:
          $next_trigger_on = date("Y-m-d",strtotime($last_triggered_on . " +3 month"));
          break;
        case self::FREQUENCY_SIX_MONTHS:
          $next_trigger_on = date("Y-m-d",strtotime($last_triggered_on . " +6 month"));
          break;
        case self::FREQUENCY_YEARLY:
          $next_trigger_on = date("Y-m-d",strtotime($last_triggered_on . " +1 year"));
          break;
        case self::FREQUENCY_BIANNUAL:
          $next_trigger_on = date("Y-m-d",strtotime($last_triggered_on . " +2 year"));
          break;
      }//switch

      $this->setNextTriggerOn($next_trigger_on);
    } // setNextTriggerOn

    /**
     * Check to see if this profile is archived
     *
     * @return boolean
     */
    function isArchived() {
      return $this->getState() == STATE_ARCHIVED ? true : false;
    } // isArchived

    /**
     * Check to see if this recurring profile skip to trigger (If administrator forget to approve it or similar)
     *
     * @return boolean
     */
    function isSkippedToTrigger() {
      $today = new DateValue();
      if (strtotime($today) > strtotime($this->getNextTriggerOn()) && $this->getState() == STATE_VISIBLE) {
        return true;
      }//if
      return false;
    } // isSkippedToTrigger

    /**
     * Return next occurrence number
     */
    function getNextOccurrenceNumber() {
      return $this->getTriggeredNumber() + 1;
    } // getNextOccurrenceNumber

    /**
     * Return is last occurrence
     */
    function isLastOccurrence() {
      return $this->getNextOccurrenceNumber() == $this->getOccurrences();
    } // isLastOccurrence

    /**
     * Increase triggered number on trigger
     */
    function increaseTriggeredNumber() {
      $number = $this->getTriggeredNumber() + 1;
      $this->setTriggeredNumber($number);
    } // increaseTriggeredNumber

    /**
     * Reset triggered number to 0
     */
    function resetTriggeredNumber() {
      $this->setTriggeredNumber(0);
      return $this->save();
    } // resetTriggeredNumber

    /**
     * Return allow payments display text
     *
     * @return string
     */
    function getAllowPaymentsText() {
      if (!$this->getAllowPayments()) {
        return lang('Do not allow payments');
      } else if ($this->getAllowPayments() == 1) {
        return lang('Allow full payments');
      } else if ($this->getAllowPayments() == 2) {
        return lang('Allow partial payments');
      } else if ($this->getAllowPayments() == -1) {
        return lang('Use system default');
      } // if
    } // getAllowPaymentsText

    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------

    /**
     * State helper instance
     *
     * @var IStateImplementation
     */
    private $state = false;

    /**
     * Return state helper instance
     *
     * @return IRecurringProfileStateImplementation
     */
    function state() {
      if($this->state === false) {
        $this->state = new IRecurringProfileStateImplementation($this);
      } // if

      return $this->state;
    } // state

	  /**
	   * Cached history implementation instance
	   *
	   * @var IHistoryImplementation
	   */
	  private $history = false;

	  /**
	   * Return history helper instance
	   *
	   * @return IHistoryImplementation
	   */
	  function history() {
		  if($this->history === false) {
			  $this->history = new IHistoryImplementation($this, array('company_id', 'company_name', 'company_address', 'currency_id', 'language_id', 'note', 'private_note', 'status'));
		  } // if

		  return $this->history;
	  } // history

    // ---------------------------------------------------
    //  URLS
    // ---------------------------------------------------

    /**
     * Return main page url
     */
    function getMainPageUrl() {
      return Router::assemble('recurring_profiles');
    } // getMainPageUrl

    /**
     * Return view url
     */
    function getViewUrl() {
      return Router::assemble('recurring_profile', array('recurring_profile_id' => $this->getId()));
    } // getViewUrl

    /**
     * Return add new recurring profile url
     */
    function getAddUrl() {
      return Router::assemble('recurring_profile_add');
    } // getAddUrl

    /**
     * Return edit recurring profile url
     */
    function getEditUrl() {
      return Router::assemble('recurring_profile_edit', array('recurring_profile_id' => $this->getId()));
    } // getAddUrl

    /**
     * Return trigger recurring profile url
     */
    function getTriggerUrl() {
      return Router::assemble('recurring_profile_trigger', array('recurring_profile_id' => $this->getId()));
    } // getTriggerUrl

    /**
     * Return duplicate recurring profile url
     */
    function getDuplicateUrl() {
      return Router::assemble('recurring_profile_duplicate', array('recurring_profile_id' => $this->getId()));
    } // getDuplicateUrl

    // ---------------------------------------------------
    //  OPTIONS
    // ---------------------------------------------------

    /**
     * Prepare list of options that $user can use
     *
     * @param IUser $user
     * @param NamedList $options
     * @param string $interface
     * @return NamedList
     */
    protected function prepareOptionsFor(IUser $user, NamedList $options, $interface = AngieApplication::INTERFACE_DEFAULT) {
      parent::prepareOptionsFor($user, $options, $interface);

      // mark archive as important
      if ($options->exists('archive')) {
        $archive = $options->get('archive');
        $archive['important'] = true;
        $options->add('archive', $archive);
      } // if

      if($this->isSkippedToTrigger()) {
        $options->add('trigger', array(
          'text' => lang('Trigger'),
          'url'  => $this->getTriggerUrl(),
          'important' => true,
          'onclick' => new AsyncLinkCallback(array(
            'confirmation' => lang('Are you sure that you want to trigger this :object_name profile?', array("object_name" => $this->getName())),
            'success_message' => lang(':object_name profile has been successfully triggered', array("object_name" => $this->getName())),
            'success_event' => $this->getUpdatedEventName(),
          ))
        ));
      }//if

      if(RecurringProfiles::canAdd($user)) {
        $options->add('duplicate', array(
          'text' => lang('Duplicate'),
          'url'  => $this->getDuplicateUrl(),
          'onclick' => new FlyoutFormCallback($this->getCreatedEventName()),
          'icon' => AngieApplication::getImageUrl('icons/12x12/duplicate-invoice.png', INVOICING_MODULE)
        ));
      }//if

      return $options;
    } // prepareOptionsFor

    // ---------------------------------------------------
    //  SYSTEM
    // ---------------------------------------------------

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

      $result['purchase_order_number'] = $this->getPurchaseOrderNumber();

      $result['start_on'] = $this->getStartOn();
      $result['frequency'] = $this->getFrequency();
      $result['occurrence'] = $this->getOccurrences();
      $result['auto_issue'] = $this->getAutoIssue();
      $result['invoice_due_after'] = $this->getInvoiceDueAfterText();

      $result['next_trigger_on'] = $this->getNextTriggerOn();
      $result['next_occurrence_number'] = $this->getNextOccurrenceNumber();
      $result['triggered_number'] = $this->getTriggeredNumber();
      $result['is_skipped'] = $this->isSkippedToTrigger();
      $result['occurrence_left'] = $this->getTriggeredNumber() . '/' . $this->getOccurrences();

      $result['allow_payments_text'] = $this->getAllowPaymentsText();

      return $result;
    } // describe

    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {

      if(!$this->validatePresenceOf('name')) {
        $errors->addError(lang('Please enter recurring profile name.'), 'name');
      } // if

      if(!$this->validatePresenceOf('occurrences', 1)) {
        $errors->addError(lang('Occurrence has to be numeric value larger than 0'), 'occurrence');
      } // if

      if(!$this->validatePresenceOf('start_on') || !$this->getStartOn() instanceof DateValue) {
        $errors->addError(lang('Start date has to be valid date value.'), 'start_on');
      } // if

      if(!$this->validatePresenceOf('company_address')) {
        $errors->addError(lang('Please enter company address.'), 'company_address');
      } // if

      parent::validate($errors, true);
    } // validate

  }