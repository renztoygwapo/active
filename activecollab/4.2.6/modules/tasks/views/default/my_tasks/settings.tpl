{use_widget name=ui_multicomplete module=$smarty.const.ENVIRONMENT_FRAMEWORK}

<div id="my_tasks_settings"">
  {form action=Router::assemble('my_tasks_settings')}
    {wrap_fields}
      {wrap field=labels visible_overflow=true}
        {label}Labels{/label}

        <div class="label_filter_option">{radio_field name='settings[my_tasks_labels_filter]' value=$smarty.const.AssignmentFilter::LABEL_FILTER_ANY pre_selected_value=$settings_data.my_tasks_labels_filter label='Show Assignments with Any Label'}</div>

        <div class="label_filter_option">
          {radio_field name='settings[my_tasks_labels_filter]' value=$smarty.const.AssignmentFilter::LABEL_FILTER_SELECTED pre_selected_value=$settings_data.my_tasks_labels_filter label="Only Show Assignments with these Labels"}
          <div class="slide_down_settings" style="padding-top: 12px; padding-bottom: 12px; {if $settings_data.my_tasks_labels_filter != $smarty.const.AssignmentFilter::LABEL_FILTER_SELECTED}display: none{/if}">
            <div class="label_picker_wrapper" style="position: relative"><input type="text" name="settings[only_show_labels]" {if $settings_data.my_tasks_labels_filter == $smarty.const.AssignmentFilter::LABEL_FILTER_SELECTED}value="{$settings_data.my_tasks_labels_filter_data}" required{/if}></div>
            <p class="aid">{lang}Separate multiple labels with comma{/lang}</p>
          </div>
        </div>

        <div class="label_filter_option">
          {radio_field name='settings[my_tasks_labels_filter]' value=$smarty.const.AssignmentFilter::LABEL_FILTER_NOT_SELECTED pre_selected_value=$settings_data.my_tasks_labels_filter label='Ignore Assignments with these Labels'}
          <div class="slide_down_settings" style="padding-top: 12px; padding-bottom: 12px; {if $settings_data.my_tasks_labels_filter != $smarty.const.AssignmentFilter::LABEL_FILTER_NOT_SELECTED}display: none{/if}" >
            <div class="label_picker_wrapper" style="position: relative"><input type="text" name="settings[ignore_labels]" {if $settings_data.my_tasks_labels_filter == $smarty.const.AssignmentFilter::LABEL_FILTER_NOT_SELECTED}value="{$settings_data.my_tasks_labels_filter_data}" required{/if}></div>
            <p class="aid">{lang}Separate multiple labels with comma{/lang}</p>
          </div>
        </div>
      {/wrap}
    {/wrap_fields}

    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
  $('#my_tasks_settings').each(function() {
    var wrapper = $(this);

    wrapper.find('div.label_picker_wrapper').each(function() {
      var picker_wrapper = $(this);
      var picker = picker_wrapper.find('input[type=text]');

      picker.multicomplete({
        'source' : {$labels|json nofilter},
        'appendTo' : picker_wrapper,
        'position' : {
          'my' : 'left bottom',
          'at' : 'left bottom',
          'of' : picker
        }
      });
    });

    wrapper.on('click', 'input[type=radio]', function() {
      var input = $(this);
      var input_wrapper = input.parents('div.label_filter_option:first');

      var slide_down_settings = input_wrapper.find('div.slide_down_settings');

      if(slide_down_settings.length > 0 && slide_down_settings.is(':visible')) {
        return;
      } // if

      wrapper.find('div.label_filter_option div.slide_down_settings:visible').slideUp();
      wrapper.find('div.label_filter_option div.slide_down_settings input').prop('required', false);

      slide_down_settings.slideDown(function() {
        slide_down_settings.find('input[type=text]').prop('required', true).focus();
      });
    });
  });
</script>