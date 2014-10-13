<?php

  /**
   * InvoiceObject class
   *
   * @package ActiveCollab.modules.invoicing
   * @subpackage models
   */
  class InvoiceObject extends BaseInvoiceObject implements IObjectContext, IRoutingContext, IAccessLog {

    /**
     * Decimal spaces in this object
     *
     * @var int
     */
    protected $decimal_precision = false;

    /**
     * Get decimal scale
     *
     * @return int
     */
    function getDecimalPrecision() {
      if ($this->decimal_precision === false) {
        $currency = $this->getCurrency();
        if ($currency instanceof Currency) {
          $this->decimal_precision = $currency->getDecimalSpaces();
        } else {
          $this->decimal_precision = 2;
        } // if
      } // if

      return $this->decimal_precision;
    } // getDecimalSpaces

    /**
     * Fields that
     *
     * @var array
     */
    protected $roundable_fields = array(
      'subtotal', 'tax', 'total'
    );

    /**
     * Get Field Value
     *
     * @param string $field
     * @param null $default
     * @return float|mixed
     */
    function getFieldValue($field, $default = null) {
      if (in_array($field, $this->roundable_fields)) {
        return round(parent::getFieldValue($field, $default), $this->getDecimalPrecision());
      } else {
        return parent::getFieldValue($field, $default);
      } // if
    } // getFieldValue

    /**
     * Set field value
     *
     * @return float|mixed
     */
    function setFieldValue($name, $value) {
      if (in_array($name, $this->roundable_fields)) {
        return parent::setFieldValue($name, round($value, $this->getDecimalPrecision()));
      } else {
        return parent::setFieldValue($name, $value);
      } // if
    } // setFieldValue

    /**
     * Get Second Tax is Enabled
     *
     * @return boolean
     */
    function getSecondTaxIsEnabled() {
      if ($this->isNew() && !$this->isModifiedField('second_tax_is_enabled')) {
        return Invoices::isSecondTaxEnabled();
      } // if

      return parent::getSecondTaxIsEnabled();
    } // getSecondTaxIsEnabled

    /**
     * Get Second Tax is Compound
     *
     * @return boolean
     */
    function getSecondTaxIsCompound() {
      if (!$this->getSecondTaxIsEnabled()) {
        return false;
      } // if

      if ($this->isNew() && !$this->isModifiedField('second_tax_is_compound')) {
        return Invoices::isSecondTaxCompound();
      } // if

      return parent::getSecondTaxIsCompound();
    } // getSecondTaxIsCompound

    /**
     * Return user object of person who closed this invoice
     *
     * @return IUser|null
     */
    function getClosedBy() {
      return $this->getUserFromFieldSet('closed_by');
    } // getClosedBy

    /**
     * Set closed by
     *
     * @param IUser|null $user
     * @return IUser|null
     */
    function setClosedBy($user) {
      return parent::setUserFromFieldSet($user, 'closed_by');
    } // setClosedBy

    /**
     * Get sent by user instance
     *
     * @return IUser|null
     */
    function getSentBy() {
      return parent::getUserFromFieldSet('sent_by');
    } // getSentBy

    /**
     * Set sent by user instance
     *
     * @param IUser|null $user
     * @return IUser|null
     */
    function setSentBy($user) {
      return parent::setUserFromFieldSet($user, 'sent_by');
    } // setSentBy

    /**
     * Return based on object
     *
     * @return IInvoiceBasedOn
     */
    function getBasedOn() {
      if($this->getBasedOnType() && $this->getBasedOnId()) {
        return DataObjectPool::get($this->getBasedOnType(), $this->getBasedOnId());
      } // if

      return null;
    } // getBasedOn

    /**
     * Set based on value
     *
     * @param IInvoiceBasedOn $based_on
     * @throws InvalidInstanceError
     */
    function setBasedOn($based_on) {
      // @TODO this is only for invoices, right?
      if($based_on instanceof IInvoiceBasedOn) {
        $this->setBasedOnType(get_class($based_on));
        $this->setBasedOnId($based_on->getId());
      } elseif($based_on === null) {
        $this->setBasedOnType(null);
        $this->setBasedOnId(null);
      } else {
        throw new InvalidInstanceError('based_on', $based_on, 'IInvoiceBasedOn');
      } // if
    } // setBasedOn

    /**
     * Cached company value
     *
     * @var Company
     */
    private $company = false;

    /**
     * Return invoice company
     *
     * @return Company
     */
    function getCompany() {
      if ($this->company === false) {
        $this->company = DataObjectPool::get('Company', $this->getCompanyId());
      } // if
      return $this->company;
    } // getCompany

    /**
     * Get client company name
     *
     * @return string
     */
    function getCompanyName() {
      if ($this->fieldExists('company_name')) {
        return $this->getFieldValue('company_name');
      } // if

      $company = $this->getCompany();
      if ($company instanceof Company) {
        return $company->getName();
      } // if

      return false;
    } // getCompanyName

    /**
     * Cached project instance
     *
     * @var Project
     */
    private $project = false;

    /**
     * Return project instance
     *
     * @return Project
     */
    function getProject() {
      if($this->project === false) {
        $this->project = DataObjectPool::get('Project', $this->getProjectId());
      } // if
      return $this->project;
    } // getProject

    /**
     * Cached currency instance
     *
     * @var Currency
     */
    private $currency = false;

    /**
     * Return invoice object currency
     *
     * @return Currency
     */
    function getCurrency() {
      if ($this->currency === false) {
        $this->currency = DataObjectPool::get('Currency', $this->getCurrencyId());
        if (!($this->currency instanceof Currency)) {
          $this->currency = Currencies::getDefault();
        } // if
      } // if
      return $this->currency;
    } // getCurrency

    /**
     * Return currency name
     *
     * @return string
     */
    function getCurrencyName() {
      return $this->getCurrency() instanceof Currency ? $this->getCurrency()->getName() : lang('Unknown Currency');
    } // getCurrencyName

    /**
     * Return currency code
     *
     * @return string
     */
    function getCurrencyCode() {
      return $this->getCurrency() instanceof Currency ? $this->getCurrency()->getCode() : lang('Unknown Currency');
    } // getCurrencyCode

    /**
     * Cached language value
     *
     * @var Language
     */
    private $language = false;

    /**
     * Return invoice object language
     *
     * @return language
     */
    function getLanguage() {
      if($this->language === false) {
        $this->language = Languages::findById($this->getLanguageId());
        if (!($this->language instanceof Language)) {
          $this->language = Languages::getBuiltIn();
        } // if
      } // if
      return $this->language;
    } // getLanguage

    /**
     * Get Verbose status
     *
     * @return string
     */
    function getVerboseStatus() {
      return '';
    } // getVerboseStatus

    // ---------------------------------------------------
    //  Object Context
    // ---------------------------------------------------

    /**
     * Return object context domain
     *
     * @return string
     */
    function getObjectContextDomain() {
      return Inflector::pluralize(Inflector::underscore($this->getType()));
    } // getObjectContextDomain

    /**
     * Return object context path
     *
     * @return string
     */
    function getObjectContextPath() {
      return Inflector::pluralize(Inflector::underscore($this->getType())) . '/' . $this->getId();
    } // getObjectContextPath

    // ---------------------------------------------------
    //  Routing Context
    // ---------------------------------------------------

    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return Inflector::underscore($this->getType());
    } // getRoutingContext

    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array(Inflector::underscore($this->getType()) . '_id' => $this->getId());
    } // getRoutingContextParams

	  /**
	   * Cached access log helper instance
	   *
	   * @var IAccessLogImplementation
	   */
	  private $access_log = false;

	  /**
	   * Return access log helper instance
	   *
	   * @return IAccessLogImplementation
	   */
	  function accessLog() {
		  if($this->access_log === false) {
			  $this->access_log = new IAccessLogImplementation($this);
		  } // if

		  return $this->access_log;
	  } // accessLog

    // ---------------------------------------------------
    //  Items
    // ---------------------------------------------------

    /**
     * Cached invoice items
     *
     * @var array
     */
    private $items = false;

    /**
     * Return invoice items
     *
     * @param bool $use_cache
     * @return InvoiceItem[]
     */
    function getItems($use_cache = false) {
      if (($this->items === false) || !$use_cache) {
        $this->items = InvoiceItems::findByParent($this);
      } // if

      return $this->items;
    } // getItems

    /**
     * Add items
     *
     * @param InvoiceItem[] $items
     * @throws ValidationErrors
     * @throws Exception
     */
    function setItems($items) {
      $counter = 0;

      if(is_foreachable($items)) {
        try {
          DB::beginWork('Add invoice items @ ' . __CLASS__);

          if ($this->isNew()) {
            $this->save();
          } else {
            $items_to_delete = $this->getItemsToDelete($items);
            if (is_foreachable($items_to_delete)) {
              InvoiceObjectItems::deleteByParentAndIds($this, $items_to_delete);
            } // if
          } // if

          $this->items = array();
          foreach($items as $item_data) {
            $this->addItem($item_data, $counter);
            $counter++;
          } // foreach

          $this->recalculate();

          DB::commit('Invoice items added @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to add invoice items @ ' . __CLASS__);
          throw $e;
        } // try
      } // if

      if($counter == 0) {
        throw new ValidationErrors(array('invoice' => lang('Invoice items data is not valid. All descriptions are required and there need to be at least one unit with cost set per item!')));
      } // if
    } // setItems

    /**
     * Get Items to delete when updating invoices
     *
     * @param array $updated_items
     * @return array
     */
    function getItemsToDelete($updated_items) {
      $existing_ids = array();
      $new_ids = array();

      // extract existing ids
      if (is_foreachable($this->getItems(true))) {
        foreach ($this->getItems(true) as $existing_item) {
          $existing_ids[] = $existing_item->getId();
        } // foreach
      } // if

      // extract new ids
      if (is_foreachable($updated_items)) {
        foreach ($updated_items as $item) {
          $new_ids[] = $item['id'];
        } // foreach
      } // if

      return array_diff($existing_ids, $new_ids);
    } // getItemsToDelete

    /**
     * Add Item
     *
     * @param array $item
     * @param int $position
     */
    function addItem($item, $position) {
      $item_class = $this->getType() . 'Item';
      $item_id = isset($item['id']) ? $item['id'] : null;
      $item_id = $item_id > 0 ? $item_id : null;
      $inserted_item = new $item_class($item_id);
      $inserted_item->setParentType(get_class($this));
      $inserted_item->setParentId($this->getId());
      $inserted_item->setAttributes($item);
      $inserted_item->setSecondTaxIsEnabled($this->getSecondTaxIsEnabled());
      $inserted_item->setSecondTaxIsCompound($this->getSecondTaxIsCompound());
      $inserted_item->setPosition($position);
      $inserted_item->save();
      $this->items[] = $inserted_item;
      return $inserted_item;
    } // addItem

    /**
     * Get invoice total
     *
     * @param bool $round
     * @return float
     */
    function getTotal($round = false) {
      if ($round && $this->requireRounding()) {
        return Currencies::roundDecimal(parent::getTotal(), $this->getCurrency());
      } else {
        return parent::getTotal();
      } // if
    } // getTotal

    /**
     * Get Rounding Difference
     *
     * @return float
     */
    function getRoundingDifference() {
      if ($this->requireRounding()) {
        return $this->getTotal(true) - $this->getTotal(false);
      } else {
        return 0;
      } // if
    } // getRoundingDifference

    /**
     * Check if invoice total require rounding
     *
     * @return bool
     */
    function requireRounding() {
      $currency = $this->getCurrency();
      return $currency->getDecimalRounding() > 0;
    } // requireRounding

    /**
     * Calculate total by walking through list of items
     */
    function recalculate($save = false) {
      $subtotal = 0;
      $tax = 0;

      if (is_foreachable($this->getItems())) {
        foreach ($this->getItems() as $item) {
          $subtotal += $item->getSubTotal();
          $tax += $item->getFirstTax();

          if ($this->getSecondTaxIsEnabled()) {
            $tax += $item->getSecondTax();
          } // if
        } // foreach
      } // if

      $this->setSubtotal(round($subtotal, $this->getCurrency()->getDecimalSpaces()));
      $this->setTax(round($tax, $this->getCurrency()->getDecimalSpaces()));
      $this->setTotal(round($tax + $subtotal, $this->getCurrency()->getDecimalSpaces()));

      if ($this instanceof IPayments) {
        $this->setPaidAmount(round($this->payments()->getPaidAmount(), $this->getCurrency()->getDecimalSpaces()));
        $this->setBalanceDue(round($this->getTotal(true) - $this->getPaidAmount(), $this->getCurrency()->getDecimalSpaces()));
      } // if

      return $save ? $this->save() : true;
    } // recalculate

    /**
     * Return % that was paid
     *
     * @param boolean $cache
     * @return integer
     */
    function getPercentPaid($cache = true) {
      if (!($this instanceof IPayments)) {
        $percents = 0;
      } else {
        $percents = ($this->getPaidAmount($cache) * 100) / $this->getTotal($cache);
      } // if

      return (float) Globalization::formatNumber($percents);
    } // getPercentPaid

    /**
     * Return max amount that can be paid with the next payment
     *
     * @param boolean $cache
     * @return float
     */
    function getMaxPayment($cache = true) {
      if (!($this instanceof IPayments)) {
        return 0;
      } // if

      return $this->getTotal($cache) - $this->getPaidAmount($cache);
    } // getMaxPayment

    /**
     * Tax grouped by type
     *
     * @var bool
     */
    private $tax_grouped_by_type = false;

    /**
     * Get Tax Grouped by Tax Type
     *
     * @return array
     */
    function getTaxGroupedByType() {
      if ($this->tax_grouped_by_type === false) {
        $this->tax_grouped_by_type = array();
        if (is_foreachable($this->getItems())) {
          foreach ($this->getItems() as $item) {

            if ($item->getFirstTaxRateId()) {
              if (!array_key_exists($item->getFirstTaxRateId(), $this->tax_grouped_by_type)) {
                $this->tax_grouped_by_type[$item->getFirstTaxRateId()] = array(
                  'id'      => $item->getFirstTaxRateId(),
                  'name'    => $item->getFirstTaxRate()->getName(),
                  'amount'  => 0
                );
              } // if
              $this->tax_grouped_by_type[$item->getFirstTaxRateId()]['amount'] += $item->getFirstTax();
            } // if

            if ($this->getSecondTaxIsEnabled()) {
              if ($item->getSecondTaxRateId()) {
                if (!array_key_exists($item->getSecondTaxRateId(), $this->tax_grouped_by_type)) {
                  $this->tax_grouped_by_type[$item->getSecondTaxRateId()] = array(
                    'id'      => $item->getSecondTaxRateId(),
                    'name'    => $item->getSecondTaxRate()->getName(),
                    'amount'  => 0
                  );
                } // if
                $this->tax_grouped_by_type[$item->getSecondTaxRateId()]['amount'] += $item->getSecondTax();
              } // if
            } // if
          } // foreach
        } // if
      } // if

      return $this->tax_grouped_by_type;
    } // getTaxGroupedByType

    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------

    /**
     * Returns true if $user can view invoice object
     *
     * @param User $user
     * @return boolean
     */
    function canView(User $user) {
      return $user->isFinancialManager();
    } // canView

    /**
     * Returns true if $user can edit this invoice object
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return $user->isFinancialManager();
    } // canEdit

    /**
     * Returns true if $user can delete this invoice object
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return $user->isFinancialManager();
    } // canDelete

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

      $result['name'] = $this->getName();
      $result['short_name'] = $this->getName(true);

      if ($this->fieldExists('status')) {
        $result['status'] = (int) $this->getStatus();
        $result['verbose_status'] = $this->getVerboseStatus();
      } // if

      $result['currency'] = $this->getCurrency();
      $result['language'] = $this->getLanguage();
      $project = $this->getProject();
      if ($project instanceof Project) {
        $result['project'] = array(
          'name' => $project->getName(),
          'permalink' => $project->getViewUrl()
        );
      } // if

      if ($this->fieldExists('based_on_type')) {
        $result['based_on'] = $this->getBasedOn();
      } // if

      $result['created_by'] = $this->getCreatedBy();
      $result['created_on'] = $this->getCreatedOn();

      if ($this->fieldExists('sent_on')) {
        $result['sent_on'] = $this->getSentOn();
      } // if

      if ($this->fieldExists('closed_on')) {
        $result['closed_on'] = $this->getClosedOn();
        $result['closed_by'] = $this->getClosedBy();
      } // if

      $result['note'] = $this->getNote();
      $peer_class = Inflector::pluralize($this->getType());
      if (class_exists($peer_class) && method_exists($peer_class, 'canManage')) {
        $result['private_note'] = $peer_class::canManage($user) ? $this->getPrivateNote() : '';
      } // if

      // client
      $result['client'] = $this->getCompany() instanceof Company ? $this->getCompany()->describe($user, false, $for_interface) : null;
      $result['client_name'] = $this->getCompanyName();
      $result['client_address'] = $this->getCompanyAddress();

      // totals
      $result['subtotal'] = $this->getSubTotal();
      $result['tax'] = $this->getTax();
      $result['tax_grouped_by_type'] = $this->getTaxGroupedByType();
      $result['total_before_rounding'] = $this->getTotal(false);
      $result['rounding_difference'] = $this->getRoundingDifference();
      $result['total'] = $this->getTotal(true);

      // payments
      if ($this instanceof IPayments) {
        $result['balance_due'] = $this->getBalanceDue(true);
        $result['paid_amount'] = $this->getPaidAmount(true);
      } // if

      // items
      $items = $this->getItems();
      $result['items'] = array();
      if (is_foreachable($items)) {
        foreach ($items as $item) {
          $result['items'][] = $item->describe($user, false, $for_interface);
        } // foreach
      } // if

      // permissions
      $result['permissions']['can_view'] = $this->canView($user);
      $result['permissions']['can_add'] = Invoices::canAdd($user);
      $result['permissions']['can_edit'] = $this->canEdit($user);
      $result['permissions']['can_delete'] = $this->canDelete($user);

      if ($this->fieldExists('allow_payments')) {
        $result['allow_payments'] = $this->getAllowPayments();
      } // if

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
      return $this->describe($user, $detailed, false);
    } // describeForApi

    /**
     * Save Invoice Object
     *
     * @return bool|void
     */
    function save() {
      // if this is a new object perform some caching
      if ($this->isNew()) {
        // save the second tax is enabled state
        if (!$this->isModifiedField('second_tax_is_enabled')) {
          $this->setSecondTaxIsEnabled(Invoices::isSecondTaxEnabled());
        } // if

        // save the second tax is compund state
        if (!$this->isModifiedField('second_tax_is_compound')) {
          $this->setSecondTaxIsCompound(Invoices::isSecondTaxCompound());
        } // if

        if($this instanceof Invoice || $this instanceof Quote) {
          $this->setHash(Invoices::generateHash());
        } //if
      } // if

      $this->recalculate();
      return parent::save();
    } // save

    /**
     * Delete existing invoice object from database
     *
     * @return boolean
     * @throws Exception
     */
    function delete() {
      try {
        DB::beginWork('Removing invoice object @ ' . __CLASS__);

        // perform the object deletion
        parent::delete();
        // delete intems linked to this object
        InvoiceItems::deleteByParent($this);
        // if this is instance of IPayments delete linked payments
        if ($this instanceof IPayments) {
          $this->payments()->delete();
        } // if

        DB::commit('Invoice object removed @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to remove invoice object @ ' . __CLASS__);
        throw $e;
      } // try

      return true;
    } // delete
    
  }