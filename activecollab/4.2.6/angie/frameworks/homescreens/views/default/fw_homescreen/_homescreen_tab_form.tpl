{wrap field=name}
  {text_field name="homescreen_tab[name]" value=$homescreen_tab_data.name required=true label='Name'}
{/wrap}

{if !($active_homescreen_tab instanceof HomescreenTab) || $active_homescreen_tab->isNew()}
  {wrap field=homescreen_tab_type}
    {select_homescreen_tab_type name="homescreen_tab[type]" value=$homescreen_tab_data.type user=$logged_user required=true label='Type'}
  {/wrap}
{/if}