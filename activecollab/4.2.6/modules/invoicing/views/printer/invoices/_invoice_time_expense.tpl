<h2>{lang}Time and Expenses{/lang}</h2>
{if is_foreachable($time_records) || is_foreachable($expenses)}
  <div id="timerecords">
    <table class="common" cellspacing="0">
      <thead>
        <tr>
        	<th class="type">{lang}Item{/lang}</th>
          <th class="date">{lang}Date{/lang}</th>
          <th class="user">{lang}User{/lang}</th>
          <th class="hours">{lang}Value{/lang}</th>
          <th class="description">{lang}Description{/lang}</th>
        </tr>
      </thead>
      <tbody>
    {foreach from=$time_records item=time_record}
      <tr>
      	<td class="type">{$time_record->getVerboseType()}</td>
        <td class="date">{$time_record->getRecordDate()|date:0}</td>
        <td class="user">{user_link user=$time_record->getUser()}</td>
        <td class="hours">{$time_record->getName(true)}</td>
        <td class="description">
        {if $time_record->getParent() instanceof ProjectObject}
          {object_link object=$time_record->getParent()} 
          {if $time_record->getSummary()}
            &mdash; {$time_record->getSummary()}
          {/if}
        {else}
          {$time_record->getSummary()}
        {/if}
        </td>
        
      </tr>
    {/foreach}
    {foreach from=$expenses item=expense}
      <tr>
      	<td class="type">{$expense->getVerboseType()}</td>
        <td class="date">{$expense->getRecordDate()|date:0}</td>
        <td class="user">{user_link user=$expense->getUser()}</td>
        <td class="hours">{$expense->getName(true, true)}</td>
        <td class="description">
        {if $expense->getParent() instanceof ProjectObject}
          {object_link object=$expense->getParent()} 
          {if $expense->getSummary()}
            &mdash; {$expense->getSummary()}
          {/if}
        {else}
          {$expense->getSummary()}
        {/if}
        </td>
      </tr>
    {/foreach}
      </tbody>
    </table>
  </div>
{else}
  <p class="empty_page"><span class="inner">{lang}There is no time attached to this invoice{/lang}</span></p>
{/if}