<?php
/*

 */


if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Opinion_comment extends My_Model_Factory
{
}
class mysql_Opinion_comment extends My_Model
{
    protected $modelName = "opinion_comment";
    protected $_table = "opinion_comments";
    protected $_listFieldName = "opinion_id";
    protected $_fieldsNames = ["id", "opinion_id", "comment", "createdOn", "createdBy", "modifiedOn", "modifiedBy", "edited","added_from_channel"];
    protected $builtInLogs = true;
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["opinion_id" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "comment" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "createdOn" => ["required" => true, "allowEmpty" => false, "rule" => "datetime", "message" => $this->ci->lang->line("cannot_be_blank_rule")], "createdBy" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->ci->lang->line("cannot_be_blank_rule")]];
    }
    public function load_comments($id, $show_all = true)
    {
        if (!$show_all) {
            $this->pagination_config_set("inPage", 6);
        }
        $query = [];
        $query["select"] = ["SQL_CALC_FOUND_ROWS opinion_comments.id, opinion_comments.opinion_id, opinion_comments.comment, opinion_comments.createdBy, opinion_comments.createdOn, opinion_comments.modifiedOn, opinion_comments.edited,CONCAT(user_profiles.firstName, ' ',user_profiles.lastName) as created_by_name, CONCAT( modified.firstName, ' ', modified.lastName) AS modified_by_name", false];
        $query["join"] = [["user_profiles", "user_profiles.user_id = opinion_comments.createdBy", "left"], ["user_profiles modified", "modified.user_id = opinion_comments.modifiedBy", "left"]];
        $query["where"] = ["opinion_comments.opinion_id", $id];
        $query["order_by"] = !$show_all ? ["opinion_comments.createdOn desc"] : ["opinion_comments.createdOn asc"];
        $response["records"] = $show_all ? parent::load_all($query) : parent::paginate($query, ["urlPrefix" => ""]);
        if (!$show_all) {
            asort($response["records"]);
        }
        $response["count"] = $this->ci->db->query("SELECT FOUND_ROWS() AS `count`")->row()->count;
        return $response;
    }
    public function load_comment($id)
    {
        $query = [];
        $query["select"] = ["opinion_comments.id, opinion_comments.opinion_id, opinion_comments.comment, opinion_comments.createdBy, opinion_comments.createdOn, opinion_comments.modifiedOn,opinion_comments.edited,CONCAT(user_profiles.firstName, ' ',user_profiles.lastName) as created_by_name, CONCAT( modified.firstName, ' ', modified.lastName) AS modified_by_name", false];
        $query["join"] = [["user_profiles", "user_profiles.user_id = opinion_comments.createdBy", "left"], ["user_profiles modified", "modified.user_id = opinion_comments.modifiedBy", "left"]];
        $query["where"] = ["opinion_comments.id", $id];
        $response = parent::load($query);
        return $response;
    }
    public function load_all_attachments($opinion_id)
    {
        $query = [];
        $query["select"] = ["attachments.id , attachments.comment_id, attachments.document_id , documents.type, documents.name, documents.extension, (case when (documents.type = 'file') then concat(documents.name,'.',documents.extension) else name end) AS full_name", false];
        $query["join"][] = ["opinion_comment_attachments as attachments", "attachments.comment_id = opinion_comments.id", "left"];
        $query["join"][] = ["documents_management_system as documents", "attachments.document_id = documents.id", "inner"];
        $query["where"][] = ["opinion_comments.opinion_id", $opinion_id];
        $response = $this->load_all($query);
        return $response;
    }
    public function regenerate_comment($comment)
    {
        $comment = mb_convert_encoding($comment, "UTF-8", "UTF-8");
        $comment = str_replace("&lt !--[if !supportLists]--&gt;", "", $comment);
        $comment = str_replace("&lt;!--[endif]--&gt;", "", $comment);
        return $comment;
    }
}
class mysqli_Opinion_comment extends mysql_Opinion_comment
{
}
class sqlsrv_Opinion_comment extends mysql_Opinion_comment
{
    public function load_comments($id, $show_all = true)
    {
        if (!$show_all) {
            $this->pagination_config_set("inPage", 6);
        }
        $query = [];
        $query["select"] = ["COUNT(*) OVER() AS total_rows, opinion_comments.id, opinion_comments.opinion_id, opinion_comments.comment, opinion_comments.createdBy, opinion_comments.createdOn, opinion_comments.modifiedOn,opinion_comments.edited,(user_profiles.firstName + ' ' + user_profiles.lastName) as created_by_name, ( modified.firstName + ' ' + modified.lastName) AS modified_by_name ", false];
        $query["join"] = [["user_profiles", "user_profiles.user_id = opinion_comments.createdBy", "left"], ["user_profiles modified", "modified.user_id = opinion_comments.modifiedBy", "left"]];
        $query["where"] = ["opinion_comments.opinion_id", $id];
        $query["order_by"] = !$show_all ? ["opinion_comments.createdOn desc"] : ["opinion_comments.createdOn asc"];
        $response["records"] = $show_all ? parent::load_all($query) : parent::paginate($query, ["urlPrefix" => ""]);
        if (!$show_all) {
            asort($response["records"]);
        }
        $response["count"] = $response["records"][0]["total_rows"] ?? false;
        return $response;
    }
    public function load_comment($id)
    {
        $query = [];
        $query["select"] = ["opinion_comments.id, opinion_comments.opinion_id, opinion_comments.comment, opinion_comments.createdBy, opinion_comments.createdOn, opinion_comments.modifiedOn,opinion_comments.edited,(user_profiles.firstName + ' ' + user_profiles.lastName) as created_by_name, ( modified.firstName + ' ' + modified.lastName) AS modified_by_name ", false];
        $query["join"] = [["user_profiles", "user_profiles.user_id = opinion_comments.createdBy", "left"], ["user_profiles modified", "modified.user_id = opinion_comments.modifiedBy", "left"]];
        $query["where"] = ["opinion_comments.id", $id];
        $response = parent::load($query);
        return $response;
    }
    public function load_all_attachments($opinion_id)
    {
        $query = [];
        $query["select"] = ["attachments.id , attachments.comment_id, attachments.document_id , documents.type, documents.name, documents.extension, (case when (documents.type = 'file') then (documents.name + '.' + documents.extension) else name end) AS full_name", false];
        $query["join"][] = ["opinion_comment_attachments as attachments", "attachments.comment_id = opinion_comments.id", "left"];
        $query["join"][] = ["documents_management_system as documents", "attachments.document_id = documents.id", "left"];
        $query["where"][] = ["opinion_comments.opinion_id", $opinion_id];
        $response = $this->load_all($query);
        return $response;
    }
}

?>