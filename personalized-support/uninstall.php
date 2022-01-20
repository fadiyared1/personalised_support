<?php

if (!defined('WP_UNINSTALL_PLUGIN'))
	exit();

if (!is_multisite())
{
	delete_option('simple_spoiler_bg_wrap');
	delete_option('simple_spoiler_bg_body');
	delete_option('simple_spoiler_br_color');
}

else
{
	global $wpdb;

	$blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
	$original_blog_id = get_current_blog_id();

	foreach ($blog_ids as $blog_id)
	{
		switch_to_blog($blog_id);
		delete_site_option('simple_spoiler_bg_wrap');
		delete_site_option('simple_spoiler_bg_body');
		delete_site_option('simple_spoiler_br_color');
	}

	switch_to_blog($original_blog_id);
}
