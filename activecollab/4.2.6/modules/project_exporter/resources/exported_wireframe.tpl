<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-us" lang="en-us">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="{$url_prefix}_assets/style.css" type="text/css" media="screen"/>
    <title>activeCollab</title>
  </head>
  
  <body>
    <div class="wrapper">
      <div id="project_name">
        <img src="{$url_prefix}_uploaded_files/_avatars/avatar_project_logo.gif" alt="project_logo" />{$project_name}
      </div>
      
			<div id="sidebar">
				{$main_navigation}
			  <div class="copy"><p>&copy;{$year} by A51</p></div>
			</div>

     <div id="content_container">{$content_for_layout}</div>
  </div>
</body>
</html>