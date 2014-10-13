<div id="empty_slate_send_quote" class="empty_slate">
  <h3>{lang}About Quote Sending{/lang}</h3>
  
  <ul class="icon_list">
    <li>
      <img src="{image_url name="empty-slates/mailing.png" module=$smarty.const.SYSTEM_MODULE}" class="icon_list_icon" alt="" />
      <span class="icon_list_title">{lang}Email will be Sent{/lang}</span>
      <span class="icon_list_description">
        {lang}Selected contact person will receive an e-mail with this quote attached in PDF format.{/lang}
        {if $active_quote->isDraft()}
          {lang}Also, a public page will be generated where client can post comments in case that they do not have activeCollab user account.{/lang}
        {/if}
      </span>
    </li>
  </ul>
</div>