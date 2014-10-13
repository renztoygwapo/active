{title}Reply to Comment{/title}
{add_bread_crumb}Reply to Comment{/add_bread_crumb}
{use_widget name='checklist_page' module=$smarty.const.ENVIRONMENT_FRAMEWORK}

<div id="reply_to_comment" class="checklist_page">
  <table class="checklist_page_sections" cellspacing="0" cellpadding="0">
  {foreach $sections as $section}
    <tr class="checklist_page_section {if $section.all_ok}all_ok{else}not_all_ok{/if}">
      <td class="section_content">
        <h2>{$section.title}</h2>
        <ul>
        {foreach $section.steps as $step}
          <li class="{if $step.is_ok}ok{else}nok{/if}">{$step.text}</li>
        {/foreach}
        </ul>
      </td>
      <td class="section_next_step">
      {if $section.next_step}
        {button href=$section.next_step.url mode=$section.next_step.mode success_event=$section.next_step.success_event flyout_width=$section.next_step.flyout_width title=$section.next_step.text error_event=$section.next_step.error_event}{$section.next_step.text}{/button}
      {/if}
      </td>
    </tr>
  {/foreach}
  </table>
  <p class="checklist_page_info">{lang}Use this checklist to configure activeCollab's "Reply to Comment" feature. This feature will not work unless all of the listed requirements are met!{/lang}</p>
</div>

<script type="text/javascript">
  App.Wireframe.Events.bind('from_address_updated.content incoming_mailbox_created.content incoming_mailbox_updated.content', function() {
    App.Wireframe.Content.reload();
  });
  
  App.Wireframe.Events.bind('incoming_mailbox_updated_error.content', function(response, response_message) {
   	App.Wireframe.Flash.error(response_message);
  });
</script>