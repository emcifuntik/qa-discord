<?php

class qa_discord_login
{
	public function match_source($source)
	{
		return $source == 'discord';
	}


	public function login_html($tourl, $context)
	{
		$app_id = qa_opt('discord_app_client_id');
		if (!strlen($app_id))
			return;

		$this->discord_html(qa_path_absolute('discord-login', array()), false, $context);
	}


	public function logout_html($tourl)
	{
		$app_id = qa_opt('discord_app_client_id');

		if (!strlen($app_id))
			return;

		$this->discord_html($tourl, true, 'menu');
	}


	public function discord_html($tourl, $logout, $context)
	{
		if ($context == 'login' || $context == 'register')
			$size = 'large';
		else
			$size = 'medium';

		$client_id = qa_opt('discord_app_client_id');
		if(strlen($client_id))
		{
			$params = array(
				'client_id' => $client_id,
				'redirect_uri' => $tourl,
				'response_type' => 'code',
				'scope' => 'identify email'
			);
	
			$auth_url = 'https://discordapp.com/api/oauth2/authorize?' . http_build_query($params);
			if($logout)
			{
				echo "<a href=\"./index.php?qa=logout\" class=\"qa-nav-user-link\">Logout</a>";
			}
			else
			{
				echo "<a href=\"" . $auth_url . "\" class=\"qa-form-tall-button qa-form-tall-button-login\" style=\"display: block; text-decoration: none;text-align: center;color: white;\"><img style=\"margin-right:2px;\" width=\"20\" height=\"20\" src=\"https://discordapp.com/assets/1c8a54f25d101bdc607cec7228247a9a.svg\"/>Discord</a>";
			}
		}
	}


	public function admin_form()
	{
		$saved = false;

		if (qa_clicked('discord_save_button')) {
			qa_opt('discord_app_client_id', qa_post_text('discord_app_client_id_field'));
			qa_opt('discord_app_client_secret', qa_post_text('discord_app_client_secret_field'));
			$saved = true;
		}

		$ready = strlen(qa_opt('discord_app_client_id')) && strlen(qa_opt('discord_app_client_secret'));

		return array(
			'ok' => $saved ? 'Discord application details saved' : null,

			'fields' => array(
				array(
					'label' => 'Discord App client ID:',
					'value' => qa_html(qa_opt('discord_app_client_id')),
					'tags' => 'name="discord_app_client_id_field"',
				),

				array(
					'label' => 'Discord App client secret:',
					'value' => qa_html(qa_opt('discord_app_client_secret')),
					'tags' => 'name="discord_app_client_secret_field"',
					'error' => $ready ? null : 'To use Discord Login, please <a href="https://discordapp.com/developers/applications/" target="_blank">set up a Discord application</a>.',
				),
			),

			'buttons' => array(
				array(
					'label' => 'Save Changes',
					'tags' => 'name="discord_save_button"',
				),
			),
		);
	}
}
