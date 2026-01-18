<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
$route["default_controller"] = "pages";
$route["compressed_asset/(.+)"] = "compressed_asset/index/\$1";
$route["404_override"] = "";
$route["translate_uri_dashes"] = false;

?>