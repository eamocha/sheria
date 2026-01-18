<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
define("FILE_READ_MODE", 420);
define("FILE_WRITE_MODE", 438);
define("DIR_READ_MODE", 493);
define("DIR_WRITE_MODE", 511);
define("FOPEN_READ", "rb");
define("FOPEN_READ_WRITE", "r+b");
define("FOPEN_WRITE_CREATE_DESTRUCTIVE", "wb");
define("FOPEN_READ_WRITE_CREATE_DESTRUCTIVE", "w+b");
define("FOPEN_WRITE_CREATE", "ab");
define("FOPEN_READ_WRITE_CREATE", "a+b");
define("FOPEN_WRITE_CREATE_STRICT", "xb");
define("FOPEN_READ_WRITE_CREATE_STRICT", "x+b");

?>