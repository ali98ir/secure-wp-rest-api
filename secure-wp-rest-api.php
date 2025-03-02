<?php
/**
 * Plugin Name: Secure WP Rest API
 * Description: Adds custom authentication for the REST API using Basic Auth or WordPress login session, ensuring that only authorized users (Administrator or Editor) can access the API.
 * Version: 1.1
 * Requires at least: 4.7
 * Tested up to: 6.2
 * Author: Ali Iranpour
 * Author URI: https://aliiranpour.ir
 * Plugin URI: https://github.com/ali98ir/secure-wp-rest-api
 */

function custom_rest_api_auth($user) {
    // If user is already authenticated, return it
    if (!empty($user)) {
        return $user;
    }

    // Check Basic Auth
    if (!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW'])) {
        $username = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];

        // Attempt to authenticate the user with Basic Auth
        $user = wp_authenticate($username, $password);

        // Return authenticated user ID if successful
        if (!is_wp_error($user)) {
            return $user->ID;
        }
    }

    return null;
}

function custom_rest_api_auth_error($error) {
    if (!empty($error)) {
        return $error;
    }

    // Check if the user is logged in
    $user = wp_get_current_user();

    // If user is not logged in, return error
    if (!is_user_logged_in()) {
        return new WP_Error('rest_forbidden', 'You must be logged in to access the API.', ['status' => 403]);
    }

    // Check user role (Administrator or Editor) in one in_array check
    $valid_roles = ['administrator', 'editor'];
    if (!array_intersect($valid_roles, $user->roles)) {
        return new WP_Error(
            'rest_forbidden',
            'You must be an Administrator or Editor to access the API. Your current role is: ' . implode(', ', $user->roles),
            ['status' => 403]
        );
    }

    return null;
}

// Apply the custom authentication logic
add_filter('determine_current_user', 'custom_rest_api_auth', 20);
add_filter('rest_authentication_errors', 'custom_rest_api_auth_error');
