  {foreach from=$commits item=commits_day key=date}
    <div class="groupped_day_commits">
      <div class="commit_day">
        <div class="date_stamp" title="{$date}">
          <span class="date_stamp_month">{$date|date_format:"%b"}</span>
          <span class="date_stamp_month_day">{$date|date_format:"%e"}</span>
        </div>
      </div>
  
      <div class="day_commits">
        <table class="commit_history_table common" cellspacing="0">
          {foreach from=$commits_day name=commit_list item=commit}
          <tr class="commit {cycle values='odd,even'}">
            <td class="revision_number">
                  <a href="{$commit->getViewUrl($active_project->getSlug(),$project_object_repository->getId())}" title="{$commit->getName()}" class="number">{substr($commit->getName(),0,8)}</a><br />
            </td>
            
            <td class="revision_user">
              {assign var=commit_author value=$commit->getCommitedBy()}
              <a href="{$project_object_repository->getHistoryUrl($commit->getCommitedByName())}">
                  {if $commit_author instanceof User}
                    {$commit_author->getDisplayName(true)}
                  {else}
                    {$commit->getCommitedByName()}
                  {/if}
              </a>
            </td>
            <td class="revision_details">
              <div class="commit_message">
                {$commit->getMessageTitle()|stripslashes nofilter}
              </div>
            </td>
            <td class="revision_files">
              <a title="{lang revision_num=$commit->getRevisionNumber()}Changes to repository in revision: :revision_num{/lang}" class="toggle_files commit_modified_files" href="{assemble route=repository_commit_paths project_slug=$active_project->getSlug() project_source_repository_id=$project_object_repository->getId() r=$commit->getRevisionNumber()}">
              {assign var=actions value=$commit->getActions()}
              {if $actions.A > 0}
                <span class="commit_changed_files added">
                  <img alt="added" src="{image_url name='icons/12x12/add.png' module='source'}">{$actions.A}
                </span>
              {/if}
              {if $actions.D > 0}
                <span class="commit_changed_files deleted">
                  <img alt="deleted" src="{image_url name='icons/12x12/delete.png' module='source'}">{$actions.D}
                </span>
              {/if}
              {if $actions.M > 0}
                <span class="commit_changed_files edited">
                  <img alt="edited" src="{image_url name='icons/12x12/edit.png' module='source'}">{$actions.M}
                </span>
              {/if}
              </a>
            </td>
          </tr>
          {/foreach}
        </table>
      </div>
    </div>
  {/foreach}