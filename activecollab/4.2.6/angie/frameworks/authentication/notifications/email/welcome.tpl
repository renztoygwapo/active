{lang language=$language}An Account has been Created for You{/lang}
================================================================================
{notification_wrapper title='Welcome' context=$context recipient=$recipient sender=$sender}
  <p>{lang link_style=$style.link name=$sender->getName() creator_url=$sender->getViewUrl() login_url=Router::assemble('homepage') language=$language}<a href=":creator_url" style=":link_style">:name</a> has created an account for you. You can <a href=":login_url" style=":link_style">log in</a> with the following parameters{/lang}:</p>
  <table style="width: 100%; margin-top: 20px; {if $welcome_message}margin-bottom: 20px;{/if}">
    <tr>
      <td style="width: 80px; font-weight: bold">{lang language=$language}Login Page{/lang}:</td>
      <td><a href="{assemble route=homepage}">{assemble route=homepage}</a></td>
    </tr>
    <tr>
      <td style="width: 80px; font-weight: bold">{lang language=$language}Email{/lang}:</td>
      <td>&quot;{$recipient->getEmail()}&quot; ({lang language=$language}without quotes{/lang})</td>
    </tr>
    <tr>
      <td style="width: 80px; font-weight: bold">{lang language=$language}Password{/lang}:</td>
      <td>&quot;{$password}&quot; ({lang language=$language}without quotes{/lang})</td>
    </tr>
  </table>
{if $welcome_message}
  <p>{lang language=$recipient->getLanguage()}Additionally, the following welcome message was provided{/lang}:</p>
  {notification_wrap_body}{$welcome_message|nl2br nofilter}{/notification_wrap_body}
{/if}
{/notification_wrapper}