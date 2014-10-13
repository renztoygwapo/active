{wrap field=my_discussions_num}
  {number_field name='homescreen_widget[num]' value=$widget_data.num label='Max Number of Discussions that will be Displayed'}
{/wrap}

{wrap field=my_discussions_extended}
  {label}Show Discussions{/label}
  <div>{radio_field name="homescreen_widget[extended]" value=true checked=$widget_data.extended label="That I Started or Commented in"}</div>
  <div>{radio_field name="homescreen_widget[extended]" value=false checked=!$widget_data.extended label="That I Started Only"}</div>
{/wrap}

{wrap field=my_discussions_extended}
  {yes_no name='homescreen_widget[include_completed_projects]' value=$widget_data.include_completed_projects label='Include Discussions from Completed Projects'}
{/wrap}