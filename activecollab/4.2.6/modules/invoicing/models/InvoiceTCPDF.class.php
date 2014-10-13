<?php 
  /**
   * Class that generates the pdf
   * 
   * @author Goran Radulovic
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */

  // class is based on TCPDF so we need to include it
  require_once(ANGIE_PATH .'/classes/tcpdf/init.php');

  class InvoiceTCPDF extends TCPDF {

    // constants
    const COLUMN_ITEM_NUMBER = 'item_number';
    const COLUMN_DESCRIPTION = 'description';
    const COLUMN_QUANTITY = 'quantity';
    const COLUMN_UNIT_COST = 'unit_cost';
    const COLUMN_SUBTOTAL = 'subtotal';
    const COLUMN_TAX = 'tax';
    const COLUMN_TOTAL = 'total';
    
    /**
     * Invoice instance
     * 
     * @var IInvoice
     */
    private $invoice;
    
    /**
     * Invoice template
     * 
     * @var InvoiceTemplate
     */
    private $template;

    /**
     * Line Height
     *
     * @var int
     */
    private $line_height = 3;
    
    /**
     * Class constructor
     * 
     * @param IInvoice $invoice
     */
    function __construct(IInvoice $invoice) {
      $this->invoice = $invoice;
      $this->template = new InvoiceTemplate();
      
      parent::__construct();
      
      $this->paper_format = $this->template->getPaperSize();
      $this->paper_orientation = Globalization::PAPER_ORIENTATION_PORTRAIT;
      
      // margins
      $this->SetLeftMargin(15);
      $this->SetRightMargin(15);

      $this->setHeaderMargin(10);
      $this->setFooterMargin(10);
            
      $this->SetTitle($this->invoice->getName(), true);
      $this->SetAuthor('activeCollab (http://www.activecollab.com/)');
      $this->SetCreator('activeCollab (http://www.activecollab.com/)');
      $this->SetAutoPageBreak(true, 20);
      $this->AddPage($this->paper_orientation, $this->paper_format); 
    } // __construct

    /**
     * Numbered columns count
     *
     * @var bool
     */
    private $numbered_columns_count = false;

    /**
     * Numbered column width
     *
     * @var bool
     */
    private $numbered_column_width = false;

    /**
     * Column widths
     *
     * @var bool
     */
    private $column_widths = false;
    
    /**
     * Calculate column width
     * 
     * @param number $column_id
     * @return float
     */
    function getColumnWidth($column_id) {
      if ($this->numbered_columns_count === false) {
        $this->numbered_columns_count = (int) $this->template->getDisplayQuantity() + (int) $this->template->getDisplayUnitCost() + (int) $this->template->getDisplaySubtotal() + (int) $this->template->getDisplayTotal();
        if ($this->template->getDisplayTaxRate() || $this->template->getDisplayTaxAmount()) {
          $this->numbered_columns_count += 1 + (int) $this->invoice->getSecondTaxIsEnabled();
        } // if

        $this->numbered_column_width = (57 / $this->numbered_columns_count) > 15 ? 15 : (57 / $this->numbered_columns_count);

        $this->column_widths = array(
          self::COLUMN_ITEM_NUMBER  => 5,
          self::COLUMN_DESCRIPTION  => 100 - ($this->numbered_column_width * $this->numbered_columns_count),
          self::COLUMN_QUANTITY     => $this->numbered_column_width,
          self::COLUMN_UNIT_COST    => $this->numbered_column_width,
          self::COLUMN_SUBTOTAL     => $this->numbered_column_width,
          self::COLUMN_TAX          => $this->numbered_column_width,
          self::COLUMN_TOTAL        => $this->numbered_column_width
        );

        if ($this->template->getDisplayItemOrder()) {
          $this->column_widths[self::COLUMN_DESCRIPTION] -= $this->column_widths[self::COLUMN_ITEM_NUMBER];
        } // if
      } // if

      // content width
      $content_width = $this->getUsefulPageWidth();

      // return column width
      return ($content_width * $this->column_widths[$column_id] / 100);
    } // getColumnWidth

    /**
     * Get Usefull Page Width
     *
     * @return
     */
    function getUsefulPageWidth() {
      return $this->getPageWidth() - $this->lMargin - $this->rMargin;
    } // getUsefulPageWidth

    /**
     * Shorten text to fit column
     *
     * @param string $text
     * @param integer $column
     */
    function shortenTextToFitColumn($text, $column) {
      $column_width = $this->getColumnWidth($column);

      $original_length = strlen_utf($text);
      while (($length = strlen_utf($text))) {
        $string_width = $this->GetStringWidth($text);

        if ($string_width <= $column_width) {
          return $original_length != $length ? $text . '.' : $text;
        } // if

        $text = substr_utf($text, 0, $length - 1);
      } // while

      return '';
    } // shortenTextToFitColumn
        
    /**
     * Generate the invoice
     * 
     * @param void
     * @return null
     */
    function generate() {
      $max_details_block_width = 60/100 * ($this->getPageWidth() - $this->lMargin - $this->rMargin);
      $max_client_block_width = 40/100 * ($this->getPageWidth() - $this->lMargin - $this->rMargin);
      $invoice_top_start = $this->GetY();

      // INVOICE DETAILS
      $text_rgb = $this->convertHTMLColorToDec($this->template->getInvoiceDetailsTextColor());
      $this->SetTextColor($text_rgb['R'], $text_rgb['G'], $text_rgb['B']);
      
      if ($this->template->getBodyLayout() == 1) {
        $invoice_details_starting_x = $this->lMargin;
        $text_alignment = 'L';        
      } else {
        $invoice_details_starting_x = $this->getPageWidth() - $max_details_block_width - $this->lMargin;
        $text_alignment = 'R';
      } // if

      $invoice_currency = $this->invoice->getCurrency();
      $invoice_language = $this->invoice->getLanguage();
      
      $this->SetFont($this->template->getInvoiceDetailsFont(), 'B', $this->template->getFontSize() + 6);
      $this->SetX($invoice_details_starting_x);
      $this->MultiCell($max_details_block_width, $this->line_height, $this->invoice->getName(), null, $text_alignment, false, false);
      $this->Ln();
      $this->SetY($this->GetY() + 2);
      
      $this->SetFont($this->template->getInvoiceDetailsFont(), '', $this->template->getFontSize());
      $invoice_details_content = '';

      // Details for invoice
      if($this->invoice instanceof Invoice) {
        if ($this->invoice->getPurchaseOrderNumber()) {
          $invoice_details_content .= lang('Purchase Order #: :po_number', array('po_number' => $this->invoice->getPurchaseOrderNumber()), true, $this->invoice->getLanguage()) . "<br />";
        } // if

        if($this->invoice->getProject() instanceof Project) {
          $invoice_details_content .= lang('Project: :project_name', array('project_name' => $this->invoice->getProject()->getName()), true, $this->invoice->getLanguage()) . "<br />";
        } // if

        if($this->invoice->getStatus() >= INVOICE_STATUS_ISSUED) {
          if($this->invoice->getIssuedOn()) {
            $invoice_details_content .= lang('Issued On: :issued_date', array('issued_date' => $this->invoice->getIssuedOn()->formatDateForUser(Authentication::getLoggedUser(), 0)), true, $this->invoice->getLanguage()) . "<br />";
          } // if

          if($this->invoice->getDueOn()) {
            $payment_text = lang('Payment Due On: :due_date', array('due_date' => $this->invoice->getDueOn()->formatForUser(Authentication::getLoggedUser(), 0)), true, $this->invoice->getLanguage());
            if ($this->invoice instanceof Invoice && $this->invoice->isOverdue()) {
              $invoice_details_content .= '<span color="red">' . $payment_text . '</span>';
            } else {
              $invoice_details_content .= $payment_text;
            } // if

            $invoice_details_content .= '<br />';
          } // if
        } // if
      } // if

      // Details for quotes
      if($this->invoice instanceof Quote) {
        if($this->invoice->getStatus() >= QUOTE_STATUS_SENT && $this->invoice->getSentOn()) {
          $invoice_details_content .= lang('Sent On: :sent_date', array('sent_date' => $this->invoice->getSentOn()->formatDateForUser(Authentication::getLoggedUser(), 0)), true, $this->invoice->getLanguage()) . "<br />";
        } // if
      } // if
 
      $this->SetX($invoice_details_starting_x);
      $this->writeHTMLCell($max_details_block_width, 5, null, null, $invoice_details_content, 0, $this->template->getLineHeight(), false, true, $text_alignment);

      $this->Ln();
      $invoice_details_end = $this->GetY();
      
      // CLIENT COMPANY
      $corner_width = 3;      
      $padding = 0;
      
      $max_block_width = 40/100 * ($this->getPageWidth() - $this->lMargin - $this->rMargin);
      
      if ($this->template->getBodyLayout() == 1) {
        $client_info_starting_x = $this->getPageWidth() - $max_block_width - $this->lMargin;
        $text_alignment = 'R';
      } else {
        $client_info_starting_x = $this->lMargin; 
        $text_alignment = 'L';  
      } // if
      
      $this->SetY($invoice_top_start);     
      
      $text_rgb = $this->convertHTMLColorToDec($this->template->getClientDetailsTextColor());
      $this->SetTextColor($text_rgb['R'], $text_rgb['G'], $text_rgb['B']);
      
      $this->SetY($invoice_top_start + $padding + 0);
      $this->SetX($client_info_starting_x + $padding);
      $this->SetFont($this->template->getClientDetailsFont(), 'B', $this->template->getFontSize());
      $this->Cell($max_client_block_width - (2 * $padding), $this->line_height + 3, $this->invoice->getCompanyName(), 0, 0, $text_alignment);
      $this->Ln();

      $this->SetX($client_info_starting_x + $padding);
      $this->SetFont($this->template->getClientDetailsFont(), '', $this->template->getFontSize());
      $this->MultiCell($max_client_block_width - (2 * $padding), $this->getFontSize(), $this->invoice->getCompanyAddress(), 0, $text_alignment, false, 1, '', '', true, 0, false, 1.25);
      $client_details_text_bottom = $this->GetY();
      
      $this->Ln();
      
      $client_details_bottom = $this->GetY();
      $this->SetY(max(array($client_details_bottom, $invoice_details_end)));
      
      // ITEMS TABLE HEADER   
      $this->SetY($this->GetY() + 5);
      
      $rgb = $this->convertHTMLColorToDec($this->template->getItemsTextColor());
      $this->SetTextColor($rgb['R'],$rgb['G'],$rgb['B']);                 
      $this->SetFont($this->template->getItemsFont(),'B',$this->template->getFontSize());
      
      $cell_border = 0;
      if ($this->template->getPrintItemsBorder()) {
        $rgb = $this->convertHTMLColorToDec($this->template->getItemsBorderColor());
        $this->SetDrawColor($rgb['R'], $rgb['G'], $rgb['B']);
        $cell_border = 'B';
      } // if     
      
      if ($this->template->getPrintTableBorder()) {
        $rgb = $this->convertHTMLColorToDec($this->template->getTableBorderColor());
        $this->SetDrawColor($rgb['R'], $rgb['G'], $rgb['B']);
        $cell_border = 'B';
      } // if

      // item order
      if ($this->template->getDisplayItemOrder()) {
        $this->Cell($this->getColumnWidth(self::COLUMN_ITEM_NUMBER), $this->template->getFontSize(), '#', $cell_border, 0, 'R', false, false);
      } // if

      // description
      $this->Cell($this->getColumnWidth(self::COLUMN_DESCRIPTION), $this->template->getFontSize(), lang('Description', null, true, $this->invoice->getLanguage()), $cell_border, 0, 'L', false, false);

      // quantity
      if ($this->template->getDisplayQuantity()) {
        $this->Cell($this->getColumnWidth(self::COLUMN_QUANTITY), $this->template->getFontSize(), $this->shortenTextToFitColumn(lang('Qty.', null, true, $this->invoice->getLanguage()), self::COLUMN_QUANTITY), $cell_border, 0, 'R', false, false);
      } // if

      // unit cost
      if ($this->template->getDisplayUnitCost()) {
        $this->Cell($this->getColumnWidth(self::COLUMN_UNIT_COST), $this->template->getFontSize(), $this->shortenTextToFitColumn(lang('Unit Cost', null, true, $this->invoice->getLanguage()), self::COLUMN_UNIT_COST), $cell_border, 0, 'R', false, false);
      } // if

      // subtotal
      if ($this->template->getDisplaySubtotal()) {
        $this->Cell($this->getColumnWidth(self::COLUMN_SUBTOTAL), $this->template->getFontSize(), $this->shortenTextToFitColumn(lang('Subtotal', null, true, $this->invoice->getLanguage()), self::COLUMN_SUBTOTAL), $cell_border, 0, 'R', false, false);
      } // if

      // tax rate and tax amount
      if ($this->template->getDisplayTaxAmount() || $this->template->getDisplayTaxRate()) {
        if ($this->invoice->getSecondTaxIsEnabled()) {
          $this->Cell($this->getColumnWidth(self::COLUMN_TAX), $this->template->getFontSize(), $this->shortenTextToFitColumn(lang('Tax #1', null, true, $this->invoice->getLanguage()), self::COLUMN_TAX), $cell_border, 0, 'R', false, false);
          $this->Cell($this->getColumnWidth(self::COLUMN_TAX), $this->template->getFontSize(), $this->shortenTextToFitColumn(lang('Tax #2', null, true, $this->invoice->getLanguage()), self::COLUMN_TAX), $cell_border, 0, 'R', false, false);
        } else {
          $this->Cell($this->getColumnWidth(self::COLUMN_TAX), $this->template->getFontSize(), $this->shortenTextToFitColumn(lang('Tax', null, true, $this->invoice->getLanguage()), self::COLUMN_TAX), $cell_border, 0, 'R', false, false);
        } // if
      } // if

      // total
      if ($this->template->getDisplayTotal()) {
        $this->Cell($this->getColumnWidth(self::COLUMN_TOTAL), $this->template->getFontSize(), $this->shortenTextToFitColumn(lang('Total', null, true, $this->invoice->getLanguage()), self::COLUMN_TOTAL), $cell_border, 0, 'R', false, false);
      } // if

      $this->Ln();
       
       // INVOICE ITEMS
      
      //  remember starting coordinates      
      $starting_x = $this->GetX();
      
      $this->setCellPaddings(0, 1, 0, 1);
      $this->SetFont($this->template->getItemsFont(), '', $this->template->getFontSize());
      $items = $this->invoice->getItems();

      if (is_foreachable($items)) {
        $table = '<table border="0" style="line-height: 1;">';

        $row_id = 1;
        $row_count = count($items);
        foreach ($items as $item) {
          $border_top = ' border-top: 0.2mm transparent #FFF;';
          $border_bottom = ' border-bottom: 0.2mm transparent #FFF;';

          if ($this->template->getPrintItemsBorder()) {
            $border_top = ' border-top: 0.2mm solid ' . $this->template->getItemsBorderColor() . ';';
          } // if

          if ($row_count == $row_id && $this->template->getPrintTableBorder()) {
            $border_bottom = ' border-bottom: 0.3mm solid ' . $this->template->getTableBorderColor() . ';';
          } // if


          $cell_style = $border_bottom . $border_top . ' line-height: 0.45mm';


          $table.= '<tr>';

          if ($this->template->getDisplayItemOrder()) {
            $table.= '<td width="'. $this->getColumnWidth(self::COLUMN_ITEM_NUMBER) . 'mm" style="text-align: right;' . $cell_style . '">' . $row_id . '. &nbsp;</td>';
          } // if

          // description
          $table.= '<td width="'. $this->getColumnWidth(self::COLUMN_DESCRIPTION) . 'mm" style="text-align: left;' . $cell_style . '">' . $item->getFormattedDescription() . '</td>';

          // quantity
          if ($this->template->getDisplayQuantity()) {
            $table.= '<td width="'. $this->getColumnWidth(self::COLUMN_QUANTITY) . 'mm" style="text-align: right;' . $cell_style . '">' . $item->getQuantity() . '</td>';
          } // if

          // unit cost
          if ($this->template->getDisplayUnitCost()) {
            $table.= '<td width="'. $this->getColumnWidth(self::COLUMN_UNIT_COST) . 'mm" style="text-align: right;' . $cell_style . '">' . Globalization::formatMoney($item->getUnitCost(), $invoice_currency, $invoice_language) . '</td>';
          } // if

          // subtotal
          if ($this->template->getDisplaySubtotal()) {
            $table.= '<td width="'. $this->getColumnWidth(self::COLUMN_SUBTOTAL) . 'mm" style="text-align: right;' . $cell_style . '">' . Globalization::formatMoney($item->getSubtotal(), $invoice_currency, $invoice_language) . '</td>';
          } // if

          // tax rate or tax amount
          if ($this->template->getDisplayTaxRate()) {
            $table.= '<td width="'. $this->getColumnWidth(self::COLUMN_TAX) . 'mm" style="text-align: right;' . $cell_style . '">' . ($item->getFirstTaxRatePercentageVerbose() ? $item->getFirstTaxRatePercentageVerbose() : '-') . '</td>';
            if ($this->invoice->getSecondTaxIsEnabled()) {
              $table.= '<td width="'. $this->getColumnWidth(self::COLUMN_TAX) . 'mm" style="text-align: right;' . $cell_style . '">' . ($item->getSecondTaxRatePercentageVerbose() ? $item->getSecondTaxRatePercentageVerbose() : '-') . '</td>';
            } // if
          } else if ($this->template->getDisplayTaxAmount()) {
            $table.= '<td width="'. $this->getColumnWidth(self::COLUMN_TAX) . 'mm" style="text-align: right;' . $cell_style . '">' . Globalization::formatMoney($item->getFirstTax(), $invoice_currency, $invoice_language) . '</td>';
            if ($this->invoice->getSecondTaxIsEnabled()) {
              $table.= '<td width="'. $this->getColumnWidth(self::COLUMN_TAX) . 'mm" style="text-align: right;' . $cell_style . '">' . Globalization::formatMoney($item->getSecondTax(), $invoice_currency, $invoice_language) . '</td>';
            } // if
          } // if

          // total
          if ($this->template->getDisplayTotal()) {
            $table.= '<td width="'. $this->getColumnWidth(self::COLUMN_TOTAL) . 'mm" style="text-align: right;' . $cell_style . '">' . Globalization::formatMoney($item->getTotal(), $invoice_currency, $invoice_language) . '</td>';
          } // if

          $table.= '</tr>';
          $row_id++;
        } // foreach

        $table.= '</table>';
        $this->writeHTML($table, false, false, false, false, '');
      } // if

      if ($this->getColumnWidth(self::COLUMN_TOTAL) > 26) {
        $col1_width = $this->getColumnWidth(self::COLUMN_TOTAL);
        $col2_width = $this->getColumnWidth(self::COLUMN_TOTAL);
      } else {
        $col1_width = $this->getColumnWidth(self::COLUMN_TOTAL);
        $col2_width = $this->getColumnWidth(self::COLUMN_TOTAL) * 2;
      } // if
      $starting_x = $this->getUsefulPageWidth() - $col1_width - $col2_width + $this->lMargin;

      $cell_border = 0;
      if ($this->template->getPrintItemsBorder()) {
        $rgb = $this->convertHTMLColorToDec($this->template->getItemsBorderColor());
        $this->SetDrawColor($rgb['R'], $rgb['G'], $rgb['B']);
        $cell_border = 'B';
      } // if

      // SUBTOTAL
      $this->SetX($starting_x);
      $this->Cell($col1_width, $this->template->getLineHeight(), lang('Subtotal:', null, true, $this->invoice->getLanguage()), $cell_border, 0, 'R', false, false);
      $this->Cell($col2_width, $this->template->getLineHeight(), Globalization::formatMoney($this->invoice->getSubTotal(), $invoice_currency, $invoice_language), $cell_border, 0, 'R', false, false);
      $this->Ln();

      // TAX
      if(($this->template->getSummarizeTax() || !is_foreachable($this->invoice->getTaxGroupedByType())) && !($this->template->getHideTaxSubtotal() && $this->invoice->getTax() == 0)) {
        $this->SetX($starting_x);
        $this->Cell($col1_width, $this->template->getLineHeight(), lang('Tax:', null, true, $this->invoice->getLanguage()), $cell_border, 0, 'R', false, false);
        $this->Cell($col2_width, $this->template->getLineHeight(), Globalization::formatMoney($this->invoice->getTax(), $invoice_currency, $invoice_language), $cell_border, 0, 'R', false, true);
        $this->Ln();
      } else {
        foreach($this->invoice->getTaxGroupedByType() as $grouped_tax) {
          $this->SetX($starting_x);
          $this->Cell($col1_width, $this->template->getLineHeight(), lang(array_var($grouped_tax, 'name').':', null, true, $this->invoice->getLanguage()), $cell_border, 0, 'R', false, false);
          $this->Cell($col2_width, $this->template->getLineHeight(), Globalization::formatMoney(array_var($grouped_tax, 'amount'), $invoice_currency, $invoice_language), $cell_border, 0, 'R', false, true);
          $this->Ln();
        } // foreach
      } // if

      if ($this->invoice->requireRounding()) {
        // ROUNDING DIFFERENCE
        $this->SetX($starting_x);
        $this->Cell($col1_width, $this->template->getLineHeight(), lang('Rounding Difference:', null, true, $this->invoice->getLanguage()), $cell_border, 0, 'R', false, false);
        $this->Cell($col2_width, $this->template->getLineHeight(), Globalization::formatMoney($this->invoice->getRoundingDifference(), $invoice_currency, $invoice_language), $cell_border, 0, 'R', false, true);
        $this->Ln();
      } // if
      
      if ($this->template->getPrintTableBorder()) {
        $rgb = $this->convertHTMLColorToDec($this->template->getTableBorderColor());
        $this->SetDrawColor($rgb['R'], $rgb['G'], $rgb['B']);
        $cell_border = 'B';
      } // if

      // TOTAL
      $this->SetFont($this->template->getItemsFont(), 'B', $this->template->getFontSize());
      $this->SetX($starting_x);
      $this->Cell($col1_width, $this->template->getLineHeight(), lang('Total:', null, true, $this->invoice->getLanguage()), $cell_border, 0, 'R', false, false);
      $this->Cell($col2_width, $this->template->getLineHeight(), Globalization::formatMoney($this->invoice->getTotal(true), $invoice_currency, $invoice_language), $cell_border, 0, 'R', false, true);
      $this->Ln();

      if (!($this->invoice instanceof Quote) && !(!$this->template->getShowAmountPaidBalanceDue() && (!$this->invoice->isPaid() && $this->invoice->payments()->getPayments() === null || $this->invoice->isPaid()))) {
        $cell_border = 0;
        if ($this->template->getPrintItemsBorder()) {
          $rgb = $this->convertHTMLColorToDec($this->template->getItemsBorderColor());
          $this->SetDrawColor($rgb['R'], $rgb['G'], $rgb['B']);
          $cell_border = 'B';
        } // if

        // AMOUNT PAID
        $this->SetFont($this->template->getItemsFont(), '', $this->template->getFontSize());
        $this->SetX($starting_x);
        $this->Cell($col1_width, $this->template->getLineHeight(), lang('Amount Paid:', null, true, $this->invoice->getLanguage()), $cell_border, 0, 'R', false, false);
        $this->Cell($col2_width, $this->template->getLineHeight(), Globalization::formatMoney($this->invoice->getPaidAmount(), $invoice_currency, $invoice_language), $cell_border, 0, 'R', false, true);
        $this->Ln();

        if ($this->template->getPrintTableBorder()) {
          $rgb = $this->convertHTMLColorToDec($this->template->getTableBorderColor());
          $this->SetDrawColor($rgb['R'], $rgb['G'], $rgb['B']);
          $cell_border = 'B';
        } // if

        // BALANCE DUE
        if ($this->invoice->isOverdue()) {
          $this->SetTextColor(255, 0, 0);
        } // if

        $this->SetFont($this->template->getItemsFont(), 'B', $this->template->getFontSize());
        $this->SetX($starting_x);
        $this->Cell($col1_width, $this->template->getLineHeight(), lang('Balance Due:', null, true, $this->invoice->getLanguage()), $cell_border, 0, 'R', false, false);
        $this->Cell($col2_width, $this->template->getLineHeight(), $this->invoice->getCurrencyCode() . ' ' . Globalization::formatMoney($this->invoice->getBalanceDue(), $invoice_currency, $invoice_language), $cell_border, 0, 'R', false, true);
        $this->Ln();
      } // if

      // INVOICE NOTE
      if (!trim($this->invoice->getNote())) {
        return true;
      } // if
      $this->SetY($this->GetY() + 10);
      $rgb = $this->convertHTMLColorToDec($this->template->getNoteTextColor());
      $this->SetTextColor($rgb['R'],$rgb['G'],$rgb['B']);
      $this->SetFont($this->template->getNoteFont(), 'B', $this->template->getFontSize());
      $this->Cell(0, $this->template->getLineHeight(), lang('Note', null, null, $this->invoice->getLanguage()) . ':');
      $this->Ln();
      $this->SetFont($this->template->getNoteFont(), '', $this->template->getFontSize());
      $this->MultiCell(0, $this->template->getLineHeight(), $this->invoice->getNote(), 0, 'L', false, 1, '', '', true, 0, false, 0);
    } // generate
    
    /**
     * Function which renders the header (and page background if needed)
     * 
     * @param void
     * @return null
     */
    function Header() {
      // page background if its enabled
      if ($this->template->hasBackgroundImage()) {
        // @TUTORIAL http://www.tcpdf.org/examples/example_051.phps
        $bMargin = $this->getBreakMargin();
        $auto_page_break = $this->AutoPageBreak;
        $this->SetAutoPageBreak(false, 0);
        $this->Image($this->template->getBackgroundImagePath(), 0, 0, $this->getPageWidth(), $this->getPageHeight(), '', '', '', false, 300, 'C', false, false, 0);
        $this->SetAutoPageBreak($auto_page_break, $bMargin);
        $this->setPageMark();
      } // if
      
      if (!$this->template->getPrintLogo() && !$this->template->getPrintCompanyDetails()) {
        $this->SetTopMargin(47.78 + 5);
        $this->SetY($this->tMargin + 5);

        return false;
      } // if
      
      $max_block_width = 40/100 * ($this->getPageWidth() - $this->lMargin - $this->rMargin);
      $start_y = $this->GetY();
      
      // font style
      $text_rgb = $this->convertHTMLColorToDec($this->template->getHeaderTextColor());
      $this->SetTextColor($text_rgb['R'], $text_rgb['G'], $text_rgb['B']);
      $this->SetFont($this->template->getHeaderFont(),'',$this->template->getFontSize());

      $details_height = 0;
      $details_start = $this->GetY();
      
      // print details if you enabled the company details
      if ($this->template->getPrintCompanyDetails()) {
        if ($this->template->getHeaderLayout()) {
          $text_alignment = 'L';
          $this->SetX($this->lMargin);
        } else {
          $text_alignment = 'R';
          $this->SetX($this->getPageWidth() - $this->rMargin - $max_block_width);
        } // if
        $this->MultiCell($max_block_width, $this->template->getFontSize(), trim($this->template->getCompanyName() . "\n" . $this->template->getCompanyDetails()), 0, $text_alignment, false, 1, '', '', true, 0, false, 1.25);
        $details_end = $this->GetY();
        $details_height = $details_end - $details_start;
      } // if

      $calculated_height = 0;
      if ($this->template->hasLogoImage() && $this->template->getPrintLogo()) {
        // get constraints
        $max_logo_width = 50/100 * ($this->getPageWidth() - $this->lMargin - $this->rMargin);
        $max_logo_height = $details_height > 20 ? ($details_height > 30 ? 30 : $details_height) : 20;

        // get the real logo dimensions
        $logo_info = getimagesize($this->template->getLogoImagePath());
        $logo_info_width = $logo_info[0];
        $logo_info_height = $logo_info[1];

        $ratio = min($max_logo_width / $logo_info_width, $max_logo_height / $logo_info_height); // calculate the resize ratio

        $calculated_width = floor($logo_info_width * $ratio); // calculate new width
        $calculated_height = floor($logo_info_height * $ratio); // calculate new height

        $logo_y_offset = ($details_height - $calculated_height) / 2; // get the vertical logo offset
        $logo_y_offset = $logo_y_offset < 0 ? 0 : $logo_y_offset  ; // make sure that we get valid vertical offset

        $align = $this->template->getHeaderLayout() ? 'R' : 'L'; // find out how to align logo
        
        $this->Image($this->template->getLogoImagePath(), 0, $start_y + $logo_y_offset, $calculated_width, $calculated_height, false, false, false, false, false, $align);
      } // if

      $current_y = max($calculated_height, $details_height) + $details_start; // calculate the height of current header
      
      // header border
      if ($this->template->getPrintHeaderBorder()) {
        $this->SetLineStyle(array('color' => $this->convertHTMLColorToDec($this->template->getHeaderBorderColor())));
        $this->Line($this->lMargin, $current_y + 5, $this->getPageWidth() - $this->lMargin, $current_y + 5);
      } // if

      // page(s) offset
      if($this->template->getPrintLogo() || $this->template->getPrintCompanyDetails()) {
        $this->SetTopMargin($current_y + 5 + $this->getHeaderMargin());
        $this->SetY($this->GetY() + 5);
      } // if
    } // Header
    
    /**
     * Function which renders the footer
     * 
     * @param void
     * @return null
     */
    function Footer() {
      // footer font style
      if ($this->template->getPrintFooter()) {
        $rgb = $this->convertHTMLColorToDec($this->template->getFooterTextColor());
        $this->SetTextColor($rgb['R'],$rgb['G'],$rgb['B']);
        $this->SetFont($this->template->getFooterFont(),'',$this->template->getFontSize());

        if ($this->template->getPrintFooterBorder()) {
          $this->SetY(-10 - $this->template->getFontSize());
          $this->SetLineStyle(array('color' => $this->convertHTMLColorToDec($this->template->getFooterBorderColor())));
          $this->Line($this->lMargin, $this->GetY()+2, $this->getPageWidth() - $this->lMargin, $this->GetY()+2);
        } else {
          $this->SetY(-10 - $this->template->getFontSize());
        } // if

        $this->SetY($this->GetY() + 5);

        if ($this->template->getFooterLayout()) {
          $this->SetX($this->getPageWidth() - $this->rMargin - 60);
          $this->Cell(60, $this->template->getLineHeight(),$this->invoice->getName(), 0, 0, 'R');
          $this->SetX($this->lMargin);
          $this->Cell(0, $this->template->getLineHeight(), lang('Page :page_no of :total_pages', array('page_no' => $this->PageNo(), 'total_pages' => $this->getAliasNbPages()), true, $this->invoice->getLanguage()), 0, 0, 'L');
        } else {
          $this->SetX($this->getPageWidth() - $this->rMargin - 34);
          $this->Cell(40, $this->template->getLineHeight(),lang('Page :page_no of :total_pages', array('page_no' => $this->PageNo(), 'total_pages' => $this->getAliasNbPages()), true, $this->invoice->getLanguage()), 0, 0, 'R');
          $this->SetX($this->lMargin);
          $this->Cell(0, $this->template->getLineHeight(), $this->invoice->getName(), 0, 0, 'L');
        } // if
      } // if

    } // Footer
    
  } // InvoiceTCPDF