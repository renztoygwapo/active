<?php

  /**
   * Invoice based on quote helper implementation
   * 
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  class IInvoiceBasedOnQuoteImplementation extends IInvoiceBasedOnImplementation {

    /**
     * Create new invoice instance based on parent object
     *
     * @param Company $client
     * @param string $client_address
     * @param null|array $additional
     * @param IUser $user
     * @return Invoice
     * @throws Error
     */
    function create(Company $client, $client_address = null, $additional = null, IUser $user = null) {
      $invoice = new Invoice();

      $invoice->setBasedOn($this->object);
      $invoice->setDueOn(new DateValue());
      $invoice->setStatus(INVOICE_STATUS_DRAFT);
      $invoice->setState(STATE_VISIBLE);

      if(isset($additional['project_id'])) {
        $project_id = $additional['project_id'];
      } //if
      $invoice->setProjectId($project_id);

      $items = $this->prepareItemsForInvoice();
      
      $invoice->setCompanyId($client->getId());
      //$invoice->setCompanyName($client->getName());
      $invoice->setCompanyAddress($client_address);

      $invoice->setCurrencyId($this->object->getCurrency()->getId());
      $invoice->setLanguageId($this->object->getLanguageId());
      $invoice->setNote(array_var($additional, 'note', ''));
      $invoice->setPrivateNote(array_var($additional, 'private_note', ''));
      $invoice->setAllowPayments(array_var($additional, 'allow_payments', -1));
      $invoice->setPurchaseOrderNumber($additional['purchase_order_number']);

      if (!$this->object->isWon()) {
        DB::beginWork('Marking quote as won @ ' . __CLASS__);

        $this->object->markAsWon($user);
      
        $subscribers = $this->object->subscriptions()->get();
        if(is_foreachable($subscribers)) {
          // exclude a user who have won the quote
          foreach($subscribers as $k => $subscriber) {
            if($subscriber->getId() == $user->getId()) {
              unset($subscribers[$k]);
            } // if
          } // foreach
        } // if
      
        DB::commit('Quote marked as won @ ' . __CLASS__);
      } // if
      
      if($items && is_foreachable($items)) {
        return $this->commitInvoiceItems($items, $invoice); // Save, add items, recalculate
      } else {
        throw new Error('Invoice must have at least one item.');
      } // if
    } // create

    /**
     * Return items preview based on given settings
     *
     * @param array $settings
     * @param IUser $user
     * @return mixed
     */
    function previewItems($settings = null, IUser $user = null) {
      return $this->prepareItemsForInvoice();
    } // previewItems

    /**
     * Create items for invoice
     *
     * @return mixed
     */
    protected function prepareItemsForInvoice() {
      $items = array();

      if(is_foreachable($this->object->getItems())) {
        foreach($this->object->getItems() as $item) {
          $items[] = array(
            'description' => $item->getDescription(),
            'unit_cost' => $item->getUnitCost(),
            'quantity' => $item->getQuantity(),
            'first_tax_rate_id' => $item->getFirstTaxRateId(),
            'second_tax_rate_id' => $item->getSecondTaxRateId(),
            'total' => $item->getTotal(),
            'subtotal' => $item->getSubtotal()
          );
        } // foreach
      } // if

      return $items;
    } // prepareItemsForInvoice
    
  }