<?php

function http_post($url, $post, $headers=array()) {
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

	$headers[] = 'Content-Type: application/x-www-form-urlencoded';
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$server_output = curl_exec($ch);
	curl_close ($ch);

	return json_decode($server_output, true);
}

function http_get($url, $bearer) {
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);

	$headers = array('Authorization: Bearer ' . $bearer);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$server_output = curl_exec($ch);
	curl_close ($ch);

	return json_decode($server_output, true);
}

class qa_discord_login_page
{
	private $directory;

	public function load_module($directory, $urltoroot)
	{
		$this->directory = $directory;
	}

	public function match_request($request)
	{
		return ($request == 'discord-login');
	}

	public function process_request($request)
	{
		if ($request == 'discord-login') {
			$client_id = qa_opt('discord_app_client_id');
			$client_secret = qa_opt('discord_app_client_secret');
			
			$code = qa_get("code");
			if(strlen($code))
			{
				$response = http_post('https://discordapp.com/api/v6/oauth2/token', array(
					'grant_type' => 'authorization_code',
					'client_id' => $client_id,
					'client_secret' => $client_secret,
					'redirect_uri' => qa_path_absolute('discord-login', array()),
					'code' => $code,
					'scope' => 'identify email'
				));

				$user_data = http_get('https://discordapp.com/api/v6/users/@me', $response["access_token"]);

				if(strlen($user_data["email"]))
				{
					$avatar = null;
					if(strlen($user_data["avatar"]))
					{
						if(substr($user_data["avatar"], 0, 2) == "a_")
						{
							$avatar = 'https://cdn.discordapp.com/avatars/' . $user_data["id"] . '/' . $user_data["avatar"] . '.gif';
						}
						else
						{
							$avatar = 'https://cdn.discordapp.com/avatars/' . $user_data["id"] . '/' . $user_data["avatar"] . '.png';
						}
					}
					else
					{
						$avatar = 'https://cdn.discordapp.com/embed/avatars/' . ($user_data["discriminator"] % 5) . '.png';
					}
	
					qa_log_in_external_user('discord', $user_data["id"], array(
						'email' => $user_data["email"],
						'handle' => $user_data["username"] . '#' . $user_data["discriminator"],
						'confirmed' => $user_data["verified"],
						'name' => $user_data["username"] . '#' . $user_data["discriminator"],
						'avatar' => strlen($avatar) ? qa_retrieve_url($avatar) : null,
					));
				}
			}
			qa_redirect_raw(qa_path_absolute(''));
		}
	}
}
