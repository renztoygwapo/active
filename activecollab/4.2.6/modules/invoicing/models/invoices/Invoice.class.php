<?php

  /**
   * Invoice record class
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class Invoice extends InvoiceObject implements IState, IPayments, IConfigContext, IActivityLogs, IHistory, IInspector, IInvoice {

    // Invoice setting
    const INVOICE_SETTINGS_SUM_ALL = 'sum_all';
    const INVOICE_SETTINGS_SUM_ALL_BY_PROJECT = 'sum_all_by_project';
    const INVOICE_SETTINGS_SUM_ALL_BY_TASK = 'sum_all_by_task';
    const INVOICE_SETTINGS_SUM_ALL_BY_JOB_TYPE = 'sum_records_by_job_type';
    const INVOICE_SETTINGS_KEEP_AS_SEPARATE = 'keep_records_as_separate_invoice_items';

    //notify financial managers about new invoice
    const INVOICE_NOTIFY_FINANCIAL_MANAGERS_NONE = 0; // 'Don't Notify Financial Managers';
    const INVOICE_NOTIFY_FINANCIAL_MANAGERS_SELECTED = 1; // 'Notify Selected Financial Managers';
    const INVOICE_NOTIFY_FINANCIAL_MANAGERS_ALL = 2; // 'Notify All Financial Managers';

    /**
     * Define fields used by this invoice object
     *
     * @var array
     */
    protected $fields = array(
      'id', 'type',
      'based_on_type', 'based_on_id',
      'company_id', 'project_id', 'currency_id', 'language_id',
      'varchar_field_1', // number
      //'company_name',
      'company_address',
      'varchar_field_2', // purchase_order_number
      'private_note', 'note',
      'subtotal', 'tax', 'total', 'balance_due', 'paid_amount',
      'status', 'state', 'original_state',
      'second_tax_is_enabled', 'second_tax_is_compound',
      'date_field_2', 'integer_field_1', 'varchar_field_3', 'varchar_field_4', 'integer_field_2', // issued_on, issued_by_id, issued_by_name, issued_by_email, issued_to_id
      'date_field_1', // due on
      'closed_on', 'closed_by_id', 'closed_by_name', 'closed_by_email',
      'created_on', 'created_by_id', 'created_by_name', 'created_by_email',
      'reminder_sent_on',
      'allow_payments','hash'
    );

    /**
     * Field map
     *
     * @var array
     */
    var $field_map = array(
      'number'                => 'varchar_field_1',
      'purchase_order_number' => 'varchar_field_2',
      'issued_by_name'        => 'varchar_field_3',
      'issued_by_email'       => 'varchar_field_4',
      'issued_by_id'          => 'integer_field_1',
      'issued_to_id'          => 'integer_field_2',
      'due_on'                => 'date_field_1',
      'issued_on'             => 'date_field_2'
    );

    /**
     * List of protected fields (can't be set using setAttributes() method)
     *
     * @var array
     */
    protected $protect = array(
      'state',
      'status',
      'issued_on',
      'issued_by_id',
      'issued_by_name',
      'issued_by_email',
      'closed_on',
      'closed_by_id',
      'closed_by_name',
      'closed_by_email',
      'created_on',
      'created_by_id',
      'created_by_name',
      'created_by_email'
    );

    /**
     * Return true if this invoice is credit invoice and has total less then zero
     *
     * @return boolean
     */
    function isCreditInvoice() {
      return $this->getTotal() <= 0;
    } //isCreditInvoice

    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------

    /**
     * Return verbose type name
     *
     * @param boolean $lowercase
     * @param Language $language
     * @return string
     */
    function getVerboseType($lowercase = false, $language = null) {
      return $lowercase ? lang('invoice', null, true, $language) : lang('Invoice', null, true, $language);
    } // getVerboseType

    /**
     * Get the invoice number
     *
     * @return string
     */
    function getNumber() {
      return $this->getVarcharField1();
    } // getNumber

    /**
     * Set the invoice number
     *
     * @param int $number
     * @return string
     */
    function setNumber($number) {
      return $this->setVarcharField1($number);
    } // setNumber

    /**
     * Get purchase order number
     *
     * @return string
     */
    function getPurchaseOrderNumber() {
      return $this->getVarcharField2();
    } // getPurchaseOrderNumber

    /**
     * Set the purchase order number
     *
     * @param string $purchase_order_number
     * @return string
     */
    function setPurchaseOrderNumber($purchase_order_number) {
      return $this->setVarcharField2($purchase_order_number);
    } // setPurchaseOrderNumber

    /**
     * Get Issued On
     *
     * @return DateTimeValue
     */
    function getIssuedOn() {
      return $this->getDateField2();
    } // getIssuedOn

    /**
     * Set issued on
     *
     * @param $issued_on
     * @return DateValue
     */
    function setIssuedOn($issued_on) {
      return $this->setDateField2($issued_on);
    } // setIssuedOn

    /**
     * Return issued On Month
     *
     * @return string
     */
    function getIssuedOnMonth() {
      if ($this->getStatus() > 0) {
        return $this->getIssuedOn()->getMonth() . ', ' . $this->getIssuedOn()->getYear();
      }//if
      return 0;
    } //getIssuedOnMonth

    /**
     * Get issued by id
     *
     * @return int
     */
    function getIssuedById() {
      return $this->getIntegerField1();
    } // getIssuedById

    /**
     * Set issued by id
     *
     * @param int $issued_by_id
     * @return integer
     */
    function setIssuedById($issued_by_id) {
      return $this->setIntegerField1($issued_by_id);
    } // setIssuedById

    /**
     * Get issued by name
     *
     * @return string
     */
    function getIssuedByName() {
      return $this->getVarcharField3();
    } // getIssuedByName

    /**
     * Set issued by name
     *
     * @param string $issued_by_name
     * @return string
     */
    function setIssuedByName($issued_by_name) {
      return $this->setVarcharField3($issued_by_name);
    } // setIssuedByName

    /**
     * Get issued by email
     *
     * @return string
     */
    function getIssuedByEmail() {
      return $this->getVarcharField4();
    } // getIssuedByEmail

    /**
     * Set issued by email
     *
     * @param string $issued_by_email
     * @return string
     */
    function setIssuedByEmail($issued_by_email) {
      return $this->setVarcharField4($issued_by_email);
    } // setIssuedByEmail

    /**
     * Cached issued by instance
     *
     * @var User
     */
    private $issued_by = false;

    /**
     * Return user object of person who issued this invoice
     *
     * @return User
     */
    function getIssuedBy() {
      if ($this->issued_by === false) {
        if ($this->getIssuedById()) {
          $this->issued_by = DataObjectPool::get('User', $this->getIssuedById());
        } // if

        if (!$this->issued_by instanceof IUser && is_valid_email($this->getIssuedByEmail())) {
          $this->issued_by = new AnonymousUser($this->getIssuedByName(), $this->getIssuedByEmail());
        } // if
      } // if

      return $this->issued_by;
    } // getIssuedBy

    /**
     * Get issued to id
     *
     * @return int
     */
    function getIssuedToId() {
      return $this->getIntegerField2();
    } // getIssuedToId

    /**
     * Set issued to id
     *
     * @param $issued_to_id
     * @return integer
     */
    function setIssuedToId($issued_to_id) {
      return $this->setIntegerField2($issued_to_id);
    } // setIssuedToId

    /**
     * Cached issued to by instance
     *
     * @var User
     */
    private $issued_to = false;

    /**
     * Return user to which invoice is issued to
     *
     * @return User
     */
    function getIssuedTo() {
      if ($this->issued_to === false) {
        $this->issued_to = DataObjectPool::get('User', $this->getIssuedToId());
      } // if
      return $this->issued_to;
    } // getIssuedTo

    /**
     * Set issued to
     *
     * @param null|User $value
     * @return null|User
     * @throws InvalidInstanceError
     */
    function setIssuedTo($value) {
      if($value instanceof User) {
        $this->setIssuedToId($value->getId());
      } elseif($value === null) {
        $this->setIssuedToId(null);
      } else {
        throw new InvalidInstanceError('user', $value, '$user should be instance of User class or NULL');
      } // if

      $this->issued_to = $value;
      return $this->issued_to;
    } // setIssuedTo

    /**
     * Get due on
     *
     * @return DateTimeValue
     */
    function getDueOn() {
      return $this->getDateField1();
    } // getDueOn

    /**
     * Set Due On
     *
     * @param $due_on
     * @return DateValue
     */
    function setDueOn($due_on) {
      return $this->setDateField1($due_on);
    } // setDueOn

    /**
     * Return due On Month
     *
     * @return string
     */
    function getDueOnMonth() {
      if($this->getStatus() > 0) {
        return $this->getDueOn()->getMonth() . ', ' . $this->getDueOn()->getYear();
      }//if
      return 0;
    }//getDueOnMonth

    /**
     * Return invoice name
     *
     * @param bool $short
     * @return string
     */
    function getName($short = false) {
      return Invoices::getInvoiceName($this->getId(), $this->getStatus(), $this->getNumber(), $short);
    } // getName

    // ---------------------------------------------------
    //  Items
    // ---------------------------------------------------

    /**
     * Add Item
     *
     * @param array $item
     * @param int $position
     * @return mixed
     */
    function addItem($item, $position) {
      $inserted_item = parent::addItem($item, $position);

      return $inserted_item;
    } // addItem

    /**
     * Generates invoice id by invoice pattern
     *
     * @return string
     */
    function generateInvoiceId() {
      $pattern = ConfigOptions::getValue('invoicing_number_pattern');
      $padding = (integer) ConfigOptions::getValue('invoicing_number_counter_padding');

      // retrieve counters
      list($total_counter, $year_counter, $month_counter) = Invoices::getDateInvoiceCounters();
      $total_counter++; $year_counter++; $month_counter++;

      // Apply padding, if needed
      $prepared_total_counter = $padding ? str_pad($total_counter, $padding, '0', STR_PAD_LEFT) : $total_counter;
      $prepared_year_counter = $padding ? str_pad($year_counter, $padding, '0', STR_PAD_LEFT) : $year_counter;
      $prepared_month_counter = $padding ? str_pad($month_counter, $padding, '0', STR_PAD_LEFT) : $month_counter;

      // retrieve variables
      $variable_year = date('Y');
      $variable_month = date('n');
      $variable_month_short = date('M');
      $variable_month_long = date('F');

      $generated_invoice_id = str_ireplace(array(
        INVOICE_NUMBER_COUNTER_TOTAL,
        INVOICE_NUMBER_COUNTER_YEAR,
        INVOICE_NUMBER_COUNTER_MONTH,
        INVOICE_VARIABLE_CURRENT_YEAR,
        INVOICE_VARIABLE_CURRENT_MONTH,
        INVOICE_VARIABLE_CURRENT_MONTH_SHORT,
        INVOICE_VARIABLE_CURRENT_MONTH_LONG,
      ), array(
        $prepared_total_counter,
        $prepared_year_counter,
        $prepared_month_counter,
        $variable_year,
        $variable_month,
        $variable_month_short,
        $variable_month_long,
      ), $pattern);

      return $generated_invoice_id;
    } // generateInvoiceId

    // ---------------------------------------------------
    //  Options
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
      if($this->canView($user) && !$this->isCanceled()) {
        $options->add('view_pdf', array(
          'url' => $this->canEdit($user) ? $this->getPdfUrl() : $this->getCompanyPdfUrl(),
          'text' => $this->getStatus() == INVOICE_STATUS_DRAFT ? lang('Preview PDF') : lang('View PDF'),
          'onclick' => new TargetBlankCallback(),
          'icon' => AngieApplication::getImageUrl('icons/12x12/download-pdf.png', INVOICING_MODULE),
          'important' => true
        ), true);
      } // if

      if ($this->isIssued() && $this->canView($user) && !$this->isCreditInvoice() && $this->payments()->canMake($user) && ($user->isFinancialManager() || ($this->payments()->hasDefinedGateways()))) {
        $options->add('make_a_payment', array(
          'url' => $this->payments()->getAddUrl(),
          'text' => lang('Make a Payment'),
          'onclick' => new FlyoutFormCallback('payment_created'),
          'icon' => AngieApplication::getImageUrl('icons/12x12/add-payment.png', INVOICING_MODULE),
          'important' => true
        ));

        if($this->payments()->canMakePublicPayment($user)) {
          $options->add('make_a_public_payment', array(
            'url' => $this->getPublicPaymentInfoUrl(),
            'text' => lang('Payments Options'),
            'onclick' => new FlyoutFormCallback('payment_created', array(
              'width' => 600
            ))
          ));
        } //if

      } // if

      if($this->isIssued() && $this->canView($user) && $user->isFinancialManager() && $this->isCreditInvoice()) {
        $options->add('mark_as_paid', array(
          'url' => $this->getMarkAsPaidUrl(),
          'text' => lang('Mark as Paid'),
          'onclick' => new AsyncLinkCallback(array(
            'confirmation' => lang('Are you sure that you want to mark this invoice as paid? You can not undo this action.'),
            'success_message' => lang('Invoice has been marked as paid'),
            'success_event' => $this->getUpdatedEventName()
          )),
          'icon' => AngieApplication::getImageUrl('icons/12x12/add-payment.png', INVOICING_MODULE),
          'important' => true
        ));
      } //if

      if ($this->canResendEmail($user)) {
        $options->add('resend_email', array(
          'url' => $this->getNotifyUrl(),
          'text' => lang('Resend Email'),
          'onclick' => new FlyoutFormCallback('resend_email', array(
            'width' => 400
          )),
          'important' => $this->isOverdue() || $this->isPaid()
        ));
      } // if

      if($this->canIssue($user)) {
        $options->add('issue', array(
          'url' => $this->getIssueUrl(),
          'text' => lang('Issue'),
          'onclick' => new FlyoutFormCallback($this->getUpdatedEventName(), array(
            'width' => 400,
          )),
          'icon' => AngieApplication::getImageUrl('icons/12x12/issue-invoice.png', INVOICING_MODULE),
          'important' => true
        ), true);
      } // if

      if($this->canCancel($user)) {
        $options->add('cancel', array(
          'url' => $this->getCancelUrl(),
          'text' => lang('Cancel'),
          'onclick' => new AsyncLinkCallback(array(
            'confirmation' => lang('Are you sure that you want to cancel this invoice? All existing payments associated with this invoice will be deleted!'),
            'success_message' => lang('Invoice has been successfully canceled'),
            'success_event' => $this->getUpdatedEventName()
          ))
        ), true);
      } // if

      if(Invoices::canAdd($user)) {
        $options->add('duplicate', array(
          'url' => $this->getDuplicateUrl(),
          'text' => lang('Duplicate'),
          'onclick' => new FlyoutFormCallback('invoice_created'),
          'icon' => AngieApplication::getImageUrl('icons/12x12/duplicate-invoice.png', INVOICING_MODULE),
        ), true);
      } // if

      if($this->canViewRelatedItems($user) && ($this->countTimeRecords() || $this->countExpenses())) {
        $options->add('time', array(
          'url' => $this->getTimeUrl(),
          'text' => lang('Items (:count)', array('count' => $this->countTimeRecords() + $this->countExpenses())),
        ), true);
      } // if


      if($this->canEdit($user)) {
        $options->add('edit', array(
          'url' => $this->getEditUrl(),
          'text' => lang('Edit'),
          'onclick' => new FlyoutFormCallback('invoice_updated'),
          'icon' => AngieApplication::getImageUrl('icons/12x12/edit.png', ENVIRONMENT_FRAMEWORK),
          'important' => true
        ), true);
      } // if

      if ($this->canChangeLanguage($user)) {
        $options->add('change_language', array(
          'url' => $this->getChangeLanguageUrl(),
          'text' => lang('Change Language'),
          'onclick' => new FlyoutFormCallback('invoice_updated', array(
            'width' => 'narrow'
          )),
          'icon' => AngieApplication::getImageUrl('icons/12x12/edit.png', ENVIRONMENT_FRAMEWORK),
          'important' => true
        ), false);
      } // if

      if($this->canDelete($user)) {
        $options->add('delete', array(
          'url' => $this->getDeleteUrl(),
          'text' => lang('Delete'),
          'onclick' => new AsyncLinkCallback(array(
            'confirmation' => lang('Are you sure that you want to delete this invoice?'),
            'success_message' => lang('Invoice successfully deleted'),
            'success_event' => 'invoice_deleted'
          )),
          'icon' => AngieApplication::getImageUrl('icons/12x12/delete.png', ENVIRONMENT_FRAMEWORK),
        ), true);
      } // if

      parent::prepareOptionsFor($user, $options, $interface);
    } // prepareOptionsFor

    // ---------------------------------------------------
    //  Status
    // ---------------------------------------------------

    /**
     * Return verbose invoice status
     *
     * @return string
     */
    function getVerboseStatus() {
      switch($this->getStatus()) {
        case INVOICE_STATUS_DRAFT:
          return lang('Draft');
        case INVOICE_STATUS_ISSUED:
          return lang('Issued');
        case INVOICE_STATUS_PAID:
          return lang('Paid');
        case INVOICE_STATUS_CANCELED:
          return lang('Canceled');
      } // switch
    } // getVerboseStatus

    /**
     * Change invoice status
     *
     * @param int $status
     * @param User $by
     * @param DateValue $on
     * @param array $additional_params
     * @return int|void
     * @throws InvalidParamError
     */
    function setStatus($status, $by = null, $on = null, $additional_params = null) {
      $by = $by instanceof IUser ? $by : Authentication::getLoggedUser();
      $on = $on instanceof DateValue ? $on : new DateValue();

      switch($status) {

        // Mark invoice as draft
        case INVOICE_STATUS_DRAFT:
          parent::setStatus($status);

          $this->setIssuedOn(null);
          $this->setIssuedById(null);
          $this->setIssuedByName(null);
          $this->setIssuedByEmail(null);

          $this->setClosedOn(null);
          $this->setClosedById(null);
          $this->setClosedByName(null);
          $this->setClosedByEmail(null);
          break;

        // Mark invoice as issued
        case INVOICE_STATUS_ISSUED:
          parent::setStatus($status);

          if($on) {
            $this->setIssuedOn($on);
          } // if

          if($by) {
            $this->setIssuedById($by->getId());
            $this->setIssuedByName($by->getName());
            $this->setIssuedByEmail($by->getEmail());
          } // if

          $this->setClosedOn(null);
          $this->setClosedById(null);
          $this->setClosedByName(null);
          $this->setClosedByEmail(null);

          $this->setTimeRecordsStatus(BILLABLE_STATUS_PENDING_PAYMENT);
          $this->setExpensesStatus(BILLABLE_STATUS_PENDING_PAYMENT);
          break;

        // Mark invoice as paid
        case INVOICE_STATUS_PAID:
          parent::setStatus(INVOICE_STATUS_PAID);

          $this->setClosedOn($on);
          $this->setClosedById(($by instanceof User ? $by->getId() : 0));
          $this->setClosedByName($by->getName());
          $this->setClosedByEmail($by->getEmail());

          $this->setTimeRecordsStatus(BILLABLE_STATUS_PAID);
          $this->setExpensesStatus(BILLABLE_STATUS_PAID);

          // recalculate
          $this->recalculate(true);

          // Sent notification invoice paid
          if($this->getIssuedTo() instanceof IUser && !$this->getIssuedTo()->isFinancialManager() && (isset($additional_params['notify_client']) && $additional_params['notify_client'])) {
            AngieApplication::notifications()
              ->notifyAbout('invoicing/invoice_paid', $this)
              ->sendToUsers($this->getIssuedTo());
          } // if

          // Notify financial managers (all or everyone execept manager making the payment)
          $exclude_user = $by->isFinancialManager() ? $by : null;
          
          AngieApplication::notifications()
            ->notifyAbout('invoicing/invoice_paid', $this)
            ->sendToFinancialManagers(false, $exclude_user);

          break;

        // Mark invoice as canceled
        case INVOICE_STATUS_CANCELED:
          parent::setStatus(INVOICE_STATUS_CANCELED);

          $this->setClosedOn($on);
          $this->setClosedById($by->getId());
          $this->setClosedByName($by->getName());
          $this->setClosedByEmail($by->getEmail());

          //InvoicePayments::deleteByInvoice($this);
          $this->payments()->delete();

          $this->setTimeRecordsStatus(BILLABLE_STATUS_BILLABLE);
          $this->releaseTimeRecords();
          $this->setExpensesStatus(BILLABLE_STATUS_BILLABLE);
          $this->releaseExpenses();
          break;

        default:
          throw new InvalidParamError('status', $status, '$status is not valid invoice status', true);
      } // switch
    } // setStatus

    /**
     * Mark this invoice as issued
     *
     * @param User $issued_by
     * @param User $issued_to
     * @param DateValue $issued_on
     * @param DateValue $due_on
     */
    function markAsIssued(User $issued_by, $issued_to, DateValue $issued_on, DateValue $due_on) {
      if($this->getStatus() == INVOICE_STATUS_DRAFT) {
        try {
          DB::beginWork('Marking invoice as issued @ ' . __CLASS__);

          $this->setStatus(INVOICE_STATUS_ISSUED, $issued_by, $issued_on);
          $this->setDueOn($due_on);

          if($issued_to instanceof User) {
            $this->setIssuedTo($issued_to);
          } // if

          $autogenerated = false;

          // Generate invoice number?
          if(!$this->getNumber()) {
            $this->setNumber($this->generateInvoiceId());
            $autogenerated = true;
          } // if

          $this->save();

          $this->activityLogs()->logIssuing($issued_by);

          if($autogenerated) {
            Invoices::incrementDateInvoiceCounters();
          } // if

          DB::commit('Invoice marked as issued @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to mark invoice as issued @ ' . __CLASS__);
          throw $e;
        } // try
      } else {
        throw new InvalidParamError('status', $this->getStatus(), 'Only draft invoices can be marked as issued');
      } // if
    } // markAsIssued

    /**
     * Mark invoice as paid
     *
     * @param IUser $by
     * @param Payment $payment
     * @param  $additional_params
     */
    function markAsPaid(IUser $by, Payment $payment, $additional_params = null) {
      if($this->getStatus() == INVOICE_STATUS_ISSUED) {
        try {
          DB::beginWork('Marking invoice as paid @ ' . __CLASS__);

          if($payment instanceof CustomPayment) {
            $date = $payment->getPaidOn();
          } else {
            $date = DateTimeValue::now();
          } //if

          $this->setStatus(INVOICE_STATUS_PAID, $by, $date, $additional_params);
          $this->save();

          $this->activityLogs()->logPaid($by);

          DB::commit('Invoice marked as paid @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to mark invoice as paid @ ' . __CLASS__);
          throw $e;
        } // try
      } else {
        throw new InvalidParamError('status', $this->getStatus(), 'Only issued invoices can be marked as paid');
      } // if
    } // markAsPaid

    /**
     * Mark this invoice as canceled
     *
     * @param User $by
     */
    function markAsCanceled(User $by) {
      if($this->getStatus() == INVOICE_STATUS_ISSUED || $this->getStatus() == INVOICE_STATUS_PAID) {
        try {
          DB::beginWork('Marking invoice as canceled @ ' . __CLASS__);

          $this->setStatus(INVOICE_STATUS_CANCELED, $by, DateTimeValue::now());
          $this->save();

          $this->activityLogs()->logCancelation($by);

          DB::commit('Invoice marked as canceled @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to mark invoice as canceled @ ' . __CLASS__);
          throw $e;
        } // try
      } else {
        throw new InvalidParamError('status', $this->getStatus(), 'Only issued and paid invoices can be marked as canceled');
      } // if
    } // markAsCanceled

    /**
     * Check if invoice is draft
     *
     * @return boolean
     */
    function isDraft() {
      return !($this->getIssuedOn() instanceof DateValue) && $this->getStatus() == INVOICE_STATUS_DRAFT;
    } // isDraft

    /**
     * Returns true if this invoice is issued
     *
     * @return boolean
     */
    function isIssued() {
      return $this->getIssuedOn() instanceof DateValue && $this->getStatus() == INVOICE_STATUS_ISSUED;
    } // isIssued

    /**
     * Returns true if this invoice is marked as paid
     *
     * @return boolean
     */
    function isPaid() {
      return $this->getClosedOn() instanceof DateValue && $this->getStatus() == INVOICE_STATUS_PAID;
    } // isPaid

    /**
     * Check if this invoice is overdue
     *
     * @return boolean
     */
    function isOverdue() {
      $today = new DateValue(time() + get_user_gmt_offset());
      $due_on = $this->getDueOn();
      return (boolean) ($this->isIssued() && !$this->isPaid() && !$this->isCanceled() && ($due_on instanceof DateValue && ($due_on->toMySQL() < $today->toMySql())));
    } // isOverdue

    /**
     * Returns true if this invoice is canceled
     *
     * @return boolean
     */
    function isCanceled() {
      return $this->getClosedOn() instanceof DateValue && $this->getStatus() == INVOICE_STATUS_CANCELED;
    } // isCanceled

    /**
     * Check whether invoice is based on quote
     *
     * @return boolean
     */
    function isBasedOnQuote() {
      return (boolean) $this->getBasedOnType() == 'Quote';
    } // isBasedOnQuote

    /**
     * Get based on quote
     *
     * @return Quote
     */
    function getBasedOnQuote() {
      return Quotes::findById($this->getBasedOnId());
    } // getBasedOnQuote

    // ---------------------------------------------------
    //  Time records
    // ---------------------------------------------------

    /**
     * Cached array of related time records
     *
     * @var array
     */
    private $time_record_ids = false;

    /**
     * Return array of related time record ID-s
     *
     * @param $cached
     * @return array
     */
    function getTimeRecordIds($cached = false) {
      if(AngieApplication::isModuleLoaded('tracking')) {
        if($this->time_record_ids === false || !$cached) {
          $rows = DB::execute('SELECT parent_id FROM ' . TABLE_PREFIX . 'invoice_related_records WHERE invoice_id = ? AND parent_type = ?', $this->getId(), 'TimeRecord');
          if(is_foreachable($rows)) {
            $this->time_record_ids = array();
            foreach($rows as $row) {
              $this->time_record_ids[] = (integer) $row['parent_id'];
            } // foreach
          } else {
            $this->time_record_ids = null;
          } // if
        } // if
        return $this->time_record_ids;
      } else {
        return null;
      } // if
    } // getTimeRecordIds

    /**
     * Cached value of related time records count
     *
     * @var integer
     */
    private $time_records_count = false;

    /**
     * Return number of related time records
     *
     * @return integer
     */
    function countTimeRecords() {
      if(AngieApplication::isModuleLoaded('tracking')) {
        if($this->time_records_count === false) {
          $time_records_table = TABLE_PREFIX . 'time_records';
          $items_table = TABLE_PREFIX . 'invoice_related_records';

          $this->time_records_count = (integer) DB::executeFirstCell("SELECT COUNT($items_table.parent_id) AS 'row_count' FROM $items_table, $time_records_table WHERE $items_table.parent_id = $time_records_table.id AND $time_records_table.state >= ? AND $items_table.invoice_id = ? AND $items_table.parent_type = ?", STATE_ARCHIVED, $this->getId(), 'TimeRecord');
        } // if

        return $this->time_records_count;
      } else {
        return 0;
      } // if
    } // countTimeRecords

    /**
     * Release time records
     *
     * @return boolean
     */
    function releaseTimeRecords() {
      if(AngieApplication::isModuleLoaded('tracking')) {
        $ids = $this->getTimeRecordIds();

        if($ids) {
          $this->setTimeRecordsStatus(BILLABLE_STATUS_BILLABLE, $ids);
          return DB::execute('DELETE FROM ' . TABLE_PREFIX . 'invoice_related_records WHERE invoice_id = ? AND parent_type = ? AND parent_id IN (?)', $this->getId(), 'TimeRecord', $ids);
        } // if
      } else {
        return true;
      } // if
    } // releaseTimeRecords

    /**
     * Release time records
     *
     * @param array $ids
     * @return boolean
     */
    function releaseTimeRecordsByIds($ids) {
      if(AngieApplication::isModuleLoaded('tracking')) {
        $this->setTimeRecordsStatus(BILLABLE_STATUS_BILLABLE, $ids);
        return DB::execute('DELETE FROM ' . TABLE_PREFIX . 'invoice_related_records WHERE invoice_id = ? AND parent_type = ? AND parent_id IN (?)', $this->getId(), 'TimeRecord', $ids);
      } else {
        return true;
      }
    } // releaseTimeRecords

    /**
     * Return related time records
     *
     * @param int $visibility
     * @return array
     */
    function getTimeRecords($visibility = VISIBILITY_NORMAL) {
      if(AngieApplication::isModuleLoaded('tracking')) {
        $ids = $this->getTimeRecordIds();
        return is_foreachable($ids) ? TimeRecords::findByIds($ids, STATE_ARCHIVED) : null;
      } else {
        return null;
      } // if
    } // getTimeRecords

    /**
     * Set status to related time records
     *
     * @param integer $new_status
     * @param bool|array $ids
     */
    function setTimeRecordsStatus($new_status, $ids = false) {
      if(AngieApplication::isModuleLoaded('tracking')) {
        if($ids === false) {
          $ids = $this->getTimeRecordIds();
        }//if

        if(is_foreachable($ids)) {
          DB::execute('UPDATE ' . TABLE_PREFIX . 'time_records SET billable_status = ? WHERE id IN (?)', $new_status, $ids);
          AngieApplication::cache()->removeByModel('time_records');
        } // if
      } // if
    } // setTimeRecordsStatus

    // ---------------------------------------------------
    //  Expenses
    // ---------------------------------------------------

    /**
     * Cached array of expenses records
     *
     * @var array
     */
    private $expense_ids = false;

    /**
     * Return array of related expense ID-s
     *
     * @param $cached
     * @return array
     */
    function getExpenseIds($cached = false) {
      if(AngieApplication::isModuleLoaded('tracking')) {
        if($this->expense_ids === false || !$cached) {
          $rows = DB::execute('SELECT parent_id FROM ' . TABLE_PREFIX . 'invoice_related_records WHERE invoice_id = ? AND parent_type = ?', $this->getId(), 'Expense');
          if(is_foreachable($rows)) {
            $this->expense_ids = array();
            foreach($rows as $row) {
              $this->expense_ids[] = (integer) $row['parent_id'];
            } // foreach
          } else {
            $this->expense_ids = null;
          } // if
        } // if
        return $this->expense_ids;
      } else {
        return null;
      } // if
    } // getExpenseIds

    /**
     * Cached value of related expenses count
     *
     * @var integer
     */
    private $expenses_count = false;

    /**
     * Return number of related time records
     *
     * @return integer
     */
    function countExpenses() {
      if(AngieApplication::isModuleLoaded('tracking')) {
        if($this->expenses_count === false) {
          $expenses_table = TABLE_PREFIX . 'expenses';
          $items_table = TABLE_PREFIX . 'invoice_related_records';

          $this->expenses_count = (integer) DB::executeFirstCell("SELECT COUNT($items_table.parent_id) AS 'row_count' FROM $items_table, $expenses_table WHERE $items_table.parent_id = $expenses_table.id AND $expenses_table.state >= ? AND $items_table.invoice_id = ? AND $items_table.parent_type = ?", STATE_ARCHIVED, $this->getId(), 'Expense');
        } // if
        return $this->expenses_count;
      } else {
        return 0;
      } // if
    } // countExpenses

    /**
     * Release expenses
     *
     * @return boolean
     */
    function releaseExpenses() {
      if(AngieApplication::isModuleLoaded('tracking')) {
        $ids = $this->getExpenseIds();
        if($ids) {
          $this->setExpensesStatus(BILLABLE_STATUS_BILLABLE,$ids);
          return DB::execute('DELETE FROM ' . TABLE_PREFIX . 'invoice_related_records WHERE invoice_id = ? AND parent_type = ? AND parent_id IN (?)', $this->getId(), 'Expense', $ids);
        } // if
      } // if

      return true;
    } // releaseExpenses

    /**
     * Release expenses
     *
     * @param array $ids
     * @return boolean
     */
    function releaseExpensesByIds($ids) {
      if(AngieApplication::isModuleLoaded('tracking')) {
        $this->setExpensesStatus(BILLABLE_STATUS_BILLABLE,$ids);
        return DB::execute('DELETE FROM ' . TABLE_PREFIX . 'invoice_related_records WHERE invoice_id = ? AND parent_type = ? AND parent_id IN (?)', $this->getId(), 'Expense', $ids);
      } else {
        return true;
      } // if
    } // releaseExpenses

    /**
     * Return related expenses
     *
     * @return array
     */
    function getExpenses() {
      if(AngieApplication::isModuleLoaded('tracking')) {
        $ids = $this->getExpenseIds();
        return is_foreachable($ids) ? Expenses::findByIds($ids, STATE_ARCHIVED) : null;
      } else {
        return null;
      } // if
    } // getExpenses

    /**
     * Set status to related expenses
     *
     * @param integer $new_status
     * @param mixed $ids
     * @return boolean
     */
    function setExpensesStatus($new_status, $ids = false) {
      if(AngieApplication::isModuleLoaded('tracking')) {
        if(!$ids) {
          $ids = $this->getExpenseIds();
        } // if

        if(is_foreachable($ids)) {
          $update = DB::execute('UPDATE ' . TABLE_PREFIX . 'expenses SET billable_status = ? WHERE id IN (?)', $new_status, $ids);
          if($update) {
            AngieApplication::cache()->removeByModel('expenses');
            return true;
          } else {
            return $update;
          } // if
        } // if
      } // if

      return true;
    } // setExpensesStatus

    // ---------------------------------------------------
    //  PDF
    // ---------------------------------------------------

    /**
     * Make a PDF copy and prepare it to be emailed
     *
     * This function returns file path of the created PDF file
     *
     * @return string
     */
    function getPdfAttachmentPath() {
      $filename = WORK_PATH . '/invoice_' . $this->getId() . '.pdf';

      InvoicePDFGenerator::save($this, $filename);

      return $filename;
    } // getPdfAttachmentPath

    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------

    /**
     * State helper instance
     *
     * @var IProjectObjectStateImplementation
     */
    private $state = false;

    /**
     * Return state helper instance
     *
     * @return IProjectObjectStateImplementation
     */
    function state() {
      if($this->state === false) {
        $this->state = new IInvoiceStateImplementation($this);
      } // if

      return $this->state;
    } // state

    /**
     * Cached inspector instance
     *
     * @var IInvoiceInspectorImplementation
     */
    private $inspector = false;

    /**
     * Return inspector helper instance
     *
     * @return IInvoiceInspectorImplementation
     */
    function inspector() {
      if($this->inspector === false) {
        $this->inspector = new IInvoiceInspectorImplementation($this);
      } // if

      return $this->inspector;
    } // inspector

    /**
     * Payments helper
     *
     * @var IInvoicePaymentsImplementation
     */
    private $payments = false;

    /**
     * Return payments helper instance
     *
     * @return IInvoicePaymentsImplementation
     */
    function payments() {
      if($this->payments === false) {
        $this->payments = new IInvoicePaymentsImplementation($this);
      } // if
      return $this->payments;
    } // payments

    /**
     * Cached activity logs helper instance
     *
     * @var IInvoiceActivityLogsImplementation
     */
    private $activity_logs = false;

    /**
     * Return activity logs helper instance
     *
     * @return IInvoiceActivityLogsImplementation
     */
    function activityLogs() {
      if($this->activity_logs === false) {
        $this->activity_logs = new IInvoiceActivityLogsImplementation($this);
      } // if

      return $this->activity_logs;
    } // activityLogs

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
        $this->history = new IHistoryImplementation($this, array('company_id', 'project_id', 'currency_id', 'language_id', 'number', 'company_address', 'private_note', 'note', 'status', 'due_on', 'allow_payments'));
      } // if

      return $this->history;
    } // history

    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------

    /**
     * Returns true if $user can view invoice
     *
     * @param User $user
     * @return boolean
     */
    function canView(User $user) {
      return $user->isFinancialManager() || (Invoices::canAccessCompanyInvoices($user, $this->getCompany()) && !$this->isDraft());
    } // canView

    /**
     * Returns true if $user can see related invoice items
     *
     * @param User $user
     * @return boolean
     */
    function canViewRelatedItems(User $user) {
      return AngieApplication::isModuleLoaded('tracking') && $user->isFinancialManager();
    } // canViewRelatedItems

    /**
     * Returns true if $user can edit this invoice
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      if($this->getStatus() == INVOICE_STATUS_DRAFT || $this->getStatus() == INVOICE_STATUS_ISSUED) {
        return parent::canEdit($user);
      } else {
        return false;
      } // if
    } // canEdit

    /**
     * Can Change Language
     *
     * @param User $user
     * @return boolean
     */
    function canChangeLanguage(User $user) {
      if($this->isPaid()) {
        return parent::canEdit($user);
      } else {
        return false;
      } // if
    } // canChangeLanguage

    /**
     * Return true if $user can resend email for this invoice
     *
     * @param User $user
     * @return bool
     */
    function canResendEmail(User $user) {
      return $user->isFinancialManager() && ($this->getStatus() == INVOICE_STATUS_ISSUED || $this->getStatus() == INVOICE_STATUS_PAID);
    } // canResendEmail

    /**
     * Returns true if $user can delete this invoice
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      if($this->getStatus() == INVOICE_STATUS_DRAFT) {
        return parent::canDelete($user);
      } else {
        return false;
      } // if
    } // canDelete

    /**
     * Returns true if this invoice can be issue by $user
     *
     * @param User $user
     * @return boolean
     */
    function canIssue(User $user) {
      return $this->getStatus() == INVOICE_STATUS_DRAFT && $user->isFinancialManager();
    } // canIssue

    /**
     * Returns true if $user can cancel this invoice
     *
     * @param User $user
     * @return boolean
     */
    function canCancel(User $user) {
      if($this->getStatus() == INVOICE_STATUS_ISSUED || $this->getStatus() == INVOICE_STATUS_PAID) {
        return $user->isFinancialManager();
      } else {
        return false;
      } // if
    } // canCancel

    /**
     * Returns true if $user can add payment to this invoice
     *
     * @param User $user
     * @return boolean
     */
    function canAddPayment(User $user) {
      return $this->getStatus() == INVOICE_STATUS_ISSUED && $user->isFinancialManager();
    } // canAddPayment

    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------

    /**
     * Mark this invoice as paid
     *
     * @return string
     */
    function getMarkAsPaidUrl() {
      return Router::assemble('invoice_paid', array(
        'invoice_id' => $this->getId(),
      ));
    } //getMarkAsPaidUrl

    /**
     * Return PDF doc URL
     *
     * @return string
     */
    function getPdfUrl() {
      return Router::assemble('invoice_pdf', array(
        'invoice_id' => $this->getId(),
        'force' => true,
        'disposition' => 'attachment'
      ));
    } // getPdfUrl

    /**
     * Return company view URL
     *
     * @return string
     */
    function getCompanyViewUrl() {
      return Router::assemble('people_company_invoice', array('invoice_id' => $this->getId(), 'company_id' => $this->getCompanyId()));
    } // getCompanyViewUrl

    /**
     * Return public PDF URL accessible from company invoices page
     *
     * @return string
     */
    function getCompanyPdfUrl() {
      return Router::assemble('people_company_invoice_pdf', array(
        'invoice_id' => $this->getId(),
        'company_id' => $this->getCompanyId(),
        'force' => true,
        'disposition' => 'attachment'
      ));
    } // getCompanyPdfUrl

    /**
     * Return Url for viewing public payment info - url
     */
    function getPublicPaymentInfoUrl() {
      return Router::assemble('invoice_public_payment_info', array('invoice_id' => $this->getId()));
    } //getPublicPaymentInfoUrl

    /**
     * Return public pdf invoice URL
     *
     * @return string
     */
    function getPublicPdfUrl() {
      return Router::assemble('public_invoice_pdf', array(
          'invoice_id' => $this->getId(),
          'client_id' => $this->getCompanyId(),
          'invoice_hash' => $this->getHash())
      );
    } // getPublicPdfUrl

    /**
     * Return send invoice URL
     *
     * @return string
     */
    function getIssueUrl() {
      return Router::assemble('invoice_issue', array('invoice_id' => $this->getId()));
    } // getIssueUrl

    /**
     * Return cancel invoice URL
     *
     * @return string
     */
    function getCancelUrl() {
      return Router::assemble('invoice_cancel', array('invoice_id' => $this->getId()));
    } // getCancelUrl

    /**
     * Return duplicate invoice URL
     *
     * @return string
     */
    function getDuplicateUrl() {
      return Router::assemble('invoices_add', array('duplicate_invoice_id' => $this->getId()));
    } // getDuplicateUrl

    /**
     * Return add payment URL
     *
     * @return string
     */
    function getAddPaymentUrl() {
      return Router::assemble('invoice_custom_payments_add_old', array('invoice_id' => $this->getId()));
    } // getAddPaymentUrl

    /**
     * Return invoice time URL
     *
     * @return string
     */
    function getTimeUrl() {
      return Router::assemble('invoice_time', array('invoice_id' => $this->getId()));
    } // getTimeUrl

    /**
     * Return invoice expense URL
     *
     * @return string
     */
    function getExpenseUrl() {
      return Router::assemble('invoice_expenses', array('invoice_id' => $this->getId()));
    } // getExpenseUrl

    /**
     * Return release URL
     *
     * @return string
     */
    function getReleaseUrl() {
      return Router::assemble('invoice_items_release', array('invoice_id' => $this->getId()));
    } // getReleaseTimeUrl

    /**
     * Return notify URL
     *
     * @return string
     */
    function getNotifyUrl() {
      return Router::assemble('invoice_notify', array('invoice_id' => $this->getId()));
    } // getNotifyUrl

    /**
     * Get Change Language Url
     *
     * @return string
     */
    function getChangeLanguageUrl() {
      return Router::assemble('invoice_change_language', array('invoice_id' => $this->getId()));
    } // getChangeLanguageUrl

    // ---------------------------------------------------
    //  System
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

      // state
      $result['status_conditions']['is_draft'] = $this->isDraft();
      $result['status_conditions']['is_issued'] = $this->isIssued();
      $result['status_conditions']['is_overdue'] = $this->isOverdue();
      $result['status_conditions']['is_paid'] = $this->isPaid();
      $result['status_conditions']['is_canceled'] = $this->isCanceled();

      // issued
      $result['issued_on'] = $this->getIssuedOn();
      $result['issued_on_month'] = $this->getIssuedOn() ? date('Y-m', $this->getIssuedOn()->getTimestamp()) : null;
      $result['issued_on_month_verbose'] = $this->getIssuedOn() ? date('F Y', $this->getIssuedOn()->getTimestamp()) : null;
      $result['issued_by'] = $this->isIssued() || $this->getIssuedBy() instanceof IUser ? $this->getIssuedBy() : null;

      // due
      $result['due_on'] = $this->getDueOn();
      $result['due_on_month'] = $this->getDueOn() ? date('Y-m', $this->getDueOn()->getTimestamp()) : null;
      $result['due_on_month_verbose'] = $this->getDueOn() ? date('F Y', $this->getDueOn()->getTimestamp()) : null;

      // paid
      $result['paid_on'] = $this->isPaid() ? $this->getClosedOn() : null;
      $result['paid_by'] = $this->isPaid() ? $this->getClosedBy() : null;

      // closed
      $result['closed_on'] = $this->isCanceled() ? $this->getClosedOn() : null;
      $result['closed_by'] = $this->isCanceled() ? $this->getClosedBy() : null;

      // permissions
      $result['permissions']['can_issue'] = $this->canIssue($user);
      $result['permissions']['can_cancel'] = $this->canCancel($user);
      $result['permissions']['can_add_payment'] = $this->canAddPayment($user);

      // urls
      $result['urls']['issue'] = $this->getIssueUrl();
      $result['urls']['cancel'] = $this->getCancelUrl();
      $result['urls']['duplicate'] = $this->getDuplicateUrl();
      $result['urls']['pdf'] = $this->getPdfUrl();

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
        'number' => $this->getNumber(),
        'client' => array(
          'id' => $this->getCompanyId(),
          'name' => $this->getCompanyName(),
          'address' => $this->getCompanyAddress(),
        ),
        'currency' => array(
          'id' => $this->getCurrencyId(),
          'code' => $this->getCurrencyCode(),
        ),
        'language' => array(
          'id' => $this->getLanguageId(),
          'code' => $this->getLanguage() instanceof Language ? $this->getLanguage()->getLocaleCode() : BUILT_IN_LOCALE,
        ),
        'project_id' => (integer) $this->getProjectId(),
        'amount' => array(
          'subtotal' => $this->getSubtotal(),
          'tax' => $this->getTax(),
          'total' => $this->getTotal(),
          'paid_amount' => $this->getPaidAmount(),
          'balance_due' => $this->getBalanceDue(),
        ),
        'note' => $this->getNote(),
        'purchase_order_number' => $this->getPurchaseOrderNumber(),
        'status' => $this->getStatus(),
        'created_on' => $this->getCreatedOn()->toMySQL(),
        'issued_on' => $this->getIssuedOn()->toMySQL(),
        'due_on' => $this->getDueOn()->toMySQL(),
        'items' => array(),
        'permalink' => $this->getViewUrl(),
      );

      if($this->isPaid()) {
        $result['paid_on'] = $this->getClosedOn()->toMySQL();
      } elseif($this->isCanceled()) {
        $result['canceled_on'] = $this->getClosedOn()->toMySQL();
      } // if

      if($user->isFinancialManager() || $user->isAdministrator()) {
        $result['private_note'] = $this->getPrivateNote();
      } // if

      $counter = 1;
      foreach($this->getItems() as $item) {
        $described_item = array(
          'num' => $counter++,
          'description' => $item->getDescription(),
          'quantity' => $item->getQuantity(),
          'unit_cost' => $item->getUnitCost(),
          'subtotal' => $item->getSubtotal(),
          'first_tax' => $item->getFirstTax(),
          'second_tax' => $item->getSecondTax(),
          'total' => $item->getTotal(),
        );

        if($item->getFirstTax() != 0) {
          $described_item['first_tax'] = array(
            'value' => $item->getFirstTax(),
          );

          $first_tax = DataObjectPool::get('TaxRate', $item->getFirstTaxRateId());

          if($first_tax instanceof TaxRate) {
            $described_item['first_tax']['name'] = $first_tax->getName();
            $described_item['first_tax']['rate'] = $first_tax->getPercentage();
          } // if
        } // if

        if($item->getSecondTaxIsEnabled() && $item->getSecondTax() != 0) {
          $described_item['second_tax'] = array(
            'value' => $item->getSecondTax(),
            'is_compound' => $item->getSecondTaxIsCompound(),
          );

          $second_tax = DataObjectPool::get('TaxRate', $item->getSecondTaxRateid());

          if($second_tax instanceof TaxRate) {
            $described_item['second_tax']['name'] = $second_tax->getName();
            $described_item['second_tax']['rate'] = $second_tax->getPercentage();
          } // if
        } // if

        $result['items'][] = $described_item;
      } // if

      return $result;
    } // describeForApi

    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if(!$this->validatePresenceOf('company_id')) {
        $errors->addError(lang('Client is required'), 'company_id');
      } // if

      if(!$this->validatePresenceOf('company_address')) {
        $errors->addError(lang('Client address is required'), 'company_address');
      } // if

      if($this->validatePresenceOf('varchar_field_1')) {
        if(!$this->validateUniquenessOf('varchar_field_1')) {
          $errors->addError(lang('Invoice No. needs to be unique'), 'number');
        } // if
      } // if
    } // validate
  }