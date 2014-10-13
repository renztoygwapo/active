<div class="project_milestone">
{if is_foreachable($_upcoming_objects)}
    <div class="project_overview_box">
      <div class="project_overview_box_title">
        <h2>{$_headline}</h2>
      </div>
      <div class="project_overview_box_content"><div class="project_overview_box_content_inner">
        <table class="common" cellspacing="0">
          <thead>
            <tr>
              <th class="milestone">{lang}Milestone{/lang}</th>
              <th class="responsible">{lang}Responsible Person{/lang}</th>
              <th class="start">{lang}Start On{/lang}</th>
              <th class="due">{lang}Due On{/lang}</th>
            </tr>
          </thead>
          <tbody>
          {foreach from=$_upcoming_objects item=object}
            <tr class="{if $object->isLate()}late{elseif $object->isUpcoming()}upcoming{else}today{/if}">
              <td class="milestone"><a href="{$object->getViewUrl()}">{$object->getName()}</a></td>
              <td class="responsible">
                {if $object->assignees()->hasAssignee()}
                  <span class="details block">{user_link user=$object->assignees()->getAssignee()}</span>
                {else}
                  ---
                {/if}
              </td>
              <td class="due">{$object->getStartOn()|date:0}</td>
              <td class="due">{due_on object=$object}</td>
            </tr>
          {/foreach}
          </tbody>
        </table>
      </div></div>
    </div>
  {/if}  
</div>