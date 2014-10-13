{lang language=$language}Your Password has been Changed{/lang}
================================================================================
{notification_wrapper title='Password changed' context=$context recipient=$recipient sender=$sender}
  <p>{lang link_style=$style.link login_url=Router::assemble('homepage') language=$language}Your password has been changed. You can <a href=":login_url" style=":link_style">log in</a> with the following parameters{/lang}:</p>
  <table style="width: 100%; margin-top: 20px; {if $welcome_message}margin-bottom: 20px;{/if}">
    <tr>
      <td style="width: 80px; font-weight: bold">{lang language=$language}Email{/lang}:</td>
      <td>&quot;{$recipient->getEmail()}&quot; ({lang language=$language}without quotes{/lang})</td>
    </tr>
    <tr>
      <td style="width: 80px; font-weight: bold">{lang language=$language}Password{/lang}:</td>
      <td>&quot;{$password}&quot; ({lang language=$language}without quotes{/lang})</td>
    </tr>
  </table>
{/notification_wrapper}