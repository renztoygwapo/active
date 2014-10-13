{lang language=$language}Good Morning{/lang}
================================================================================
{notification_wrapper title='Good Morning' sender=$sender recipient=$recipient language=$language}
  {assign var='offset' value=get_user_gmt_offset($recipient)}

{if $first_morning_paper}
  <p style="text-align: center"><b>{lang language=$language}What is Morning Paper?{/lang}</b>: {lang language=$language}Morning Paper is a daily email which recaps the important events which happened the previous business day, as well as what is due today{/lang}. <a href="https://www.activecollab.com/help/whats-new/morning-paper.html" style="{$style.link}">{lang language=$language}Learn more{/lang}</a>.</p>
{/if}

  {if $today_data}
    <p>{lang language=$language}Today{/lang}:</p>

    <table cellspacing="0" cellpadding="2" width="100%">
    {foreach $today_data as $event}
      <tr>
        <td style="font-size: 11px;">
          {if $event.event == $smarty.const.MorningPaper::TASK_DUE}
            <span style="color: #b6d138; text-transform: uppercase">({lang task_id=$event.task_id language=$language}Task #:task_id{/lang})</span> <a href="{$event.permalink}" style="{$style.link}">{$event.name}</a>
          {elseif $event.event == $smarty.const.MorningPaper::MILESTONE_DUE}
            <span style="color: #b05ecb; text-transform: uppercase">({lang language=$language}Milestone{/lang})</span> <a href="{$event.permalink}" style="{$style.link}">{$event.name}</a>
          {elseif $event.event == $smarty.const.MorningPaper::SUBTASK_DUE}
            <span style="color: #e3ab5f; text-transform: uppercase">({lang language=$language}Subtask{/lang})</span> <a href={$event.permalink} style="{$style.link}">{$event.name}</a> &middot; <span style="color: #b6d138; text-transform: uppercase">({lang task_id=$event.task_id language=$language}Task #:task_id{/lang})</span> <a href="{$event.task_permalink}" style="{$style.link}">{$event.task_name}</a>
          {/if}
        </td>
        <td  style="font-size: 11px; color: red;" align="right" width="100">
          {if $event.diff == 0}
            {lang language=$language}Due Today{/lang}
          {elseif $event.diff == -1}
            {lang language=$language}One Day Late{/lang}
          {else}
            {lang num=$event.diff|abs language=$language}:num Days Late{/lang}
          {/if}
        </td>
      </tr>
    {/foreach}
    </table>
  {/if}

  {if $prev_data}
    <p>{lang date=$previous_day|date:0 language=$language}Retrospective for :date{/lang}:</p>

    {foreach $prev_data as $project}
      <p>{lang}Project{/lang}: {$project.name}</p>
      <table cellspacing="0" cellpadding="2" width="100%">
        {foreach $project.events as $event}
        <tr>
          <td style="font-size: 11px;">
          {if $event.event == $smarty.const.MorningPaper::PROJECT_COMPLETED}
            <span style="color: #66C0DE; text-transform: uppercase">({lang language=$language}Project{/lang})</span> {lang action_by=$event.action_by language=$language}Completed by :action_by{/lang}
          {elseif $event.event == $smarty.const.MorningPaper::PROJECT_STARTED}
            <span style="color: #66C0DE; text-transform: uppercase">({lang language=$language}Project{/lang})</span> {lang action_by=$event.action_by language=$language}Started by :action_by{/lang}
          {elseif $event.event == $smarty.const.MorningPaper::MILESTONE_COMPLETED}
            <span style="color: #b05ecb; text-transform: uppercase">({lang language=$language}Milestone{/lang})</span> {lang action_by=$event.action_by name=$event.name url=$event.permalink link_style=$style.link language=$language}<a href=":url" style=":link_style">:name</a> completed by :action_by{/lang}
          {elseif $event.event == $smarty.const.MorningPaper::TASK_COMPLETED}
            <span style="color: #b6d138; text-transform: uppercase">({lang task_id=$event.task_id language=$language}Task #:task_id{/lang})</span> {lang action_by=$event.action_by name=$event.name url=$event.permalink link_style=$style.link language=$language}<a href=":url" style=":link_style">:name</a> completed by :action_by{/lang}
          {elseif $event.event == $smarty.const.MorningPaper::SUBTASK_COMPLETED}
            <span style="color: #e3ab5f; text-transform: uppercase">({lang language=$language}Subtask{/lang})</span> {lang action_by=$event.action_by name=$event.name url=$event.permalink link_style=$style.link task_id=$event.task_id task_name=$event.task_name task_permalink=$event.task_permalink language=$language}<a href=":url" style=":link_style">:name</a> completed by :action_by in <a href=":task_permalink" style=":link_style">:task_name</a> task (#:task_id){/lang}
          {elseif $event.event == $smarty.const.MorningPaper::DISCUSSION_STARTED}
            <span style="color: #5271d0; text-transform: uppercase">({lang language=$language}Discussion{/lang})</span> {lang action_by=$event.action_by name=$event.name url=$event.permalink link_style=$style.link language=$language}<a href=":url" style=":link_style">:name</a> started by :action_by{/lang}
          {elseif $event.event == $smarty.const.MorningPaper::FILE_UPLOADED}
            <span style="color: #56ba96; text-transform: uppercase">({lang language=$language}File{/lang})</span> {lang action_by=$event.action_by name=$event.name url=$event.permalink link_style=$style.link language=$language}<a href=":url" style=":link_style">:name</a> uploaded by :action_by{/lang}
          {/if}
          </td>
          <td style="font-size: 11px;" align="right" width="60">{$event.timestamp|time:$offset}</td>
        </tr>
        {/foreach}
      </table>
    {/foreach}
  {/if}
{/notification_wrapper}