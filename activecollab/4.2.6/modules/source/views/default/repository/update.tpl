{title}Update Repository{/title}
{add_bread_crumb}Update{/add_bread_crumb}

<div id="repository_update" class="fields_wrapper">
  <div id="repository_update_progress">
    <div id="progress_content"></div>
  </div>
</div>
<script type="text/javascript">
  if (!window['repository_updating']) {
	  var repository_uptodate = {$uptodate|json nofilter};
	  var repository_head_revision = {$head_revision|json nofilter};
	  var repository_last_revision = {$last_revision|json nofilter};
	  var repository_update_url = {$repository_update_url|json nofilter};
	  var repository_logs_per_request = {$logs_per_request|json nofilter};
    var error_message = {$error_message|json nofilter};
  } //if
	        
  progress_div = $('#repository_update_progress');
  
  var finishing_update = function(total_commits) {
    $('#progress_content').append('<p class="subscribers"><img src="' + App.Wireframe.Utils.indicatorUrl() + '" alt="" /> ' + App.lang('Sending subscriptions...') + ' </p>');

    $.ajax({
      url: App.extendUrl(repository_update_url, {
        'finished' : total_commits
      }),
      type: 'GET',
      success : function(response) {
        $('#progress_content p.subscribers img').attr({
        	'src' : App.Wireframe.Utils.indicatorUrl('ok')
        });
        $('#progress_content p.subscribers').append(App.lang('Done!'));
        window['repository_updating'] = false;
        App.widgets.FlyoutDialog.front().close();
        App.Wireframe.Content.reload();
      },
      error : function () {
    	  window['repository_updating'] = false;
      }
    });
  };

  var get_logs = function(commit, total_commits) {
    progress_content = $('#progress_content');
    commit_to = repository_logs_per_request + commit;
    if (commit_to > repository_head_revision) {
        commit_to = repository_head_revision;
    } //if
    progress_content.html('<p><img src="' + App.Wireframe.Utils.indicatorUrl() + '" alt="" /> Importing commit(s): #' + commit + ' - #' + commit_to + '</p>');
    
    $.ajax( {
      url: App.extendUrl(repository_update_url, {
        'r' : commit
      }),
      type: 'GET',
      success : function(response) {

    	  var return_json = jQuery.parseJSON( response );
    	  total_commits -= return_json.skipped_commits;
        if (return_json.finished == 0) {
        	commit = commit_to; 
        	get_logs(commit, total_commits); 
        } else if (return_json.finished == 1) {
            progress_content.html('<p><img src="' + App.Wireframe.Utils.indicatorUrl('ok') + '" alt="" /> '+ App.lang('Repository successfully updated') + '</p>');
            finishing_update(total_commits);
        } else {
          if (return_json.message) {
            progress_content.html('<p><img src="' + App.Wireframe.Utils.indicatorUrl('error') + '" alt="" /> ' + return_json.message + '</p>');
          } else {
            progress_content.html('<p><img src="' + App.Wireframe.Utils.indicatorUrl('error') + '" alt="" /> '+ App.lang('We have encountered an error. Please try updating again. If this problem persists please contact your supervisor.') + '</p>'); // if not success, reponse is a error message
          } //if
          window['repository_updating'] = false;
          return false;
        }
    	},
  	  error : function() {
        progress_content.html('<p><img src="' + App.Wireframe.Utils.indicatorUrl('error') + '" alt="" />There has been an error. Please try again or contact an administrator.</p>');
      }
    });
  };
  
  if (error_message) {
    progress_div.html('<p><img src="' + App.Wireframe.Utils.indicatorUrl('error') + '" alt="" /> '+ error_message + '</p>');
  } else if (repository_uptodate == 1) {
    progress_div.html('<p><img src="' + App.Wireframe.Utils.indicatorUrl('ok') + '" alt="" /> '+ App.lang('Repository is already up-to-date') + '</p>');
  } else {
    if (window['repository_updating']) {
    	progress_div.html('<p><img src="' + App.Wireframe.Utils.indicatorUrl('ok') + '" alt="" /> '+ App.lang('Another update process is already running, please stand by...') + '</p>');
    } else {
    	total_commits = repository_head_revision - repository_last_revision;
      commit = repository_last_revision + 1;
      
      if (total_commits > 0) {
        progress_div.prepend('<p>There are new commits, please wait until the repository gets updated to revision #'+ repository_head_revision +'</p>');
        window['repository_updating'] = true;
        get_logs(commit, total_commits);
      } else {
        progress_div.prepend('<p>' + App.lang('Error getting new commits') + ':</p>');
      } //if
    } //if
  } //if

</script>