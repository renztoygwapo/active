{title}Performance Checklist{/title}
{add_bread_crumb}Performance Checklist{/add_bread_crumb}
{use_widget name='checklist_page' module=$smarty.const.ENVIRONMENT_FRAMEWORK}

<div id="performance_checklist" class="checklist_page">
  <table class="checklist_page_sections" cellspacing="0" cellpadding="0">
  {foreach $sections as $section}
    <tr class="checklist_page_section {if $section.all_ok}all_ok{else}not_all_ok{/if}">
      <td class="section_content">
        <h2>{$section.title}</h2>
        <ul>
        {foreach $section.steps as $step}
          <li class="{if $step.is_ok}ok{else}nok{/if}">{$step.text}{if isset($step.description) && $step.description}<span class="description">{$step.description nofilter}</span>{/if}</li>
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
  <p class="checklist_page_info">{lang}Use this checklist to set up your platform for optimal performance{/lang}</p>
</div>

<script type="text/javascript">
  App.Wireframe.Events.bind('old_application_versions_removed.content', function() {
    App.Wireframe.Content.reload();
  });
</script>