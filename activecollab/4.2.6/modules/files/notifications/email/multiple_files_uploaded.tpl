[{$project->getName()}] {lang language=$language}Several Files have been Uploaded{/lang}
================================================================================
{notification_wrapper title='Files Uploaded' recipient=$recipient sender=$sender}
  <p>{lang author_name=$sender->getDisplayName() link_style=$style.link language=$language name=$project->getName()}:author_name has just uploaded several files in ':name' project{/lang}:</p>

  <div style="padding: 10px; text-align: center">
    <table cellspacing='0' cellspacing='0' border='0'>
      {foreach $files as $file}
        <tr>
          <td style='text-align: left; vertical-align: top; padding:0px 5px 0 0px;width:10px;'><img src='{$file->preview()->getSmallIconUrl()}'></td>
          <td align='left'><a href='{$file->getDownloadUrl(true)}' style='{$style.link}'>{$file->getName()}</a><span style='margin-left:10px;font-size:9px;color:#91918D;'>({$file->getSize()|filesize})</span></td>
        </tr>
      {/foreach}
    </table>
  </div>
{/notification_wrapper}