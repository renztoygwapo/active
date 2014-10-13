<div class="project_list">
{if is_foreachable($_projects)}
    <div class="project_overview_box">
      <div class="project_overview_box_title">
        <h2>{lang}Projects{/lang}</h2>
      </div>
      <div class="project_overview_box_content"><div class="project_overview_box_content_inner">
        <table class="common" cellspacing="0">
          <thead>
            <tr>
              <th class="project">{lang}Project{/lang}</th>
              <th class="label">{lang}Label{/lang}</th>
              {if $_user}
              	<th class="role">{lang}Project role{/lang}</th>
              {/if}
            </tr>
          </thead>
          <tbody>
          {foreach from=$_projects item=object}
            <tr>
              <td class="name">{$object->getName()}</td>
              <td class="label_name">
              	{if $object->label()->get() instanceof Label}
                	{$object->label()->get()->getName()}
                {/if}
              </td>
              {if $_user}
              <td class="role_name">
              	{$_user->projects()->getRoleName($object)}
              </td>
              {/if}
            </tr>
          {/foreach}
          </tbody>
        </table>
      </div></div>
    </div>
  {/if}  
</div>