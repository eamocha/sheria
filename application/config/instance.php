<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

// this file is owned by the client of sheria360 and can be updated at any time

$config['notification_refresh_interval'] = '300000'; // refresh interval in [ms] to load the pending notifications with 5 minute (300000ms) as default value. To stop the refresh, leave it empty or set zero. If the value less than one minute, then the system override it to 1 minute for security measure issues

$config['reminder_refresh_interval'] = '300000'; // refresh interval in [ms] to load the pending reminders with 5 minute (300000ms) as default value. To stop the refresh, leave it empty or set zero. If the value less than one minute, then the system override it to 1 minute for security measure issues

$config['api_key_validity_time'] = '1440'; //time in-terms of Minutes(m), Default value is 1440m which is equal to 24h

$config['PHP_executable_path'] ='C:\php\php7.2\php.exe';// '/bin/php71'; // PHP executable path that located in Environment variables. For windows, it should be like: 'C:\php\php.exe'. For Linux, it should be like: /bin/php71

$config['allow_download_folder'] = false;