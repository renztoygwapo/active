<?php 
  /**
   * Class that cares about storing invoice template configuration
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
	class InvoiceTemplate implements IDescribe {
		
		/**
		 * Default font
		 * 
		 * @var string
		 */
		const DEFAULT_FONT = 'dejavusans';
		
		/**
		 * Settings
		 * 
		 * @var array
		 */
		private $attributes;
		
		/**
		 * Constructor
		 * 
		 * @param void
		 * @return InvoiceTemplate
		 */
		function __construct() {
			$this->attributes = ConfigOptions::getValue('invoice_template');
			
			if (!$this->attributes) {
				$this->attributes = array(
					'font_size'                     => $this->getFontSize(),
					'paper_size'                    => $this->getPaperSize(),
					'print_logo'                    => $this->getPrintLogo(),
					'print_company_details'         => $this->getPrintCompanyDetails(),
					'company_name'                  => $this->getCompanyName(),
					'company_details'               => $this->getCompanyDetails(),
					'header_font'                   => $this->getHeaderFont(),
					'header_text_color'             => $this->getHeaderTextColor(),
					'header_layout'                 => $this->getHeaderLayout(),
					'print_header_border'           => $this->getPrintHeaderBorder(),
					'header_border_color'           => $this->getHeaderBorderColor(),
					'body_layout'                   => $this->getBodyLayout(),
					'client_details_font'           => $this->getClientDetailsFont(),
					'client_details_text_color'     => $this->getClientDetailsTextColor(),
					'invoice_details_font'          => $this->getInvoiceDetailsFont(),
					'invoice_details_text_color'    => $this->getInvoiceDetailsTextColor(),
					'items_font'                    => $this->getItemsFont(),
					'items_text_color'              => $this->getItemsTextColor(),
					'print_table_border'            => $this->getPrintTableBorder(),
					'table_border_color'            => $this->getTableBorderColor(),
					'print_items_border'            => $this->getPrintItemsBorder(),
					'items_border_color'            => $this->getItemsBorderColor(),
					'note_font'                     => $this->getNoteFont(),
					'note_text_color'               => $this->getNoteTextColor(),
          'print_footer'                  => $this->getPrintFooter(),
					'footer_layout'                 => $this->getFooterLayout(),
					'footer_font'                   => $this->getFooterFont(),
					'footer_text_color'             => $this->getFooterTextColor(),
					'print_footer_border'           => $this->getPrintFooterBorder(),
					'footer_border_color'           => $this->getFooterBorderColor(),
          'display_item_order'            => $this->getDisplayItemOrder(),
          'display_quantity'              => $this->getDisplayQuantity(),
          'display_unit_cost'             => $this->getDisplayUnitCost(),
          'display_subtotal'              => $this->getDisplaySubtotal(),
          'display_tax_rate'              => $this->getDisplayTaxRate(),
          'display_tax_amount'            => $this->getDisplayTaxAmount(),
          'display_total'                 => $this->getDisplayTotal(),
          'summarize_tax'                 => $this->getSummarizeTax(),
          'hide_tax_subtotal'             => $this->getHideTaxSubtotal(),
          'show_amount_paid_balance_due'  => $this->getShowAmountPaidBalanceDue()
				);
			} // if
		} // construct
		
		/**
		 * Set attributes
		 * 
		 * @return array
		 */
		function getAttributes() {
			return $this->attributes;
		} // getAttributes
		
		/**
		 * Get global font size
		 * 
		 * @param void
		 * @return integer
		 */
		function getFontSize() {
			return array_var($this->attributes, 'font_size', 9);
		} // getFontSize
		
		/**
		 * Set global font size
		 * 
		 * @param integer
		 * @return null
		 */
		function setFontSize($size) {
			$this->attributes['font_size'] = $size;
		} // setFontSize

		/**
		 * Get global font size
		 * 
		 * @param void
		 * @return integer
		 */
		function getLineHeight() {
			return 5.5;
//			return array_var($this->attributes, 'line_height', 15);
		} // getLineHeight
		
		/**
		 * Set global font size
		 * 
		 * @param integer
		 * @return null
		 */
		function setLineHeight($size) {
			return false;
//			$this->attributes['line_height'] = $size;
		} // setLineHeight
		
		/**
		 * Return paper size
		 * 
		 * @return string
		 */
		function getPaperSize() {
			return array_var($this->attributes, 'paper_size', Globalization::PAPER_FORMAT_A4);
		} // getPaperSize
		
		/**
		 * set paper size
		 * 
		 * @param string $paper_size
		 */
		function setPaperSize($paper_size) {
			$this->attributes['paper_size'] = $paper_size;
		} // setPaperSize
		
		/**
		 * Set print logo
		 * 
		 * @return boolean
		 */
		function getPrintLogo() {
			return array_var($this->attributes, 'print_logo', true);
		} // getPrintLogo
		
		/**
		 * Set print logo
		 * 
		 * @param boolean $print_logo
		 */
		function setPrintLogo($print_logo) {
			$this->attributes['print_logo'] = $print_logo;
		} // setPrintLogo
		
		/**
		 * Get print company details
		 * 
		 * @return boolean
		 */
		function getPrintCompanyDetails() {
			return array_var($this->attributes, 'print_company_details', true);
		} // setPrintCompanyDetails
		
		/**
		 * Get company name
		 * 
		 * @return string
		 */
		function getCompanyName() {
			$company_name = array_var($this->attributes, 'company_name');
			if ($company_name) {
				return $company_name;
			} // if
			
			$owner_company = Companies::findOwnerCompany();
			if (!$owner_company instanceof Company) {
				throw new Error(lang('Owner company does not exists'));
			} // if
			
			return $owner_company->getName();
		} // getCompanyName
		
		/**
		 * Set company name
		 * 
		 * @param string $company_name
		 */
		function setCompanyName($company_name) {
			$this->attributes['company_name'] = $company_name;
		} // setCompanyName
		
		/**
		 * Get company details
		 * 
		 * @return string
		 */
		function getCompanyDetails() {
			$company_details = array_var($this->attributes, 'company_details');
			if ($company_details) {
				return $company_details;
			} // if
			
			$owner_company = Companies::findOwnerCompany();
			if (!$owner_company instanceof Company) {
				throw new Error(lang('Owner company does not exists'));
			} // if
			
			$company_details = '';
			if ($owner_company->getConfigValue('office_address')) {
				$company_details .= "\n" . $owner_company->getConfigValue('office_address');
      } // if
      if ($owner_company->getConfigValue('office_phone')) {
      	$company_details .= "\n" . $owner_company->getConfigValue('office_phone');
			} // if			
      if ($owner_company->getConfigValue('office_fax')) {
				$company_details .= "\n" . $owner_company->getConfigValue('office_fax');
			} // if
      if ($owner_company->getConfigValue('office_homepage')) {
      	$company_details .= "\n" . $owner_company->getConfigValue('office_homepage');
			} // if
			
			return $company_details;
		} // getCompanyDetails
		
		/**
		 * Get the header font
		 * 
		 * @return string
		 */
		function getHeaderFont() {
			return array_var($this->attributes, 'header_font', InvoiceTemplate::DEFAULT_FONT);
		} // getHeaderFont
		
		/**
		 * Set's the header font
		 * 
		 * @param string $font_name
		 */
		function setHeaderFont($font_name) {
			$this->attributes['header_font'] = $font_name;
		} // setHeaderFont
		
		/**
		 * returns the header text color with # in front
		 * 
		 * @return string
		 */
		function getHeaderTextColor() {
			return array_var($this->attributes, 'header_text_color', '#000000');
		} // getHeaderTextColor
		
		/**
		 * sets the header text color
		 * 
		 * @param string $text_color
		 */
		function setHeaderTextColor($text_color) {
			if (!is_valid_hex_color($text_color)) {
				throw new Error('Text color is not valid');
			} // if
			$this->attributes['header_text_color'] = $text_color;
		} // setHeaderTextColor
		
		/**
		 * Set company details
		 * 
		 * @param string $company_details
		 */
		function setCompanyDetails($company_details) {
			$this->attributes['company_details'] = $company_details;
		} // setCompanyDetails
		
		/**
		 * Set print company details
		 * 
		 * @param boolean $print_company_details
		 */
		function setPrintCompanyDetails($print_company_details) {
			$this->attributes['print_company_details'] = $print_company_details;
		} // setPrintCompanyDetails
		
		/**
		 * Uses the background image
		 * 
		 * @param string $path
		 * @return boolean
		 */
		function useBackgroundImage($path) {
			if (!is_file($path)) {
				throw new FileDnxError($path);
			} // if
			
			$info = getimagesize($path);
			$mime_type = strtolower($info['mime']);
			
			if (!in_array($mime_type, array('image/png', 'image/jpg', 'image/jpeg'))) {
				throw new Error(lang('Background image should be PNG or JPEG image'));
			} // if		
			
			if (!@copy($path, $this->getBackgroundImagePath())) {
				throw new FileCopyError($path, $this->getBackgroundImagePath());
			} // if
			return true;
		} // useBackgroundImage
		
		/**
		 * Remove background image
		 * 
		 * @return boolean
		 */
		function removeBackgroundImage() {
			if ($this->hasBackgroundImage() && !@unlink($this->getBackgroundImagePath())) {
				throw new FileDeleteError($this->getBackgroundImagePath());
			} // if
			return true;
		} // removeBackgroundImage
		
		/**
		 * Has background image
		 * 
		 * @return boolean
		 */
		function hasBackgroundImage() {
			return is_file($this->getBackgroundImagePath());
		} // hasBackgroundImage
		
		/**
		 * Get path to the background image
		 * 
		 * @return string
		 */
		function getBackgroundImagePath() {
			return PUBLIC_PATH . '/brand/invoice-background-image.png';
		} // getBackgroundImagePath
		
		/**
		 * Get url to the background image
		 * 
		 * @return string
		 */
		function getBackgroundImageUrl() {
      return AngieApplication::getBrandImageUrl('invoice-background-image.png', true);
		} // getBackgroundImageUrl
		
		/**
		 * Get header layout
		 * 
		 * @return integer
		 */
		function getHeaderLayout() {
			return array_var($this->attributes, 'header_layout', 0);
		} // getHeaderLayout
		
		/**
		 * Set header layout
		 * 
		 * @param integer $layout
		 * @return null
		 */
		function setHeaderLayout($layout) {
			$this->attributes['header_layout'] = $layout;
		} // setHeaderLayout
		
		/**
		 * Use logo image
		 * 
		 * @param string $path
		 * @return boolean
		 */
		function useLogoImage($path) {
			if (!is_file($path)) {
				throw new FileDnxError($path);
			} // if
			
			$info = getimagesize($path);
			$mime_type = strtolower($info['mime']);
			
			if (!in_array($mime_type, array('image/png', 'image/jpg', 'image/jpeg'))) {
				throw new Error(lang('Logo should be PNG or JPEG image'));
			} // if		
			
			if (!@copy($path, $this->getLogoImagePath())) {
				throw new FileCopyError($path, $this->getLogoImagePath());
			} // if
			
			return true;
		} // useLogoImage
		
		/**
		 * Remove logo image
		 * 
		 * @return boolean
		 */
		function removeLogoImage() {
			if ($this->hasLogoImage() && !@unlink($this->getLogoImagePath())) {
				throw new FileDeleteError($this->getLogoImagePath());
			} // if
			return true;
		} // removeLogoImage
		
		/**
		 * Has logo image
		 * 
		 * @return boolean
		 */
		function hasLogoImage() {
			return is_file($this->getLogoImagePath());
		} // hasLogoImage
		
		/**
		 * Get path to the logo image
		 * 
		 * @return string
		 */
		function getLogoImagePath() {
			return PUBLIC_PATH . '/brand/invoice-logo.png';
		} // getLogoImagePath
		
		/**
		 * Get url to the background image
		 * 
		 * @return string
		 */
		function getLogoImageUrl() {
      return AngieApplication::getBrandImageUrl('invoice-logo.png', true);
		} // getBackgroundImageUrl
		
		/**
		 * Set print header border
		 * 
		 * @return boolean
		 */
		function getPrintHeaderBorder() {
			return array_var($this->attributes, 'print_header_border', true);
		} // getPrintHeaderBorder
		
		/**
		 * Set print header border
		 * 
		 * @param boolean $print_border
		 */
		function setPrintHeaderBorder($print_border) {
			$this->attributes['print_header_border'] = $print_border;
		} // setPrintHeaderBorder
		
		/**
		 * Get header border color
		 * 
		 * @param void
		 * @return string
		 */
		function getHeaderBorderColor() {
			return array_var($this->attributes, 'header_border_color', '#cccccc');
		} // getHeaderBorderColor
		
		/**
		 * Set header border color
		 * 
		 * @param string $font_name
		 * @return null
		 */
		function setHeaderBorderColor($color) {
			$this->attributes['header_border_color'] = $color;
		} // setHeaderBorderColor		
		
		/**
		 * Get body layout
		 * 
		 * @return integer
		 */
		function getBodyLayout() {
			return array_var($this->attributes, 'body_layout', 0);
		} // getBodyLayout
		
		/**
		 * Set body layout
		 * 
		 * @param integer $layout
		 * @return null
		 */
		function setBodyLayout($layout) {
			$this->attributes['body_layout'] = $layout;
		} // setBodyLayout
		
		/**
		 * Get client details font
		 * 
		 * @param void
		 * @return string
		 */
		function getClientDetailsFont() {
			return array_var($this->attributes, 'client_details_font', InvoiceTemplate::DEFAULT_FONT);
		} // getClientDetailsFont
		
		/**
		 * Set client details font
		 * 
		 * @param string $font_name
		 * @return null
		 */
		function setClientDetailsFont($font_name) {
			$this->attributes['client_details_font'] = $font_name;
		} // setClientDetailsFont
		
		/**
		 * Get client details font
		 * 
		 * @param void
		 * @return string
		 */
		function getClientDetailsTextColor() {
			return array_var($this->attributes, 'client_details_text_color', '#000000');
		} // getClientDetailsTextColor
		
		/**
		 * Set client details font
		 * 
		 * @param string $font_name
		 * @return null
		 */
		function setClientDetailsTextColor($color) {
			$this->attributes['client_details_text_color'] = $color;
		} // setClientDetailsTextColor
		
		/**
		 * Get invoice details font
		 * 
		 * @param void
		 * @return string
		 */
		function getInvoiceDetailsFont() {
			return array_var($this->attributes, 'invoice_details_font', InvoiceTemplate::DEFAULT_FONT);
		} // getInvoiceDetailsFont
		
		/**
		 * Set invoice details font
		 * 
		 * @param string $font_name
		 * @return null
		 */
		function setInvoiceDetailsFont($font_name) {
			$this->attributes['invoice_details_font'] = $font_name;
		} // setInvoiceDetailsFont
		
		/**
		 * Get invoice details font
		 * 
		 * @param void
		 * @return string
		 */
		function getInvoiceDetailsTextColor() {
			return array_var($this->attributes, 'invoice_details_text_color', '#000000');
		} // getInvoiceDetailsTextColor
		
		/**
		 * Set invoice details font
		 * 
		 * @param string $font_name
		 * @return null
		 */
		function setInvoiceDetailsTextColor($color) {
			$this->attributes['invoice_details_text_color'] = $color;
		} // setInvoiceDetailsTextColor		
		
		/**
		 * Get items font
		 * 
		 * @param void
		 * @return string
		 */
		function getItemsFont() {
			return array_var($this->attributes, 'items_font', InvoiceTemplate::DEFAULT_FONT);
		} // getItemsFont
		
		/**
		 * Set items font
		 * 
		 * @param string $font_name
		 * @return null
		 */
		function setItemsFont($font_name) {
			$this->attributes['items_font'] = $font_name;
		} // setItemsFont
		
		/**
		 * Get items font
		 * 
		 * @param void
		 * @return string
		 */
		function getItemsTextColor() {
			return array_var($this->attributes, 'items_text_color', '#000000');
		} // getItemsTextColor
		
		/**
		 * Set items font
		 * 
		 * @param string $font_name
		 * @return null
		 */
		function setItemsTextColor($color) {
			$this->attributes['items_text_color'] = $color;
		} // setItemsTextColor

		/**
		 * get Print table border
		 * 
		 * @param void
		 * @return boolean
		 */
		function getPrintTableBorder() {
			return array_var($this->attributes, 'print_table_border', true);
		} // getPrintTableBorder

		/**
		 * set print table border
		 * 
		 * @param boolean $print
		 * @return null
		 */
		function setPrintTableBorder($print) {
			$this->attributes['print_table_border'] = (boolean) $print;
			
//			var_dump($print);
//			var_dump($this->attributes);
			
		} // setPrintTableBorder

		/**
		 * Get table border color
		 * 
		 * @param void
		 * @return string
		 */
		function getTableBorderColor() {
			return array_var($this->attributes, 'table_border_color', '#cccccc');
		} // getTableBorderColor
		
		/**
		 * Set table border color
		 * 
		 * @param string $font_name
		 * @return null
		 */
		function setTableBorderColor($color) {
			$this->attributes['table_border_color'] = $color;
		} // setTableBorderColor

		/**
		 * get Print items border
		 * 
		 * @param void
		 * @return boolean
		 */
		function getPrintItemsBorder() {
			return array_var($this->attributes, 'print_items_border', true);
		}

		/**
		 * set print border
		 * 
		 * @param boolean $print
		 * @return null
		 */
		function setPrintItemsBorder($print) {
			$this->attributes['print_items_border'] = (boolean) $print;
		} // setPrintItemsBorder

		/**
		 * Get items font
		 * 
		 * @param void
		 * @return string
		 */
		function getItemsBorderColor() {
			return array_var($this->attributes, 'items_border_color', '#eeeeee');
		} // getItemsBorderColor
		
		/**
		 * Set items font
		 * 
		 * @param string $font_name
		 * @return null
		 */
		function setItemsBorderColor($color) {
			$this->attributes['items_border_color'] = $color;
		} // setItemsBorderColor		
		
		/**
		 * Get note font
		 * 
		 * @return string
		 */
		function getNoteFont() {
			return array_var($this->attributes, 'note_font', InvoiceTemplate::DEFAULT_FONT);
		} // getNoteFont
		
		/**
		 * Set note font
		 * 
		 * @param string $font_name
		 */
		function setNoteFont($font_name) {
			$this->attributes['note_font'] = $font_name;
		} // setNoteFont
		
		/**
		 * Get note font color
		 * 
		 * @return string
		 */
		function getNoteTextColor() {
			return array_var($this->attributes, 'note_text_color', '#333333');
		} // getNoteTextColor
		
		/**
		 * Set note font
		 * 
		 * @param string $color
		 */
		function setNoteTextColor($color) {
			$this->attributes['note_text_color'] = $color;
		} // setNoteTextColor

    /**
     * Set print logo
     *
     * @return boolean
     */
    function getPrintFooter() {
      return array_var($this->attributes, 'print_footer', true);
    } // getPrintFooter

    /**
     * Set print logo
     *
     * @param boolean $print_footer
     */
    function setPrintFooter($print_footer) {
      $this->attributes['print_footer'] = $print_footer;
    } // setPrintFooter

		/**
		 * Get footer layout
		 * 
		 * @return integer
		 */
		function getFooterLayout() {
			return array_var($this->attributes, 'footer_layout', 0);
		} // getFooterLayout
		
		/**
		 * Set footer layout
		 * 
		 * @param integer $layout
		 */
		function setFooterLayout($layout) {
			$this->attributes['footer_layout'] = $layout;
		} // setFooterLayout
		
		/**
		 * Get footer font
		 * 
		 * @return string
		 */
		function getFooterFont() {
			return array_var($this->attributes, 'footer_font', InvoiceTemplate::DEFAULT_FONT);
		} // getFooterFont
		
		/**
		 * Set footer font
		 * 
		 * @param string $font_name
		 */
		function setFooterFont($font_name) {
			$this->attributes['footer_font'] = $font_name;
		} // setFooterFont
		
		/**
		 * Get footer font color
		 * 
		 * @return string
		 */
		function getFooterTextColor() {
			return array_var($this->attributes, 'footer_text_color', '#333333');
		} // getFooterTextColor
		
		/**
		 * Set footer font
		 * 
		 * @param string $font_name
		 */
		function setFooterTextColor($color) {
			$this->attributes['footer_text_color'] = $color;
		} // setFooterTextColor
		
		/**
		 * Set print footer border
		 * 
		 * @return boolean
		 */
		function getPrintFooterBorder() {
			return array_var($this->attributes, 'print_footer_border', true);
		} // getPrintFooterBorder
		
		/**
		 * Set print footer border
		 * 
		 * @param boolean $print_border
		 */
		function setPrintFooterBorder($print_border) {
			$this->attributes['print_footer_border'] = $print_border;
		} // setPrintFooterBorder
		
		/**
		 * Get footer border color
		 * 
		 * @return string
		 */
		function getFooterBorderColor() {
			return array_var($this->attributes, 'footer_border_color', '#cccccc');
		} // getFooterBorderColor
		
		/**
		 * Set footer border color
		 * 
		 * @param string $color
		 */
		function setFooterBorderColor($color) {
			$this->attributes['footer_border_color'] = $color;
		} // setFooterBorderColor

    /**
     * Display item order
     *
     * @return boolean
     */
    function getDisplayItemOrder() {
      return array_var($this->attributes, 'display_item_order', true);
    } // getDisplayItemOrder

    /**
     * Set display item order
     *
     * @param boolean $display
     */
    function setDisplayItemOrder($display) {
      $this->attributes['display_item_order']= $display;
    } // setDisplayItemOrder

    /**
     * Display quantity
     *
     * @return boolean
     */
    function getDisplayQuantity() {
      return array_var($this->attributes, 'display_quantity', true);
    } // getDisplayQuantity

    /**
     * Set display quantity
     *
     * @param boolean $display
     */
    function setDisplayQuantity($display) {
      $this->attributes['display_quantity'] = $display;
    } // setDisplayQuantity

    /**
     * Display unit cost
     *
     * @return boolean
     */
    function getDisplayUnitCost() {
      return array_var($this->attributes, 'display_unit_cost', true);
    } // getDisplayUnitCost

    /**
     * Set display unit cost
     *
     * @param boolean $display
     */
    function setDisplayUnitCost($display) {
      $this->attributes['display_unit_cost'] = $display;
    } // setDisplayUnitCost

    /**
     * Display subtotal
     *
     * @return boolean
     */
    function getDisplaySubtotal() {
      return array_var($this->attributes, 'display_subtotal', true);
    } // getDisplaySubtotal

    /**
     * Set display subtotal
     *
     * @param boolean $display
     */
    function setDisplaySubtotal($display) {
      $this->attributes['display_subtotal'] = $display;
    } // setDisplaySubtotal

    /**
     * Display tax rate
     *
     * @return boolean
     */
    function getDisplayTaxRate() {
      return array_var($this->attributes, 'display_tax_rate', true);
    } // getDisplayTaxRate

    /**
     * Set display tax rate
     *
     * @param boolean $display
     */
    function setDisplayTaxRate($display) {
      $this->attributes['display_tax_rate'] = $display;
    } // setDisplayTaxRate

    /**
     * Display tax amount
     *
     * @return boolean
     */
    function getDisplayTaxAmount() {
      return array_var($this->attributes, 'display_tax_amount', false);
    } // getDisplayTaxAmount

    /**
     * Set display tax amount
     *
     * @param boolean $display
     */
    function setDisplayTaxAmount($display) {
      $this->attributes['display_tax_amount'] = $display;
    } // setDisplayTaxAmount

    /**
     * Display total
     *
     * @return boolean
     */
    function getDisplayTotal() {
      return array_var($this->attributes, 'display_total', false);
    } // getDisplayTotal

    /**
     * Set display total
     *
     * @param boolean $display
     */
    function setDisplayTotal($display) {
      $this->attributes['display_total'] = $display;
    } // setDisplayTotal

    /**
     * Check if have to summarize tax
     *
     * @return boolean
     */
    function getSummarizeTax() {
      return array_var($this->attributes, 'summarize_tax', true);
    } // getSummarizeTax

    /**
     * Set Summarize tax
     *
     * @param boolean $summarize
     */
    function setSummarizeTax($summarize) {
      $this->attributes['summarize_tax'] = $summarize;
    } // setSummarizeTax

    /**
     * Check if we have to hide tax subtotal
     *
     * @return boolean
     */
    function getHideTaxSubtotal() {
      return array_var($this->attributes, 'hide_tax_subtotal', false);
    } // getHideTaxSubtotal

    /**
     * Set hide tax subtotal
     *
     * @param boolean $hide
     */
    function setHideTaxSubtotal($hide) {
      $this->attributes['hide_tax_subtotal'] = $hide;
    } // setHideTaxSubtotal

    /**
     * Check if we have to show amount paid and balance due
     *
     * @return boolean
     */
    function getShowAmountPaidBalanceDue() {
      return array_var($this->attributes, 'show_amount_paid_balance_due', false);
    } // getShowAmountPaidBalanceDue

    /**
     * Set show amount paid and balance due
     *
     * @param boolean $show
     */
    function setShowAmountPaidBalanceDue($show) {
      $this->attributes['show_amount_paid_balance_due'] = $show;
    } // setShowAmountPaidBalanceDue

    /**
     * Get Totals Columns Count
     */
    function getTotalColumnsCount() {
      return 7;
    } // getTotalColumnsCount

    /**
     * Get Columns Count
     *
     * @return int
     */
    function getColumnsCount() {
      return (int) $this->getDisplayItemOrder() + (int) $this->getDisplayQuantity() + (int) $this->getDisplayUnitCost() + (int) $this->getDisplaySubtotal() + ((int) $this->getDisplayTaxAmount() || (int) $this->getDisplayTaxRate()) + (int) $this->getDisplayTotal();
    } // getColumnsCount

		/**
		 * Save the configuration
		 * 
		 * @return boolean
		 */
		function save() {
			ConfigOptions::setValue('invoice_template', $this->attributes);
		} // save
		
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
			$result = $this->attributes;
			
			if ($this->hasBackgroundImage()) {
				$result['background_image'] = $this->getBackgroundImageUrl();
			} // if
			
			if ($this->hasLogoImage()) {
				$result['company_logo'] = $this->getLogoImageUrl();
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
      throw new NotImplementedError(__METHOD__);
    } // describeForApi
		
	}