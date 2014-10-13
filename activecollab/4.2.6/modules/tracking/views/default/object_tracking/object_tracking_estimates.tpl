<div id="object_estimates">
{if $estimates}
  <table class="common" cellspacing="0">
    <thead>
      <tr>
        <th class="estimate">{lang}Estimate{/lang}</th>
        <th class="on_by">{lang}On / By{/lang}</th>
        <th class="comment">{lang}Comment{/lang}</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
    {foreach $estimates as $estimate}
      {if $estimate@first}
      <tr class="current_estimate">
      {elseif $estimate@last}
      <tr class="original_estimate">
      {else}
      <tr class="previous_estimate">
      {/if}
        <td class="estimate">{lang estimate=$estimate->getValue()|estimate job_type=$estimate->getJobTypeName()}:estimate of :job_type{/lang}</td>
        <td class="on_by">{lang date=$estimate->getCreatedOn()|date:0 user=$estimate->getCreatedBy()->getDisplayName(true)}:date by :user{/lang}</td>
        <td class="comment">{$estimate->getComment()}</td>
        <td class="first_or_last right">
        {if $estimate@first}
          <span class="pill ok">{lang}Latest{/lang}</span>
        {elseif $estimate@last}
          <span class="pill ok">{lang}Original{/lang}</span>
        {/if}
        </td>
      </tr>
    {/foreach}
    </tbody>
  </table>
{else}
  <p class="empty_page">{lang type=$active_tracking_object->getVerboseType(true)}There are no estimates tracked for this :type{/lang}</p>
{/if}
</div>