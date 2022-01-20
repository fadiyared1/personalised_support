<?php
/*
Plugin Name: Personalized Support
Plugin URI: 
Description: The plugin allows a personalized support through feedback sliders.
Version: 1.0
Author: F & Q
Author URI: 
Text Domain: simple-spoiler
License: MIT
License URI: 
*/

if (!defined('WPINC'))
{
	die;
}

require_once('ps-user.php');
require_once('ps-users.php');
PSUsers::init();

require_once(join(DIRECTORY_SEPARATOR, array('helpers', 'init.php')));

require_once(join(DIRECTORY_SEPARATOR, array('shortcodes', 'init.php')));

require_once('admin-page.php');

add_action('wp_enqueue_scripts', 'personalized_support_head');
function personalized_support_head()
{
	wp_register_style('personalized_support_style', plugins_url('css/personalized-support.min.css', __FILE__), null, '1.2');
	wp_register_script('personalized_support_script', plugins_url('js/personalized-support.min.js', __FILE__), array('jquery'), '1.2', true);

	wp_enqueue_style('personalized_support_style');
	wp_enqueue_script('personalized_support_script');

	Feedback::enqueue_ajax();
}
