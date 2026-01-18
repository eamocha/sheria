<?php

defined("BASEPATH") or exit("No direct script access allowed");
class CI_Input
{
    protected $ip_address = false;
    protected $_allow_get_array = true;
    protected $_standardize_newlines;
    protected $_enable_xss = false;
    protected $_enable_csrf = false;
    protected $headers = [];
    protected $_raw_input_stream;
    protected $_input_stream;
    protected $security;
    protected $uni;
   // private $default_allowed_tags = "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p>"; atinga
    private $default_allowed_tags =  "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img><ul><ol><li>";
    public function __construct()
    {
        $this->_allow_get_array = config_item("allow_get_array") !== false;
        $this->_enable_xss = config_item("global_xss_filtering") === true;
        $this->_enable_csrf = config_item("csrf_protection") === true;
        $this->_standardize_newlines = (bool) config_item("standardize_newlines");
        $this->security =& load_class("Security", "core");
        if (UTF8_ENABLED === true) {
            $this->uni =& load_class("Utf8", "core");
        }
        $this->_sanitize_globals();
        if ($this->_enable_csrf === true && !is_cli()) {
            $this->security->csrf_verify();
        }
        log_message("info", "Input Class Initialized");
    }
    protected function _fetch_from_array(&$array, $index = NULL, $xss_clean = NULL, $allowed_tags = false)
    {
        is_bool($xss_clean) || ($xss_clean = $this->_enable_xss);
        isset($index) || ($index = array_keys($array));
        if (is_array($index)) {
            $output = [];
            foreach ($index as $key) {
                $output[$key] = $this->_fetch_from_array($array, $key, $xss_clean, $allowed_tags);
            }
            return $output;
        } else {
            if (isset($array[$index])) {
                $value = $array[$index];
            } else {
                if (1 < ($count = preg_match_all("/(?:^[^\\[]+)|\\[[^]]*\\]/", $index, $matches))) {
                    $value = $array;
                    $i = 0;
                    while ($i < $count) {
                        $key = trim($matches[0][$i], "[]");
                        if ($key !== "") {
                            if (isset($value[$key])) {
                                $value = $value[$key];
                                $i++;
                            } else {
                                return NULL;
                            }
                        }
                    }
                } else {
                    return NULL;
                }
            }
            if ($xss_clean === true) {
                $value = $this->security->xss_clean($value);
            }
            is_bool($allowed_tags) && $allowed_tags ? $allowed_tags = $this->default_allowed_tags : NULL;
            return is_array($value) ? $this->strip_tags_array($value, $allowed_tags) : strip_tags($value, is_string($allowed_tags) ? $allowed_tags : "");
        }
    }
    private function strip_tags_array($arr, $allowed_tags)
    {
        if ($this->is_assoc($arr)) {
            $result = [];
            foreach ($arr as $k => $v) {
                $result[$k] = is_array($v) ? $this->strip_tags_array($v, $allowed_tags) : trim(strip_tags($v, is_string($allowed_tags) ? $allowed_tags : ""));
            }
            return $result;
        } else {
            $result = [];
            foreach ($arr as $v) {
                $result[] = is_array($v) ? $this->strip_tags_array($v, $allowed_tags) : trim(strip_tags($v, is_string($allowed_tags) ? $allowed_tags : ""));
            }
            return $result;
        }
    }
    private function is_assoc($arr)
    {
        if ([] === $arr) {
            return false;
        }
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
    public function get($index = NULL, $xss_clean = NULL)
    {
        return $this->_fetch_from_array($_GET, $index, $xss_clean);
    }
    public function post($index = NULL, $xss_clean = NULL, $allowed_tags = false)
    {
        return $this->_fetch_from_array($_POST, $index, $xss_clean, $allowed_tags);
    }
    public function post_get($index, $xss_clean = NULL)
    {
        return isset($_POST[$index]) ? $this->post($index, $xss_clean) : $this->get($index, $xss_clean);
    }
    public function get_post($index, $xss_clean = NULL)
    {
        return isset($_GET[$index]) ? $this->get($index, $xss_clean) : $this->post($index, $xss_clean);
    }
    public function cookie($index = NULL, $xss_clean = NULL)
    {
        return $this->_fetch_from_array($_COOKIE, $index, $xss_clean);
    }
    public function server($index, $xss_clean = NULL)
    {
        return $this->_fetch_from_array($_SERVER, $index, $xss_clean);
    }
    public function input_stream($index = NULL, $xss_clean = NULL)
    {
        if (!is_array($this->_input_stream)) {
            parse_str($this->raw_input_stream, $this->_input_stream);
            is_array($this->_input_stream) || ($this->_input_stream = []);
        }
        return $this->_fetch_from_array($this->_input_stream, $index, $xss_clean);
    }
    public function set_cookie($name, $value = "", $expire = "", $domain = "", $path = "/", $prefix = "", $secure = NULL, $httponly = NULL)
    {
        if (is_array($name)) {
            foreach (["value", "expire", "domain", "path", "prefix", "secure", "httponly", "name"] as $item) {
                if (isset($name[$item])) {
                    ${$item} = $name[$item];
                }
            }
        }
        if ($prefix === "" && config_item("cookie_prefix") !== "") {
            $prefix = config_item("cookie_prefix");
        }
        if ($domain == "" && config_item("cookie_domain") != "") {
            $domain = config_item("cookie_domain");
        }
        if ($path === "/" && config_item("cookie_path") !== "/") {
            $path = config_item("cookie_path");
        }
        $secure = $secure === NULL && config_item("cookie_secure") !== NULL ? (bool) config_item("cookie_secure") : (bool) $secure;
        $httponly = $httponly === NULL && config_item("cookie_httponly") !== NULL ? (bool) config_item("cookie_httponly") : (bool) $httponly;
        if (!is_numeric($expire)) {
            $expire = time() - 86500;
        } else {
            $expire = 0 < $expire ? time() + $expire : 0;
        }
        setcookie($prefix . $name, $value, $expire, $path, $domain, $secure, $httponly);
    }
    public function ip_address()
    {
        if ($this->ip_address !== false) {
            return $this->ip_address;
        }
        $proxy_ips = config_item("proxy_ips");
        if (!empty($proxy_ips) && !is_array($proxy_ips)) {
            $proxy_ips = explode(",", str_replace(" ", "", $proxy_ips));
        }
        $this->ip_address = $this->server("REMOTE_ADDR");
        if ($proxy_ips) {
            foreach (["HTTP_X_FORWARDED_FOR", "HTTP_CLIENT_IP", "HTTP_X_CLIENT_IP", "HTTP_X_CLUSTER_CLIENT_IP"] as $header) {
                if (($spoof = $this->server($header)) !== NULL) {
                    sscanf($spoof, "%[^,]", $spoof);
                    if (!$this->valid_ip($spoof)) {
                        $spoof = NULL;
                    } else {
                        if ($spoof) {
                            $i = 0;
                            for ($c = count($proxy_ips); $i < $c; $i++) {
                                if (strpos($proxy_ips[$i], "/") === false) {
                                    if ($proxy_ips[$i] === $this->ip_address) {
                                        $this->ip_address = $spoof;
                                    }
                                } else {
                                    isset($separator) || ($separator = $this->valid_ip($this->ip_address, "ipv6") ? ":" : ".");
                                    if (strpos($proxy_ips[$i], $separator) !== false) {
                                        if (!(isset($ip) && isset($sprintf))) {
                                            if ($separator === ":") {
                                                $ip = explode(":", str_replace("::", str_repeat(":", 9 - substr_count($this->ip_address, ":")), $this->ip_address));
                                                for ($j = 0; $j < 8; $j++) {
                                                    $ip[$j] = intval($ip[$j], 16);
                                                }
                                                $sprintf = "%016b%016b%016b%016b%016b%016b%016b%016b";
                                            } else {
                                                $ip = explode(".", $this->ip_address);
                                                $sprintf = "%08b%08b%08b%08b";
                                            }
                                            $ip = vsprintf($sprintf, $ip);
                                        }
                                        sscanf($proxy_ips[$i], "%[^/]/%d", $netaddr, $masklen);
                                        if ($separator === ":") {
                                            $netaddr = explode(":", str_replace("::", str_repeat(":", 9 - substr_count($netaddr, ":")), $netaddr));
                                            for ($j = 0; $j < 8; $j++) {
                                                $netaddr[$j] = intval($netaddr[$j], 16);
                                            }
                                        } else {
                                            $netaddr = explode(".", $netaddr);
                                        }
                                        if (strncmp($ip, vsprintf($sprintf, $netaddr), $masklen) === 0) {
                                            $this->ip_address = $spoof;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if (!$this->valid_ip($this->ip_address)) {
            return $this->ip_address = "0.0.0.0";
        }
        return $this->ip_address;
    }
    public function valid_ip($ip, $which = "")
    {
        strtolower($which);
        switch (strtolower($which)) {
            case "ipv4":
                $which = FILTER_FLAG_IPV4;
                break;
            case "ipv6":
                $which = FILTER_FLAG_IPV6;
                break;
            default:
                $which = NULL;
                return (bool) filter_var($ip, FILTER_VALIDATE_IP, $which);
        }
    }
    public function user_agent($xss_clean = NULL)
    {
        return $this->_fetch_from_array($_SERVER, "HTTP_USER_AGENT", $xss_clean);
    }
    protected function _sanitize_globals()
    {
        if ($this->_allow_get_array === false) {
            $_GET = [];
        } else {
            if (is_array($_GET)) {
                foreach ($_GET as $key => $val) {
                    $_GET[$this->_clean_input_keys($key)] = $this->_clean_input_data($val);
                }
            }
        }
        if (is_array($_POST)) {
            foreach ($_POST as $key => $val) {
                $_POST[$this->_clean_input_keys($key)] = $this->_clean_input_data($val);
            }
        }
        if (is_array($_COOKIE)) {
            unset($_COOKIE["\$Version"]);
            unset($_COOKIE["\$Path"]);
            unset($_COOKIE["\$Domain"]);
            foreach ($_COOKIE as $key => $val) {
                if (($cookie_key = $this->_clean_input_keys($key)) !== false) {
                    $_COOKIE[$cookie_key] = $this->_clean_input_data($val);
                } else {
                    unset($_COOKIE[$key]);
                }
            }
        }
        $_SERVER["PHP_SELF"] = strip_tags($_SERVER["PHP_SELF"]);
        log_message("debug", "Global POST, GET and COOKIE data sanitized");
    }
    protected function _clean_input_data($str)
    {
        if (is_array($str)) {
            $new_array = [];
            foreach (array_keys($str) as $key) {
                $new_array[$this->_clean_input_keys($key)] = $this->_clean_input_data($str[$key]);
            }
            return $new_array;
        } else {
           // if (!is_php("5.4") && get_magic_quotes_gpc()) { magic quotes error atinga
             //   $str = stripslashes($str);
            // }
            if (UTF8_ENABLED === true) {
                $str = $this->uni->clean_string($str);
            }
            $str = remove_invisible_characters($str, false);
            if ($this->_standardize_newlines === true) {
                return preg_replace("/(?:\\r\\n|[\\r\\n])/", PHP_EOL, $str);
            }
            return $str;
        }
    }
    protected function _clean_input_keys($str, $fatal = true)
    {
        if (!preg_match("/^[a-z0-9:_\\/|-]+\$/i", $str)) {
            if ($fatal === true) {
                return false;
            }
            set_status_header(503);
            echo "Disallowed Key Characters.";
            exit(7);
        }
        if (UTF8_ENABLED === true) {
            return $this->uni->clean_string($str);
        }
        return $str;
    }
    public function request_headers($xss_clean = false)
    {
        if (!empty($this->headers)) {
            return $this->_fetch_from_array($this->headers, NULL, $xss_clean);
        }
        if (function_exists("apache_request_headers")) {
            $this->headers = apache_request_headers();
        } else {
            $this->headers["Content-Type"] = $_SERVER["CONTENT_TYPE"];
            isset($_SERVER["CONTENT_TYPE"]) && $this->headers["Content-Type"];
            foreach ($_SERVER as $key => $val) {
                if (sscanf($key, "HTTP_%s", $header) === 1) {
                    $header = str_replace("_", " ", strtolower($header));
                    $header = str_replace(" ", "-", ucwords($header));
                    $this->headers[$header] = $_SERVER[$key];
                }
            }
        }
        return $this->_fetch_from_array($this->headers, NULL, $xss_clean);
    }
    public function get_request_header($index, $xss_clean = false)
    {
        if (!isset($headers)) {
            empty($this->headers) && $this->request_headers();
            foreach ($this->headers as $key => $value) {
                $headers[strtolower($key)] = $value;
            }
        }
        $index = strtolower($index);
        if (!isset($headers[$index])) {
            return NULL;
        }
        return $xss_clean === true ? $this->security->xss_clean($headers[$index]) : $headers[$index];
    }
    public function is_ajax_request()
    {
        return !empty($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) === "xmlhttprequest";
    }
    public function is_cli_request()
    {
        return is_cli();
    }
    public function method($upper = false)
    {
        return $upper ? strtoupper($this->server("REQUEST_METHOD")) : strtolower($this->server("REQUEST_METHOD"));
    }
    public function __get($name)
    {
        if ($name === "raw_input_stream") {
            isset($this->_raw_input_stream) || ($this->_raw_input_stream = file_get_contents("php://input"));
            return $this->_raw_input_stream;
        }
        if ($name === "ip_address") {
            return $this->ip_address;
        }
    }
}