<div id="homescreen_widget_{$widget->getId()}" class="welcome_options_wrapper">
  {wrap field=welcome_message}
    {textarea_field name='homescreen_widget[welcome_message]' label='Welcome Message'}{$widget_data.welcome_message nofilter}{/textarea_field}
    <p class="aid">{lang}New lines will be preserved. HTML is not allowed{/lang}</p>
  {/wrap}

  {wrap field=logo_on_white}
    {yes_no name='homescreen_widget[logo_on_white]' value=$widget_data.logo_on_white label='Put the Logo on White Background'}
  {/wrap}
</div>