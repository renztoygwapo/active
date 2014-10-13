{title company_name=$active_company->getName()}:company_name Project Requests{/title}
{add_bread_crumb}Project Requests{/add_bread_crumb}

<div id="company_project_requests">
  {if is_foreachable($project_requests)}
    <table class="active_projects common" cellspacing="0">
      <tr>
        <th class="name">{lang}Project Request Name{/lang}</th>
        <th class="created_on">{lang}Created On{/lang}</th>
        <th class="last_reply">{lang}Last Comment{/lang}</th>
      </tr>
      {foreach from=$project_requests item=project_request}
      <tr>
        <td class="name">
          {if ProjectRequests::canManage($logged_user)}
            {object_link object=$project_request quick_view=true}
          {else}
            {$project_request->getName()|clean}
          {/if}
        </td>
        <td class="created_on">{$project_request->getCreatedOn()|ago nofilter}</td>
        <td class="last_reply">{if ($project_request->getLastCommentOn() instanceof DateValue)}{$project_request->getLastCommentOn()|ago nofilter}{else}---{/if}</td>
      </tr>
      {/foreach}
    </table>
  {else}
    <p class="empty_page"><span class="inner">{lang}There are no project requests for this company{/lang}</span></p>
  {/if}
</div>

<script type="text/javascript">
  $('#company_project_requests').each(function() {
    var wrapper = $(this);
    var inline_tabs = wrapper.parents('.inline_tabs:first');
    var no_projects;

    if (inline_tabs.length) {
      var tabs_id = inline_tabs.attr('id');
      var projects_tab = inline_tabs.find('div.inline_tabs_links ul li a.selected');

      // refresh tabs on project update
      App.Wireframe.Events.bind('project_request_created.inline_tab project_request_updated.inline_tab project_request_deleted.inline_tab', function (event, invoice) {
        App.widgets.InlineTabs.refresh(tabs_id);
      });
    } // if
  });
</script>