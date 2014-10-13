{use_widget name="quick_search" module="environment"}

{form action=$project_object_repository->getDoChangeBranchUrl($branch) method=get ask_on_leave=yes autofocus=yes}
<div id="change_branch_wrapper" class="fields_wrapper">
  {wrap field=text}
    <input type="text" id="filter_source_branches" placeholder="{lang}Filter Branches{/lang}"/>
  {/wrap}
  <table class="common" id="change_branch_table">
    <thead>
      <tr>
        <th>{lang}Repository Branches{/lang}</th>
      </tr>
    </thead>
    <tbody>
      {foreach $all_branches as $branch}

        <tr class="branch_row" _search_index=" {strtolower($branch)} ">
          <td class="branch_name">
            {radio_field name=branch value=$branch label=$branch checked=($active_branch == $branch) change_branche_url=$project_object_repository->getDoChangeBranchUrl($branch)}
          </td>
        </tr>
      {/foreach}
    </tbody>
  </table>
</div>
  {wrap_buttons}
    {submit}Change Branch{/submit}
  {/wrap_buttons}
{/form}


<script type="text/javascript">
  var branch_table = $('#change_branch_table');
  var filter_source_branches = $("#filter_source_branches");

  filter_source_branches.quickSearch({
    'target' : branch_table,
    'rows' : 'tr.branch_row'
  });

  $(document).ready (function () {
    filter_source_branches.focus();
  });

  App.Wireframe.Events.bind('source_branch_changed', function (event, object) {
    App.Wireframe.Content.reload();
  });

</script>