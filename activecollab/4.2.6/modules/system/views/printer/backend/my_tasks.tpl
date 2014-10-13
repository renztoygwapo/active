{title}My Tasks{/title}

<div id="print_container">
  {if $assignments}
    {foreach $assignments as $assignments_group}
      {if is_foreachable($assignments_group.assignments)}
        <h3>{$assignments_group.label}</h3>
        <table class="common" cellspacing="0">
          <thead>
          <tr>
            <th>{lang}Priority{/lang}</th>
            <th>{lang}Label{/lang}</th>
            <th>{lang}(Type) Name{/lang}</th>

            <!-- Additional Column 1 -->
            {if $filter->getAdditionalColumn1() == 'assignee'}
              <th class="additional_column_1">{lang}Assignee{/lang}</th>
            {else if $filter->getAdditionalColumn1() == 'project'}
              <th class="additional_column_1">{lang}Project{/lang}</th>
            {else if $filter->getAdditionalColumn1() == 'category'}
              <th class="additional_column_1">{lang}Category{/lang}</th>
            {else if $filter->getAdditionalColumn1() == 'milestone'}
              <th class="additional_column_1">{lang}Milestone{/lang}</th>
            {else if $filter->getAdditionalColumn1() == 'created_on'}
              <th class="additional_column_1">{lang}Created On{/lang}</th>
            {else if $filter->getAdditionalColumn1() == 'age'}
              <th class="additional_column_1">{lang}Age{/lang}</th>
            {else if $filter->getAdditionalColumn1() == 'created_by'}
              <th class="additional_column_1">{lang}Created By{/lang}</th>
            {else if $filter->getAdditionalColumn1() == 'due_on'}
              <th class="additional_column_1">{lang}Due On{/lang}</th>
            {else if $filter->getAdditionalColumn1() == 'completed_on'}
              <th class="additional_column_1">{lang}Completed On{/lang}</th>
            {else if $filter->getAdditionalColumn1() == 'estimated_time'}
              <th class="additional_column_1">{lang}Estimated Time{/lang}</th>
            {else if $filter->getAdditionalColumn1() == 'tracked_time'}
              <th class="additional_column_1">{lang}Tracked Time{/lang}</th>
            {/if}

            <!-- Additional Column 2 -->
            {if $filter->getAdditionalColumn2() == 'assignee'}
              <th class="additional_column_2">{lang}Assignee{/lang}</th>
            {else if $filter->getAdditionalColumn2() == 'project'}
              <th class="additional_column_2">{lang}Project{/lang}</th>
            {else if $filter->getAdditionalColumn2() == 'category'}
              <th class="additional_column_2">{lang}Category{/lang}</th>
            {else if $filter->getAdditionalColumn2() == 'milestone'}
              <th class="additional_column_2">{lang}Milestone{/lang}</th>
            {else if $filter->getAdditionalColumn2() == 'created_on'}
              <th class="additional_column_2">{lang}Created On{/lang}</th>
            {else if $filter->getAdditionalColumn2() == 'age'}
              <th class="additional_column_2">{lang}Age{/lang}</th>
            {else if $filter->getAdditionalColumn2() == 'created_by'}
              <th class="additional_column_2">{lang}Created By{/lang}</th>
            {else if $filter->getAdditionalColumn2() == 'due_on'}
              <th class="additional_column_2">{lang}Due On{/lang}</th>
            {else if $filter->getAdditionalColumn2() == 'completed_on'}
              <th class="additional_column_2">{lang}Completed On{/lang}</th>
            {else if $filter->getAdditionalColumn2() == 'estimated_time'}
              <th class="additional_column_2">{lang}Estimated Time{/lang}</th>
            {else if $filter->getAdditionalColumn2() == 'tracked_time'}
              <th class="additional_column_2">{lang}Tracked Time{/lang}</th>
            {/if}
          </tr>
          </thead>
          <tbody>
          {foreach $assignments_group.assignments as $assignment}
            {print_assignment_filter_row assignment=$assignment filter=$filter user=$logged_user}

            {if is_foreachable($assignment.subtasks)}
              {foreach $assignment.subtasks as $subtask}
                {print_assignment_filter_row assignment=$subtask filter=$filter user=$logged_user subtask=true}
              {/foreach}
            {/if}
          {/foreach}
          </tbody>
        </table>
      {/if}
    {/foreach}
  {else}
    <p>{lang}There are no tasks assigned to you{/lang}</p>
  {/if}
</div>