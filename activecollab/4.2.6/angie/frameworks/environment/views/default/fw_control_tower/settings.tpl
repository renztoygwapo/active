<div id="control_tower_settings">
  {form action=Router::assemble('control_tower_settings')}
    <div class="content_stack_wrapper">
    {foreach $control_tower_settings as $group => $options}
      <div class="content_stack_element {if $options@last}last{/if}">
        <div class="content_stack_element_info">
          <h3>{$group}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=control_tower_group_settings}
            {foreach $options as $option_name => $option}
              <div>{checkbox_field name=$option_name checked=$option.value label=$option.label}</div>
            {/foreach}
          {/wrap}
        </div>
      </div>
    {/foreach}
    </div>

    {wrap_buttons}
      {submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
</div>