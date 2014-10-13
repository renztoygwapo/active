{title}Time Record{/title}
{add_bread_crumb}Time Record Details{/add_bread_crumb}

{use_widget name='properties_list' module=$smarty.const.ENVIRONMENT_FRAMEWORK}

<div id="tracking_object_detials">
  <div id="tracking_object_detials_inner_wrapper">
    <dl class="properties_list">
      <dt>{lang}Hours{/lang}</dt>
      <dd>{$active_time_record->getValue()|hours:null:2:true}h</dd>

      <dt>{lang}Status{/lang}</dt>
      <dd>{$active_time_record->getBillableVerboseStatus()}</dd>

      {if $job_type instanceof JobType}
        <dt>{lang}Job Type{/lang}</dt>
        <dd>{$job_type->getName()}</dd>
      {/if}

      <dt>{lang}Date{/lang}</dt>
      <dd>{$active_time_record->getRecordDate()|date:0}</dd>

      <dt>{lang}User{/lang}</dt>
      <dd>{user_link user=$active_time_record->getUser()}</dd>
    </dl>
  </div>
</div>