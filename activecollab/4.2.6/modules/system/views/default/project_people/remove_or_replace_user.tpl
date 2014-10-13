{title}Remove or Replace User{/title}

<div id="remove_or_replace_user">
  {form action=$initial_form_action}
    {wrap_fields}
      {wrap field=operation}
        {if $user_can_be_removed}
        <div>
          {radio_field name='remove_or_replace[operation]' value='remove' pre_selected_value=$remove_or_replace_data.operation label='Remove User from the Project' id="remove_user_operation_remove"}

          <div id="remove_user_remove_options" class="slide_down_settings" {if $remove_or_replace_data.operation != 'remove'}style="display: none;"{/if}>
            <p>{lang name=$active_user->getDisplayName(true)}<b>Warning</b>: This operation will set all :name's assigments to unassigned!{/lang}</p>
          </div>
        </div>
        {/if}

        <div>
        {if $user_can_be_removed}
          {radio_field name='remove_or_replace[operation]' value='replace' pre_selected_value=$remove_or_replace_data.operation label='Replace with Another User' id="remove_user_operation_replace"}
        {else}
          <input name="remove_or_replace[operation]" type="hidden" value="replace">
        {/if}

        {if $user_can_be_removed}
          <div id="remove_user_replace_options" class="slide_down_settings" {if $remove_or_replace_data.operation == 'remove'}style="display: none;"{/if}>
        {/if}
            {wrap field=replace_with_id}
              {select_user name='remove_or_replace[replace_with_id]' value=$replace_data.replace_with_id user=$logged_user exclude_ids=$active_user->getId() label="Replace With"}
            {/wrap}

            {wrap field=send_notification}
              {checkbox_field name="remove_or_replace[send_notification]" value="1" checked=$replace_data.send_notification label="Notify Users About this Change"}
            {/wrap}
        {if $user_can_be_removed}
          </div>
        {/if}
        </div>
      {/wrap}

      <div class="empty_slate">
        <h3>{lang}Open Assignments{/lang}</h3>
      {if $open_responsibilities == 1}
        <p>{lang name=$active_user->getDisplayName(true)}:name is responsible for <u>one open assignment</u> in this project{/lang}.</p>
        {else if $open_responsibilities > 1}
        <p>{lang name=$active_user->getDisplayName(true) num=$open_responsibilities}:name is responsible for <u>:num open assignments</u> in this project{/lang}.</p>
        {else}
        <p>{lang name=$active_user->getDisplayName(true)}:name is not responsible for any open assignment in this project{/lang}.</p>
      {/if}
      </div>
    {/wrap_fields}

    {wrap_buttons}
      {if $remove_or_replace_data.operation == 'remove'}
        {submit}Remove User{/submit}
      {else}
        {submit}Replace User{/submit}
      {/if}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
  $('#remove_or_replace_user').each(function() {
    var wrapper = $(this);

    var remove_user_url = {$active_project->getRemoveUserUrl($active_user)|json nofilter};
    var replace_user_url = {$active_project->getReplaceUserUrl($active_user)|json nofilter};

    var form = wrapper.find('form');
    var submit_button = form.find('button[type=submit]');

    var remove_options = form.find('#remove_user_remove_options');
    var replace_options = form.find('#remove_user_replace_options');

    wrapper.find('#remove_user_operation_remove').click(function() {
      replace_options.slideUp(function() {
        remove_options.slideDown();
      });

      form.attr('action', remove_user_url);
      submit_button.text(App.lang('Remove User'));
    });

    wrapper.find('#remove_user_operation_replace').click(function() {
      remove_options.slideUp(function() {
        replace_options.slideDown();
      });

      form.attr('action', replace_user_url);
      submit_button.text(App.lang('Replace User'));
    });
  });
</script>