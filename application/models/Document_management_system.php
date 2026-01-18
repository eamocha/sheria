<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Document_management_system extends My_Model_Factory
{
}
class mysql_Document_management_system extends My_Model
{
    protected $modelName = "document_management_system";
    protected $_table = "documents_management_system";
    protected $_listFieldName = "name";
    protected $_fieldsNames = ["id", "type", "name", "extension", "parent", "lineage", "size", "version", "private", "document_type_id", "document_status_id", "comment", "module", "module_record_id", "system_document", "visible", "visible_in_cp", "visible_in_ap", "createdOn", "createdBy", "createdByChannel", "initial_version_created_on", "initial_version_created_by", "initial_version_created_by_channel", "modifiedOn", "modifiedBy", "modifiedByChannel", "is_locked", "last_locked_by", "last_locked_by_channel", "last_locked_on"];
    protected $allowedNulls = ["extension", "parent", "lineage", "size", "version", "private", "document_type_id", "document_status_id", "comment", "module_record_id", "initial_version_created_on", "initial_version_created_by", "initial_version_created_by_channel", "last_locked_by", "last_locked_by_channel", "last_locked_on"];
    protected $builtInLogs = false;
    protected $user_id;
    public $viewable_documents_extensions = [["extension" => "pdf", "editable" => true], ["extension" => "jpg", "editable" => false], ["extension" => "jpeg", "editable" => false], ["extension" => "png", "editable" => false], ["extension" => "gif", "editable" => false], ["extension" => "bmp", "editable" => false]];
    public $image_types = ["jpg", "jpeg", "png", "gif", "bmp"];
    public $notA4LChannels = "('AP', 'CP')";
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["name" => ["isRequired" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->ci->lang->line("cannot_be_blank_rule")], "maxLength" => ["rule" => ["maxLength", 255], "message" => sprintf($this->ci->lang->line("max_characters"), 255)]]];
    }
    public function set_user_id($user_id)
    {
        $this->user_id = $user_id;
    }
    public function load_documents($module, $module_record_id, $lineage, $term, $type = false, $visible_in_cp = 0)
    {
        $response["data"] = [];
        $_table = $this->_table;
        $this->_table = "documents_management_system d";
        $query["select"][] = ["d.id, d.type, d.name, d.extension, (case when (d.type = 'file') then concat(d.name,'.',d.extension) else d.name end) AS full_name, d.parent, p.lineage as parent_lineage, d.lineage, d.size, d.version, d.private, d.document_type_id,(SELECT count(id) FROM documents_management_system where documents_management_system.parent =d.id) children_count, d.document_status_id, d.comment, d.module, d.module_record_id, d.system_document, d.visible, d.visible_in_cp, d.visible_in_ap, d.createdOn, d.createdBy, (case when (d.createdByChannel = 'CP') then concat(creatorCP.firstName, ' ', creatorCP.lastName, ' (Portal User)') when (d.createdByChannel = 'AP') then concat(creatorAP.firstName, ' ', creatorAP.lastName, ' (Advisor)') else concat(creatorU.firstName, ' ', creatorU.lastName) end) AS creator_full_name, d.createdByChannel, d.initial_version_created_on, d.initial_version_created_by, d.initial_version_created_by_channel, (case when (d.initial_version_created_On is not null) then d.initial_version_created_On else d.createdOn end) AS display_created_on, (\r\n                    case when (\r\n                      d.initial_version_created_by is not null\r\n                    ) then (\r\n                      case when (\r\n                        d.initial_version_created_by_channel = 'CP'\r\n                      ) then concat(\r\n                        displayIniCreatorCP.firstName, ' ', \r\n                        displayIniCreatorCP.lastName, ' (Portal User)'\r\n                      ) \r\n                      when (\r\n                        d.initial_version_created_by_channel = 'AP'\r\n                      ) then concat(\r\n                        displayIniCreatorAP.firstName, ' ', \r\n                        displayIniCreatorAP.lastName, ' (Advisor)'\r\n                      )\r\n                      else concat(\r\n                        displayIniCreatorU.firstName, ' ', \r\n                        displayIniCreatorU.lastName\r\n                      ) end\r\n                    ) else (\r\n                      case when (d.createdByChannel = 'CP') then concat(\r\n                        displayCreatorCP.firstName, ' ', \r\n                        displayCreatorCP.lastName, ' (Portal User)'\r\n                      )\r\n                      when (d.createdByChannel = 'AP') then concat(\r\n                        displayCreatorAP.firstName, ' ', \r\n                        displayCreatorAP.lastName, ' (Advisor)'\r\n                      )\r\n                       else concat(\r\n                        displayCreatorU.firstName, ' ', displayCreatorU.lastName\r\n                      ) end\r\n                    ) end\r\n                ) AS display_creator_full_name,(\r\n                case when (\r\n                  d.initial_version_created_by_channel is not null\r\n                ) then d.initial_version_created_by_channel else d.createdByChannel end\r\n              ) AS display_created_by_channel,(\r\n                case when (d.modifiedByChannel = 'CP') then concat(\r\n                  modifierCP.firstName, ' ', modifierCP.lastName, \r\n                  ' (Portal User)'\r\n                ) \r\n                when (d.modifiedByChannel = 'AP') then concat(\r\n                    modifierAP.firstName, ' ', modifierAP.lastName, \r\n                    ' (Advisors)'\r\n                  ) \r\n                else concat(\r\n                  modifierU.firstName, ' ', modifierU.lastName\r\n                ) end\r\n              ) AS modifier_full_name,d.modifiedOn, d.modifiedBy, d.modifiedByChannel,CASE WHEN d.type = 'folder' AND d.private = 1 THEN (SELECT CASE WHEN dmuau.recordId IS NOT NULL THEN 1 ELSE 0 END\r\n                FROM document_managment_users dmuau\r\n                WHERE dmuau.user_id = " . $this->user_id . " AND dmuau.recordId = d.id) ELSE (SELECT CASE WHEN dms.private = 1 THEN (SELECT CASE WHEN dmuau.recordId IS NOT NULL THEN 1 ELSE 0 END\r\n                FROM document_managment_users dmuau\r\n                WHERE dmuau.user_id = " . $this->user_id . " AND dmuau.recordId = dms.id) ELSE 1 END\r\n                FROM documents_management_system dms\r\n                WHERE dms.id = d.parent) END as is_accessible"];
        $query["join"] = [["documents_management_system p", "p.id = d.parent", "left"], ["customer_portal_users creatorCP", "creatorCP.id = d.createdBy AND d.createdByChannel = 'CP'", "left"], ["user_profiles creatorU", "creatorU.user_id = d.createdBy AND d.createdByChannel NOT IN " . $this->notA4LChannels, "left"], ["advisor_users creatorAP", "creatorAP.id = d.createdBy AND d.createdByChannel != 'AP'", "left"], ["customer_portal_users displayIniCreatorCP", "displayIniCreatorCP.id = d.initial_version_created_by AND d.initial_version_created_by is not null and d.initial_version_created_by_channel = 'CP'", "left"], ["user_profiles displayIniCreatorU", "displayIniCreatorU.user_id = d.initial_version_created_by AND d.initial_version_created_by is not null and d.initial_version_created_by_channel NOT IN " . $this->notA4LChannels, "left"], ["advisor_users displayIniCreatorAP", "displayIniCreatorAP.id = d.initial_version_created_by AND d.initial_version_created_by is not null and d.initial_version_created_by_channel = 'AP'", "left"], ["customer_portal_users displayCreatorCP", "displayCreatorCP.id = d.createdBy AND d.initial_version_created_by is null and d.createdByChannel = 'CP'", "left"], ["user_profiles displayCreatorU", "displayCreatorU.user_id = d.createdBy AND d.initial_version_created_by is null and d.createdByChannel NOT IN " . $this->notA4LChannels, "left"], ["advisor_users displayCreatorAP", "displayCreatorAP.id = d.createdBy AND d.initial_version_created_by is null and d.createdByChannel = 'AP'", "left"], ["customer_portal_users modifierCP", "modifierCP.id = d.modifiedBy AND d.modifiedByChannel = 'CP'", "left"], ["user_profiles modifierU", "modifierU.user_id = d.modifiedBy AND d.modifiedByChannel NOT IN " . $this->notA4LChannels, "left"], ["advisor_users modifierAP", "modifierAP.id = d.modifiedBy AND d.modifiedByChannel = 'AP'", "left"]];
        if ($module == "contract") {
            $query["select"][] = ["(CASE WHEN contract_approval_submission.status = 'approved' THEN 0 ELSE (CASE WHEN approval_signature_documents.to_be_approved IS NOT NULL THEN approval_signature_documents.to_be_approved ELSE 0 END)END) as to_be_approved,\r\n            (CASE WHEN contract_signature_submission.status = 'signed' THEN 0 ELSE (CASE WHEN approval_signature_documents.to_be_signed IS NOT NULL THEN approval_signature_documents.to_be_signed ELSE 0 END)END) as to_be_signed"];
            $query["join"][] = ["approval_signature_documents", "approval_signature_documents.document_id = d.id", "left"];
            $query["join"][] = ["contract_approval_submission", "contract_approval_submission.contract_id = d.module_record_id", "left"];
            $query["join"][] = ["contract_signature_submission", "contract_signature_submission.contract_id = d.module_record_id", "left"];
        }
        $query["where"] = [["d.module", $module], ["d.visible", 1]];
        $query["where"][] = empty($module_record_id) ? ["d.module_record_id IS NULL"] : ["d.module_record_id", $module_record_id];
        if (empty($lineage)) {
            $module_container = $this->get_document_details(["name" => empty($module_record_id) ? $module . "_container" : $module . "_" . (int) $module_record_id, "system_document" => 1]);
         $parent_lineage = $module_container["lineage"]??"";
        } else {
            $parent_lineage = $lineage;
        }
        if ($type == "file") {
            $query["where"][] = ["d.type", $type];
            $query["like"] = [["p.lineage", $this->ci->db->escape_str($parent_lineage), "after"]];
            if (!empty($term)) {
                $query["where"][] = ["((case when (d.type = 'file') then concat(d.name,'.',d.extension) else d.name end) LIKE '%" . $this->ci->db->escape_str($term) . "%' OR d.comment LIKE '%" . $this->ci->db->escape_str($term) . "%')"];
            }
        } else {
            if (empty($term)) {
                $query["where"][] = ["p.lineage", $parent_lineage];
            } else {
                $query["like"] = [["p.lineage", $this->ci->db->escape_str($parent_lineage), "after"]];
                $query["where"][] = ["((case when (d.type = 'file') then concat(d.name,'.',d.extension) else d.name end) LIKE '%" . $this->ci->db->escape_str($term) . "%' OR d.comment LIKE '%" . $this->ci->db->escape_str($term) . "%')"];
            }
        }
        if ($visible_in_cp) {
            $query["where"][] = ["d.visible_in_cp", 1];
        }
        if ($parent_lineage) {
            if (!empty($term)) {
                $this->ci->load->model("user_profile");
                $this->ci->user_profile->fetch(["user_id" => $this->user_id]);
                if ($this->ci->user_profile->get_field("overridePrivacy") != "yes") {
                    $private_folders_lineages = $this->fetch_private_documents_lineages($module, "folder");
                    foreach ($private_folders_lineages as $private_lineage) {
                        $query["not_like"][] = ["d.lineage", $this->ci->db->escape_str($private_lineage), "both"];
                    }
                }
            }
            $query["order_by"] = ["d.type desc, d.name asc"];
            $response["data"] = $this->load_all($query);
            $this->_table = $_table;
            $response["crumbLinkData"] = $this->get_crumb_link_data($lineage, !empty($module_record_id));
        }
        $response["totalRows"] = count($response["data"]);
        $response["lineage"] = $lineage;
        return $response;
    }
    public function load_document_by_id($file_id, $type = false, $visible_in_cp = 0)
    {
        $response["data"] = [];
        $_table = $this->_table;
        $this->_table = "documents_management_system d";
        $query["select"] = ["d.id, d.type, d.name, d.extension, (case when (d.type = 'file') then concat(d.name,'.',d.extension) else d.name end) AS full_name, d.parent, p.lineage as parent_lineage, d.lineage, d.size, d.version, d.private, d.document_type_id,(SELECT count(id) FROM documents_management_system where documents_management_system.parent =d.id) children_count, d.document_status_id, d.comment, d.module, d.module_record_id, d.system_document, d.visible, d.visible_in_cp, d.createdOn, d.createdBy, (case when (d.createdByChannel = 'CP') then concat(creatorCP.firstName, ' ', creatorCP.lastName, ' (Portal User)') when (d.createdByChannel = 'AP') then concat(creatorAP.firstName, ' ', creatorAP.lastName, ' (Advisor)') else concat(creatorU.firstName, ' ', creatorU.lastName) end) AS creator_full_name, d.createdByChannel, d.initial_version_created_on, d.initial_version_created_by, d.initial_version_created_by_channel, (case when (d.initial_version_created_On is not null) then d.initial_version_created_On else d.createdOn end) AS display_created_on, (\r\n                    case when (\r\n                      d.initial_version_created_by is not null\r\n                    ) then (\r\n                      case when (\r\n                        d.initial_version_created_by_channel = 'CP'\r\n                      ) then concat(\r\n                        displayIniCreatorCP.firstName, ' ', \r\n                        displayIniCreatorCP.lastName, ' (Portal User)'\r\n                      ) \r\n                      when (\r\n                        d.initial_version_created_by_channel = 'AP'\r\n                      ) then concat(\r\n                        displayIniCreatorAP.firstName, ' ', \r\n                        displayIniCreatorAP.lastName, ' (Advisor)'\r\n                      )\r\n                      else concat(\r\n                        displayIniCreatorU.firstName, ' ', \r\n                        displayIniCreatorU.lastName\r\n                      ) end\r\n                    ) else (\r\n                      case when (d.createdByChannel = 'CP') then concat(\r\n                        displayCreatorCP.firstName, ' ', \r\n                        displayCreatorCP.lastName, ' (Portal User)'\r\n                      )\r\n                      when (d.createdByChannel = 'AP') then concat(\r\n                        displayCreatorAP.firstName, ' ', \r\n                        displayCreatorAP.lastName, ' (Advisor)'\r\n                      )\r\n                       else concat(\r\n                        displayCreatorU.firstName, ' ', displayCreatorU.lastName\r\n                      ) end\r\n                    ) end\r\n                ) AS display_creator_full_name,(\r\n                case when (\r\n                  d.initial_version_created_by_channel is not null\r\n                ) then d.initial_version_created_by_channel else d.createdByChannel end\r\n              ) AS display_created_by_channel,(\r\n                case when (d.modifiedByChannel = 'CP') then concat(\r\n                  modifierCP.firstName, ' ', modifierCP.lastName, \r\n                  ' (Portal User)'\r\n                )\r\n                when (d.modifiedByChannel = 'AP') then concat(\r\n                    modifierAP.firstName, ' ', modifierAP.lastName, \r\n                    ' (Advisor)'\r\n                  )\r\n                 else concat(\r\n                  modifierU.firstName, ' ', modifierU.lastName\r\n                ) end\r\n              ) AS modifier_full_name,d.modifiedOn, d.modifiedBy, d.modifiedByChannel,CASE WHEN d.type = 'folder' AND d.private = 1 THEN (SELECT CASE WHEN dmuau.recordId IS NOT NULL THEN 1 ELSE 0 END\r\n                FROM document_managment_users dmuau\r\n                WHERE dmuau.user_id = " . $this->user_id . " AND dmuau.recordId = d.id) ELSE (SELECT CASE WHEN dms.private = 1 THEN (SELECT CASE WHEN dmuau.recordId IS NOT NULL THEN 1 ELSE 0 END\r\n                FROM document_managment_users dmuau\r\n                WHERE dmuau.user_id = " . $this->user_id . " AND dmuau.recordId = dms.id) ELSE 1 END\r\n                FROM documents_management_system dms\r\n                WHERE dms.id = d.parent) END as is_accessible"];
        $query["join"] = [["documents_management_system p", "p.id = d.parent", "left"], ["customer_portal_users creatorCP", "creatorCP.id = d.createdBy AND d.createdByChannel = 'CP'", "left"], ["user_profiles creatorU", "creatorU.user_id = d.createdBy AND d.createdByChannel NOT IN " . $this->notA4LChannels, "left"], ["advisor_users creatorAP", "creatorAP.id = d.createdBy AND d.createdByChannel = 'AP'", "left"], ["customer_portal_users displayIniCreatorCP", "displayIniCreatorCP.id = d.initial_version_created_by AND d.initial_version_created_by is not null and d.initial_version_created_by_channel = 'CP'", "left"], ["user_profiles displayIniCreatorU", "displayIniCreatorU.user_id = d.initial_version_created_by AND d.initial_version_created_by is not null and d.initial_version_created_by_channel NOT IN " . $this->notA4LChannels, "left"], ["advisor_users displayIniCreatorAP", "displayIniCreatorAP.id = d.initial_version_created_by AND d.initial_version_created_by is not null and d.initial_version_created_by_channel = 'AP'", "left"], ["customer_portal_users displayCreatorCP", "displayCreatorCP.id = d.createdBy AND d.initial_version_created_by is null and d.createdByChannel = 'CP'", "left"], ["user_profiles displayCreatorU", "displayCreatorU.user_id = d.createdBy AND d.initial_version_created_by is null and d.createdByChannel NOT IN " . $this->notA4LChannels, "left"], ["advisor_users displayCreatorAP", "displayCreatorAP.id = d.createdBy AND d.initial_version_created_by is null and d.createdByChannel = 'AP'", "left"], ["customer_portal_users modifierCP", "modifierCP.id = d.modifiedBy AND d.modifiedByChannel = 'CP'", "left"], ["user_profiles modifierU", "modifierU.user_id = d.modifiedBy AND d.modifiedByChannel NOT IN " . $this->notA4LChannels, "left"], ["advisor_users modifierAP", "modifierAP.id = d.modifiedBy AND d.modifiedByChannel = 'AP'", "left"]];
        $query["where"] = [["d.id", $file_id], ["d.visible", 1]];
        if ($visible_in_cp) {
            $query["where"][] = ["d.visible_in_cp", 1];
        }
        $query["order_by"] = ["d.type desc, d.name asc"];
        $response["data"] = $this->load_all($query);
        $this->_table = $_table;
        $response["totalRows"] = count($response["data"]);
        return $response;
    }
    public function get_crumb_link_data($lineage)
    {
        $exploded_lineage = explode(DIRECTORY_SEPARATOR, $lineage);
        $sliced_lineage = array_slice($exploded_lineage, 2);
        $lineage = implode(DIRECTORY_SEPARATOR, $sliced_lineage);
        $crumb_link_data = [];
        if (!empty($lineage)) {
            $directories = explode(DIRECTORY_SEPARATOR, $lineage);
            foreach ($directories as $directory_id) {
                $directory = $this->get_document_details(["id" => $directory_id]);
                if (!empty($directory)) {
                    $crumb_link_data[] = ["id" => $directory["id"], "name" => $directory["name"], "lineage" => $directory["lineage"]];
                }
            }
        }
        return $crumb_link_data;
    }
    public function get_document_full_details($document_fetch_criteria, $condition = "where", $return = "")
    {
        $_table = $this->_table;
        $this->_table = "documents_management_system d";
        $query["select"] = ["d.id, d.type, d.name, d.extension, (case when (d.type = 'file') then concat(d.name, '.', d.extension) else d.name end) as full_name, d.parent, p.lineage as parent_lineage, d.lineage, d.size, d.version, d.private, d.document_type_id, d.document_status_id, d.comment, d.module, d.module_record_id, d.system_document, d.visible, d.createdOn, d.createdBy, (case when (d.createdByChannel = 'CP') then concat(creatorCP.firstName, ' ', creatorCP.lastName, ' (Portal User)') when (d.createdByChannel = 'AP') then concat(creatorAP.firstName, ' ', creatorAP.lastName, ' (Advisor)') else concat(creatorU.firstName, ' ', creatorU.lastName) end) AS creator_full_name, d.createdByChannel, d.initial_version_created_on, d.initial_version_created_by, d.initial_version_created_by_channel, (case when (d.initial_version_created_On is not null) then d.initial_version_created_On else d.createdOn end) AS display_created_on, d.modifiedOn, d.modifiedBy, d.modifiedByChannel,(\r\n                case when (\r\n                  d.initial_version_created_by is not null\r\n                ) then (\r\n                  case when (\r\n                    d.initial_version_created_by_channel = 'CP'\r\n                  ) then concat(\r\n                    displayIniCreatorCP.firstName, ' ', \r\n                    displayIniCreatorCP.lastName, ' (Portal User)'\r\n                  )\r\n                  when (\r\n                    d.initial_version_created_by_channel = 'AP'\r\n                  ) then concat(\r\n                    displayIniCreatorAP.firstName, ' ', \r\n                    displayIniCreatorAP.lastName, ' (Advisor)'\r\n                  )\r\n                  else concat(\r\n                    displayIniCreatorU.firstName, ' ', \r\n                    displayIniCreatorU.lastName\r\n                  ) end\r\n                ) else (\r\n                  case when (d.createdByChannel = 'CP') then concat(\r\n                    displayCreatorCP.firstName, ' ', \r\n                    displayCreatorCP.lastName, ' (Portal User)'\r\n                  ) \r\n                  when (d.createdByChannel = 'AP') then concat(\r\n                    displayCreatorAP.firstName, ' ', \r\n                    displayCreatorAP.lastName, ' (Advisor)'\r\n                  )\r\n                  else concat(\r\n                    displayCreatorU.firstName, ' ', displayCreatorU.lastName\r\n                  ) end\r\n                ) end\r\n            ) AS display_creator_full_name,(\r\n                case when (\r\n                  d.initial_version_created_by_channel is not null\r\n                ) then d.initial_version_created_by_channel else d.createdByChannel end\r\n              ) AS display_created_by_channel,(\r\n              case when (d.modifiedByChannel = 'CP') then concat(\r\n                modifierCP.firstName, ' ', modifierCP.lastName, \r\n                ' (Portal User)'\r\n              )\r\n              when (d.modifiedByChannel = 'AP') then concat(\r\n                modifierAP.firstName, ' ', modifierAP.lastName, \r\n                ' (Advisor)'\r\n              )\r\n              else concat(\r\n                modifierU.firstName, ' ', modifierU.lastName\r\n              ) end\r\n            ) AS modifier_full_name"];
        $query["join"] = [["documents_management_system p", "p.id = d.parent", "left"], ["customer_portal_users creatorCP", "creatorCP.id = d.createdBy AND d.createdByChannel = 'CP'", "left"], ["user_profiles creatorU", "creatorU.user_id = d.createdBy AND d.createdByChannel NOT IN " . $this->notA4LChannels, "left"], ["advisor_users creatorAP", "creatorAP.id = d.createdBy AND d.createdByChannel = 'AP'", "left"], ["customer_portal_users displayIniCreatorCP", "displayIniCreatorCP.id = d.initial_version_created_by AND d.initial_version_created_by is not null and d.initial_version_created_by_channel = 'CP'", "left"], ["user_profiles displayIniCreatorU", "displayIniCreatorU.user_id = d.initial_version_created_by AND d.initial_version_created_by is not null and d.initial_version_created_by_channel NOT IN " . $this->notA4LChannels, "left"], ["advisor_users displayIniCreatorAP", "displayIniCreatorAP.id = d.initial_version_created_by AND d.initial_version_created_by is not null and d.initial_version_created_by_channel = 'AP'", "left"], ["customer_portal_users displayCreatorCP", "displayCreatorCP.id = d.createdBy AND d.initial_version_created_by is null and d.createdByChannel = 'CP'", "left"], ["user_profiles displayCreatorU", "displayCreatorU.user_id = d.createdBy AND d.initial_version_created_by is null and d.createdByChannel NOT IN " . $this->notA4LChannels, "left"], ["advisor_users displayCreatorAP", "displayCreatorAP.id = d.createdBy AND d.initial_version_created_by is null and d.createdByChannel = 'AP'", "left"], ["customer_portal_users modifierCP", "modifierCP.id = d.modifiedBy AND d.modifiedByChannel = 'CP'", "left"], ["user_profiles modifierU", "modifierU.user_id = d.modifiedBy AND d.modifiedByChannel NOT IN " . $this->notA4LChannels, "left"], ["advisor_users modifierAP", "modifierU.id = d.modifiedBy AND d.modifiedByChannel = 'AP'", "left"]];
        foreach ($document_fetch_criteria as $criteria_key => $criteria_value) {
            $query[$condition][] = [$criteria_key, $criteria_value];
        }
        $document = $return == "array" ? $this->load_all($query) : $this->load($query);
        $this->_table = $_table;
        return $document;
    }
    public function get_document_details($document_fetch_criteria, $condition = "where", $return = "")
    {
        $_table = $this->_table;
        $this->_table = "documents_management_system d";
        $query["select"] = ["d.id, d.type, d.name, d.extension, d.parent, d.lineage, d.module, d.module_record_id,\r\n         d.is_locked, d.last_locked_by, d.last_locked_by_channel, d.last_locked_on,\r\n         (case when (d.last_locked_by_channel = 'CP') then concat(cp_users.firstName, ' ', cp_users.lastName, ' (Portal User)') else concat(users.firstName, ' ', users.lastName) end ) AS last_locked_by_name,\r\n         (case when (d.type = 'file') then concat(d.name,'.',d.extension) else d.name end) AS full_name, d.system_document", false];
        $query["join"] = [["customer_portal_users cp_users", "cp_users.id = d.last_locked_by AND d.last_locked_by_channel = 'CP'", "left"], ["user_profiles users", "users.user_id = d.last_locked_by AND d.last_locked_by_channel != 'CP'", "left"]];
        foreach ($document_fetch_criteria as $criteria_key => $criteria_value) {
            $query[$condition][] = ["d." . $criteria_key, $criteria_value];
        }
        $document = $return == "array" ? $this->load_all($query) : $this->load($query);
        $this->_table = $_table;
        return $document;
    }
    public function is_visible_in_cp($id)
    {
        if ($id) {
            $this->fetch($id);
            return $this->get_field("visible_in_cp");
        }
        return false;
    }
    public function is_visible_in_ap($id)
    {
        if ($id) {
            $this->fetch($id);
            return $this->get_field("visible_in_ap");
        }
        return false;
    }
    public function get_document_extended_details($document_fetch_criteria, $condition = "where", $return = "")
    {
        $_table = $this->_table;
        $this->_table = "documents_management_system d";
        $query["select"] = ["d.id, d.type, d.name, d.extension, d.parent, p.lineage as parent_lineage, d.lineage, d.module, (case when (d.type = 'file') then concat(d.name, '.', d.extension) else d.name end) as full_name", false];
        $query["join"] = [["documents_management_system p", "p.id = d.parent", "left"]];
        foreach ($document_fetch_criteria as $criteria_key => $criteria_value) {
            $query[$condition][] = [$criteria_key, $criteria_value];
        }
        $document = $return == "array" ? $this->load_all($query) : $this->load($query);
        $this->_table = $_table;
        return $document;
    }
    public function get_document_newest_version_details($id)
    {
        $_table = $this->_table;
        $this->_table = "documents_management_system";
        $document = [];
        $selected_version = $this->get_document_details(["id" => $id]);
        if (!empty($selected_version)) {
            $selected_version_parent_query["select"] = ["id, type, name, extension, parent, lineage, module, module_record_id, is_locked, last_locked_by, last_locked_by_channel, last_locked_on"];
            $selected_version_parent_query["where"] = [["id", $selected_version["parent"]], ["name LIKE '%_versions'"]];
            $selected_version_parent = $this->load($selected_version_parent_query);
            if (!empty($selected_version_parent)) {
                $selected_version_parent_parent = $this->get_document_details(["id" => $selected_version_parent["parent"]]);
                if (!empty($selected_version_parent_parent)) {
                    $expected_newest_version_query["select"] = ["id, type, name, extension, parent, lineage, module, module_record_id, is_locked, last_locked_by, last_locked_by_channel, last_locked_on, (case when (type = 'file') then concat(name,'.',extension) else name end) AS full_name"];
                    $expected_newest_version_query["where"] = [["name", $selected_version["name"]], ["extension", $selected_version["extension"]], ["parent", $selected_version_parent_parent["id"]], ["version > 1"]];
                    $expected_newest_version = $this->load($expected_newest_version_query);
                    if (!empty($expected_newest_version) && $selected_version_parent["name"] == $expected_newest_version["id"] . "_versions") {
                        $document = $expected_newest_version;
                    }
                }
            }
        }
        $this->_table = $_table;
        return !empty($document) ? $document : $selected_version;
    }
    public function get_file_versions($file_id)
    {
        $_table = $this->_table;
        $this->_table = "documents_management_system v";
        $query["select"] = ["v.id, v.version, v.module, v.createdOn, v.createdByChannel, (case when (v.createdByChannel = 'CP') then \r\n        concat(creator_cp.firstName,' ',creator_cp.lastName,' (Portal User)') else concat(creator_u.firstName,' ',creator_u.lastName) end) AS creator_full_name", false];
        $query["or_where"] = [["v.id", $file_id], ["parent = (\r\n                SELECT p.id\r\n                FROM documents_management_system p\r\n                WHERE p.name = '" . $file_id . "_versions'\r\n            )"]];
        $query["join"] = [["customer_portal_users creator_cp", "creator_cp.id = v.createdBy AND v.createdByChannel = 'CP'", "left"], ["user_profiles creator_u", "creator_u.user_id = v.createdBy AND v.createdByChannel != 'CP'", "left"]];
        $query["order_by"] = ["v.version DESC"];
        $versions = $this->load_all($query);
        $this->_table = $_table;
        return $versions;
    }
    public function get_document_existant_version($document_name, $document_type, $lineage, $previous_version_id = 0)
    {
        $document_latest_version = NULL;
        $_table = $this->_table;
        $this->_table = "documents_management_system v";

        // Normalize the filename for case-insensitive comparison
        $normalized_document_name = strtolower($document_name);

        $query["select"] = [
            "v.id, v.name, v.extension, v.type, 
        (case when (v.type = 'file') then concat(v.name, '.', v.extension) else v.name end) AS full_name, 
        p.lineage as parent_lineage, v.lineage, v.version, 
        (case when isnull(v.document_type_id) then '' else v.document_type_id end) AS document_type_id, 
        (case when isnull(v.document_status_id) then '' else v.document_status_id end) AS document_status_id, 
        v.comment, v.module, v.createdOn, v.createdBy, v.createdByChannel, v.visible_in_cp",
            false
        ];

        $query["join"] = ["documents_management_system p ", "p.id = v.parent", "left"];

        // Use case-insensitive comparison with LOWER()
        $name_condition = $previous_version_id
            ? "v.id = " . (int)$previous_version_id
            : "LOWER(case when (v.type = 'file') then concat(v.name, '.', v.extension) else v.name end) = '" . addslashes($normalized_document_name) . "'";

        $query["where"] = [
            ["v.type", $document_type],
            [$name_condition]
        ];

        if (empty($lineage)) {
            $query["where"][] = ["p.lineage IS NULL"];
        } else {
            // FIXED: Properly extract the numeric parent ID from lineage for MySQL
            $lineage = trim($lineage, '/\\'); // Remove leading/trailing slashes
            $lineage_parts = preg_split('/[\/\\\\]/', $lineage); // Split by both forward and backslashes

            // Get the last numeric part (should be the parent ID)
            $parent_id = 0;
            foreach (array_reverse($lineage_parts) as $part) {
                if (is_numeric($part) && $part > 0) {
                    $parent_id = (int)$part;
                    break;
                }
            }

            $query["where"][] = ["v.parent", $parent_id];
        }

        $document_versions = $this->load_all($query);

        if (!empty($document_versions)) {
            $document_latest_version = $document_versions[array_search(
                max(array_column($document_versions, "version")),
                array_column($document_versions, "version")
            )];
        }

        $this->_table = $_table;
        return $document_latest_version;
    }
    public function get_document_existant_versionold($document_name, $document_type, $lineage, $previous_version_id = 0)
    {
        $document_latest_version = NULL;
        $_table = $this->_table;
        $this->_table = "documents_management_system v";
        $query["select"] = ["v.id, v.name, v.extension, v.type, (case when (v.type = 'file') then concat(v.name,'.',v.extension) else v.name end) AS full_name, p.lineage as parent_lineage, v.lineage, v.version,(case when isnull(v.document_type_id) then '' else v.document_type_id end) AS document_type_id, (case when isnull(v.document_status_id) then '' else v.document_status_id end) AS document_status_id, v.comment, v.module, v.createdOn, v.createdBy, v.createdByChannel, v.visible_in_cp", false];
        $query["join"] = ["documents_management_system p ", "p.id = v.parent", "left"];
        $query["where"] = [["v.type", $document_type], [$previous_version_id ? "v.id = " . $previous_version_id : "((case when (v.type = 'file') then concat(v.name,'.',v.extension) else v.name end) = '" . addslashes($document_name) . "')"]];
        if (empty($lineage)) {
            $query["where"][] = ["p.lineage IS NULL"];
        } else {
            $lineage_arr = explode(DIRECTORY_SEPARATOR, $lineage);
            $parent_id = count($lineage_arr) - 1;
            $query["where"][] = ["v.parent", $lineage_arr[$parent_id]];
        }
        $document_versions = $this->load_all($query);
        if (!empty($document_versions)) {
            $document_latest_version = $document_versions[array_search(max(array_column($document_versions, "version")), array_column($document_versions, "version"))];
        }
        $this->_table = $_table;
        return $document_latest_version;
    }
    public function folder_is_empty($folder_lineage)
    {
        $_table = $this->_table;
        $this->_table = "documents_management_system d";
        $query["select"] = ["count(d.id) AS folder_content"];
        $query["join"] = ["documents_management_system p ", "p.id = d.parent", "left"];
        $query["like"] = [["p.lineage", $this->ci->db->escape_str($folder_lineage), "both"]];
        $query_data = $this->load($query);
        $this->_table = $_table;
        return $query_data["folder_content"] == 0 ? true : false;
    }
    public function delete_folder_and_content($folder_id, $delete_container = false)
    {
        $this->ci->db->query("DELETE FROM documents_management_system WHERE id = " . $folder_id);
        if ($delete_container) {
            $separator = DIRECTORY_SEPARATOR;
            return $this->ci->db->query("DELETE FROM documents_management_system WHERE lineage LIKE '%" . $separator . $folder_id . $separator . "%' ESCAPE '|';");
        }
        return $this->ci->db->query("DELETE FROM documents_management_system WHERE parent=" . $folder_id);
    }
    public function delete_folder_by_id($folder_id)
    {
        return $this->ci->db->query("DELETE FROM documents_management_system WHERE documents_management_system.id = " . $folder_id);
    }
    public function fetch_private_folder($id, $lineage)
    {
        $query["select"] = ["DISTINCT documents_management_system.id", false];
        $query["join"] = [["document_managment_users dmu", "dmu.recordId = documents_management_system.id AND dmu.user_id = " . $this->user_id, "inner"]];
        $query["where"] = [["documents_management_system.type", "folder"], ["documents_management_system.private", 1]];
        if ($id) {
            $query["where"][] = ["documents_management_system.id", $id];
        }
        if ($lineage) {
            $query["where"][] = ["documents_management_system.lineage", $lineage];
        }
        return $this->load($query);
    }
    public function fetch_private_documents_lineages($module, $type = false, $is_api = false)
    {
        $_table = $this->_table;
        $this->_table = "documents_management_system as dmsfd";
        $query["select"] = ["DISTINCT dmsfd.lineage", false];
        $query["join"] = [["document_managment_users dmu", "dmu.recordId = dmsfd.id", "left"]];
        $user_id = $is_api ? $this->ci->user_logged_in_data["user_id"] : $this->user_id;
        $query["where"] = [["dmsfd.module", $module], ["dmsfd.private", 1], ["dmu.recordId NOT IN (\r\n                SELECT dmuau.recordId\r\n                FROM document_managment_users dmuau\r\n                WHERE dmuau.user_id = " . $user_id . ")"]];
        if ($type) {
            $query["where"][] = ["dmsfd.type", $type];
        }
        $data = $this->load_all($query);
        $response = array_column($data, "lineage");
        $this->_table = $_table;
        return $response;
    }
    public function fetch_universal_search_data($module, $term, $user_id, $override_privacy)
    {
        $_table = $this->_table;
        $this->_table = "documents_management_system as d";
        $query = [];
        $select = "d.id, d.lineage, (case when (d.type = 'file') then concat(d.name,'.',d.extension) else d.name end) as full_name, d.extension, d.version, d.module, d.module_record_id, d.comment, d.modifiedOn, d.type,(\r\n          case when (d.modifiedByChannel = 'CP') then concat(\r\n            modifierCP.firstName, ' ', modifierCP.lastName, \r\n            ' (Portal User)'\r\n          ) else concat(\r\n            modifierU.firstName, ' ', modifierU.lastName\r\n          ) end\r\n        ) AS modifier_full_name";
        $query["where"] = [["d.module", $module], ["d.visible", 1], ["((case when (d.type = 'file') then concat(d.name,'.',d.extension) else d.name end) like '%" . $this->ci->db->escape_str($term) . "%' OR d.comment like '%" . $this->ci->db->escape_str($term) . "%')"]];
        if ($module === "case") {
            $this->ci->load->model("legal_case", "legal_casefactory");
            $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
            $select .= ",legal_cases.category";
            $query["join"][] = ["legal_cases", "legal_cases.id = d.module_record_id", "left"];
            $query["where"][] = $this->ci->legal_case->get_matter_privacy_conditions($user_id, $override_privacy);
        }
        $query["select"] = $select;
        $query["join"][] = ["customer_portal_users modifierCP", "modifierCP.id = d.modifiedBy AND d.modifiedByChannel = 'CP'", "left"];
        $query["join"][] = ["user_profiles modifierU", "modifierU.user_id = d.modifiedBy AND d.modifiedByChannel != 'CP'", "left"];
        $this->ci->load->model("user_profile");
        $this->ci->user_profile->fetch(["user_id" => $this->user_id]);
        if ($this->ci->user_profile->get_field("overridePrivacy") != "yes") {
            $private_folders_lineages = $this->fetch_private_documents_lineages($module, "folder");
            foreach ($private_folders_lineages as $private_lineage) {
                $query["not_like"][] = ["d.lineage", $this->ci->db->escape_str($private_lineage), "both"];
            }
        }
        $response = parent::paginate($query, ["urlPrefix" => ""]);
        $this->_table = $_table;
        return $response;
    }
    public function load_root_folders_list($parent)
    {
        $config_list = ["key" => "id", "value" => "name"];
        $config_query = ["select" => "id,  name", "where" => [["parent", $parent], ["type", "folder"], ["visible", 1]]];
        return $this->load_list($config_query, $config_list);
    }
    public function get_integration_settings($model_settings)
    {
        $this->ci->load->model("system_configuration");
        $return["tabs_list"] = ["1" => "A4L"];
        require_once substr(COREPATH, 0, -12) . "/application/libraries/Document_third_party.php";
        $factory = new dropbox_factory();
        $this->dropbox = $factory->get_instance();
        if ($this->ci->system_configuration->get_value_by_key("dropbox_access_token") && $this->dropbox->get_token()) {
            $return["tabs_list"] = $this->ci->system_configuration->get_value_by_key("document_first_tab") == "dropbox" ? ["1" => "dropbox", "2" => "A4L"] : ["1" => "A4L", "2" => "dropbox"];
            if (in_array($model_settings["model"], ["contact", "company", "matter", "litigation", "intellectual property", "case_container"])) {
                $return["model_lineage"] = $this->dropbox->create_model_folder($model_settings["root_dir"], $model_settings["model_id"], $model_settings["model_name"], $this->ci->system_configuration->get_value_by_key("dropbox_default_folder"));
            } else {
                $model_settings["model"] = "doc";
                if ($model_settings["model"]) {
                    $return["model_lineage"] = $this->ci->system_configuration->get_value_by_key("dropbox_default_folder");
                    if ($return["model_lineage"] == "/") {
                        $return["model_lineage"] = "";
                    }
                }
            }
        }
        $return["model"] = $model_settings["model"];
        return $return;
    }
    public function load_all_folders($module, $module_record_id, $is_api = false)
    {
        $_table = $this->_table;
        $this->_table = "documents_management_system d";
        $query["select"] = ["d.id, (case when (d.type = 'file') then concat(d.name, '.', d.extension) else d.name end) as full_name, d.parent, p.lineage as parent_lineage, d.lineage"];
        $query["join"] = [["documents_management_system p", "p.id = d.parent", "left"]];
        $query["where"] = [["d.module", $module], ["d.visible", 1], ["d.type", "folder"]];
        $query["where"][] = empty($module_record_id) ? ["d.module_record_id IS NULL"] : ["d.module_record_id", $module_record_id];
        $this->ci->load->model("user_profile");
        $user_id = $is_api ? $this->ci->user_logged_in_data["user_id"] : $this->user_id;
        $this->ci->user_profile->fetch(["user_id" => $user_id]);
        if ($this->ci->user_profile->get_field("overridePrivacy") != "yes") {
            $private_folders_lineages = $this->fetch_private_documents_lineages($module, "folder", $is_api);
            foreach ($private_folders_lineages as $private_lineage) {
                $query["not_like"][] = ["d.lineage", $this->ci->db->escape_str($private_lineage), "both"];
            }
        }
        $query["order_by"] = ["d.lineage asc"];
        $response = $this->load_all($query);
        $this->_table = $_table;
        return $response;
    }
    public function load_all_documents($module, $module_record_id)
    {
        $_table = $this->_table;
        $this->_table = "documents_management_system d";
        $query["select"] = ["d.id, d.lineage"];
        $query["where"] = [["d.module", $module], ["d.visible", 1]];
        $query["where"][] = empty($module_record_id) ? ["d.module_record_id IS NULL"] : ["d.module_record_id", $module_record_id];
        $this->ci->load->model("user_profile");
        $this->ci->user_profile->fetch(["user_id" => $this->user_id]);
        if ($this->ci->user_profile->get_field("overridePrivacy") != "yes") {
            $private_documents_lineages = $this->fetch_private_documents_lineages($module);
            foreach ($private_documents_lineages as $private_lineage) {
                $query["not_like"][] = ["d.lineage", $this->ci->db->escape_str($private_lineage), "both"];
            }
        }
        $response = $this->load_all($query);
        $this->_table = $_table;
        return $response;
    }
    public function get_module_record_root_folder($module, $module_record_id)
    {
        $_table = $this->_table;
        $this->_table = "documents_management_system d";
        $query["select"] = ["d.id, (case when (d.type = 'file') then concat(d.name, '.', d.extension) else d.name end) AS full_name, d.parent, p.lineage as parent_lineage, d.lineage"];
        $query["join"] = [["documents_management_system p", "p.id = d.parent", "left"]];
        $query["where"] = [["d.module", $module], ["d.visible", 0], ["d.type", "folder"]];
        $query["where"][] = empty($module_record_id) ? ["d.module_record_id IS NULL"] : ["d.module_record_id", $module_record_id];
        $response = $this->load($query);
        $this->_table = $_table;
        return $response;
    }
    public function load_signature_documents($module, $module_record_id, $lineage, $term, $type = false, $visible_in_cp = 0)
    {
        $response["data"] = [];
        $_table = $this->_table;
        $this->_table = "documents_management_system d";
        $query["select"] = ["d.id, d.type, d.name, d.extension, d.system_document, d.visible, d.private,d.version,d.module, d.module_record_id,d.parent, p.lineage as parent_lineage, d.lineage, d.size,d.visible_in_cp,\r\n        (case when (d.type = 'file') then concat(d.name,'.',d.extension) else d.name end) AS full_name,(\r\n                    case when (\r\n                      d.initial_version_created_by is not null\r\n                    ) then (\r\n                      case when (\r\n                        d.initial_version_created_by_channel = 'CP'\r\n                      ) then concat(\r\n                        displayIniCreatorCP.firstName, ' ', \r\n                        displayIniCreatorCP.lastName, ' (Portal User)'\r\n                      ) \r\n                      when (\r\n                        d.initial_version_created_by_channel = 'AP'\r\n                      ) then concat(\r\n                        displayIniCreatorAP.firstName, ' ', \r\n                        displayIniCreatorAP.lastName, ' (Advisor)'\r\n                      )\r\n                      else concat(\r\n                        displayIniCreatorU.firstName, ' ', \r\n                        displayIniCreatorU.lastName\r\n                      ) end\r\n                    ) else (\r\n                      case when (d.createdByChannel = 'CP') then concat(\r\n                        displayCreatorCP.firstName, ' ', \r\n                        displayCreatorCP.lastName, ' (Portal User)'\r\n                      ) \r\n                      when (d.createdByChannel = 'AP') then concat(\r\n                        displayCreatorAP.firstName, ' ', \r\n                        displayCreatorAP.lastName, ' (Advisor)'\r\n                      ) \r\n                      else concat(\r\n                        displayCreatorU.firstName, ' ', displayCreatorU.lastName\r\n                      ) end\r\n                    ) end\r\n                ) AS display_creator_full_name,CASE WHEN d.type = 'folder' AND d.private = 1 THEN (SELECT CASE WHEN dmuau.recordId IS NOT NULL THEN 1 ELSE 0 END\r\n                FROM document_managment_users dmuau\r\n                WHERE dmuau.user_id = " . $this->user_id . " AND dmuau.recordId = d.id) ELSE (SELECT CASE WHEN dms.private = 1 THEN (SELECT CASE WHEN dmuau.recordId IS NOT NULL THEN 1 ELSE 0 END\r\n                FROM document_managment_users dmuau\r\n                WHERE dmuau.user_id = " . $this->user_id . " AND dmuau.recordId = dms.id) ELSE 1 END\r\n                FROM documents_management_system dms\r\n                WHERE dms.id = d.parent) END as is_accessible,\r\n                CASE WHEN signed_doc.signed_by_type = 'user' \r\n           THEN (SELECT concat(user_profiles.firstName, ' ', user_profiles.lastName) from user_profiles WHERE user_profiles.user_id = signed_doc.signed_by) \r\n           ELSE ( CASE WHEN signed_doc.signed_by_type = 'contact' THEN (SELECT concat(contacts.firstName, ' ', contacts.lastName, ' (', '" . $this->ci->lang->line("contact") . "', ') ') from contacts WHERE contacts.id = signed_doc.signed_by) \r\n           ELSE (SELECT concat(collaborator.firstName, ' ', collaborator.lastName, ' (', '" . $this->ci->lang->line("collaborator") . "', ') ')  from customer_portal_users as collaborator WHERE collaborator.id = signed_doc.signed_by) END )END as signed_by,\r\n                signed_doc.signed_by as signed_by_id, signed_doc.signed_on"];
        $query["join"] = [["documents_management_system p", "p.id = d.parent", "left"], ["contract_signed_document signed_doc", "signed_doc.document_id = d.id", "left"], ["customer_portal_users displayIniCreatorCP", "displayIniCreatorCP.id = d.initial_version_created_by AND d.initial_version_created_by is not null and d.initial_version_created_by_channel = 'CP'", "left"], ["user_profiles displayIniCreatorU", "displayIniCreatorU.user_id = d.initial_version_created_by AND d.initial_version_created_by is not null and d.initial_version_created_by_channel NOT IN " . $this->notA4LChannels, "left"], ["advisor_users displayIniCreatorAP", "displayIniCreatorAP.id = d.initial_version_created_by AND d.initial_version_created_by is not null and d.initial_version_created_by_channel = 'AP'", "left"], ["customer_portal_users displayCreatorCP", "displayCreatorCP.id = d.createdBy AND d.initial_version_created_by is null and d.createdByChannel = 'CP'", "left"], ["user_profiles displayCreatorU", "displayCreatorU.user_id = d.createdBy AND d.initial_version_created_by is null and d.createdByChannel NOT IN " . $this->notA4LChannels, "left"], ["advisor_users displayCreatorAP", "displayCreatorAP.id = d.createdBy AND d.initial_version_created_by is null and d.createdByChannel = 'AP'", "left"]];
        $query["where"] = [["d.module", $module], ["d.visible", 1]];
        $query["where"][] = empty($module_record_id) ? ["d.module_record_id IS NULL"] : ["d.module_record_id", $module_record_id];
        if (empty($lineage)) {
            $module_container = $this->get_document_details(["name" => empty($module_record_id) ? $module . "_container" : $module . "_" . (int) $module_record_id, "system_document" => 1]);
            $parent_lineage = $module_container["lineage"];
        } else {
            $parent_lineage = $lineage;
        }
        if ($type == "file") {
            $query["where"][] = ["d.type", $type];
            $query["like"] = [["p.lineage", $this->ci->db->escape_str($parent_lineage), "after"]];
            if (!empty($term)) {
                $query["where"][] = ["((case when (d.type = 'file') then concat(d.name,'.',d.extension) else d.name end) LIKE '%" . $this->ci->db->escape_str($term) . "%' OR d.comment LIKE '%" . $this->ci->db->escape_str($term) . "%')"];
            }
        } else {
            if (empty($term)) {
                $query["where"][] = ["p.lineage", $parent_lineage];
            } else {
                $query["like"] = [["p.lineage", $this->ci->db->escape_str($parent_lineage), "after"]];
                $query["where"][] = ["((case when (d.type = 'file') then concat(d.name,'.',d.extension) else d.name end) LIKE '%" . $this->ci->db->escape_str($term) . "%' OR d.comment LIKE '%" . $this->ci->db->escape_str($term) . "%')"];
            }
        }
        if ($visible_in_cp) {
            $query["where"][] = ["d.visible_in_cp", 1];
        }
        if ($parent_lineage) {
            if (!empty($term)) {
                $this->ci->load->model("user_profile");
                $this->ci->user_profile->fetch(["user_id" => $this->user_id]);
                if ($this->ci->user_profile->get_field("overridePrivacy") != "yes") {
                    $private_folders_lineages = $this->fetch_private_documents_lineages($module, "folder");
                    foreach ($private_folders_lineages as $private_lineage) {
                        $query["not_like"][] = ["d.lineage", $this->ci->db->escape_str($private_lineage), "both"];
                    }
                }
            }
            $query["order_by"] = ["d.type desc, d.name asc"];
            $response["data"] = $this->load_all($query);
            $this->_table = $_table;
            $response["crumbLinkData"] = $this->get_crumb_link_data($lineage, !empty($module_record_id));
        }
        $response["totalRows"] = count($response["data"]);
        $response["lineage"] = $lineage;
        return $response;
    }
    public function count_contract_related_documents($contract_id, $is_cp = false)
    {
        $_table = $this->_table;
        $this->_table = "documents_management_system d";
        $query["select"] = ["COUNT(d.id) as documents_count", false];
        $query["where"][] = ["d.module", "contract"];
        $query["where"][] = ["d.module_record_id", $contract_id];
        $query["where"][] = ["d.type", "file"];
        $query["where"][] = ["d.visible", "1"];
        if ($is_cp) {
            $query["where"][] = ["d.visible_in_cp", 1];
        }
        $document_count = $this->load($query)["documents_count"];
        $this->_table = $_table;
        return $document_count;
    }
    public function get_matters_attachments_report_info($sortable = [], $page_number = "")
    {
        $_table = $this->_table;
        $this->_table = "legal_cases_grid legal_cases";
        $query["select"] = ["legal_cases.id                                                                                                 AS id,\r\n                                   CONCAT(\"M\", legal_cases.id)                                                                                    AS caseId,\r\n                                   legal_cases.subject                                                                                            AS subject,\r\n                                   legal_cases.category                                                                                           AS category,\r\n                                   legal_cases.internalReference                                                                                  AS internalReference,\r\n                                   legal_cases.status                                                                                             AS status, \r\n                                   legal_cases.type                                                                                               AS type, \r\n                                   legal_cases.assignee                                                                                           AS assignee, \r\n                                   legal_cases.clientName                                                                                         AS client,\r\n\r\n                                   (SELECT GROUP_CONCAT(dms.name SEPARATOR ', ') from documents_management_system dms WHERE dms.type=\"folder\" AND dms.module_record_id = legal_cases.id AND dms.name NOT LIKE \"case_%\" AND dms.name NOT LIKE \"%_versions\" AND dms.module = \"case\")                                                                                                        AS foldersNames,\r\n\r\n                                   (SELECT CONCAT(COUNT(dms.id), \"::\" ,SUM(dms.size)) FROM documents_management_system dms WHERE legal_cases.id = dms.module_record_id AND dms.type = \"file\" AND dms.module = \"case\")                                                                 AS filesDetails\r\n                                   "];
        $query["group_by"] = ["legal_cases.id"];
        $response = [];
        $response["totalRows"] = $this->ci->db->get($this->get("_table"))->num_rows();
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["legal_cases.id", "asc"];
        }
        if ($page_number != "") {
            $query["limit"] = [1000, ($page_number - 1) * 1000];
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$limit, $this->ci->input->post("skip", true)];
        }
        $response["data"] = $this->load_all($query);
        $this->_table = $_table;
        return $response;
    }
    public function load_documents_for_collaboration($module, $module_record_id, $is_cp = false)
    {
        $_table = $this->_table;
        $this->_table = "documents_management_system d";
        $query["select"] = ["d.id, concat(d.name,'.',d.extension) as name"];
        $query["where"] = [["d.module", $module], ["d.visible", 1], ["d.type", "file"]];
        $query["where"][] = empty($module_record_id) ? ["d.module_record_id IS NULL"] : ["d.module_record_id", $module_record_id];
        if ($is_cp) {
            $query["where"][] = ["d.visible_in_cp", 1];
        } else {
            $this->ci->load->model("user_profile");
            $this->ci->user_profile->fetch(["user_id" => $this->user_id]);
            if ($this->ci->user_profile->get_field("overridePrivacy") != "yes") {
                $private_documents_lineages = $this->fetch_private_documents_lineages($module);
                foreach ($private_documents_lineages as $private_lineage) {
                    $query["not_like"][] = ["d.lineage", $this->ci->db->escape_str($private_lineage), "both"];
                }
            }
        }
        $response = $this->load_list($query);
        $this->_table = $_table;
        return $response;
    }
}
class mysqli_Document_management_system extends mysql_Document_management_system
{
}
class sqlsrv_Document_management_system extends mysql_Document_management_system
{
    public function get_document_details($document_fetch_criteria, $condition = "where", $return = "")
    {
        $_table = $this->_table;
        $this->_table = "documents_management_system d";
        $query["select"] = ["d.id, d.type, d.name, d.extension, d.parent, d.lineage, d.module, d.module_record_id,\r\n         d.is_locked, d.last_locked_by, d.last_locked_by_channel, d.last_locked_on,\r\n         (case when (d.last_locked_by_channel = 'CP') then (cp_users.firstName + ' ' + cp_users.lastName+ ' (Portal User)') else (users.firstName + ' ' + users.lastName) end ) AS last_locked_by_name,\r\n         (case when (d.type = 'file') then (d.name + '.' + d.extension) else d.name end) AS full_name, d.system_document", false];
        $query["join"] = [["customer_portal_users cp_users", "cp_users.id = d.last_locked_by AND d.last_locked_by_channel = 'CP'", "left"], ["user_profiles users", "users.user_id = d.last_locked_by AND d.last_locked_by_channel != 'CP'", "left"]];
        foreach ($document_fetch_criteria as $criteria_key => $criteria_value) {
            $query[$condition][] = ["d." . $criteria_key, $criteria_value];
        }
        $document = $return == "array" ? $this->load_all($query) : $this->load($query);
        $this->_table = $_table;
        return $document;
    }
    public function get_file_versions($file_id)
    {
        $_table = $this->_table;
        $this->_table = "documents_management_system v";
        $query["select"] = ["v.id, v.version, v.module, v.createdOn, v.createdByChannel,\r\n        (case when (v.createdByChannel = 'CP') then (creator_cp.firstName + ' ' + creator_cp.lastName + ' (Portal User)') else (creator_u.firstName + ' ' + creator_u.lastName) end ) AS creator_full_name", false];
        $query["or_where"] = [["v.id", $file_id], ["parent = (\r\n                SELECT p.id\r\n                FROM documents_management_system p\r\n                WHERE p.name = '" . $file_id . "_versions'\r\n            )"]];
        $query["join"] = [["customer_portal_users creator_cp", "creator_cp.id = v.createdBy AND v.createdByChannel = 'CP'", "left"], ["user_profiles creator_u", "creator_u.user_id = v.createdBy AND v.createdByChannel != 'CP'", "left"]];
        $query["order_by"] = ["v.version DESC"];
        $versions = $this->load_all($query);
        $this->_table = $_table;
        return $versions;
    }
    public function get_document_existant_version($document_name, $document_type, $lineage, $previous_version_id = 0)
    {
        $document_latest_version = NULL;
        $_table = $this->_table;
        $this->_table = "documents_management_system v";

        $normalized_document_name = strtolower($document_name);

        $query["select"] = [
            "v.id, v.name, v.extension, v.type, 
        (case when (v.type = 'file') then (v.name + '.' + v.extension) else v.name end) AS full_name, 
        p.lineage as parent_lineage, v.lineage, v.version, 
        ISNULL(CONVERT(NVARCHAR, v.document_type_id), '') AS document_type_id, 
        ISNULL(CONVERT(NVARCHAR, v.document_status_id), '') AS document_status_id, 
        v.comment, v.module, v.createdOn, v.createdBy, v.createdByChannel, v.visible_in_cp",
            false
        ];

        $query["join"] = ["documents_management_system p ", "p.id = v.parent", "left"];

        $name_condition = $previous_version_id
            ? "v.id = " . (int)$previous_version_id
            : "LOWER(CASE WHEN v.type = 'file' THEN (v.name + '.' + v.extension) ELSE v.name END) = '" . str_replace("'", "''", $normalized_document_name) . "'";

        $query["where"] = [
            ["v.type", $document_type],
            [$name_condition]
        ];

        if (empty($lineage)) {
            $query["where"][] = ["p.lineage IS NULL"];
        } else {
            // FIXED: Properly extract the numeric parent ID from lineage
            $lineage = trim($lineage, '/\\'); // Remove leading/trailing slashes
            $lineage_parts = preg_split('/[\/\\\\]/', $lineage); // Split by both forward and backslashes

            // Get the last numeric part (should be the parent ID)
            $parent_id = 0;
            foreach (array_reverse($lineage_parts) as $part) {
                if (is_numeric($part) && $part > 0) {
                    $parent_id = (int)$part;
                    break;
                }
            }

            $query["where"][] = ["v.parent", $parent_id];
        }

        $document_versions = $this->load_all($query);

        if (!empty($document_versions)) {
            $document_latest_version = $document_versions[array_search(
                max(array_column($document_versions, "version")),
                array_column($document_versions, "version")
            )];
        }

        $this->_table = $_table;
        return $document_latest_version;
    }

    public function get_document_existant_version_caseinsenstiveold($document_name, $document_type, $lineage, $previous_version_id = 0)
    {
        $document_latest_version = NULL;
        $_table = $this->_table;
        $this->_table = "documents_management_system v";
        $query["select"] = ["v.id, v.name, v.extension, v.type, (case when (v.type = 'file') then (v.name + '.' + v.extension) else v.name end) AS full_name, p.lineage as parent_lineage, v.lineage, v.version, ISNULL(CONVERT(NVARCHAR, v.document_type_id), '') AS document_type_id, ISNULL(CONVERT(NVARCHAR, v.document_status_id), '') AS document_status_id, v.comment, v.module, v.createdOn, v.createdBy, v.createdByChannel, v.visible_in_cp", false];
        $query["join"] = ["documents_management_system p ", "p.id = v.parent", "left"];
        $query["where"] = [["v.type", $document_type], [$previous_version_id ? "v.id = " . $previous_version_id : "((case when (v.type = 'file') then (v.name + '.' + v.extension) else v.name end) = '" . addslashes($document_name) . "')"]];
        if (empty($lineage)) {
            $query["where"][] = ["p.lineage IS NULL"];
        } else {
//            $lineage_arr = explode(DIRECTORY_SEPARATOR, $lineage);
//            $parent_id = count($lineage_arr) - 1;
//            $query["where"][] = ["v.parent", $lineage_arr[$parent_id]];
            // Extract the last ID from the lineage path
            $lineage_ids = array_filter(explode('\\', $lineage));
            $parent_id = end($lineage_ids);

            // Use parameter binding for the parent ID
            $query["where"][] = ["v.parent", (int)$parent_id]; // Cast to integer for safety
        }
        $document_versions = $this->load_all($query);
        if (!empty($document_versions)) {
            $document_latest_version = $document_versions[array_search(max(array_column($document_versions, "version")), array_column($document_versions, "version"))];
        }
        $this->_table = $_table;
        return $document_latest_version;
    }
    public function load_signature_documents($module, $module_record_id, $lineage, $term, $type = false, $visible_in_cp = 0)
    {
        $response["data"] = [];
        $_table = $this->_table;
        $this->_table = "documents_management_system d";
        $query["select"] = ["d.id, d.type, d.name, d.extension, d.system_document, d.visible, d.private,d.version,d.module, d.module_record_id,d.parent, p.lineage as parent_lineage, d.lineage, d.size,d.visible_in_cp,\r\n        (case when (d.type = 'file') then (d.name + '.' + d.extension) else d.name end) AS full_name,(\r\n                    case when (\r\n                      d.initial_version_created_by is not null\r\n                    ) then (\r\n                      case when (\r\n                        d.initial_version_created_by_channel = 'CP'\r\n                      ) then (\r\n                        displayIniCreatorCP.firstName + ' ' + \r\n                        displayIniCreatorCP.lastName + ' (Portal User)'\r\n                      )\r\n                     when (\r\n                        d.initial_version_created_by_channel = 'AP'\r\n                      ) then (\r\n                        displayIniCreatorAP.firstName + ' ' + \r\n                        displayIniCreatorAP.lastName + ' (Advisor)'\r\n                      )\r\n                      else (\r\n                        displayIniCreatorU.firstName + ' ' + \r\n                        displayIniCreatorU.lastName\r\n                      ) end\r\n                    ) else (\r\n                      case when (d.createdByChannel = 'CP') then (\r\n                        displayCreatorCP.firstName + ' ' + \r\n                        displayCreatorCP.lastName + ' (Portal User)'\r\n                      )\r\n                      when (d.createdByChannel = 'AP') then (\r\n                        displayCreatorAP.firstName + ' ' + \r\n                        displayCreatorAP.lastName + ' (Advisor)'\r\n                      )\r\n                       else (\r\n                        displayCreatorU.firstName + ' ' + displayCreatorU.lastName\r\n                      ) end\r\n                    ) end\r\n                ) AS display_creator_full_name,CASE WHEN d.type = 'folder' AND d.private = 1 THEN (SELECT CASE WHEN dmuau.recordId IS NOT NULL THEN 1 ELSE 0 END\r\n                FROM document_managment_users dmuau\r\n                WHERE dmuau.user_id = " . $this->user_id . " AND dmuau.recordId = d.id) ELSE (SELECT CASE WHEN dms.private = 1 THEN (SELECT CASE WHEN dmuau.recordId IS NOT NULL THEN 1 ELSE 0 END\r\n                FROM document_managment_users dmuau\r\n                WHERE dmuau.user_id = " . $this->user_id . " AND dmuau.recordId = dms.id) ELSE 1 END\r\n                FROM documents_management_system dms\r\n                WHERE dms.id = d.parent) END as is_accessible,\r\n                CASE WHEN signed_doc.signed_by_type = 'user' \r\n           THEN (SELECT (user_profiles.firstName + ' ' + user_profiles.lastName) from user_profiles WHERE user_profiles.user_id = signed_doc.signed_by) \r\n           ELSE (CASE WHEN signed_doc.signed_by_type = 'user' THEN (SELECT (contacts.firstName + ' ' + contacts.lastName + ' (' + '" . $this->ci->lang->line("contact") . "' + ') ') from contacts WHERE contacts.id = signed_doc.signed_by) \r\n           ELSE (SELECT (collaborator.firstName + ' ' + collaborator.lastName + ' ('+ '" . $this->ci->lang->line("collaborator") . "' +  ') ')  from customer_portal_users as collaborator WHERE collaborator.id = signed_doc.signed_by) END) END as signed_by,\r\n                signed_doc.signed_by as signed_by_id, signed_doc.signed_on"];
        $query["join"] = [["documents_management_system p", "p.id = d.parent", "left"], ["contract_signed_document signed_doc", "signed_doc.document_id = d.id", "left"], ["customer_portal_users displayIniCreatorCP", "displayIniCreatorCP.id = d.initial_version_created_by AND d.initial_version_created_by is not null and d.initial_version_created_by_channel = 'CP'", "left"], ["user_profiles displayIniCreatorU", "displayIniCreatorU.user_id = d.initial_version_created_by AND d.initial_version_created_by is not null and d.initial_version_created_by_channel NOT IN " . $this->notA4LChannels, "left"], ["advisor_users displayIniCreatorAP", "displayIniCreatorAP.id = d.initial_version_created_by AND d.initial_version_created_by is not null and d.initial_version_created_by_channel = 'AP'", "left"], ["customer_portal_users displayCreatorCP", "displayCreatorCP.id = d.createdBy AND d.initial_version_created_by is null and d.createdByChannel = 'CP'", "left"], ["user_profiles displayCreatorU", "displayCreatorU.user_id = d.createdBy AND d.initial_version_created_by is null and d.createdByChannel NOT IN " . $this->notA4LChannels, "left"], ["advisor_users displayCreatorAP", "displayCreatorAP.id = d.createdBy AND d.initial_version_created_by is null and d.createdByChannel = 'AP'", "left"]];
        $query["where"] = [["d.module", $module], ["d.visible", 1]];
        $query["where"][] = empty($module_record_id) ? ["d.module_record_id IS NULL"] : ["d.module_record_id", $module_record_id];
        if (empty($lineage)) {
            $module_container = $this->get_document_details(["name" => empty($module_record_id) ? $module . "_container" : $module . "_" . (int) $module_record_id, "system_document" => 1]);
            $parent_lineage = $module_container["lineage"];
        } else {
            $parent_lineage = $lineage;
        }
        if ($type == "file") {
            $query["where"][] = ["d.type", $type];
            $query["like"] = [["p.lineage", $this->ci->db->escape_str($parent_lineage), "after"]];
            if (!empty($term)) {
                $query["where"][] = ["((case when (d.type = 'file') then (d.name + '.' + d.extension) else d.name end) LIKE '%" . $this->ci->db->escape_str($term) . "%' OR d.comment LIKE '%" . $this->ci->db->escape_str($term) . "%')"];
            }
        } else {
            if (empty($term)) {
                $query["where"][] = ["p.lineage", $parent_lineage];
            } else {
                $query["like"] = [["p.lineage", $this->ci->db->escape_str($parent_lineage), "after"]];
                $query["where"][] = ["((case when (d.type = 'file') then (d.name + '.' + d.extension) else d.name end) LIKE '%" . $this->ci->db->escape_str($term) . "%' OR d.comment LIKE '%" . $this->ci->db->escape_str($term) . "%')"];
            }
        }
        if ($visible_in_cp) {
            $query["where"][] = ["d.visible_in_cp", 1];
        }
        if ($parent_lineage) {
            if (!empty($term)) {
                $this->ci->load->model("user_profile");
                $this->ci->user_profile->fetch(["user_id" => $this->user_id]);
                if ($this->ci->user_profile->get_field("overridePrivacy") != "yes") {
                    $private_folders_lineages = $this->fetch_private_documents_lineages($module, "folder");
                    foreach ($private_folders_lineages as $private_lineage) {
                        $query["not_like"][] = ["d.lineage", $this->ci->db->escape_str($private_lineage), "both"];
                    }
                }
            }
            $query["order_by"] = ["d.type desc, d.name asc"];
            $response["data"] = $this->load_all($query);
            $this->_table = $_table;
            $response["crumbLinkData"] = $this->get_crumb_link_data($lineage, !empty($module_record_id));
        }
        $response["totalRows"] = count($response["data"]);
        $response["lineage"] = $lineage;
        return $response;
    }
    public function get_matters_attachments_report_info($sortable = [], $page_number = "")
    {
        $_table = $this->_table;
        $this->_table = "legal_cases_grid legal_cases";
        $query["select"] = ["legal_cases.id                                                                                                AS id,\r\n                                   'M' + CONVERT(NVARCHAR, legal_cases.id)                                                                      AS caseId,\r\n                                   MAX(legal_cases.subject)                                                                                       AS subject,\r\n                                   MAX(legal_cases.category)                                                                                      AS category,\r\n                                   MAX(legal_cases.internalReference)                                                                                  AS internalReference,\r\n                                   MAX(legal_cases.status)                                                                                             AS status, \r\n                                   MAX(legal_cases.type)                                                                                               AS type, \r\n                                   MAX(legal_cases.assignee)                                                                                           AS assignee, \r\n                                   MAX(legal_cases.clientName)                                                                                         AS client,\r\n\r\n                                   foldersNames = STUFF(\r\n                                       (SELECT ',' + dms.name from documents_management_system dms WHERE dms.type='folder' AND dms.module_record_id = legal_cases.id AND dms.name NOT LIKE 'case_%' and dms.module = 'case' FOR XML PATH('')), 1, 1, ''),\r\n\r\n                                   (SELECT (CONVERT(NVARCHAR, COUNT(dms.id)) + '::'  + CONVERT(NVARCHAR, SUM(dms.size)) ) FROM documents_management_system dms WHERE legal_cases.id = dms.module_record_id AND dms.type = 'file' AND dms.module = 'case')                                                                 AS filesDetails\r\n                                   "];
        $query["group_by"] = ["legal_cases.id"];
        $response = [];
        $response["totalRows"] = $this->ci->db->get($this->get("_table"))->num_rows();
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["legal_cases.id", "asc"];
        }
        if ($page_number != "") {
            $query["limit"] = [1000, ($page_number - 1) * 1000];
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$limit, $this->ci->input->post("skip", true)];
        }
        $response["data"] = $this->load_all($query);
        $this->_table = $_table;
        return $response;
    }
    public function load_documents_for_collaboration($module, $module_record_id, $is_cp = false)
    {
        $_table = $this->_table;
        $this->_table = "documents_management_system d";
        $query["select"] = ["d.id, (d.name + '.' + d.extension) as name"];
        $query["where"] = [["d.module", $module], ["d.visible", 1], ["d.type", "file"]];
        $query["where"][] = empty($module_record_id) ? ["d.module_record_id IS NULL"] : ["d.module_record_id", $module_record_id];
        if ($is_cp) {
            $query["where"][] = ["d.visible_in_cp", 1];
        } else {
            $this->ci->load->model("user_profile");
            $this->ci->user_profile->fetch(["user_id" => $this->user_id]);
            if ($this->ci->user_profile->get_field("overridePrivacy") != "yes") {
                $private_documents_lineages = $this->fetch_private_documents_lineages($module);
                foreach ($private_documents_lineages as $private_lineage) {
                    $query["not_like"][] = ["d.lineage", $this->ci->db->escape_str($private_lineage), "both"];
                }
            }
        }
        $response = $this->load_list($query);
        $this->_table = $_table;
        return $response;
    }
}

?>