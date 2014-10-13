{title}Related commits{/title}

{add_bread_crumb}List of commits{/add_bread_crumb}

<div id="repository_project_object_commits">
{if is_foreachable($commits) and $commits|@count > 0}
  <div class="grouped_commits">
  {foreach from=$commits item=commits_day key=date}
  <table class="commit_object_history_table common" cellspacing="0">
      <tr>
        <th class="date" colspan="4">{$date}</th>
        <th></th>
      </tr>
      {foreach from=$commits_day name=commit_list item=commit}
        <tr class="commit {cycle values='odd,even'}">
          <td class="revision_number">
            <a href="{$commit->getViewUrl($active_project->getSlug())}" title="{lang}View commit details{/lang}" class="number">{$commit->getName()}</a><br />
          </td>
          {assign var=source_repository value=SourceRepositories::findById($commit->getRepositoryId())}
          <td class="repository_name">
            <span class="gray_text">{lang}Name{/lang} </span>
            {$source_repository->getName()}
          </td>
          <td class="repository_type">
            <img alt="{$source_repository->getVerboseName()}"
              src="{image_url name='icons/16x16/'|cat:$source_repository->getIconFileName() module=$smarty.const.SOURCE_MODULE}"
              title="{$source_repository->getVerboseName()}"
            />
          </td>
          <td class="revision_user">
            <span class="gray_text">{lang}By{/lang} </span>
            {$commit->getAuthor() nofilter}
          </td>
          <td class="revision_details">
            <div class="commit_message">
              {$commit->getMessageTitle()|stripslashes nofilter}
            </div>
          </td>
        </tr>
      {/foreach}
  </table>
  {/foreach}
  </div>
{else}
  <p class="empty_page"><span class="inner">{lang}There are no commits related to this project object in the database.{/lang}</span></p>
{/if}
</div>