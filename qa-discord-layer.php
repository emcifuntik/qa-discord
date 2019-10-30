<?php

class qa_html_theme_layer extends qa_html_theme_base
{
	public function head_css()
	{
		qa_html_theme_base::head_css();

		if (strlen(qa_opt('discord_app_client_id')) && strlen(qa_opt('discord_app_client_secret'))) {
			$this->output(
				'<style>',
				'.qa-nav-user-discord-login {padding: 0;padding-bottom: 2px;}',
				'</style>'
			);
		}
	}
}
