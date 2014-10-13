<?php

  /**
   * smarty_function_select_payment_gateway helper implementation
   * 
   * @package angie.frameworks.payments
   * @subpackage helpers
   */

  /**
   * Render select_payment_gateway helper
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_data_source($params, &$smarty) {
    $user = array_required_var($params, 'user', true, 'IUser');
    $active_data_source = array_var($params, 'active_data_source',null, true);
    
    $options = array();

    $sources = array();
    EventsManager::trigger('on_new_data_source',array(&$sources));
    if($sources) {
      foreach($sources as $source) {
        $options[get_class($source)] = array(
          'name' => $source->getDataSourceName(),
          'can_test_connection' => $source->canTestConnection(),
          'options' => $source->renderOptions($user),
        );
      } // foreach
    } // if

    $selected_data_source = $active_data_source;

    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('select_data_source');
    } // if
    
    if(empty($params['class'])) {
      $params['class'] = 'select_data_source';
    } else {
      $params['class'] .= ' select_data_source';
    } // if

    AngieApplication::useWidget('select_data_source', DATA_SOURCES_FRAMEWORK);
    
    return HTML::openTag('div', $params) . '</div><script type="text/javascript">$("#' . $params['id'] . '").selectDataSource(' . JSON::encode(array(
      'types' => $options, 'selected_data_source' => $selected_data_source
    )) . ')</script>';
  } // smarty_function_select_data_source