{title}Milestone Archive{/title}
{add_bread_crumb}Archive{/add_bread_crumb}

<div id="milestones_archive">
{if is_foreachable($milestones)}
  <table class="common" cellspacing="0">
    <tr>
      <th class="star"></th>
      <th class="name">{lang}Milestone{/lang}</th>
      <th class="date">{lang}From / To{/lang}</th>
      <th class="status">{lang}Completed{/lang}</th>
    </tr>
  {foreach $milestones as $milestone}
    <tr class="completed {cycle values='odd,even'}">
      <td class="star">{favorite_object object=$milestone user=$logged_user}</td>
      <td class="name">{object_priority object=$milestone user=$logged_user} <a href="{$milestone->getViewUrl()}" class="quick_view_item">{$milestone->getName()}</a></td>
      <td class="date">
        {if $milestone->isToBeDetermined()}
          {lang}No Due Date Set{/lang}
        {elseif $milestone->isDayMilestone()}
          {$milestone->getDueOn()|date:0}
        {else}
          {$milestone->getStartOn()|date:0} &mdash; {$milestone->getDueOn()|date:0}
        {/if}
      </td>
      <td class="status">{$milestone->getCompletedOn()|date:0}, {user_link user=$milestone->complete()->getCompletedBy()}</td>
    </tr>
  {/foreach}
  </table>
{else}
  <p class="empty_page">{lang}There are no archived milestones in this project{/lang}</p>
{/if}
</div>