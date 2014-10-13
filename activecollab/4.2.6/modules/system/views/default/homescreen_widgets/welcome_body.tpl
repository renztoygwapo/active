<div class="welcome_homescreen_widget">
  <div class="welcome_homescreen_widget_logo_wrapper">
    <div class="welcome_homescreen_widget_logo{if $logo_on_white} logo_on_white{/if}">
      <img src="{$logo_url}">
    </div>
  </div>
  
{if $welcome_message}
  <div class="welcome_homescreen_widget_welcome_message">{$welcome_message|clean|clickable|nl2br nofilter}</div>
{/if}
</div>