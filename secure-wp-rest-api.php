<?php
/*
	Plugin Name: Secure WP REST API
	Plugin URI: https://tabairan.com
	Description: Restricts WP REST API access to logged-in users or users authenticated via Basic Auth.
	Author: Ali Iranpour
	Author URI: https://aliiranpour.ir
	Version: 1.0
	Requires at least: 4.7
	Tested up to: 6.2
*/

if (!defined('ABSPATH')) die();

// ترکیب احراز هویت وردپرس و Basic Auth
function secure_rest_api_auth_handler($user) {
	global $wp_json_basic_auth_error;
	$wp_json_basic_auth_error = null;

	// اگر کاربر لاگین کرده باشد، اجازه دسترسی دارد
	if (!empty($user)) {
		return $user;
	}

	// بررسی احراز هویت Basic Auth
	if (!isset($_SERVER['PHP_AUTH_USER'])) {
		return null;
	}

	$username = $_SERVER['PHP_AUTH_USER'];
	$password = $_SERVER['PHP_AUTH_PW'];

	// جلوگیری از حلقه بی‌نهایت در احراز هویت وردپرس
	remove_filter('determine_current_user', 'secure_rest_api_auth_handler', 20);
	$user = wp_authenticate($username, $password);
	add_filter('determine_current_user', 'secure_rest_api_auth_handler', 20);

	// در صورتی که احراز هویت موفق نباشد
	if (is_wp_error($user)) {
		$wp_json_basic_auth_error = $user;
		return null;
	}

	$wp_json_basic_auth_error = true;
	return $user->ID;
}
add_filter('determine_current_user', 'secure_rest_api_auth_handler', 20);

// مسدود کردن درخواست‌ها اگر احراز هویت انجام نشده باشد
function secure_rest_api_auth_error($error) {
	if (!empty($error)) {
		return $error;
	}

	global $wp_json_basic_auth_error;

	// اگر کاربر لاگین نکرده باشد یا Basic Auth صحیح نباشد، درخواست مسدود می‌شود
	if ($wp_json_basic_auth_error !== true) {
		return new WP_Error('rest_login_required', 'You must be logged in or provide valid Basic Auth credentials.', array('status' => 401));
	}

	return null;
}
add_filter('rest_authentication_errors', 'secure_rest_api_auth_error');
