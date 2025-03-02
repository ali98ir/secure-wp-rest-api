# Secure WP REST API

This WordPress plugin adds custom authentication for the REST API, allowing access only to authorized users. The plugin supports both Basic Auth and WordPress login session for authentication. 

## Features
- Basic Authentication for REST API.
- Ensures only Administrators and Editors can access the API.
- Uses WordPress built-in login functionality to verify user access.

## Installation
1. Download the plugin ZIP file from the [Releases Page](https://github.com/ali98ir/secure-wp-rest-api/releases).
2. Go to your WordPress admin dashboard.
3. Navigate to **Plugins** > **Add New** > **Upload Plugin**.
4. Upload the downloaded ZIP file.
5. Install and activate the plugin.

## Usage
This plugin works automatically to authenticate users based on the login session or Basic Auth. Ensure that the user is either an Administrator or Editor to access the API.

## License
This plugin is licensed under the GPLv2 license.
