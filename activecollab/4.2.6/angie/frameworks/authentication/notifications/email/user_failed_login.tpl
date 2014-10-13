{lang language=$language}Failed Login{/lang}
================================================================================
{notification_wrapper title='Failed Login' context=$context context_view_url=$context_view_url recipient=$recipient sender=$sender inspect=false open_in_browser=false}
	<p>{lang max_attempts=$max_attempts language=$language}More than :max_attempts failed login detected with your account{/lang}.</p>
{/notification_wrapper}