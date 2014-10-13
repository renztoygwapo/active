<?php

  /**
   * Invoice implementation that can be attached to any object
   *
   * @package activeCollab.modules.invoiving
   * @subpackage models
   */
  abstract class IInvoiceBasedOnImplementation {
    
    /**
     * Parent object
     *
     * @var IInvoice
     */
    protected $object;

    /**
     * Create new invoice instance based on parent object
     *
     * @param Company $client
     * @param string $client_address
     * @param null|array $additional
     * @param IUser $user
     * @return Invoice
     */
    abstract function create(Company $client, $client_address = null, $additional = null, IUser $user = null);

    /**
     * Create invoice from given properties
     *
     * @param Company $client
     * @param string $client_address
     * @param array $additional
     * @return Invoice
     */
    protected function createInvoiceFromPropeties(Company $client, $client_address = null, $additional = null) {
      $invoice = new Invoice();

      $project_id = array_var($additional, 'project_id');

      if($project_id) {
        $invoice->setProjectId($project_id);
      } // if

      $invoice->setCompanyId($client->getId());
      $invoice->setCompanyAddress($client_address);
      $invoice->setBasedOn($this->object);
      $invoice->setDueOn(new DateValue());
      $invoice->setStatus(INVOICE_STATUS_DRAFT);
      $invoice->setState(STATE_VISIBLE);

      if(isset($additional['private_note']) && $additional['private_note']) {
        $invoice->setPrivateNote($additional['private_note']);
      } // if

      if(isset($additional['note']) && $additional['note']) {
        $invoice->setNote($additional['note']);
      } // if

      if(isset($additional['purchase_order_number']) && $additional['purchase_order_number']) {
        $invoice->setPurchaseOrderNumber($additional['purchase_order_number']);
      } // if

      if(isset($additional['language_id']) && $additional['language_id']) {
        $invoice->setLanguageId($additional['language_id']);
      } // if

      if(isset($additional['currency_id']) && $additional['currency_id']) {
        $invoice->setCurrencyId($additional['currency_id']);
      } // if

      if(isset($additional['payments_type']) && $additional['payments_type']) {
        $invoice->setAllowPayments($additional['payments_type']);
      } // if

      return $invoice;
    } // createInvoiceFromPropeties

    /**
     * Preview invoice items based on given settings
     *
     * @param array $settings
     * @param IUser $user
     * @return mixed
     */
    abstract function previewItems($settings = null, IUser $user = null);
    
    /**
     * Construct invoice helper
     *
     * @param IInvoiceBasedOn $object
     */
    function __construct(IInvoiceBasedOn  $object) {
      $this->object = $object;
    } // __construct
    
    /**
     * Add items to the invoice
     * 
     * @param array $items
     * @param Invoice $invoice
     * @return Invoice
     * @throws Exception
     */
    function &commitInvoiceItems($items, Invoice &$invoice) {
      try {
        DB::beginWork('Saving invoice @ ' . __CLASS__);

        if($invoice->isNew()) {
          $invoice->save();
        } // if

        $position = 1;
        foreach($items as $invoice_item_data) {
          $invoice_item = new InvoiceItem();

          $invoice_item->setAttributes($invoice_item_data);
          $invoice_item->setParentType($invoice->getType());
          $invoice_item->setParentId($invoice->getId());
          $invoice_item->setSecondTaxIsEnabled($invoice->getSecondTaxIsEnabled());
          $invoice_item->setSecondTaxIsCompound($invoice->getSecondTaxIsCompound());
          $invoice_item->setPosition($position);
          $invoice_item->recalculate();

          $position++;
          $invoice_item->save();

          if(isset($invoice_item_data['time_record_ids']) && $invoice_item_data['time_record_ids']) {
            $invoice_item->setTimeRecordIds((array) $invoice_item_data['time_record_ids']);
            $invoice->setTimeRecordsStatus(BILLABLE_STATUS_PENDING_PAYMENT);
          }//if

          if(isset($invoice_item_data['expenses_ids']) && $invoice_item_data['expenses_ids']) {
            $invoice_item->setExpensesIds((array) $invoice_item_data['expenses_ids']);
            $invoice->setExpensesStatus(BILLABLE_STATUS_PENDING_PAYMENT);
          } // if
        } // foreach

        $invoice->recalculate(true);

        DB::commit('Invoice saved @ ' . __CLASS__);

        return $invoice;
      } catch(Exception $e) {
        DB::rollback('Failed to save invoice @ ' . __CLASS__);
        throw $e;
      } // try
    } // commitInvoiceItems
    
    /**
     * Returns true if $user can create invoice
     *
     * @param User $user
     * @return boolean
     */
    function canAdd(User $user) {
      return Invoices::canAdd($user);
    } // canMake
    
    /**
     * Return make invoice url
     * 
     * @return string
     */
    function getUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_invoicing', $this->object->getRoutingContextParams());
    } // getUrl

    /**
     * Return preview items URL
     *
     * @return string
     */
    function getPreviewItemsUrl() {
      return Router::assemble($this->object->getRoutingContext() . '_invoicing_preview_items', $this->object->getRoutingContextParams());
    } // getPreviewItemsUrl
  
  }