<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
$autoload["packages"] = [];
$autoload["libraries"] = ["session", "my_model", "my_model_factory"];
$autoload["helper"] = ["form", "url", "html_header", "security", "string", "date_format"];
$autoload["config"] = [];
$autoload["language"] = [];
$autoload["model"] = [];


?>