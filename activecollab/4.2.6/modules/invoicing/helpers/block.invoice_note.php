<?php

  /**
   * invoice_note helper implementation
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage helpers
   */

  /**
   * Render invoice note field
   * 
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
   */
  function smarty_block_invoice_note($params, $content, &$smarty, &$repeat) {
    if($repeat) {
      return;
    } // if
    
    $id = isset($params['id']) && $params['id'] ? $params['id'] : HTML::uniqueId('invoice_note');
    
    $settings = array(
      'name' => array_required_var($params, 'name'),
      'value' => $content,
      'label' => isset($params['label']) && $params['label'] ? lang($params['label']) : null,
      'required' => isset($params['required']) && $params['required'],
      'select_default' => array_var($params, 'select_default', false),
    );

    $notes = InvoiceNoteTemplates::find(array(
      'order' => 'name ASC'
    ));

    $notes_map = array();
    
    if($notes) {
      foreach($notes as $note) {
        $notes_map[$note->getId()] = array(
          'name' => $note->getName(),
          'content' => $note->getContent(),
          'is_default'  => $note->getIsDefault()
        );
      } // foreach
    } // if

    // print out field, event though there are no predefined notes
    AngieApplication::useWidget('invoice_note', INVOICING_MODULE);
    return '<div id="' . $id . '"></div><script type="text/javascript">$("#' . $id . '").invoiceNote(' . JSON::encode($settings) . ', ' . JSON::map($notes_map) . ' );</script>';
  } // smarty_block_invoice_note