<?php

$root=((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? "https://" : "http://").(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
$root.= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
$config['base_url'] = $root;
$config['index_page'] = '';
$config['uri_protocol'] = 'AUTO';
$config['url_suffix'] = '';
$config['language'] = 'english';
$config['default_timezone'] = 'Africa/Nairobi';
$config['charset'] = 'UTF-8';
$config['enable_hooks'] = true;
$config['subclass_prefix'] = 'MY_';
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-@\=';
$config['allow_get_array'] = true;
$config['enable_query_strings'] = false;
$config['controller_trigger'] = 'c';
$config['function_trigger'] = 'm';
$config['directory_trigger'] = 'd'; // experimental not currently in use
$config['log_threshold'] = 1;
$config['log_path'] = 'files/logs/';
$config['log_date_format'] = 'Y-m-d H:i:s';
$config['cache_path'] = '';
$config['encryption_key'] = '8JNt3VIFzioniAlaRhTqMcrv533IpTvZ12s';
$config['sess_cookie_name'] = 'ilawlegfifthgeneration';
$config['sess_expiration'] = 2500; // default in CI is 7200s (2h). The session time is extended to be one month 3600*7200=25920000s
$config['sess_driver'] = 'database';
$config['sess_save_path'] = 'ci_sessions';
$config['sess_match_ip'] = false;
$config['sess_time_to_update'] = 300;//5minutes
$config['sess_regenerate_destroy'] = FALSE; //added by Atinga
$config['cookie_domain'] = "";
$config['cookie_path'] = "/";
$config['cookie_secure'] = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on';
$config['cookie_httponly'] = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on';
$config['global_xss_filtering'] = TRUE;
$config['csrf_protection'] = false;
$config['csrf_token_name'] = 'aeZZga';
$config['csrf_cookie_name'] = 'AGzzEA';
$config['csrf_expire'] = 14400; // 4 Hours
$config['compress_output'] = false;
$config['time_reference'] = 'local';
$config['rewrite_short_tags'] = false;
$config['proxy_ips'] = '';

$config['allowed_upload_size_bite'] = '52428800';
$config['allowed_upload_size_kilobite'] = '51200';
$config['allowed_upload_size_megabite'] = '50';
$config['allowed_post_max_size_bite'] = '52428800';
$config['allowed_single_session'] = true; // exception for users who clicked on "Keep me signed in"

$config['app4legal_website_feedback_page'] = "https://sheria360.com/en/company#contact";

$config['allow_export_to_server'] = false;

$config['help_url'] = 'https://docs.sheria360.com';
$config['cp_help_url'] = 'https://docs.sheria360.com';
$config['db_collation_package1'] = 'Arabic_100_CI_AS';
$config['db_collation_package2'] = 'Latin1_General_100_CI_AS';
$config['lang_package1'] = array('english', 'french', 'arabic');
$config['lang_package2'] = array('english', 'french', 'spanish');

$config['ota_download_link'] = 'https://docs.sheria360.com/x/IICaB';
$config['google_play_download_link'] = 'https://play.google.com/store/apps/details?id=com.xp.sheria360';
$config['apple_store_download_link'] = 'https://itunes.apple.com/us/app/sheria360/id1044203342?ls=1&mt=8';
$config['cp_documentation_link'] = 'https://docs.sheria360.com/x/ph4GAQ';

// documentation URLs for integrations with cloud management solutions
$config['integration_dropbox_documentation_url'] = 'https://docs.sheria360.com/display/A4L/Dropbox+Integration';
$config['integration_gdrive_documentation_url'] = '';
$config['integration_sharepoint_documentation_url'] = '';
$config['integration_onedrive_documentation_url'] = '';

$config['min_refresh_interval'] = "60000"; // minumun refresh interval for notifications and reminders (1 minunte)

$config['allowed_decimal_format'] = 2;

$config['encrypt_method'] = "AES-256-CBC";
$config['secret_key'] = "cA@74#CD628CC2BssaBRT935136H6H7+B/63**C27";
$config['secret_iv'] = "@#133AAd5fgf5HJ5g27@5+2*/-sd";

$config['anti_automation_protection'] = false;
$config['anti_automation_max_requests_per_session'] = 10; // if this feature is enabled, the user can create 10 records per module (matter, matter container, ...) in one minute per one session
$config['anti_automation_max_time_per_session'] = 1; // in minutes, default is one minute


// for single code base for cloud instances
//$config['files_path'] = ((getenv('A4L_CONFIG_FILESPATH', true) ?: getenv('A4L_CONFIG_FILESPATH'))?:null) ?? (INSTANCE_PATH . 'files/');
//// remove any trailing slashes, makes sure it ends with: /files
//$config['files_path'] = rtrim($config['files_path'], '/\\');

// for single code base for cloud instances
$config['files_path'] = ((getenv('A4L_CONFIG_FILESPATH', true) ?: getenv('A4L_CONFIG_FILESPATH'))?:null) ?? (INSTANCE_PATH . 'files'.DIRECTORY_SEPARATOR);
// remove any trailing slashes, makes sure it ends with: /files
$config['files_path'] = rtrim($config['files_path'], '/\\');
$config['files_path_c'] = ((getenv('A4L_CONFIG_FILESPATH', true) ?: getenv('A4L_CONFIG_FILESPATH'))?:null) ?? (INSTANCE_PATH .'..'.DIRECTORY_SEPARATOR. 'files'.DIRECTORY_SEPARATOR);


$config['forgot_password_url_expires_in'] = 12; // in hours
$config['allow_any_cors_domain'] = TRUE;
//$config['composer_autoload'] = FCPATH . 'vendor/autoload.php';
