<?php

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}

// login modules don't work with external user integration
if (!QA_FINAL_EXTERNAL_USERS) {
	qa_register_plugin_module('login', 'qa-discord-login.php', 'qa_discord_login', 'Discord Login');
	qa_register_plugin_module('page', 'qa-discord-login-page.php', 'qa_discord_login_page', 'Discord Login Page');
	qa_register_plugin_layer('qa-discord-layer.php', 'Discord Login Layer');
}
