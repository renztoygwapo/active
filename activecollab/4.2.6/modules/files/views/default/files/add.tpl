{use_widget name="file_upload_form" module="files"}

<script type="text/javascript">
  App.widgets.FlyoutDialog.front().setAutoSize(false);
</script>

<div id="upload_files" class="multi_upload_files">
  {form action=$upload_url method=post class="big_form" id="{$form_id}"}
    <div class="big_form_wrapper two_form_sidebars">
      <div class="main_form_column">
        <div class="upload_form_subform">
          <table class="common multiupload_table" cellspacing="0">
            <thead>
              <tr>
                <th class="input"><strong>{lang}File{/lang}</strong></th>
                <th class="description" colspan="2">{lang}Description{/lang}</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>

      <div class="form_sidebar form_first_sidebar">
        {wrap field=category_id}
          {label for=fileCategory}Category{/label}
          {select_asset_category name='file[category_id]' value=$file_data.category_id id=fileCategory parent=$active_project user=$logged_user success_event="category_created"}
        {/wrap}

        {if $logged_user->canSeeMilestones($active_project)}
          {wrap field=milestone_id}
            {label for=fileMilestone}Milestone{/label}
            {select_milestone name='file[milestone_id]' value=$file_data.milestone_id project=$active_project user=$logged_user id=fileMilestone}
          {/wrap}
        {/if}

        {if $logged_user->canSeePrivate()}
          {wrap field=visibility}
            {label for=fileVisibility}Visibility{/label}
            {select_visibility name='file[visibility]' value=$file_data.visibility id="fileVisibility" object=$active_asset}
          {/wrap}
        {else}
          <input type="hidden" name="file[visibility]" value="1" id="fileVisibility" />
        {/if}
      </div>

      <div class="form_sidebar form_second_sidebar">
        {wrap field=notify_users}
          {select_subscribers name=notify_users exclude=$file_data.exclude_ids object=$active_asset user=$logged_user label='Notify People'}
        {/wrap}
      </div>
    </div>

    {wrap_buttons}
      {submit}Save{/submit}
    {/wrap_buttons}
  {/form}

  <script type="text/javascript">
    $('#{$form_id}').fileUploadForm({$uploader_options|json nofilter});
  </script>
</div>