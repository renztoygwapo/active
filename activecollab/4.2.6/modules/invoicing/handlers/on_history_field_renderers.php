<?php

	/**
	 * Handle on_history_field_renderers event
	 *
	 * @package activeCollab.modules.invoicing
	 * @subpackage handlers
	 */

	/**
	 * Get history changes as log text
	 *
	 * @param $object
	 * @param $renderers
	 */
	function invoicing_handle_on_history_field_renderers(&$object, &$renderers) {
		if ($object instanceof Invoice) {
			$renderers['currency_id'] = function($old_value, $new_value) {
				$new_currency = Currencies::findById($new_value);
				$old_currency = Currencies::findById($old_value);

				if ($new_currency instanceof Currency) {
					if ($old_currency instanceof Currency) {
						return lang('Currency changed from <b>:old_value</b> to <b>:new_value</b>', array('old_value' => $old_currency->getCode(), 'new_value' => $new_currency->getCode()));
					} else {
						return lang('Currency set to <b>:new_value</b>', array('new_value' => $new_currency->getCode()));
					} // if
				} else {
					if($old_currency instanceof Currency || is_null($new_currency)) {
						return lang('Currency set to empty value');
					} // if
				} // if
			};

			$renderers['status'] = function($old_value, $new_value) {
				$old_value = is_null($old_value) ? 0 : $old_value;

				$verbose_status = array(
					INVOICE_STATUS_PAID     => lang('Paid'),
					INVOICE_STATUS_ISSUED   => lang('Issued'),
					INVOICE_STATUS_DRAFT    => lang('Draft'),
					INVOICE_STATUS_CANCELED => lang('Canceled')
				);

				return lang('Status changed from <b>:old_value</b> to <b>:new_value</b>', array('old_value' => $verbose_status[$old_value], 'new_value' => $verbose_status[$new_value]));
			};

			$renderers['number'] = function($old_value, $new_value) {
				if($new_value) {
					if($old_value) {
						return lang('Invoice Number changed from <b>:old_value</b> to <b>:new_value</b>', array('old_value' => $old_value, 'new_value' => $new_value));
					} else {
						return lang('Invoice Number set to <b>:new_value</b>', array('new_value' => $new_value));
					} // if
				} else {
					if($old_value) {
						return lang('Invoice Number set to empty value');
					} // if
				} // if
			};

			$renderers['note'] = function($old_value, $new_value) {
				if($new_value) {
					if ($old_value) {
						return lang('Note updated :diff', array('diff' => '(diff)'));
					} else {
						return lang('Note added');
					} // if
				} else {
					if($old_value) {
						return lang('Note removed');
					} // if
				} // if
			};

			$renderers['private_note'] = function($old_value, $new_value) {
				if($new_value) {
					if ($old_value) {
						return lang('Private Note updated :diff', array('diff' => '(diff)'));
					} else {
						return lang('Private Note added');
					} // if
				} else {
					if($old_value) {
						return lang('Private Note removed');
					} // if
				} // if
			};

			$renderers['company_address'] = function($old_value, $new_value) {
				if($new_value) {
					if($old_value) {
						return lang('Company Address changed from <b>:old_value</b> to <b>:new_value</b>', array('old_value' => $old_value, 'new_value' => $new_value));
					} else {
						return lang('Company Address set to <b>:new_value</b>', array('new_value' => $new_value));
					} // if
				} else {
					if($old_value) {
						return lang('Company Address set to empty value');
					} // if
				} // if
			};
		} // if
	}