{lang repository=$context->getName() language=$language}':repository' Repository has been Updated{/lang}
================================================================================
{notification_wrapper title='Repository Updated' context=$context context_view_url=$context_view_url recipient=$recipient sender=$sender}
  {if $detailed_notifications}
    {foreach $context->getSourceRepository()->getLastCommit($active_branch, $last_update_commits_count, true) as $commit}
      {assign var=commit_author value=$commit->getAuthorInstance()}
      <div style="margin-top: 15px;">
      {if $commit_author instanceof IUser}
        <a href="{$commit_author->getViewUrl()}" style="{$style.link} font-weight: bold">{$commit_author->getDisplayName(true)}</a> &nbsp;
      {else}
        <b>{$commit->getAuthor()}</b> &nbsp;
      {/if}
        {lang num=$commit->getRevisionNumber() language=$language}Revision #:num{/lang}

        <div style="padding: 10px;">{$commit->getMessageBody()|stripslashes|nl2br nofilter}</div>

        <table cellspacing="0" cellspacing="0" border="0" style="font-size: 10px; width: 100%;">
        {foreach SourcePaths::getPathsForCommit($commit->getId()) as $path}
          <tr>
            <td style="width: 50px; text-align: right; vertical-align: middle;">{$path->getAction()|notification_path_modification:$language nofilter}</td>
            <td style="vertical-align: middle; padding-left: 10px;">{$path->getPath()}</td>
          </tr>
        {/foreach}
        </table>
      </div>
    {/foreach}
  {else}
  <p style="text-align: center;">{lang count=$last_update_commits_count}There are :count new commits{/lang}</p>
  {/if}
{/notification_wrapper}