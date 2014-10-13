<div id="search_settings">
  {form action=Router::assemble('search_settings')}
    <div class="content_stack_wrapper">
      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Search Engine{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
        {foreach $available_providers as $provider}
          <div class="search_settings_provider">
            {radio_field name='search_settings[search_provider]' value=get_class($provider) pre_selected_value=$search_settings_data.search_provider label=$provider->getName()}
            <div class="slide_down_settings" id="{$provider|get_class}_settings">
              <p class="details">{$provider->getDescription()}</p>

              {if $provider->getRenderSettingsTemplate()}
                {include file=$provider->getRenderSettingsTemplate()}
              {/if}
            </div>
          </div>
        {/foreach}
        </div>
      </div>

      <div class="content_stack_element last">
        <div class="content_stack_element_info">
          <h3>{lang}Indexes{/lang}</h3>
        </div>
        <div class="content_stack_element_body">

        </div>
      </div>
    </div>

    {wrap_buttons}
      {submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
</div>