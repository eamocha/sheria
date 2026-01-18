<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class My_Model_
{
    protected $modelName = "";
    protected $_table = "";
    protected $_fields = [];
    protected $_fieldsNames = [];
    protected $allowedNulls = [];
    protected $_listFieldName = "name";
    protected $_pk = "id";
    protected $paginationLinks = "";
    protected $paginationTotalRows = 0;
    protected $validationErrors = [];
    protected $load_list_defaultConfig = [];
    protected $paginate_defaultConfig = [];
    protected $ci;
    protected $builtInLogs = false;
    protected $validate = [];
    protected $custom_validation_rules = ["date" => ["date", "", "/^\\d{4}-(0?[1-9]|1[012])-(0?[1-9]|[12][0-9]|3[01])\$/"], "decimal" => ["decimal", NULL, "/^([0-9]+|)(\\.[0-9]{1,2})?\$/"]];
    public function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->lang->load("common", $this->ci->session->userdata("AUTH_language"));
        $this->ci->lang->load("validation_message", $this->ci->session->userdata("AUTH_language"));
        $this->_fields = array_fill_keys($this->_fieldsNames, NULL);
        $this->load_list_defaultConfig = ["key" => $this->_pk, "value" => $this->_listFieldName, "optgroup" => false, "firstLine" => false, "lastLine" => false];
        $this->paginate_defaultConfig = ["anchorAttributes" => "", "currAttr" => [], "htmlCurrAfter" => "<li class=\"page-item\">", "htmlCurrBefore" => "<li class=\"page-item active\">", "htmlWrapAfter" => "</li>", "htmlWrapBefore" => "<li class=\"page-item\">", "inPage" => 30, "nextText" => "&gt;", "padding" => 2, "page" => NULL, "prevText" => "&lt;", "return" => "array", "uri_segment" => 3, "urlPrefix" => ""];
    }
    public function __get($property)
    {
        if (isset($this->{$property})) {
            return $this->{$property};
        }
        return NULL;
    }
    public function set($property, $newValue)
    {
        $this->{$property} = $newValue;
    }
    public function get($property)
    {
        return $this->{$property};
    }
    public function get_fields($fields = NULL)
    {
        return empty($fields) ? $this->_fields : array_intersect_key($this->_fields, array_fill_keys($fields, ""));
    }
    public function get_field($field)
    {
        if (array_key_exists($field, $this->_fields)) {
            return $this->_fields[$field];
        }
        trigger_error("(Use of unkown field \"" . $field . "\" in \"" . $this->modelName . "\")");
    }
    public function reset_fields()
    {
        $this->set("_fields", array_fill_keys($this->_fieldsNames, NULL));
    }
    public function set_field($field, $value)
    {
        if (array_key_exists($field, $this->_fields)) {
            $this->_fields[$field] = $value;
        } else {
            trigger_error("(Use of unkown field \"" . $field . "\" of \"" . $this->modelName . "\")");
        }
    }
    public function set_fields($values)
    {
        if (is_array($values)) {
            $values = array_intersect_key($values, $this->_fields);
            array_walk($values, [$this, "set_mysql_nulls"]);
            $this->_fields = array_merge($this->_fields, $values);
        }
    }
    public function prep_query($queryArr)
    {
        $this->parse_user_calls($queryArr);
    }
    private function parse_user_calls($userQueryParts = [])
    {
        foreach ($userQueryParts as $userCall => $call) {
            if (is_array($call[0])) {
                foreach ($call as $subCall) {
                    call_user_func_array([$this->ci->db, $userCall], $subCall);
                }
            } else {
                is_array($call);
                is_array($call) ? call_user_func_array([$this->ci->db, $userCall], $call) : call_user_func([$this->ci->db, $userCall], $call);
            }
        }
    }
    public function fetch($where)
    {
        $this->ci->db->limit(1);
        if (is_numeric($where)) {
            $result = $this->ci->db->get_where($this->_table, [$this->_pk => $where]);
        } else {
            $result = $this->ci->db->get_where($this->_table, $where);
        }
        $result = $result->result_array();
        if (isset($result[0])) {
            $this->set_fields($result[0]);
            return true;
        }
        return false;
    }
    public function load($query)
    {
        $this->ci->db->limit(1);
        is_numeric($query);
        is_numeric($query) ? $this->ci->db->where($this->_pk, $query) : $this->parse_user_calls($query);
        $result = $this->ci->db->get($this->_table);
        $result = $result->row_array();
        $result = is_array($result) ? $result : false;
        return $result;
    }
    public function load_all($userQueryParts = [], $return = "array")
    {
        $this->parse_user_calls($userQueryParts);
        $result = $this->ci->db->get($this->_table, NULL, NULL, $return == "query");
        return $return == "query" ? $result : ($return == "array" ? $result->result_array() : $result->result());
    }
    public function load_list($userQuery = [], $config = [])
    {
        $config = array_intersect_key($config, $this->load_list_defaultConfig);
        $config = array_merge($this->load_list_defaultConfig, $config);
        if (!isset($userQuery["select"])) {
            $this->ci->db->select($config["key"]);
            $this->ci->db->select($config["value"]);
            if (!empty($config["optgroup"])) {
                $this->ci->db->select($config["optgroup"]);
            }
        }
        if (!isset($userQuery["order_by"])) {
            $this->ci->db->order_by($config["value"], "asc");
        }
        $this->parse_user_calls($userQuery);
        $result = $this->ci->db->get($this->_table);
        $data = [];
        if (!empty($config["firstLine"])) {
            foreach ($config["firstLine"] as $key => $value) {
                if (empty($config["optgroup"])) {
                    $data[$key] = $value;
                } else {
                    $data[""][$key] = $value;
                }
            }
        }
        if (0 < $result->num_rows()) {
            if (empty($config["optgroup"])) {
                foreach ($result->result_array() as $row) {
                    $data[(string) $row[$config["key"]]] = $row[$config["value"]];
                }
            } else {
                foreach ($result->result_array() as $row) {
                    $data[(string) $row[$config["optgroup"]]][(string) $row[$config["key"]]] = $row[$config["value"]];
                }
            }
        }
        if (!empty($config["lastLine"])) {
            foreach ($config["lastLine"] as $key => $value) {
                if (empty($config["optgroup"])) {
                    $data[$key] = $value;
                } else {
                    $data[""][$key] = $value;
                }
            }
        }
        return $data;
    }
    public function pagination_config_all($fields = NULL)
    {
        return empty($fields) ? $this->paginate_defaultConfig : array_intersect_key($this->paginate_defaultConfig, array_fill_keys($fields, ""));
    }
    public function pagination_config_set_all($values)
    {
        if (is_array($values)) {
            $values = array_intersect_key($values, $this->paginate_defaultConfig);
            $this->paginate_defaultConfig = array_merge($this->paginate_defaultConfig, $values);
        }
    }
    public function pagination_config_set($field, $value)
    {
        if (array_key_exists($field, $this->paginate_defaultConfig)) {
            $this->paginate_defaultConfig[$field] = $value;
        } else {
            trigger_error($this->modelName . " :: Unkown pagination config item \"" . $field);
        }
    }
    public function pagination_config($field)
    {
        if (array_key_exists($field, $this->paginate_defaultConfig)) {
            return $this->paginate_defaultConfig[$field];
        }
        trigger_error($this->modelName . " :: Unkown pagination config item \"" . $field);
    }
    public function paginate($userQueryParts = [], $config = [])
    {
        $CI =& get_instance();
        $config = array_intersect_key($config, $this->paginate_defaultConfig);
        $config = array_merge($this->paginate_defaultConfig, $config);
        $page = $CI->uri->rsegment($config["uri_segment"]);
        $this->pagination_config_set("page", $page);
        $queryString = [];
        $config["urlPrefix"];
        $segments = $CI->uri->rsegments;
        $i = 1;
        for ($j = count($segments); $i <= $j; $i++) {
            $queryString[] = $i != $config["uri_segment"] ? str_replace("%", "%%", $segments[$i]) : "%d";
        }
        if (count($segments) < $config["uri_segment"]) {
            $queryString[] = "%d";
        }
        $href = site_url(implode("/", $queryString));
        $ignoredQueryParts = ["select" => "select", "order_by" => "order_by", "limit" => "limit"];
        $countingQueryParts = array_diff_key($userQueryParts, $ignoredQueryParts);
        $this->parse_user_calls($countingQueryParts);
        $this->ci->db->select("COUNT(*) total_rows");
        $totalRowsQ = $this->ci->db->get($this->_table);
        if (isset($countingQueryParts["group_by"])) {
            $totalRows = $totalRowsQ->num_rows();
        } else {
            $totalRows = $totalRowsQ->row();
            $totalRows = $totalRows->total_rows;
        }
        unset($totalRowsQ);
        if ($totalRows < $config["inPage"]) {
            $this->paginationLinks = "";
            $this->paginationTotalRows = $totalRows;
            return $this->load_all($userQueryParts);
        }
        $padding = $config["padding"];
        $this->paginationTotalRows = $totalRows;
        $lastPage = ceil($totalRows / $config["inPage"]);
        $pages = "";
        $attrStr = "";
        $currAttr = "";
        if (!empty($config["anchorAttributes"]) && is_array($config["anchorAttributes"])) {
            foreach ($config["anchorAttributes"] as $key => $value) {
                $attrStr .= $key . "=\"" . $value . "\" ";
            }
        }
        if (!empty($config["currAttr"]) && is_array($config["currAttr"])) {
            foreach ($config["currAttr"] as $key => $value) {
                $currAttr .= $key . "=\"" . $value . "\" ";
            }
        }
        if ($padding < $lastPage) {
            if (1 < $page) {
                $offset = $page < $padding ? $page : ($page + $padding < $lastPage ? $page : $lastPage - $padding);
                if ($offset != 1) {
                    if ($page != 1) {
                        $pages .= $config["htmlWrapBefore"] . "<a class='page-link' " . $attrStr . " href=\"" . sprintf($href, "1") . "\"" . ">1</a>" . $config["htmlWrapAfter"];
                    } else {
                        $pages .= $config["htmlCurrBefore"] . "<a class='page-link' " . $currAttr . " href=\"" . sprintf($href, "1") . "\"" . ">1</a>" . $config["htmlCurrAfter"];
                    }
                    $pages .= $config["htmlWrapBefore"] . "<a " . $attrStr . " class='page-link' " . " href=\"" . sprintf($href, $page - 1) . "\"> " . $config["prevText"] . "</a>" . $config["htmlWrapAfter"];
                }
            } else {
                $offset = 1;
            }
            $limit = $lastPage < ($limit = $padding + $offset) ? $lastPage + 1 : $limit;
            if ($limit - $offset < $padding) {
                $offset = $limit - $padding;
            }
        } else {
            $offset = 1;
            $limit = $lastPage;
        }
        if (1 < $offset - $padding / 2) {
            $offset = $offset - floor($padding / 2);
        }
        if ($page == 0) {
            $page++;
        }
        for ($i = $offset; $i <= $limit; $i++) {
            if ($page != $i) {
                $pages .= $config["htmlWrapBefore"] . "<a class='page-link' " . $attrStr . " href=\"" . sprintf($href, $i) . "\"" . ">" . $i . "</a>" . $config["htmlWrapAfter"];
            } else {
                $pages .= $config["htmlCurrBefore"] . "<a class='page-link' " . $currAttr . " href=\"" . sprintf($href, $i) . "\"" . ">" . $i . "</a>" . $config["htmlCurrAfter"];
            }
        }
        if ($limit < $lastPage) {
            $pages .= " " . $config["htmlWrapBefore"] . "<a class='page-link' " . $attrStr . " href=\"" . sprintf($href, $page + 1) . "\"> " . $config["nextText"] . "</a>" . $config["htmlWrapAfter"];
            $pages .= " " . $config["htmlWrapBefore"] . "<a class='page-link' " . $attrStr . " href=\"" . sprintf($href, $lastPage) . "\"";
            if ($page == $lastPage) {
                $pages .= " id=\"current\"";
            }
            $pages .= "> " . $lastPage . "</a>" . $config["htmlWrapAfter"];
        }
        $this->paginationLinks = $pages;
        $this->ci->db->limit($config["inPage"], ($page - 1) * $config["inPage"]);
        return $this->load_all($userQueryParts, $config["return"], false);
    }
    public function pagination_showing($totalRows, $take, $currPage)
    {
        return ($currPage - 1) * $take + 1 . " - " . (ceil($totalRows / $take) == $currPage ? $totalRows : $currPage * $take) . " " . $this->ci->lang->line("of") . " " . $totalRows . " " . $this->ci->lang->line("items");
    }
    public function insert($skipValidation = false)
    {
        if ($this->builtInLogs) {
            $this->_fields["createdOn"] = date("Y-m-d H:i:s");
            $this->_fields["createdBy"] = $this->ci->is_auth->get_user_id();
            $this->_fields["modifiedOn"] = date("Y-m-d H:i:s");
            $this->_fields["modifiedBy"] = $this->ci->is_auth->get_user_id();
        }
        if ($skipValidation || $this->validate()) {
            array_walk($this->_fields, [$this, "set_mysql_nulls"]);
            if ($this->_fields[$this->_pk] < 1) {
                unset($this->_fields[$this->_pk]);
            }
            $this->ci->db->set($this->_fields)->insert($this->_table);
            $this->_fields[$this->_pk] = $this->ci->db->insert_id();
            if (0 < $this->ci->db->affected_rows()) {
                $this->log_action("insert");
                $this->log_action("update");
                return true;
            }
            return false;
        }
        return false;
    }
    public function insert_batch($set)
    {
        return $this->ci->db->insert_batch($this->_table, $set);
    }
    protected function reset_write()
    {
        $ar_reset_items = ["qb_set" => [], "qb_from" => [], "qb_where" => [], "qb_like" => [], "qb_orderby" => [], "qb_keys" => [], "qb_limit" => false, "qb_order" => false];
        foreach ($ar_reset_items as $item => $reset) {
            $this->ci->db->{$item} = $reset;
        }
    }
    public function insert_batch_ignore($set)
    {
        if (!is_null($set)) {
            $this->ci->db->set_insert_batch($set);
        }
        $sql = "INSERT IGNORE INTO " . $this->ci->db->protect_identifiers($this->_table) . " (" . implode($this->ci->db->qb_keys, ", ") . ") VALUES " . implode($this->ci->db->qb_set, ", ");
        $this->reset_write();
        return $this->ci->db->query($sql);
    }
    private function _insert_on_duplicate_update_batch($table, $keys, $values)
    {
        foreach ($keys as $key) {
            $update_fields[] = $key . "=VALUES(" . $key . ")";
        }
        return "INSERT INTO " . $table . " (" . implode(", ", $keys) . ") VALUES " . implode(", ", $values) . " ON DUPLICATE KEY UPDATE " . implode(", ", $update_fields);
    }
    public function escape_universal_search_keyword($q)
    {
        $method = $this->ci->db->dbdriver . "_escape_universal_search_keyword";
        return $this->{$method}($q);
    }
    public function mysql_escape_universal_search_keyword($q)
    {
        return true;
    }
    public function sqlsrv_escape_universal_search_keyword($q)
    {
        $q1 = str_replace("'", "''", $q);
        $q2 = "";
        for ($i = 0; $i < strlen($q1); $i++) {
            if ($q1[$i] === "[") {
                $q2 .= "\\[";
            } else {
                if ($q1[$i] === "]") {
                    $q2 .= "\\]";
                } else {
                    $q2 .= $q1[$i];
                }
            }
        }
        return $q2;
    }
    public function insert_on_duplicate_key_update($dataSet = NULL, $keys = NULL)
    {
        $method = $this->ci->db->dbdriver . "_insert_on_duplicate_key_update";
        return $this->{$method}($dataSet, $keys);
    }
    private function mysql_insert_on_duplicate_key_update($dataSet)
    {
        ksort($dataSet);
        $columns = array_keys($dataSet);
        $sql = "INSERT INTO " . $this->_table . " (" . implode(", ", $columns) . ")" . "VALUES(";
        foreach ($dataSet as $value) {
            $sql .= $this->ci->db->escape($value) . ", ";
        }
        $sql = substr($sql, 0, -2) . ") ON DUPLICATE KEY UPDATE ";
        foreach ($dataSet as $k => $v) {
            $sql .= $k . " = " . $this->ci->db->escape($v) . ", ";
        }
        $sql = substr($sql, 0, -2);
        return $this->ci->db->query($sql);
    }
    private function mysqli_insert_on_duplicate_key_update($dataSet)
    {
        return $this->mysql_insert_on_duplicate_key_update($dataSet);
    }
    private function sqlsrv_insert_on_duplicate_key_update($dataSet, $keys)
    {
        $where = [];
        foreach ($keys as $k) {
            $where[] = [$k, $dataSet[$k]];
        }
        $oldRow = $this->load(compact("where"));
        if (is_array($oldRow)) {
            $oldRow = array_merge($oldRow, $dataSet);
            $this->parse_user_calls(compact("where"));
            foreach ($keys as $k) {
                unset($oldRow[$k]);
            }
            if (isset($oldRow[$this->_pk])) {
                unset($oldRow[$this->_pk]);
            }
            return $this->ci->db->update($this->_table, $oldRow);
        } else {
            return $this->ci->db->set($dataSet)->insert($this->_table);
        }
    }
    private function sqlsrv08_insert_on_duplicate_key_update($dataSet, $keys)
    {
        $where = [];
        foreach ($keys as $k) {
            $where[] = [$k, $dataSet[$k]];
        }
        $oldRow = $this->load(compact("where"));
        if (is_array($oldRow)) {
            $oldRow = array_merge($oldRow, $dataSet);
            $this->parse_user_calls(compact("where"));
            foreach ($keys as $k) {
                unset($oldRow[$k]);
            }
            if (isset($oldRow[$this->_pk])) {
                unset($oldRow[$this->_pk]);
            }
            return $this->ci->db->update($this->_table, $oldRow);
        } else {
            return $this->ci->db->set($dataSet)->insert($this->_table);
        }
    }
    public function insert_on_duplicate_update_batch($dataSet = NULL, $keys = NULL)
    {
        if (!is_array($dataSet) || empty($dataSet)) {
            return false;
        }
        $method = $this->ci->db->dbdriver . "_insert_on_duplicate_update_batch";
        return $this->{$method}($dataSet, $keys);
    }
    private function sqlsrv08_insert_on_duplicate_update_batch($dataSet, $keys)
    {
        return $this->sqlsrv_insert_on_duplicate_update_batch($dataSet, $keys);
    }
    private function sqlsrv_insert_on_duplicate_update_batch($dataSet, $keys)
    {
        if (is_string($keys)) {
            $key = $keys;
            $newDataSet = [];
            $keyValues = [];
            while ($row = array_shift($dataSet)) {
                $keyValues[] = $row[$key];
                $newDataSet[md5($row[$key])] = $row;
            }
            unset($row);
            $oldDataSetQuery = $this->ci->db->select(implode(", ", array_keys(current($newDataSet))))->where_in($key, $keyValues)->get($this->_table);
            foreach ($oldDataSetQuery->result_array() as $row) {
                $currentKeyIndex = md5($row[$key]);
                $newDataSet[$currentKeyIndex] = array_merge($row, $newDataSet[$currentKeyIndex]);
            }
            $oldDataSetQuery = $this->ci->db->where_in($key, $keyValues)->delete($this->_table);
            return $this->insert_batch($newDataSet);
        } else {
            if (is_array($keys)) {
                $newDataSet = [];
                $keyValues = [];
                while ($row = array_shift($dataSet)) {
                    $hashKey = "";
                    foreach ($keys as $key) {
                        $keyValues[$key][] = $row[$key];
                        $hashKey .= md5($row[$key]);
                    }
                    $newDataSet[$hashKey] = $row;
                }
                unset($row);
                $whereIn = ["where_in" => []];
                foreach ($keys as $key) {
                    $whereIn["where_in"][] = [$key, array_unique($keyValues[$key])];
                }
                $this->parse_user_calls($whereIn);
                $oldDataSetQuery = $this->ci->db->select(implode(", ", array_keys(current($newDataSet))))->get($this->_table);
                foreach ($oldDataSetQuery->result_array() as $row) {
                    $currentKeyIndex = "";
                    foreach ($keys as $key) {
                        $currentKeyIndex .= md5($row[$key]);
                    }
                    $newDataSet[$currentKeyIndex] = array_merge($row, $newDataSet[$currentKeyIndex]);
                }
                $this->parse_user_calls($whereIn);
                $oldDataSetQuery = $this->ci->db->delete($this->_table);
                return $this->insert_batch($newDataSet);
            } else {
                return false;
            }
        }
    }
    private function mysqli_insert_on_duplicate_update_batch($set)
    {
        return $this->mysql_insert_on_duplicate_update_batch($set);
    }
    private function mysql_insert_on_duplicate_update_batch($set)
    {
        $table = $this->_table;
        if (!is_null($set)) {
            $this->ci->db->set_insert_batch($set);
        }
        if (count($this->ci->db->qb_set) == 0) {
            if ($this->ci->db->db_debug) {
                return $this->ci->db->display_error("db_must_use_set");
            }
            return false;
        }
        if ($table == "") {
            if (!isset($this->ci->db->qb_from[0])) {
                if ($this->ci->db->db_debug) {
                    return $this->ci->db->display_error("db_must_set_table");
                }
                return false;
            }
            $table = $this->ci->db->qb_from[0];
        }
        $i = 0;
        $total = count($this->ci->db->qb_set);
        while ($i < $total) {
            $sql = $this->_insert_on_duplicate_update_batch($this->ci->db->protect_identifiers($table, true, NULL, false), $this->ci->db->qb_keys, array_slice($this->ci->db->qb_set, $i, 100));
            $this->ci->db->query($sql);
            $i = $i + 100;
        }
        $this->reset_write();
        return true;
    }
    public function update($skipValidation = false, $where = [])
    {
        array_walk($this->_fields, [$this, "set_mysql_nulls"]);
        $data = $this->_fields;
        if ($this->builtInLogs) {
            $data["modifiedOn"] = date("Y-m-d H:i:s");
            $data["modifiedBy"] = $this->ci->is_auth->get_user_id();
            $this->_fields["modifiedOn"] = $data["modifiedOn"];
            $this->_fields["modifiedBy"] = $data["modifiedBy"];
            unset($data["createdOn"]);
            unset($data["createdBy"]);
        }
        unset($data[$this->_pk]);
        if ($skipValidation || $this->validate()) {
            if (empty($where)) {
                if ($this->ci->db->where([$this->_pk => $this->_fields[$this->_pk]])->update($this->_table, $data)) {
                    $this->log_action("update");
                    return true;
                }
                return false;
            }
            if ($this->ci->db->where($where)->update($this->_table, $data)) {
                $this->log_action("update");
                return true;
            }
            return false;
        }
        return false;
    }
    public function delete($userQueryParts = [])
    {
        $result = false;
        if (is_numeric($userQueryParts)) {
            if ($result = $this->ci->db->delete($this->_table, [$this->_pk => $userQueryParts], 1)) {
                $result = $result && $this->ci->db->affected_rows();
                $this->log_action("delete", $userQueryParts);
            }
        } else {
            $this->parse_user_calls($userQueryParts);
            $result = $this->ci->db->delete($this->_table);
        }
        if ($result) {
            return $this->ci->db->affected_rows();
        }
        return false;
    }
    protected function log_action($action, $id = "", $userMaker = false)
    {
        if (isset($this->ci->is_auth) && !$userMaker) {
            $userMaker = $this->ci->is_auth->get_user_id();
        }
        if (isset($this->ci->db->action_logging) && $this->ci->db->action_logging && $userMaker && 0 < $this->_fields[$this->_pk]) {
            $log = ["user_id" => $userMaker, "model" => $this->modelName, "action" => $action, "recordId" => empty($id) ? $this->_fields[$this->_pk] : $id];
            $this->ci->db->insert("audit_logs", $log);
        }
    }
    public function log_built_in_last_action($id, $userMaker = false, $modifiedByChannel = NULL)
    {
        if (!$userMaker) {
            $userMaker = $this->ci->is_auth->get_user_id();
        }
        $data = ["modifiedOn" => date("Y-m-d H:i:s"), "modifiedBy" => $userMaker];
        if (isset($modifiedByChannel)) {
            $data["modifiedByChannel"] = $modifiedByChannel;
        }
        $this->reset_write();
        $this->ci->db->where("id", $id);
        $this->ci->db->update($this->_table, $data);
    }
    public function load_last_action_log($id)
    {
        $method = $this->ci->db->dbdriver . "_load_last_action_log";
        return $this->{$method}($id);
    }
    private function mysql_load_last_action_log($id)
    {
        $this->mysqli_load_last_action_log($id);
    }
    private function mysqli_load_last_action_log($id)
    {
        $actionLogs = [];
        $_defaultLog = ["on" => "---", "by" => "---", "email" => "---", "user_id" => "---"];
        if ($this->builtInLogs) {
            if ($id != $this->_fields[$this->_pk]) {
                $sql = "SELECT user_profiles.user_id, 'insert' as action, " . $this->_table . ".createdOn as created, " . "CONCAT(user_profiles.firstName, ' ', user_profiles.lastName) as fullName, users.username, users.email ";
                $sql .= "FROM " . $this->_table . " ";
                $sql .= "LEFT JOIN users ON users.id = " . $this->_table . ".createdBy ";
                $sql .= "LEFT JOIN user_profiles ON user_profiles.user_id = " . $this->_table . ".createdBy ";
                $sql .= "WHERE " . $this->_table . "." . $this->_pk . " = '" . $id . "' ";
                $sql .= "UNION ";
                $sql .= "SELECT user_profiles.user_id, 'update' as action, " . $this->_table . ".modifiedOn as created, " . "CONCAT(user_profiles.firstName, ' ', user_profiles.lastName) as fullName, users.username, users.email ";
                $sql .= "FROM " . $this->_table . " ";
                $sql .= "LEFT JOIN users ON users.id = " . $this->_table . ".modifiedBy ";
                $sql .= "LEFT JOIN user_profiles ON user_profiles.user_id = " . $this->_table . ".modifiedBy ";
                $sql .= "WHERE " . $this->_table . "." . $this->_pk . " = '" . $id . "'";
            } else {
                $sql = "SELECT user_profiles.user_id, 'insert' as action, '" . $this->_fields["createdOn"] . "' as created, " . "CONCAT(user_profiles.firstName, ' ', user_profiles.lastName) as fullName, users.username, users.email ";
                $sql .= "FROM users ";
                $sql .= "LEFT JOIN user_profiles ON user_profiles.user_id = users.id ";
                $sql .= "WHERE users.id = '" . $this->_fields["createdBy"] . "'";
                $sql .= " UNION ";
                $sql .= "SELECT user_profiles.user_id, 'update' as action, '" . $this->_fields["modifiedOn"] . "' as created, " . "CONCAT(user_profiles.firstName, ' ', user_profiles.lastName) as fullName, users.username, users.email ";
                $sql .= "FROM users ";
                $sql .= "LEFT JOIN user_profiles ON user_profiles.user_id = users.id ";
                $sql .= "WHERE users.id = '" . $this->_fields["modifiedBy"] . "'";
            }
            $query = $this->ci->db->query($sql);
        } else {
            $vName = $this->ci->db->last_action_log_view;
            $this->ci->db->select($vName . ".user_id, " . $vName . ".action, " . $vName . ".created, " . $vName . ".fullName, " . $vName . ".username, " . $vName . ".email");
            $this->ci->db->where("model", $this->modelName)->where("recordId", $id);
            $query = $this->ci->db->get("audit_log_last_action");
        }
        foreach ($query->result_array() as $log) {
            $actionLogs[$log["action"]] = ["on" => date("Y-m-d H:i", strtotime($log["created"])), "by" => $log["fullName"], "email" => $log["email"], "user_id" => $log["user_id"]];
        }
        if (!isset($actionLogs["insert"])) {
            $actionLogs["insert"] = $_defaultLog;
        }
        if (!isset($actionLogs["update"])) {
            $actionLogs["update"] = $_defaultLog;
        }
        return $actionLogs;
    }
    public function sqlsrv_load_last_action_log($id)
    {
        $actionLogs = [];
        $_defaultLog = ["on" => "---", "by" => "---", "email" => "---", "user_id" => "---"];
        if ($this->builtInLogs) {
            if ($id != $this->_fields[$this->_pk]) {
                $sql = "SELECT user_profiles.user_id, 'insert' as action, " . $this->_table . ".createdOn as created, " . "(user_profiles.firstName + ' ' + user_profiles.lastName) as fullName, users.username, users.email ";
                $sql .= "FROM " . $this->_table . " ";
                $sql .= "LEFT JOIN users ON users.id = " . $this->_table . ".createdBy ";
                $sql .= "LEFT JOIN user_profiles ON user_profiles.user_id = " . $this->_table . ".createdBy ";
                $sql .= "WHERE " . $this->_table . "." . $this->_pk . " = '" . $id . "' ";
                $sql .= "UNION ";
                $sql .= "SELECT user_profiles.user_id, 'update' as action, " . $this->_table . ".modifiedOn as created, " . "(user_profiles.firstName + ' ' + user_profiles.lastName) as fullName, users.username, users.email ";
                $sql .= "FROM " . $this->_table . " ";
                $sql .= "LEFT JOIN users ON users.id = " . $this->_table . ".modifiedBy ";
                $sql .= "LEFT JOIN user_profiles ON user_profiles.user_id = " . $this->_table . ".modifiedBy ";
                $sql .= "WHERE " . $this->_table . "." . $this->_pk . " = '" . $id . "'";
            } else {
                $sql = "SELECT user_profiles.user_id, 'insert' as action, '" . $this->_fields["createdOn"] . "' as created, " . "(user_profiles.firstName + ' ' + user_profiles.lastName) as fullName, users.username, users.email ";
                $sql .= "FROM users ";
                $sql .= "LEFT JOIN user_profiles ON user_profiles.user_id = users.id ";
                $sql .= "WHERE users.id = '" . $this->_fields["createdBy"] . "'";
                $sql .= " UNION ";
                $sql .= "SELECT user_profiles.user_id, 'update' as action, '" . $this->_fields["modifiedOn"] . "' as created, " . "(user_profiles.firstName + ' ' + user_profiles.lastName) as fullName, users.username, users.email ";
                $sql .= "FROM users ";
                $sql .= "LEFT JOIN user_profiles ON user_profiles.user_id = users.id ";
                $sql .= "WHERE users.id = '" . $this->_fields["modifiedBy"] . "'";
            }
            $query = $this->ci->db->query($sql);
        } else {
            $vName = $this->ci->db->last_action_log_view;
            $this->ci->db->select($vName . ".user_id, " . $vName . ".action, " . $vName . ".created, " . $vName . ".fullName, " . $vName . ".username, " . $vName . ".email");
            $this->ci->db->where("model", $this->modelName)->where("recordId", $id);
            $query = $this->ci->db->get("audit_log_last_action");
        }
        foreach ($query->result_array() as $log) {
            $actionLogs[$log["action"]] = ["on" => date("Y-m-d H:i", strtotime($log["created"])), "by" => $log["fullName"], "email" => $log["email"], "user_id" => $log["user_id"]];
        }
        if (!isset($actionLogs["insert"])) {
            $actionLogs["insert"] = $_defaultLog;
        }
        if (!isset($actionLogs["update"])) {
            $actionLogs["update"] = $_defaultLog;
        }
        return $actionLogs;
    }
    public function sqlsrv08_load_last_action_log($id)
    {
        return $this->sqlsrv_load_last_action_log($id);
    }
    public function combinedUnique($check, $combinees)
    {
        $_combinees = [];
        if (is_array($combinees)) {
            $combinees = array_intersect($combinees, $this->_fieldsNames);
            foreach ($combinees as $combinee) {
                $_combinees[$combinee] = $this->get_field($combinee);
            }
        } else {
            if (in_array((string) $combinees, $this->_fieldsNames)) {
                $_combinees[(string) $combinees] = $this->get_field($combinees);
            }
        }
        return $this->isUnique(array_merge($check, $_combinees));
    }
    public function isUnique($fields)
    {
        if (is_array($fields) && 0 < count($fields)) {
            $fields = array_intersect_key($fields, $this->_fields);
            foreach ($fields as $key => $value) {
                if ($value === NULL) {
                    $this->ci->db->where($key . " IS NULL", NULL, true);
                } else {
                    $this->ci->db->where($key, $value);
                }
            }
            if (empty($this->ci->db->qb_where)) {
                return true;
            }
            if (!is_null($this->_fields[$this->_pk])) {
                $this->ci->db->where($this->_pk . " != '" . $this->_fields[$this->_pk] . "'");
            }
        }
        return $this->ci->db->count_all_results($this->_table) == 0;
    }
    public function dispatchMethod($method, $params = [])
    {
        count($params);
        switch (count($params)) {
            case 0:
                return $this->{$method}();
                break;
            case 1:
                return $this->{$method}($params[0]);
                break;
            case 2:
                return $this->{$method}($params[0], $params[1]);
                break;
            case 3:
                return $this->{$method}($params[0], $params[1], $params[2]);
                break;
            case 4:
                return $this->{$method}($params[0], $params[1], $params[2], $params[3]);
                break;
            case 5:
                return $this->{$method}($params[0], $params[1], $params[2], $params[3], $params[4]);
                break;
            default:
                return call_user_func_array([$this, $method], $params);
        }
    }
    public function set_validation_errors($validationErrors)
    {
        $this->validationErrors = $validationErrors;
    }
    public function get_error($fields, $openingTag = "", $closingTag = "")
    {
        if (!empty($openingTag)) {
            if (is_array($openingTag)) {
                $output = $openingTag["before"] . "%s" . $openingTag["after"];
            } else {
                $output = $openingTag . "%s" . $closingTag;
            }
        } else {
            $output = "<span class=\"error red\">%s</span>";
        }
        if (is_array($fields)) {
            $fields = array_intersect($fields, $this->_fieldsNames);
            foreach ($fields as $field) {
                if (isset($this->validationErrors[$field])) {
                    echo sprintf($output, $this->validationErrors[$field]);
                }
            }
        } else {
            if (in_array((string) $fields, $this->_fieldsNames) && isset($this->validationErrors[$fields])) {
                return sprintf($output, $this->validationErrors[$fields]);
            }
        }
    }
    public function invalidate($field, $value = true)
    {
        if (!is_array($this->validationErrors)) {
            $this->validationErrors = [];
        }
        $this->validationErrors[$field] = $value;
    }
    public function is_valid()
    {
        return count($this->validationErrors) < 1;
    }
    public function validate()
    {
        $this->validationErrors = [];
        $data = $this->get("_fields");
        if (!class_exists("Validation")) {
            $this->ci->load->library("Validation");
        }
        $validation_function = Validation::getInstance();
        $validation =& $validation_function;
        foreach ($this->validate as $fieldName => $ruleSet) {
            if (!is_array($ruleSet) || is_array($ruleSet) && isset($ruleSet["rule"])) {
                $ruleSet = [$ruleSet];
            }
            $default = ["allowEmpty" => NULL, "required" => NULL, "rule" => "blank", "last" => false, "on" => NULL];
            foreach ($ruleSet as $index => $validator) {
                if (!is_array($validator)) {
                    $validator = ["rule" => $validator];
                }
                $validator = array_merge($default, $validator);
                if (isset($validator["message"])) {
                    $message = $validator["message"];
                } else {
                    $message = $this->ci->lang->line("cannot_be_blank_rule");
                }
                if (empty($validator["on"]) || $validator["on"] == "create" && !$exists || $validator["on"] == "update" && $exists) {
                    $required = !isset($data[$fieldName]) && $validator["required"] === true || isset($data[$fieldName]) && empty($data[$fieldName]) && !is_numeric($data[$fieldName]) && $validator["allowEmpty"] === false;
                    if ($required) {
                        $this->invalidate($fieldName, $message);
                        if (!$validator["last"]) {
                        }
                    } else {
                        if (array_key_exists($fieldName, $data)) {
                            if (!(empty($data[$fieldName]) && $data[$fieldName] != "0" && $validator["allowEmpty"] === true)) {
                                if (is_array($validator["rule"])) {
                                    $rule = $validator["rule"][0];
                                    unset($validator["rule"][0]);
                                    $ruleParams = array_merge([$data[$fieldName]], array_values($validator["rule"]));
                                } else {
                                    $rule = $validator["rule"];
                                    $ruleParams = [$data[$fieldName]];
                                }
                                $valid = true;
                                if (method_exists($this, strtolower($rule))) {
                                    $ruleParams[] = $validator;
                                    $ruleParams[0] = [$fieldName => $ruleParams[0]];
                                    $valid = $this->dispatchMethod($rule, $ruleParams);
                                } else {
                                    if (method_exists($validation, $rule)) {
                                        $valid = $validation->dispatchMethod($rule, $ruleParams);
                                    } else {
                                        $valid = sprintf($this->ci->lang->line("cannot_find_validation_rule"), $rule, $fieldName);
                                    }
                                }
                                if (!$valid || is_string($valid) && 0 < strlen($valid)) {
                                    if (is_string($valid) && 0 < strlen($valid)) {
                                        $validator["message"] = $valid;
                                    } else {
                                        if (!isset($validator["message"])) {
                                            if (is_string($index)) {
                                                $validator["message"] = $index;
                                            } else {
                                                if (is_numeric($index) && 1 < count($ruleSet)) {
                                                    $validator["message"] = $index + 1;
                                                } else {
                                                    $validator["message"] = $message;
                                                }
                                            }
                                        }
                                    }
                                    $this->invalidate($fieldName, $validator["message"]);
                                    if (!$validator["last"]) {
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $this->is_valid();
    }
    public function sanitize($data)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = $this->sanitize($v);
            }
            return $data;
        } else {
            $data = trim($data);
            if (get_magic_quotes_gpc()) {
                $data = stripslashes($data);
            }
            $data = mysql_real_escape_string($data);
            return $data;
        }
    }
    public function prep_k_filter(&$filter, &$query, $logic = "", $hijri_calendar_enabled = false, $fix_single_quote = false)
    {
        foreach ($filter["filters"] as &$_filter) {
            $this->remove_cast_from_filter_operator($_filter["operator"], $_filter["field"], $hijri_calendar_enabled);
            if (isset($_filter["function"])) {
                $this->get_filter_value_by_function_name($_filter["field"], $_filter["function"]);
            }
            if ($_filter["operator"] === "empty" || $_filter["operator"] === "not_empty") {
                $this->prepare_empty_check($_filter["operator"], $_filter["field"], $_filter["value"], $_filter["operator"] === "empty" ? true : false);
            }
        }
        unset($_filter);
        $logics = ["" => "where", "and" => "where", "AND" => "where", "or" => "or_where", "OR" => "or_where"];
        if (isset($filter["field"])) {
            extract($filter);
            $query[$logics[$logic]][] = [$field . $this->get_k_operator($operator, $value), $value];
        } else {
            if (is_array($filter) && isset($filter["filters"][1]) && isset($filter["filters"][0])) {
                $filter["filters"] = (array) $filter["filters"];
                $whereString = "(";
                foreach ($filter["filters"] as $value) {
                    if ($hijri_calendar_enabled && in_array($value["field"], ["legal_case_hearings.startDate", "legal_case_hearings.postponedDate", "registrationDate", "crReleasedOn", "board_members.designatedOn", "board_members.tillDate", "expiresOn"])) {
                        $value["value"] = hijriToGregorian($value["value"]);
                    }
                    if (!isset($value["operator"]) || $value["operator"] === "") {
                        $whereString .= " " . $value["field"] . " ";
                    } else {
                        $whereString .= " " . $value["field"] . " " . $this->get_k_operator($value["operator"], $value["value"]) . " " . $this->ci->db->escape($value["value"]) . " ";
                    }
                    $whereString .= $filter["logic"];
                }
                $whereString = substr($whereString, 0, -1 * strlen($filter["logic"]));
                $whereString .= ")";
                $query[$logics[$logic]][] = [$whereString, NULL, false];
            } else {
                if (isset($filter["filters"][0])) {
                    $filter["filters"] = (array) $filter["filters"];
                    extract((array) $filter["filters"][0]);
                    if (isset($value) && is_array($value)) {
                        $array_with_none_value = false;
                        foreach ($value as $value_index => $_value) {
                            if ($_value === "0") {
                                $array_with_none_value = true;
                                unset($value[$value_index]);
                            }
                        }
                        if (!empty($value)) {
                            if (isset($value[0][0]) && $value[0][0] === "'" || isset($value[1][0]) && $value[1][0] === "'") {
                                $array_values = implode(", ", $value);
                            } else {
                                $array_values = implode("', '", $value);
                                $array_values = "'" . $array_values . "'";
                            }
                            if ($array_with_none_value) {
                                $strLogic = !strcmp($operator, "in") ? $field . " IS NULL" : $field . " IS NOT NULL";
                                $noneValueLogic = !strcmp($operator, "in") ? "( " . $field . " IN (" . $array_values . ") OR " . $strLogic . ")" : "( " . $field . " NOT IN (" . $array_values . ") AND " . $strLogic . ")";
                                $query["where"][] = [$noneValueLogic, NULL, false];
                            } else {
                                if (!strcmp($operator, "in")) {
                                    $strLogic = "( " . $field . " IN (" . $array_values . ") " . ")";
                                    $query["where"][] = [$strLogic, NULL, false];
                                } else {
                                    $strLogic = "( " . $field . " NOT IN (" . $array_values . ") OR " . $field . " IS NULL )";
                                    $query["where"][] = [$strLogic, NULL, false];
                                }
                            }
                        } else {
                            $strLogic = !strcmp($operator, "in") ? $field . " IS NULL" : $field . " IS NOT NULL";
                            $query["where"][] = [$strLogic, NULL, false];
                        }
                    } else {
                        if ($hijri_calendar_enabled && in_array($field, ["legal_case_hearings.startDate", "legal_case_hearings.postponedDate", "registrationDate", "crReleasedOn", "board_members.designatedOn", "board_members.tillDate", "expiresOn"])) {
                            $value = hijriToGregorian($value);
                        }
                        if (!isset($filter["filters"][0]["operator"]) || $filter["filters"][0]["operator"] === "") {
                            $query[$logics[$logic]][] = [$field, NULL, false];
                        } else {
                            if ($this->ci->db->dbdriver == "sqlsrv" && isset($value)) {
                                $value = str_replace("[", "[[]", $value);
                                if ($fix_single_quote) {
                                    $value = str_replace("'", "''", $value);
                                }
                            }
                            $query[$logics[$logic]][] = [$field . $this->get_k_operator($operator, $value), $value];
                        }
                    }
                }
            }
        }
    }
    private function remove_cast_from_filter_operator(&$operator, &$field, $hijri_calendar_enabled)
    {
        if (4 < mb_strlen($operator) && mb_substr($operator, 0, 5) === "cast_" && !empty($field)) {
            $operator = mb_substr($operator, 5);
            if (!$this->check_if_field_is_hearings($field, $hijri_calendar_enabled)) {
                $this->add_cast_to_filter_value($field);
            }
        }
    }
    private function check_if_field_is_hearings($field, $hijri_calendar_enabled)
    {
        $is_field_hearings = $hijri_calendar_enabled && in_array($field, ["legal_case_hearings.startDate", "legal_case_hearings.postponedDate", "registrationDate", "crReleasedOn", "board_members.designatedOn", "board_members.tillDate", "expiresOn"]);
        return $is_field_hearings;
    }
    public function add_cast_to_filter_value(&$field)
    {
        if ($this->ci->db->dbdriver == "mysqli") {
            $field = "DATE(" . $field . " )";
        } else {
            if ($this->ci->db->dbdriver == "sqlsrv") {
                $field = "convert(char(16), CONVERT(date, " . $field . " ), 121)";
            }
        }
    }
    public function get_k_operator($operator, &$value)
    {
        $operators = ["eq" => " =", "neq" => " !=", "lt" => " <", "lte" => " <=", "gt" => " >", "gte" => " >=", "startswith" => " LIKE", "notstartswith" => " NOT LIKE", "endswith" => " LIKE", "contains" => " LIKE", "empty" => " IS NULL", "not_empty" => " IS NOT NULL", "lookUp" => " ="];
        if (in_array($operator, ["startswith", "notstartswith", "endswith", "contains"])) {
            $value = $this->addPercentageCharToOperatorValue($value);
        }
        return $operators[$operator];
    }
    private function addPercentageCharToOperatorValue($value)
    {
        if ($this->ci->db->dbdriver == "mysqli") {
            if (strpos($value, "%") !== false) {
                $value = str_replace("%", "%\\", $value);
            }
        } else {
            if ($this->ci->db->dbdriver == "sqlsrv" && strpos($value, "%") !== false) {
                $value = str_replace("%", "[%]", $value);
            }
        }
        $value = "%" . $value . "%";
        return $value;
    }
    public function set_mysql_nulls(&$val, $key)
    {
        if (empty($val) && !is_numeric($val) && in_array($key, $this->allowedNulls)) {
            $val = NULL;
        }
    }
    public function count_field_rows($_table, $field, $id)
    {
        $table = $this->_table;
        $this->_table = $_table;
        $query = [];
        $query["select"] = ["COUNT(0) as numRows"];
        $query["where"] = [$field, $id];
        $data = $this->load($query);
        $this->_table = $table;
        return $data["numRows"];
    }
    public function disable_builtin_logs()
    {
        $this->builtInLogs = false;
    }
    public function get_validation_errors($lookup_validate = false)
    {
        $response = [];
        if (!$this->validate() && $lookup_validate) {
            $response = $this->get("validationErrors");
            foreach ($response as $key => $val) {
                foreach ($lookup_validate as $key1 => $val1) {
                    if ($key == $key1) {
                        unset($response[$key]);
                    }
                }
            }
            $response = $response + $lookup_validate;
        } else {
            if (!$lookup_validate) {
                $response = $this->get("validationErrors");
            } else {
                $response = $lookup_validate;
            }
        }
        return $response;
    }
    public function get_lookup_validation_errors($lookupInputsToValidate, $data)
    {
        $response = [];
        foreach ($lookupInputsToValidate as $key => $value) {
            if (substr($value["input_name"], -2) == "[]" && substr($value["error_field"], -2) == "[]") {
                $value["input_name"] = substr($value["input_name"], 0, -2);
                $value["error_field"] = substr($value["error_field"], 0, -2);
                if (isset($data[$value["input_name"]])) {
                    foreach ($data[$value["input_name"]] as $k => $val) {
                        if ($data[$value["input_name"]][$k] && isset($data[$value["error_field"]]) && !$data[$value["error_field"]][$k]) {
                            $response["validationErrors"][$value["error_field"] . "_" . ($k + 1)] = sprintf($this->ci->lang->line($value["message"]["main_var"]), $data[$value["input_name"]][$k]);
                        }
                    }
                }
            } else {
                if (isset($data[$value["input_name"]]) && $data[$value["input_name"]] && isset($data[$value["error_field"]]) && !$data[$value["error_field"]] || isset($data[$value["input_name"]]) && $data[$value["input_name"]] && !isset($data[$value["error_field"]])) {
                    if (substr($value["error_field"], -2) == "[]") {
                        $value["error_field"] = substr($value["error_field"], 0, -2);
                    }
                    $response["validationErrors"][$value["error_field"]] = !isset($value["message"]["lookup_for"]) ? sprintf($this->ci->lang->line($value["message"]["main_var"]), $data[$value["input_name"]]) : sprintf($this->ci->lang->line($value["message"]["main_var"]), $this->ci->lang->line($value["message"]["lookup_for"]), $data[$value["input_name"]]);
                }
            }
        }
        return isset($response["validationErrors"]) && $response["validationErrors"] ? $response["validationErrors"] : false;
    }
    public function validate_multiple_email($check)
    {
        $emails = explode(";", $check["email"]);
        $matches = NULL;
        foreach ($emails as $email) {
            if (trim($email) != "" && !filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
                return false;
            }
        }
        return true;
    }
    public function validate_dates_combination($check, $dates)
    {
        $response = true;
        foreach ($dates as $key => $date_info) {
            if (isset($date_info["min_for"])) {
                foreach ($date_info["min_for"] as $val) {
                    if (isset($_POST[$val]) && $_POST[$val] && $check[$key]) {
                        $_POST[$val] = date("Y-m-d", strtotime($_POST[$val]));
                        $check[$key] = date("Y-m-d", strtotime($check[$key]));
                        if ($_POST[$val] < $check[$key]) {
                            $response = false;
                        }
                    }
                }
            }
            if (isset($date_info["max_for"])) {
                foreach ($date_info["max_for"] as $val) {
                    if (isset($_POST[$val]) && $_POST[$val] && $check[$key]) {
                        $_POST[$val] = date("Y-m-d", strtotime($_POST[$val]));
                        $check[$key] = date("Y-m-d", strtotime($check[$key]));
                        if ($check[$key] < $_POST[$val]) {
                            $response = false;
                        }
                    }
                }
            }
        }
        return $response;
    }
    public function count_total_matching_rows($query, $extra_select = "")
    {
        $method = $this->ci->db->dbdriver . "_count_total_matching_rows";
        return $this->{$method}($query, $extra_select);
    }
    public function mysqli_count_total_matching_rows($query, $extra_select)
    {
        $this->ci->db->_protect_identifiers = false;
        $this->ci->db->_force_escape_string_values = true;
        unset($query["limit"]);
        unset($query["order_by"]);
        $query["select"] = ["count(*) as `count`"];
        $result = $this->load($query);
        $this->ci->db->_protect_identifiers = true;
        $this->ci->db->_force_escape_string_values = false;
        return $result["count"];
    }
    public function sqlsrv_count_total_matching_rows($query, $extra_select)
    {
        unset($query["limit"]);
        unset($query["order_by"]);
        $query["select"] = ["COUNT(*) OVER() AS TotalMatchingRows"];
        $result = $this->load($query);
        return $result["TotalMatchingRows"] ?? 0;
    }
    private function get_filter_value_by_function_name(&$field, $function_name)
    {
        if (!empty($field) && !empty($function_name)) {
            $field = $this->{$function_name}();
        }
    }
    public function insert_id($table = "")
    {
        if (!$table) {
            return false;
        }
        $method = $this->ci->db->dbdriver . "_insert_id";
        return $this->{$method}($table);
    }
    public function mysqli_insert_id($table)
    {
        $data = ["id" => NULL];
        $this->ci->db->insert($table, $data);
        if (0 < $this->ci->db->affected_rows()) {
            return $this->ci->db->insert_id();
        }
        return false;
    }
    public function sqlsrv_insert_id($table)
    {
        $this->ci->db->simple_query("INSERT INTO " . $table . " DEFAULT VALUES");
        return $this->ci->db->insert_id();
    }
    public function prepare_empty_check(&$operator, &$field, &$value, $is_empty)
    {
        $operator = "";
        $value = "";
        $field = $is_empty ? "NULLIF(" . $field . ", ' ') IS NULL" : "NULLIF(" . $field . ", ' ') IS NOT NULL";
    }
    public function identity_insert_on($table)
    {
        if ($this->ci->db->dbdriver == "sqlsrv") {
            $this->ci->db->simple_query("SET IDENTITY_INSERT " . $table . " ON");
        }
    }
    public function identity_insert_off($table)
    {
        if ($this->ci->db->dbdriver == "sqlsrv") {
            $this->ci->db->simple_query("SET IDENTITY_INSERT " . $table . " OFF");
        }
    }
    public function refresh_mv_data($mv_table, $mv_data)
    {
        if ($this->ci->db->dbdriver == "mysqli") {
            $this->ci->db->set($mv_data["dataset"]);
            $this->ci->db->where($mv_data["where"]);
            return $this->ci->db->update($mv_table);
        }
        return true;
    }
    public function update_recent_ids($case_id, $category)
    {
        $recent_cases = unserialize($this->ci->user_preference->get_value("recent_cases"));
        if (isset($recent_cases[$category])) {
            if ($recent_cases[$category][0] != $case_id) {
                $new_array[0] = (int) $case_id;
                for ($i = 1; $i <= 4; $i++) {
                    $new_array[$i] = array_search($case_id, $recent_cases[$category]) && array_search($case_id, $recent_cases[$category]) < $i ? $recent_cases[$category][$i] : $recent_cases[$category][$i - 1];
                }
            }
        } else {
            $new_array = [$case_id, 0, 0, 0, 0];
        }
        if (isset($new_array)) {
            $recent_cases[$category] = $new_array;
            $this->ci->user_preference->set_value("recent_cases", serialize($recent_cases), true);
        }
    }
    public function trigger_web_hook($event, $data)
    {
        $this->ci->load->library("events");
        $this->ci->load->event($this->modelName == "legal_case" ? "CaseListener" : ($this->modelName == "legal_case_container" ? "MatterContainerListener" : "ContractListener"));
        $data["event_name"] = $event;
        Events::trigger($event, $data);
    }
}

?>