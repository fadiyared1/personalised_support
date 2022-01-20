<?php

class PSUser
{
	static function has_valid_numero()
	{
		return isset($_SESSION[Identification::NUMERO]);
	}

	static function get_numero()
	{
		return $_SESSION[Identification::NUMERO];
	}

	static function set_numero($numero)
	{
		$_SESSION[Identification::NUMERO] = $numero;
	}
}

add_action('init', 'start_session_wp', 1);
function start_session_wp()
{
	if (!session_id())
	{
		session_start();
	}
}

function end_session()
{
	session_destroy();
}
