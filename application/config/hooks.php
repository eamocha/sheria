<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
$hook["post_controller_constructor"][] = ["class" => "Start_Up", "function" => "set_timezone", "filename" => "start_up.php", "filepath" => "hooks"];
$hook["post_controller_constructor"][] = ["class" => "Start_Up", "function" => "define_base_url", "filename" => "start_up.php", "filepath" => "hooks"];
$hook["post_controller_constructor"][] = ["class" => "Start_Up", "function" => "authenticate", "filename" => "start_up.php", "filepath" => "hooks"];
$hook["post_controller_constructor"][] = ["class" => "Start_Up", "function" => "prepare_page", "filename" => "start_up.php", "filepath" => "hooks"];
$hook["post_controller_constructor"][] = ["class" => "Start_Up", "function" => "load_language", "filename" => "start_up.php", "filepath" => "hooks"];

?>