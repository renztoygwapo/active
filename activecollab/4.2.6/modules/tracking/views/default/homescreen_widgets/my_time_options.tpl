<div id="homescreen_widget_{$widget->getId()}" class="my_time_options_wrapper">
  {wrap field=my_time_caption}
    {text_field name='homescreen_widget[caption]' value=$widget_data.caption label='Caption'}
  {/wrap}

  {wrap field=my_time_user class='my_time_options_user_filter'}
    {select name='homescreen_widget[user_filter]' label='Tracked By' class="picker"}
      <option value="logged_user" {if $widget_data.user_filter == 'logged_user'}selected="selected"{/if}>{lang}Logged User{/lang}</option>
      <option value="selected" {if $widget_data.user_filter == 'selected'}selected="selected"{/if}>{lang}Selected User{/lang}</option>
    {/select} {select_user name='homescreen_widget[selected_user_id]' value=$widget_data.selected_user_id user=$user}
  {/wrap}
</div>

<script type="text/javascript">
  $('#homescreen_widget_{$widget->getId()}').each(function() {
    var wrapper = $(this);

    if(wrapper.find('div.my_time_options_user_filter select.picker').val() != 'selected') {
      wrapper.find('div.my_time_options_user_filter select.select_user').hide();
    } // if

    wrapper.find('div.my_time_options_user_filter select.picker').change(function() {
      if($(this).val() == 'selected') {
        wrapper.find('div.my_time_options_user_filter select.select_user').show();
      } else {
        wrapper.find('div.my_time_options_user_filter select.select_user').hide();
      } // if
    });
  });
</script>