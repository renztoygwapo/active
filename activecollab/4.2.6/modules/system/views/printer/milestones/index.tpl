{title}{$page_title}{/title}

{if is_foreachable($milestones)}


    <table class="milestones_table common" cellspacing="0">
      <thead>
      <tr>
        <th class="milestone_id" align="left">{lang}Id{/lang}</th>
        <th class="complete_id" align="left">{lang}Status{/lang}</th>
        <th class="name">{lang}Name{/lang}</th>
        <th class="start_on">{lang}Starts On{/lang}</th>
        <th class="due_on">{lang}Due On{/lang}</th>
      </tr>
      </thead>
      <tbody>
      {foreach from=$milestones key=object_list item=object}
        <tr>
          <td class="milestone_id" align="left">#{$object->getId()}</td>
          <td class="complete_id" align="left">
            {if $object->complete()->isCompleted()}
              {lang}Completed{/lang}
            {else}
              {lang}Active{/lang}
            {/if}
          </td>
          <td class="name">{$object->getName()}</td>
          <td class="start_on">{if $object->getStartOn()}{$object->getStartOn()|date:0}{/if}</td>
          <td class="due_on">{if $object->getDueOn()}{$object->getDueOn()|date:0}{/if}</td>
        </tr>
      {/foreach}
      </tbody>
    </table>
{else}
  <p>{lang}There are no Milestones that match this criteria{/lang}</p>
{/if}