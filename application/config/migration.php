<?php

defined("BASEPATH") or exit("No direct script access allowed");
//$config["migration_enabled"] = false;
$config["migration_version"] = 0;
$config["migration_path"] = APPPATH . "migrations/";
$config['migration_enabled'] = TRUE;
$config['migration_type'] = 'sequential'; // or 'timestamp' if you prefer

$config['migration_table'] = 'migrations';
$config['migration_auto_latest'] = FALSE;