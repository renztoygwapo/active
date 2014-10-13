<?php

  class TestInvoicingModificationLog extends AngieModelTestCase {
  
    function testInvoiceFields() {
      $invoice = new Invoice();
      
      $this->assertIsA($invoice, 'Invoice', 'Invoice instance');
      $this->assertIsA($invoice->history(), 'IHistoryImplementation', 'Valid history helper instance');
      $this->assertIsA($invoice->history()->getRenderer(), 'HistoryRenderer', 'Valid renderer instance');
      
      $fields = $invoice->history()->getTrackedFields();
      
      $this->assertTrue(in_array('company_id', $fields));
      $this->assertTrue(in_array('project_id', $fields));
      $this->assertTrue(in_array('currency_id', $fields));
      $this->assertTrue(in_array('language_id', $fields));
      $this->assertTrue(in_array('number', $fields));
      $this->assertTrue(in_array('company_address', $fields));
      $this->assertTrue(in_array('note', $fields));
      $this->assertTrue(in_array('private_note', $fields));
      $this->assertTrue(in_array('status', $fields));
      $this->assertTrue(in_array('due_on', $fields));
      $this->assertTrue(in_array('allow_payments', $fields));
    } // testInvoiceFields
    
    function testQuoteFields() {
      $quote = new Quote();
      
      $this->assertIsA($quote, 'Quote', 'Quote instance');
      $this->assertIsA($quote->history(), 'IHistoryImplementation', 'Valid history helper instance');
      $this->assertIsA($quote->history()->getRenderer(), 'HistoryRenderer', 'Valid renderer instance');
      
      $fields = $quote->history()->getTrackedFields();
      
      $this->assertTrue(in_array('company_id', $fields));
      $this->assertTrue(in_array('company_name', $fields));
      $this->assertTrue(in_array('company_address', $fields));
      $this->assertTrue(in_array('currency_id', $fields));
      $this->assertTrue(in_array('language_id', $fields));
      $this->assertTrue(in_array('name', $fields));
      $this->assertTrue(in_array('note', $fields));
      $this->assertTrue(in_array('private_note', $fields));
      $this->assertTrue(in_array('status', $fields));
    } // testQuoteFields
    
  }