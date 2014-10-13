<?php

  /**
   * Select milestone start and due dates helper implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render select milestone start and due dates widget
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_milestone_dates($params, &$smarty) {
  	$interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    static $ids = array();
    
    $start_on = array_var($params, 'start_on');
    $due_on = array_var($params, 'due_on');
    
    if(isset($params['to_be_determined'])) {
      $to_be_determined = (boolean) $params['to_be_determined'];
    } // if
        
    if($start_on instanceof DateValue && $due_on instanceof DateValue) {
      if(!isset($to_be_determined)) {
        $to_be_determined = false;
      } // if
    } elseif($due_on instanceof DateValue) {
      if(!isset($to_be_determined)) {
        $to_be_determined = false;
      } // if
    } else {
      if(!isset($to_be_determined)) {
        $to_be_determined = true;
      } // if
    } // if
    
    $name = array_var($params, 'name');
    
    $id = array_var($params, 'id');
    if(empty($id)) {
      $counter = 1;
      do {
        $id = 'milestone_start_and_due_dates_' . $counter++;
      } while(in_array($id, $ids));
    } // if
    $ids[] = $id;
    
    $start_on_input_id = $id . '_start_on_input';
    $due_on_input_id = $id . '_due_on_input';
    
    if(isset($params['first_week_day'])) {
      $first_week_day = (integer) $params['first_week_day'];
    } elseif(Authentication::getLoggedUser() instanceof User) {
      $first_week_day = ConfigOptions::getValueFor('time_first_week_day', Authentication::getLoggedUser(), 0);
    } else {
      $first_week_day = ConfigOptions::getValue('time_first_week_day', 0);
    } // if
    
    $label = isset($params['label']) && $params['label'] ? $params['label'] : null;
    
    require_once ENVIRONMENT_FRAMEWORK_PATH . '/helpers/function.select_date.php';
    
    // Mobile interface
    if($interface == AngieApplication::INTERFACE_PHONE) {
    	$result = '<div id="' . $id . '" class="select_milestone_dates">';
    	
	    if($label) {
	      unset($params['label']);
	      $result .= HTML::label($label, $id, (isset($params['required']) && $params['required']), array('class' => 'main_label  ui-input-text'));
	    } // if
    	
    	$result .= '<div data-role="fieldcontain" class="select_milestone_dates_option">
  			<fieldset data-role="controlgroup" data-type="horizontal">'
  				. label_tag(lang('To Be Determined'), $id . '_to_be_determined', false, array('class' => 'inline'), ''). ' ' .
  				HTML::openTag('input', array(
  					'type' => 'radio',
  					'class' => 'inline',
  					'id' => $id . '_to_be_determined',
  				  'name' => $name . '[to_be_determined]',
  				  'value' => 1,
  					'checked' => $to_be_determined,
  					'data-theme' => 'i'
  				)).' '
  				. label_tag(lang('Set Now'), $id . '_set_now', false, array('class' => 'inline'), ''). ' ' .
  				HTML::openTag('input', array(
  					'type' => 'radio',
  					'class' => 'inline',
  					'id' => $id . '_set_now',
  				  'name' => $name . '[to_be_determined]',
  				  'value' => 0,
  					'checked' => !$to_be_determined,
  					'data-theme' => 'i'
  				)).'
  			</fieldset>
  			<div class="select_milestone_dates_set_range" style="display: ' . ($to_be_determined ? 'none' : 'block') . ';">
          <table style="width: 98%; margin-top: 1em;"><tr><td>' . smarty_function_select_date(array(
            'type' => 'date',
            'name' => $name . '[start_on]',
            'id' => $start_on_input_id,
        		'value' => $start_on,
  					'data-role' => 'datebox',
  					'readonly' => 'readonly',
  					'interface' => AngieApplication::INTERFACE_PHONE
        	), $smarty) . '</td><td style="padding-right: 10px; vertical-align: middle;">&mdash;</td><td>' . smarty_function_select_date(array(
            'type' => 'date',
            'name' => $name . '[due_on]',
            'id' => $due_on_input_id,
        		'value' => $due_on,
        		'data-role' => 'datebox',
  					'readonly' => 'readonly',
        		'interface' => AngieApplication::INTERFACE_PHONE
          ), $smarty) . '</td></tr>
          </table>
          <p class="details">' . lang('Tip: To make a single day milestone, set start and due dates to the same date') .'</p>
        </div>
  		</div>';
	          
	    $result .= '</div>';
    				
    	return $result . "<script type=\"text/javascript\">\nApp.widgets.MilestoneDatesWidget.init('" . trim($id) . "');\n</script>";
    	
    // Default interface
    } else {
	    $result = '<div id="' . $id . '" class="select_milestone_dates">
	      <div class="select_milestone_dates_option">
	        <div class="head">' . open_html_tag('input', array(
	          'type' => 'radio',
	          'class' => 'inline',
	          'id' => $id . '_to_be_determined', 
	          'name' => $name . '[to_be_determined]',
	          'value' => 1,
	          'checked' => $to_be_determined,
	        )) . ' ' . label_tag(lang('To Be Determined'), $id . '_to_be_determined', false, array('class' => 'inline'), '') . 
	        '</div>
	      </div>
	      <div class="select_milestone_dates_option">
	        <div class="head">' . open_html_tag('input', array(
	          'type' => 'radio',
	          'class' => 'inline',
	          'id' => $id . '_set_now',
	          'name' => $name . '[to_be_determined]',
	          'value' => 0,
	          'checked' => !$to_be_determined,
	        )) . ' ' . label_tag(lang('Set Now'), $id . '_set_now', false, array('class' => 'inline'), '') . 
	        '</div>
	        <div class="select_milestone_dates_set_range" style="display: ' . ($to_be_determined ? 'none' : 'block') . ';">
	          <table style="width: auto"><tr><td>' . smarty_function_select_date(array(
	            'type' => 'text',
	            'name' => $name . '[start_on]',
	            'id' => $start_on_input_id,
	        		'value' => $start_on,
              'skip_days_off' => true,
	        	), $smarty) . '</td><td style="padding: 0 3px">&mdash;</td><td>' . smarty_function_select_date(array(
	            'type' => 'text',
	            'name' => $name . '[due_on]',
	            'id' => $due_on_input_id,
	        		'value' => $due_on,
              'skip_days_off' => true,
	          ), $smarty) . '</td></tr>
	          </table>
	          <p class="details">' . lang('Tip: To make a single day milestone, set start and due dates to the same date') .'</p>
	        </div>
	      </div>
	    </div>';

      AngieApplication::useWidget('milestone_dates_widget', SYSTEM_MODULE);
	    return $result . "<script type=\"text/javascript\">\nApp.milestones.MilestoneDatesWidget.init('" . trim($id) . "');\n</script>";
    }
  } // smarty_function_select_milestone_dates