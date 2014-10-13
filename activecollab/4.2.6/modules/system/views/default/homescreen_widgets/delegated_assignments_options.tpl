{wrap field=delegated_assignments_user_id}
  {select_user name='homescreen_widget[user_id]' value=$widget_data.user_id user=$user label='User' optional=true optional_text='Person Accessing a Page'}
{/wrap}

{wrap field=delegated_assignments_status}
  {select name='homescreen_widget[status]' label='Show Assignments'}
    <option value="any" {if $widget_data == 'any'}selected="selected"{/if}>{lang}Open and Completed{/lang}</option>
    <option value="open" {if $widget_data == 'open'}selected="selected"{/if}>{lang}Open Only{/lang}</option>
    <option value="completed" {if $widget_data == 'completed'}selected="selected"{/if}>{lang}Completed Only{/lang}</option>
  {/select}
{/wrap}

{wrap field=my_discussions_num}
  {number_field name='homescreen_widget[num]' value=$widget_data.num label='Max Number of Assignments that will be Displayed'}
{/wrap}