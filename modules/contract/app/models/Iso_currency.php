<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Iso_currency extends My_Model
{
    protected $modelName = "iso_currency";
    protected $_table = "iso_currencies";
    protected $_listFieldName = "name";
    protected $_fieldsNames = ["id", "code", "name"];
    public function __construct()
    {
        parent::__construct();
        $this->validate = [];
    }
}

?>