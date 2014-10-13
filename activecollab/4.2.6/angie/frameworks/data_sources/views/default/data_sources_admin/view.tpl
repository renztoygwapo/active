{title}{$active_data_source->getName()}{/title}
{add_bread_crumb}View{/add_bread_crumb}
{use_widget name=properties_list module=$smarty.const.ENVIRONMENT_FRAMEWORK}

<div id="data_sources_view">
  <dl class="properties_list">
    <dt>{lang}Name{/lang}</dt>
    <dd>{$active_data_source->getName()}</dd>

    <dt>{lang}Type{/lang}</dt>
    <dd>{$active_data_source->getDataSourceName()}</dd>

    {if $active_data_source->getAdditionalProperty('account_id')}
      <dt>{lang}Account ID{/lang}</dt>
      <dd>{$active_data_source->getAccountId()}</dd>
    {/if}
    {if $active_data_source->getAdditionalProperty('username')}
      <dt>{lang}Username{/lang}</dt>
      <dd>{$active_data_source->getUsername()}</dd>
    {/if}
    {if $active_data_source->getAdditionalProperty('password')}
      <dt>{lang}Password{/lang}</dt>
      <dd>{$active_data_source->getPassword()}</dd>
    {/if}
  </dl>

  <div class="body">&nbsp;</div>
  
</div>
 