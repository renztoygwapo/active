<?php

  /**
   * History renderer implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class HistoryRenderer extends FwHistoryRenderer {

	  /**
	   * Return field renderers
	   *
	   * @return array
	   */
	  protected function getFieldRenderers() {
		  $result = parent::getFieldRenderers();

		  if ($this->object instanceof Project) {
			  $result['leader_id'] = function($old_value, $new_value) {
				  $new_leader = $new_value ? Users::findById($new_value) : null;
				  $old_leader = $old_value ? Users::findById($old_value) : null;

				  if($new_leader instanceof User && $old_leader instanceof User) {
					  return lang('Changed leader from <b>:old_leader</b> to <b>:new_leader</b>', array(
						  'old_leader' => $old_leader->getName(),
						  'new_leader' => $new_leader->getName(),
					  ));
				  } elseif($new_leader instanceof User) {
					  return lang('<b>:new_leader</b> is now leader of this Project', array(
						  'new_leader' => $new_leader->getName(),
					  ));
				  } elseif($old_leader instanceof User) {
					  return lang('<b>:old_leader</b> is no longer leader of this Project', array(
						  'old_leader' => $old_leader->getName(),
					  ));
				  } // if
			  };

			  $result['overview'] = function($old_value, $new_value) {
				  if($new_value) {
					  if ($old_value) {
						  return lang('Overview updated') . ' (<a href="'.Router::assemble('compare_text').'" class="text_diffs">' . lang('diff') . '<pre class="old" style="display: none;">'.$old_value.'</pre><pre class="new" style="display: none;">'.$new_value.'</pre></a>)';
					  } else {
						  return lang('Overview added');
					  } // if
				  } else {
					  if($old_value) {
						  return lang('Overview removed');
					  } // if
				  } // if
			  };

		  } elseif ($this->object instanceof Milestone) {
			  // @petar testirati ovo
			  $result['date_field_1'] = function($old_value, $new_value) {
				  $new_start_on = $new_value ? new DateValue($new_value) : null;
				  $old_start_on = $old_value ? new DateValue($old_value) : null;

				  if ($new_start_on instanceof DateValue) {
					  AngieApplication::useHelper('date', GLOBALIZATION_FRAMEWORK, 'modifier');

					  if ($old_start_on instanceof DateValue) {
						  return lang('Start date changed from <b>:old_value</b> to <b>:new_value</b>', array(
							  'old_value' => smarty_modifier_date($old_start_on, 0),
							  'new_value' => smarty_modifier_date($new_start_on, 0),
						  ));
					  } else {
						  return lang('Start date changed to <b>:new_value</b>', array(
							  'new_value' => smarty_modifier_date($new_start_on, 0),
						  ));
					  } // if
				  } else {
					  if($old_start_on instanceof DateValue || is_null($new_start_on)) {
						  return lang('Start date set to empty value');
					  } // if
				  } // if
			  };
			  $result['assignee_id'] = function($old_value, $new_value) {
				  $new_assignee = $new_value ? Users::findById($new_value) : null;
				  $old_assignee = $old_value ? Users::findById($old_value) : null;

				  if($new_assignee instanceof User && $old_assignee instanceof User) {
					  return lang('Reassigned from <b>:old_assignee</b> to <b>:new_assignee</b>', array(
						  'old_assignee' => $old_assignee->getName(),
						  'new_assignee' => $new_assignee->getName(),
					  ));
				  } elseif($new_assignee instanceof User) {
					  return lang('<b>:new_assignee</b> is responsible for this Milestone', array(
						  'new_assignee' => $new_assignee->getName(),
					  ));
				  } elseif($old_assignee instanceof User) {
					  return lang('<b>:old_assignee</b> is no longer responsible for this Milestone', array(
						  'old_assignee' => $old_assignee->getName(),
					  ));
				  } // if
			  };

		  } elseif ($this->object instanceof Task) {
			  $result['assignee_id'] = function($old_value, $new_value) {
				  $new_assignee = $new_value ? Users::findById($new_value) : null;
				  $old_assignee = $old_value ? Users::findById($old_value) : null;

				  if($new_assignee instanceof User && $old_assignee instanceof User) {
					  return lang('Reassigned from <b>:old_assignee</b> to <b>:new_assignee</b>', array(
						  'old_assignee' => $old_assignee->getName(),
						  'new_assignee' => $new_assignee->getName(),
					  ));
				  } elseif($new_assignee instanceof User) {
					  return lang('<b>:new_assignee</b> is responsible for this Task', array(
						  'new_assignee' => $new_assignee->getName(),
					  ));
				  } elseif($old_assignee instanceof User) {
					  return lang('<b>:old_assignee</b> is no longer responsible for this Task', array(
						  'old_assignee' => $old_assignee->getName(),
					  ));
				  } // if
			  };

        $result['integer_field_1'] = function($old_value, $new_value) {
          if($new_value) {
            if($old_value) {
              return lang('Task ID set from #:old_value to #:new_value', array('old_value' => $old_value, 'new_value' => $new_value));
            } else {
              return lang('Task ID set to #:new_value', array('new_value' => $new_value));
            } // if
          } else {
            if($old_value) {
              return lang('Task ID set to empty value');
            } // if
          } // if
        };

		  } elseif ($this->object instanceof Company) {
			  $result['name'] = function($old_value, $new_value) {
				  if($new_value) {
					  if($old_value) {
						  return lang('Company Name changed from <b>:old_value</b> to <b>:new_value</b>', array('old_value' => $old_value, 'new_value' => $new_value));
					  } else {
						  return lang('Company Name set to <b>:new_value</b>', array('new_value' => $new_value));
					  } // if
				  } else {
					  if($old_value) {
						  return lang('Company Name set to empty value');
					  } // if
				  } // if
			  };
			  $result['note'] = function($old_value, $new_value) {
				  if($new_value) {
					  if ($old_value) {
						  return lang('Note updated') . ' (<a href="'.Router::assemble('compare_text').'" class="text_diffs">' . lang('diff') . '<pre class="old" style="display: none;">'.$old_value.'</pre><pre class="new" style="display: none;">'.$new_value.'</pre></a>)';
					  } else {
						  return lang('Note added');
					  } // if
				  } else {
					  if($old_value) {
						  return lang('Note removed');
					  } // if
				  } // if
			  };
		  } // if

		  $result['due_on'] = function($old_value, $new_value) {
			  $new_due_on = $new_value ? new DateValue($new_value) : null;
			  $old_due_on = $old_value ? new DateValue($old_value) : null;

			  if($new_due_on instanceof DateValue) {
				  AngieApplication::useHelper('date', GLOBALIZATION_FRAMEWORK, 'modifier');

				  if($old_due_on instanceof DateValue) {
					  return lang('Due date changed from <b>:old_value</b> to <b>:new_value</b>', array(
						  'old_value' => smarty_modifier_date($old_due_on, 0),
						  'new_value' => smarty_modifier_date($new_due_on, 0),
					  ));
				  } else {
					  return lang('Due date changed to <b>:new_value</b>', array(
						  'new_value' => smarty_modifier_date($new_due_on, 0),
					  ));
				  } // if
			  } else {
				  if($old_due_on instanceof DateValue || is_null($new_due_on)) {
					  return lang('Due date set to empty value');
				  } // if
			  } // if
		  };

		  $result['currency_id'] = function($old_value, $new_value) {
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

		  $result['budget'] = function($old_value, $new_value) {
			  if($new_value) {
				  if($old_value) {
					  return lang('Budget changed from <b>:old_value</b> to <b>:new_value</b>', array('old_value' => moneyval($old_value), 'new_value' => moneyval($new_value)));
				  } else {
					  return lang('Budget set to <b>:new_value</b>', array('new_value' => moneyval($new_value)));
				  } // if
			  } else {
				  if($old_value) {
					  return lang('Budget set to empty value');
				  } // if
			  } // if
		  };

		  $result['project_id'] = function($old_value, $new_value) {
			  $new_project = Projects::findById($new_value);
			  $old_project = Projects::findById($old_value);

			  if ($new_project instanceof Project) {
				  if ($old_project instanceof Project) {
					  return lang('Project changed from <b>:old_value</b> to <b>:new_value</b>', array(
						  'old_value' => $old_project->getName(),
						  'new_value' => $new_project->getName()
					  ));
				  } else {
					  return lang('Project set to <b>:new_value</b>', array(
						  'new_value' => $new_project->getName()
					  ));
				  } // if
			  } else {
				  if($old_project instanceof Project || is_null($new_project)) {
					  return lang('Project set to empty value');
				  } // if
			  } // if
		  };

		  $result['milestone_id'] = function($old_value, $new_value) {
			  $new_milestone = Milestones::findById($new_value);
			  $old_milestone = Milestones::findById($old_value);

			  if ($new_milestone instanceof Milestone) {
				  if ($old_milestone instanceof Milestone) {
					  return lang('Milestone changed from <b>:old_value</b> to <b>:new_value</b>', array(
						  'old_value' => $old_milestone->getName(),
						  'new_value' => $new_milestone->getName()
					  ));
				  } else {
					  return lang('Milestone set to <b>:new_value</b>', array(
						  'new_value' => $new_milestone->getName()
					  ));
				  } // if
			  } else {
				  if($old_milestone instanceof Milestone || is_null($new_milestone)) {
					  return lang('Milestone set to empty value');
				  } // if
			  } // if
		  };

		  return $result;
	  } // getFieldRenderers

  }