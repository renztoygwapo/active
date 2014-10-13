{title}Company Projects{/title}
{add_bread_crumb}Company Projects{/add_bread_crumb}

<div id="company_projects">
{if $projects}
  <table class="active_projects common" cellspacing="0">
    <tr>
      <th class="icon"></th>
      <th class="name">{lang}Project{/lang}</th>
      {if $is_archive}
      <th>{lang}Completed on{/lang}</th>
      {/if}
      <th class="label">{lang}Label{/lang}</th>
    </tr>
    {foreach from=$projects item=project}
      <tr {if $project->complete()->isCompleted()}class="completed"{/if}>
        <td class="icon"><img src="{$project->avatar()->getUrl(IProjectAvatarImplementation::SIZE_SMALL)}" alt="" /></td>
        <td class="name quick_view_item">{project_link project=$project}</td>
        {if $is_archive}
          <td class="completed_on">{$project->getCompletedOn()|date}</td>
        {/if}
        <td class="label">{object_label object=$project}</td>
      </tr>
    {/foreach}
  </table>
{else}
  <p class="empty_page"><span class="inner">{lang}There are no projects{/lang}</span></p>
{/if}
  <p class="projects_status_toggle"><a id="projects_url_toggle" href="{$projects_toggle_url}">{$projects_toggle_text}</a></p>
</div>

<script type="text/javascript">
  $('#company_projects').each(function() {
    var wrapper = $(this);
    var inline_tabs = wrapper.parents('.inline_tabs:first');

    if (inline_tabs.length) {
      var tabs_id = inline_tabs.attr('id');
      var projects_tab = inline_tabs.find('div.inline_tabs_links ul li a.selected');
      var projects_toggle_link = $('#projects_url_toggle');
      var projects_page = projects_toggle_link.attr('href');

      // toggle archive/active projects
      projects_toggle_link.click(function () {
        projects_tab.attr('href', projects_page).click();
        return false;
      });

      // refresh tabs on project update
      App.Wireframe.Events.bind('project_created.inline_tab project_updated.inline_tab project_deleted.inline_tab', function (event, invoice) {
        App.widgets.InlineTabs.refresh(tabs_id);
      });
    } // if
  });
</script>