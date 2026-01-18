<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

if (php_sapi_name() !== 'cli') {
    die('<h1>Access is Forbidden');
}

include COREPATH .'libraries/traits/MigrationLogTrait.php';

interface Top_release_script
{
    function update_db_version();
    function set_what_is_new_flag();
}