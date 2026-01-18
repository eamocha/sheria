<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require "Top_controller.php";
class Core_controller extends Top_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->authenticate_actions_per_license();
    }
}

?>