<?php


if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Dmsnew
{
    private $ci = NULL;
    public $model = NULL;
    public $modules_properties = NULL;
    public $channel = NULL;
    public $user_id = NULL;
    public $documents_root_direcotry = NULL;
    public $temporary_upload_directory = NULL;
    public $max_file_name_length = 255;
    public $release_script = NULL;
    public function __construct($parameters = NULL)
    {
        $this->ci =& get_instance();
        $this->modules_properties = ["company" => ["model" => ["name" => "company", "factory" => true], "container" => "companies", "versioning" => true], "contact" => ["model" => ["name" => "contact", "factory" => true], "container" => "contacts", "versioning" => true], "case" => ["model" => ["name" => "legal_case", "factory" => true], "container" => "cases", "versioning" => true], "caseContainer" => ["model" => ["name" => "legal_case_container", "factory" => true], "container" => "matter_containers", "versioning" => true], "task" => ["model" => ["name" => "task", "factory" => true], "container" => "tasks", "versioning" => false],"opinion" => ["model" => ["name" => "opinion", "factory" => true], "container" => "opinions", "versioning" => false],
            "conveyancing" => ["model" => ["name" => "conveyancing_instrument", "factory" => true], "container" => "conveyancing", "versioning" => true], "advisor_task" => ["model" => ["name" => "advisor_task", "factory" => true], "container" => "advisor_tasks", "versioning" => false], "doc" => ["model" => false, "container" => "docs", "versioning" => true], "client" => ["model" => ["name" => "client", "factory" => false], "container" => "clients", "versioning" => false], "BI" => ["model" => ["name" => "voucher_header", "factory" => true, "fetch_extra" => ["voucherType" => "BI"]], "container" => "money" . DIRECTORY_SEPARATOR . "bills", "versioning" => false], "BI-PY" => ["model" => ["name" => "voucher_header", "factory" => true, "fetch_extra" => ["voucherType" => "BI-PY"]], "container" => "money" . DIRECTORY_SEPARATOR . "bills_payments", "versioning" => false], "EXP" => ["model" => ["name" => "voucher_header", "factory" => true, "fetch_extra" => ["voucherType" => "EXP"]], "container" => "money" . DIRECTORY_SEPARATOR . "expenses", "versioning" => false], "INV" => ["model" => ["name" => "voucher_header", "factory" => true, "fetch_extra" => ["voucherType" => "INV"]], "container" => "money" . DIRECTORY_SEPARATOR . "invoices", "versioning" => false], "QOT" => ["model" => ["name" => "voucher_header", "factory" => true, "fetch_extra" => ["voucherType" => "QOT"]], "container" => "money" . DIRECTORY_SEPARATOR . "quotes", "versioning" => false], "INV-PY" => ["model" => ["name" => "voucher_header", "factory" => false, "fetch_extra" => ["voucherType" => "INV-PY"]], "container" => "money" . DIRECTORY_SEPARATOR . "invoices_payments", "versioning" => false], "contract" => ["model" => ["name" => "contract", "factory" => true], "container" => "contracts", "versioning" => true], "CRN" => ["model" => ["name" => "voucher_header", "factory" => true, "fetch_extra" => ["voucherType" => "CRN"]], "container" => "money" . DIRECTORY_SEPARATOR . "credit_notes", "versioning" => false], "DBN" => ["model" => ["name" => "voucher_header", "factory" => true, "fetch_extra" => ["voucherType" => "DBN"]], "container" => "money" . DIRECTORY_SEPARATOR . "debit_notes", "versioning" => false], "DBN-PY" => ["model" => ["name" => "voucher_header", "factory" => false, "fetch_extra" => ["voucherType" => "DBN-PY"]], "container" => "money" . DIRECTORY_SEPARATOR . "debit_notes_payments", "versioning" => false]];
        $this->channel = empty($parameters["channel"]) ? "A4L" : $parameters["channel"];
        $this->user_id = empty($parameters["user_id"]) ? $this->ci->is_auth->get_user_id() : $parameters["user_id"];
        $this->documents_root_direcotry = $this->ci->config->item("files_path")  . "attachments" . DIRECTORY_SEPARATOR;
        $this->temporary_upload_directory = $this->ci->config->item("files_path")  . "tmp" . DIRECTORY_SEPARATOR;
        $this->ci->load->model("document_management_system", "document_management_systemfactory");
        $this->ci->document_management_system = $this->ci->document_management_systemfactory->get_instance();
        $this->model = $this->ci->document_management_system;
        $this->model->set_user_id($this->user_id);
        $this->log_path = "dms_log";
        $this->release_script = $parameters["release_script"] ?? false;
    }
    public function load_documents($parameters)
    {
        $response = [];
        if ($this->validate_parameters($parameters)) {
            $response = $this->model->load_documents($parameters["module"], $this->get_optional_parameter_value($parameters, "module_record_id"), $this->get_optional_parameter_value($parameters, "lineage"), $this->get_optional_parameter_value($parameters, "term"), $this->get_optional_parameter_value($parameters, "type"), $this->get_optional_parameter_value($parameters, "visible_in_cp"));
        }
        $response["allowDownloadFolder"] = $this->ci->getInstanceConfig("allow_download_folder");
        $response["status"] = isset($response["data"]) && is_array($response["data"]) ? true : false;
        $response["message"] = $response["status"] ? $this->ci->lang->line("documents_loaded_successfully") : $this->ci->lang->line("documents_loading_failed");
        return $response;
    }
    public function load_document_by_id($file_id)
    {
        $response = [];
        $response = $this->model->load_document_by_id($file_id);
        $response["status"] = isset($response["data"]) && is_array($response["data"]) ? true : false;
        $response["message"] = $response["status"] ? $this->ci->lang->line("documents_loaded_successfully") : $this->ci->lang->line("documents_loading_failed");
        return $response;
    }
    public function upload_file22Aug2025($parameters)
    { 
        setlocale(LC_ALL, "en_US.utf8");
        $response = [];
        if (isset($parameters["upload_key"]) &&
		!empty($_FILES[$parameters["upload_key"]]) &&
		$this->validate_parameters($parameters)) {
            if (0 < $_FILES[$parameters["upload_key"]]["size"]) {
                if (strlen($_FILES[$parameters["upload_key"]]["name"]) < $this->max_file_name_length) {
                    $lineage = $this->get_optional_parameter_value($parameters, "lineage");
                    if (empty($lineage)) {
                        $container = $this->get_container($parameters["module"], $this->get_optional_parameter_value($parameters, "module_record_id"), $this->get_optional_parameter_value($parameters, "container_name"));
                        $lineage = $container["lineage"];
                    }
                    $_FILES[$parameters["upload_key"]]["name"] = preg_replace("/[^a-​zA-Z0-9_. ^;\\`!@#\$%&(--)+=]/", "", $_FILES[$parameters["upload_key"]]["name"]);
                    $previous_version_id = isset($parameters["previous_version_id"]) ? $parameters["previous_version_id"] : 0;
                    $file_existant_version = $this->model->get_document_existant_version($_FILES[$parameters["upload_key"]]["name"], "file", $lineage, $previous_version_id);
                    if (empty($file_existant_version) || $this->get_module_properties($parameters["module"], "versioning")) {
                        $this->ci->config->load("allowed_file_uploads", true);
                        $config["max_size"] = $this->ci->config->item("allowed_upload_size_kilobite");
                        $config["upload_path"] = $this->temporary_upload_directory;
                        $config["allowed_types"] = $this->ci->config->item($parameters["module"], "allowed_file_uploads");
                        $config["remove_spaces"] = false;
                        $config["overwrite"] = true;
                        $this->ci->load->library("upload", $config);
                        if ($this->ci->upload->do_upload($parameters["upload_key"])) {
                            $upload_data = $this->ci->upload->data();
                            $parent = $this->get_id_from_lineage($lineage);
                            $parent_visible_in_cp = $this->model->is_visible_in_cp($parent);
                            $parent_visible_in_ap = $this->model->is_visible_in_ap($parent);
                            $this->model->reset_fields();
                            setlocale(LC_ALL, "en_US.UTF-8");
                            $this->model->set_fields(["type" => "file", "name" => pathinfo($_FILES[$parameters["upload_key"]]["name"], PATHINFO_FILENAME),
                                "extension" => pathinfo($_FILES[$parameters["upload_key"]]["name"], PATHINFO_EXTENSION),
                                "size" => $_FILES[$parameters["upload_key"]]["size"],
                                "parent" => $parent, "version" => empty($file_existant_version) ? 1 : $file_existant_version["version"] + 1,
                                "document_type_id" => $this->get_optional_parameter_value($parameters, "document_type_id"),
                                "document_status_id" => $this->get_optional_parameter_value($parameters, "document_status_id"),
                                "comment" => $this->get_optional_parameter_value($parameters, "comment"),
                                "module" => $parameters["module"],
                                "module_record_id" => $this->get_optional_parameter_value($parameters, "module_record_id"),
                                "system_document" => 0,
                                "visible" => 1,
                                "visible_in_cp" => isset($parameters["visible_in_cp"]) ? $parameters["visible_in_cp"]??0 : ($file_existant_version["visible_in_cp"]??0 || $parent_visible_in_cp ? 1 : 0),
                                "visible_in_ap" => isset($parameters["visible_in_ap"]) ? $parameters["visible_in_ap"] : $parent_visible_in_ap,
                                "createdOn" => date("Y-m-d H:i:s"), "createdBy" => $this->user_id, "createdByChannel" => $this->channel, "modifiedOn" => date("Y-m-d H:i:s"), "modifiedBy" => $this->user_id, "modifiedByChannel" => $this->channel]);
                            if (!empty($file_existant_version)) {
                                $editable_fields = ["document_type_id", "document_status_id", "comment"];
                                foreach ($editable_fields as $field) {
                                    if (!$this->model->get_field($field)) {
                                        $this->model->set_field($field, $file_existant_version[$field]);
                                    }
                                }
                                $this->model->set_fields(["initial_version_created_on" => $file_existant_version["createdOn"], "initial_version_created_by" => $file_existant_version["createdBy"], "initial_version_created_by_channel" => $file_existant_version["createdByChannel"]]);
                            }
                            if ($this->model->insert()) {
                                $this->model->set_field("lineage", $lineage . DIRECTORY_SEPARATOR . $this->model->get_field("id"));
                                if ($this->model->update() && rename($upload_data["full_path"], $this->get_module_container_path($parameters["module"]) . $this->model->get_field("lineage"))) {
                                    $uploaded_file = $this->model->get_document_full_details(["d.id" => $this->model->get_field("id")]);
                                    if (empty($file_existant_version)) {
                                        $response["status"] = true;
                                        $response["message"] = $this->ci->lang->line("file_uploaded");
                                        $response["file"] = $uploaded_file;
                                    } else {
                                        $this->file_versioning($file_existant_version, $uploaded_file, $response);
                                    }
                                }
                            }
                        } else {
                            $response["message"] = strip_tags($this->ci->upload->display_errors());
                        }
                    } else {
                        $response["message"] = $this->ci->lang->line("file_already_exist");
                    }
                } else {
                    $response["message"] = $this->ci->lang->line("too_long_file_name");
                }
            } else {
                $response["message"] = $this->ci->lang->line("empty_file_upload_not_allowed");
            }
        }
        $response["status"] = empty($response["status"]) ? false : $response["status"];
        $response["message"] = empty($response["message"]) ? $response["status"] ? $this->ci->lang->line("file_uploaded_successfully") : $this->ci->lang->line("file_upload_failed") : $response["message"];
        return $response;
    }
    public function upload_file($parameters)
    {
        setlocale(LC_ALL, "en_US.utf8");
        $response = [];

        try {
            if (isset($parameters["upload_key"]) && !empty($_FILES[$parameters["upload_key"]]) && $this->validate_parameters($parameters)) {

                if (0 < $_FILES[$parameters["upload_key"]]["size"]) {
                    if (strlen($_FILES[$parameters["upload_key"]]["name"]) < $this->max_file_name_length) {

                        $lineage = $this->get_optional_parameter_value($parameters, "lineage");
                        if (empty($lineage)) {
                            $container = $this->get_container($parameters["module"], $this->get_optional_parameter_value($parameters, "module_record_id"), $this->get_optional_parameter_value($parameters, "container_name"));
                            $lineage = $container["lineage"];
                        }

                        $_FILES[$parameters["upload_key"]]["name"] = preg_replace("/[^a-​zA-Z0-9_. ^;\\`!@#\$%&(--)+=]/", "", $_FILES[$parameters["upload_key"]]["name"]);
                        $previous_version_id = isset($parameters["previous_version_id"]) ? $parameters["previous_version_id"] : 0;
                        $file_existant_version = $this->model->get_document_existant_version($_FILES[$parameters["upload_key"]]["name"], "file", $lineage, $previous_version_id);

                        if (empty($file_existant_version) || $this->get_module_properties($parameters["module"], "versioning")) {

                            $this->ci->config->load("allowed_file_uploads", true);
                            $config["max_size"] = $this->ci->config->item("allowed_upload_size_kilobite");
                            $config["upload_path"] = $this->temporary_upload_directory; 
                            $config["allowed_types"] = $this->ci->config->item($parameters["module"], "allowed_file_uploads");
                            $config["remove_spaces"] = false;
                            $config["overwrite"] = true;

                            $this->ci->load->library("upload", $config);
                            //check and create temp directory if not exists
                                $upload_path = $this->temporary_upload_directory;
                                if (!is_dir($upload_path)) {
                                 mkdir($upload_path, 0777, true);
                                    }
                            if ($this->ci->upload->do_upload($parameters["upload_key"])) {
                                $upload_data = $this->ci->upload->data();
                                $parent = $this->get_id_from_lineage($lineage);
                                $parent_visible_in_cp = $this->model->is_visible_in_cp($parent);
                                $parent_visible_in_ap = $this->model->is_visible_in_ap($parent);

                                $this->model->reset_fields();
                                setlocale(LC_ALL, "en_US.UTF-8");
                                $this->model->set_fields([
                                    "type" => "file",
                                    "name" => pathinfo($_FILES[$parameters["upload_key"]]["name"], PATHINFO_FILENAME),
                                    "extension" => pathinfo($_FILES[$parameters["upload_key"]]["name"], PATHINFO_EXTENSION),
                                    "size" => $_FILES[$parameters["upload_key"]]["size"],
                                    "parent" => $parent,
                                    "version" => empty($file_existant_version) ? 1 : $file_existant_version["version"] + 1,
                                    "document_type_id" => $this->get_optional_parameter_value($parameters, "document_type_id"),
                                    "document_status_id" => $this->get_optional_parameter_value($parameters, "document_status_id"),
                                    "comment" => $this->get_optional_parameter_value($parameters, "comment"),
                                    "module" => $parameters["module"],
                                    "module_record_id" => $this->get_optional_parameter_value($parameters, "module_record_id"),
                                    "system_document" => 0,
                                    "visible" => 1,
                                    "visible_in_cp" => isset($parameters["visible_in_cp"]) ? $parameters["visible_in_cp"]??0 : ($file_existant_version["visible_in_cp"]??0 || $parent_visible_in_cp ? 1 : 0),
                                    "visible_in_ap" => isset($parameters["visible_in_ap"]) ? $parameters["visible_in_ap"] : $parent_visible_in_ap,
                                    "createdOn" => date("Y-m-d H:i:s"),
                                    "createdBy" => $this->user_id,
                                    "createdByChannel" => $this->channel,
                                    "modifiedOn" => date("Y-m-d H:i:s"),
                                    "modifiedBy" => $this->user_id,
                                    "modifiedByChannel" => $this->channel
                                ]);

                                if (!empty($file_existant_version)) {
                                    $editable_fields = ["document_type_id", "document_status_id", "comment"];
                                    foreach ($editable_fields as $field) {
                                        if (!$this->model->get_field($field)) {
                                            $this->model->set_field($field, $file_existant_version[$field]);
                                        }
                                    }
                                    $this->model->set_fields([
                                        "initial_version_created_on" => $file_existant_version["createdOn"],
                                        "initial_version_created_by" => $file_existant_version["createdBy"],
                                        "initial_version_created_by_channel" => $file_existant_version["createdByChannel"]
                                    ]);
                                }

                                if ($this->model->insert()) {
                                    $this->model->set_field("lineage", $lineage . DIRECTORY_SEPARATOR . $this->model->get_field("id"));

                                    if ($this->model->update()) {
                                        // ENHANCED: Better path handling
                                        $destination_path = $this->get_module_container_path($parameters["module"]) . $this->model->get_field("lineage");
                                        $destination_dir = dirname($destination_path);

                                        // Create directory with proper permissions
                                        if (!is_dir($destination_dir)) {
                                            if (!mkdir($destination_dir, 0755, true)) {
                                                throw new Exception("Failed to create directory: " . $destination_dir);
                                            }
                                        }

                                        // Verify directory is writable
                                        if (!is_writable($destination_dir)) {
                                            throw new Exception("Destination directory not writable: " . $destination_dir);
                                        }

                                        if (rename($upload_data["full_path"], $destination_path)) {
                                            $uploaded_file = $this->model->get_document_full_details(["d.id" => $this->model->get_field("id")]);

                                            if (empty($file_existant_version)) {
                                                $response["status"] = true;
                                                $response["message"] = $this->ci->lang->line("file_uploaded");
                                                $response["file"] = $uploaded_file;
                                            } else {
                                                $this->file_versioning($file_existant_version, $uploaded_file, $response);
                                            }
                                        } else {
                                            // Detailed error information
                                            $error = error_get_last();
                                            $this->log_error("File move failed. Source: " . $upload_data["full_path"] .
                                                " | Destination: " . $destination_path .
                                                " | Error: " . ($error['message'] ?? 'Unknown error'));
                                            $response["message"] = $this->ci->lang->line("file_move_failed");
                                        }
                                    }
                                }
                            } else {
                                $response["message"] = strip_tags($this->ci->upload->display_errors());
                            }
                        } else {
                            $response["message"] = $this->ci->lang->line("file_already_exist");
                        }
                    } else {
                        $response["message"] = $this->ci->lang->line("too_long_file_name");
                    }
                } else {
                    $response["message"] = $this->ci->lang->line("empty_file_upload_not_allowed");
                }
            }
        } catch (Exception $e) {
            $this->log_error("Upload exception: " . $e->getMessage());
            $response["message"] = "System error: " . $e->getMessage();
        }

        $response["status"] = empty($response["status"]) ? false : $response["status"];
        $response["message"] = empty($response["message"]) ? ($response["status"] ? $this->ci->lang->line("file_uploaded_successfully") : $this->ci->lang->line("file_upload_failed")) :
            $response["message"];

        return $response;
    }
    public function replace_file($parameters)
    {
        setlocale(LC_ALL, "en_US.utf8");
        $response = [];
        if (isset($parameters["upload_key"]) && !empty($_FILES[$parameters["upload_key"]]) && $this->validate_parameters($parameters)) {
            if (0 < $_FILES[$parameters["upload_key"]]["size"]) {
                if (strlen($_FILES[$parameters["upload_key"]]["name"]) < $this->max_file_name_length) {
                    $_FILES[$parameters["upload_key"]]["name"] = preg_replace("/[^a-​zA-Z0-9_. ^;'`!@#\$%&(--)+=]/", "", $_FILES[$parameters["upload_key"]]["name"]);
                    $this->ci->config->load("allowed_file_uploads", true);
                    $config["max_size"] = $this->ci->config->item("allowed_upload_size_kilobite");
                    $config["upload_path"] = $this->temporary_upload_directory;
                    $config["allowed_types"] = $this->ci->config->item($parameters["module"], "allowed_file_uploads");
                    $config["remove_spaces"] = false;
                    $config["overwrite"] = true;
                    $this->ci->load->library("upload", $config);
                    if ($this->ci->upload->do_upload($parameters["upload_key"])) {
                        $upload_data = $this->ci->upload->data();
                        setlocale(LC_ALL, "en_US.UTF-8");
                        $this->model->fetch($parameters["id"]);
                        $this->model->set_fields(["modifiedOn" => date("Y-m-d H:i:s"), "modifiedBy" => $this->user_id, "modifiedByChannel" => $this->channel]);
                        if ($this->model->update() && rename($upload_data["full_path"], $this->get_module_container_path($parameters["module"]) . $this->model->get_field("lineage"))) {
                            $uploaded_file = $this->model->get_document_full_details(["d.id" => $this->model->get_field("id")]);
                            $response["status"] = true;
                            $response["message"] = $this->ci->lang->line("file_uploaded");
                            $response["file"] = $uploaded_file;
                        }
                    } else {
                        $response["message"] = strip_tags($this->ci->upload->display_errors());
                    }
                } else {
                    $response["message"] = $this->ci->lang->line("too_long_file_name");
                }
            } else {
                $response["message"] = $this->ci->lang->line("empty_file_upload_not_allowed");
            }
        }
        $response["status"] = empty($response["status"]) ? false : $response["status"];
        $response["message"] = empty($response["message"]) ? $response["status"] ? $this->ci->lang->line("file_uploaded_successfully") : $this->ci->lang->line("file_upload_failed") : $response["message"];
        return $response;
    }
    public function upload_directory($parameters, $auto_rename = false)
    {
        $response = [];
        if ($this->validate_parameters($parameters)) {
            $paths = explode("###", rtrim($parameters["folderext"], "###"));
            foreach ($paths as $key => $path) {
                $directory = dirname($path);
                $current_lineage = $parameters["lineage"];
                $directory_list = explode("/", $directory);
                foreach ($directory_list as $directory_item) {
                    $new_lineage = $this->create_folder_directory(["module" => $parameters["module"], "module_record_id" => $parameters["module_record_id"], "lineage" => $current_lineage, "name" => $directory_item]);
                    $current_lineage = $new_lineage["lineage"];
                }
                $file_response = $this->upload_file_for_directory(["module" => $parameters["module"], "module_record_id" => $parameters["module_record_id"], "lineage" => $current_lineage, "upload_key" => "uploadDir", "file_number" => $key]);
                array_push($response, $file_response);
            }
            $response = array_intersect_key($response, array_unique(array_column($response, "message")));
        }
        return $response;
    }
    public function create_folder_directory($parameters, $auto_rename = false)
    {
        $response = [];
        if ($this->validate_parameters($parameters)) {
            $lineage = $this->get_optional_parameter_value($parameters, "lineage");
            if (empty($lineage)) {
                $container = $this->get_container($parameters["module"], $this->get_optional_parameter_value($parameters, "module_record_id"), $this->get_optional_parameter_value($parameters, "container_name"));
                $lineage = $container["lineage"];
            }
            $folder_existant_version = $this->model->get_document_existant_version($parameters["name"], "folder", $lineage);
            if (!$folder_existant_version || $folder_existant_version && $auto_rename) {
                $parent = $this->get_id_from_lineage($lineage);
                $parent_visible_in_cp = $this->model->is_visible_in_cp($parent);
                $parent_visible_in_ap = $this->model->is_visible_in_ap($parent);
                $this->model->reset_fields();
                $this->model->set_fields(["name" => $folder_existant_version && $auto_rename ? $this->auto_generate_document_name($parameters["name"], $parameters["name"], "folder", $lineage, $parameters["name"]) : $parameters["name"], "type" => "folder", "parent" => $parent, "module" => $parameters["module"], "module_record_id" => $this->get_optional_parameter_value($parameters, "module_record_id"), "system_document" => isset($parameters["system_document"]) ? $parameters["system_document"] : 0, "visible" => 1, "visible_in_cp" => $parent_visible_in_cp, "visible_in_ap" => $parent_visible_in_ap, "createdOn" => date("Y-m-d H:i:s"), "createdBy" => $this->user_id, "createdByChannel" => $this->channel, "modifiedOn" => date("Y-m-d H:i:s"), "modifiedBy" => $this->user_id, "modifiedByChannel" => $this->channel]);
                if ($this->model->insert()) {
                    $this->model->set_field("lineage", $lineage . DIRECTORY_SEPARATOR . $this->model->get_field("id"));
                    if ($this->model->update() && mkdir($this->get_module_container_path($parameters["module"]) . $this->model->get_field("lineage"))) {
                        $response["status"] = true;
                        $response["id"] = $this->model->get_field("id");
                        $response["lineage"] = $this->model->get_field("lineage");
                    }
                }
            } else {
                $response["status"] = false;
                $response["message"] = $this->ci->lang->line("folder_name_exists");
                $response["lineage"] = $folder_existant_version["lineage"];
            }
        }
        $response["status"] = empty($response["status"]) ? false : $response["status"];
        $response["message"] = empty($response["message"]) ? $response["status"] ? $this->ci->lang->line("folder_created_successfully") : $this->ci->lang->line("folder_creation_failed") : $response["message"];
        return $response;
    }
    public function upload_file_for_directory($parameters)
    {
        $index = $parameters["file_number"];
        setlocale(LC_ALL, "en_US.utf8");
        $response = [];
        if (isset($parameters["upload_key"]) && !empty($_FILES[$parameters["upload_key"]]) && $this->validate_parameters($parameters)) {
            if (0 < $_FILES[$parameters["upload_key"]]["size"][$index]) {
                if (strlen($_FILES[$parameters["upload_key"]]["name"][$index]) < $this->max_file_name_length) {
                    $lineage = $this->get_optional_parameter_value($parameters, "lineage");
                    if (empty($lineage)) {
                        $container = $this->get_container($parameters["module"], $this->get_optional_parameter_value($parameters, "module_record_id"), $this->get_optional_parameter_value($parameters, "container_name"));
                        $lineage = $container["lineage"];
                    }
                    $_FILES[$parameters["upload_key"]]["name"][$index] = preg_replace("/[^a-​zA-Z0-9_. ^;'`!@#\$%&(--)+=]/", "", $_FILES[$parameters["upload_key"]]["name"][$index]);
                    $file_existant_version = $this->model->get_document_existant_version($_FILES[$parameters["upload_key"]]["name"][$index], "file", $lineage);
                    if (empty($file_existant_version) || $this->get_module_properties($parameters["module"], "versioning")) {
                        $_FILES["file"]["name"] = $_FILES[$parameters["upload_key"]]["name"][$index];
                        $_FILES["file"]["type"] = $_FILES[$parameters["upload_key"]]["type"][$index];
                        $_FILES["file"]["tmp_name"] = $_FILES[$parameters["upload_key"]]["tmp_name"][$index];
                        $_FILES["file"]["error"] = $_FILES[$parameters["upload_key"]]["error"][$index];
                        $_FILES["file"]["size"] = $_FILES[$parameters["upload_key"]]["size"][$index];
                        $this->ci->config->load("allowed_file_uploads", true);
                        $config["max_size"] = $this->ci->config->item("allowed_upload_size_kilobite");
                        $config["upload_path"] = $this->temporary_upload_directory;
                        $config["allowed_types"] = $this->ci->config->item($parameters["module"], "allowed_file_uploads");
                        $config["remove_spaces"] = false;
                        $config["overwrite"] = true;
                        $this->ci->load->library("upload", $config);
                        if ($this->ci->upload->do_upload("file")) {
                            $upload_data = $this->ci->upload->data();
                            $parent = $this->get_id_from_lineage($lineage);
                            $parent_visible_in_cp = $this->model->is_visible_in_cp($parent);
                            $parent_visible_in_ap = $this->model->is_visible_in_ap($parent);
                            $this->model->reset_fields();
                            $this->model->set_fields(["type" => "file", "name" => pathinfo($_FILES[$parameters["upload_key"]]["name"][$index], PATHINFO_FILENAME), "extension" => pathinfo($_FILES[$parameters["upload_key"]]["name"][$index], PATHINFO_EXTENSION), "size" => $_FILES[$parameters["upload_key"]]["size"][$index], "parent" => $parent, "version" => empty($file_existant_version) ? 1 : $file_existant_version["version"] + 1, "document_type_id" => $this->get_optional_parameter_value($parameters, "document_type_id"), "document_status_id" => $this->get_optional_parameter_value($parameters, "document_status_id"), "comment" => $this->get_optional_parameter_value($parameters, "comment"), "module" => $parameters["module"], "module_record_id" => $this->get_optional_parameter_value($parameters, "module_record_id"), "system_document" => 0, "visible" => 1, "visible_in_cp" => isset($parameters["visible_in_cp"]) ? $parameters["visible_in_cp"] : $parent_visible_in_cp, "visible_in_ap" => isset($parameters["visible_in_ap"]) ? $parameters["visible_in_ap"] : $parent_visible_in_ap, "createdOn" => date("Y-m-d H:i:s"), "createdBy" => $this->user_id, "createdByChannel" => $this->channel, "modifiedOn" => date("Y-m-d H:i:s"), "modifiedBy" => $this->user_id, "modifiedByChannel" => $this->channel]);
                            if (!empty($file_existant_version)) {
                                $editable_fields = ["document_type_id", "document_status_id", "comment"];
                                foreach ($editable_fields as $field) {
                                    if (!$this->model->get_field($field)) {
                                        $this->model->set_field($field, $file_existant_version[$field]);
                                    }
                                }
                                $this->model->set_fields(["initial_version_created_on" => $file_existant_version["createdOn"], "initial_version_created_by" => $file_existant_version["createdBy"], "initial_version_created_by_channel" => $file_existant_version["createdByChannel"]]);
                            }
                            if ($this->model->insert()) {
                                $this->model->set_field("lineage", $lineage . DIRECTORY_SEPARATOR . $this->model->get_field("id"));
                                if ($this->model->update() && rename($upload_data["full_path"], $this->get_module_container_path($parameters["module"]) . $this->model->get_field("lineage"))) {
                                    $uploaded_file = $this->model->get_document_full_details(["d.id" => $this->model->get_field("id")]);
                                    if (empty($file_existant_version)) {
                                        $response["status"] = true;
                                        $response["message"] = $this->ci->lang->line("file_uploaded");
                                        $response["file"] = $uploaded_file;
                                    } else {
                                        $this->file_versioning($file_existant_version, $uploaded_file, $response);
                                    }
                                }
                            }
                        } else {
                            $response["message"] = $this->ci->lang->line("file_upload_failed");
                        }
                    } else {
                        $response["message"] = $this->ci->lang->line("file_already_exist");
                    }
                } else {
                    $response["message"] = $this->ci->lang->line("too_long_file_name");
                }
            } else {
                $response["message"] = $this->ci->lang->line("empty_file_upload_not_allowed");
            }
        }
        $response["status"] = empty($response["status"]) ? false : $response["status"];
        $response["message"] = empty($response["message"]) ? $response["status"] ? $this->ci->lang->line("file_uploaded_successfully") : $this->ci->lang->line("file_upload_failed") : $response["message"];
        return $response;
    }
    private function file_versioning($file_existant_version, $uploaded_file, &$response)
    {
        $versions_container = [];
        if ($file_existant_version["version"] == 1) {
            $this->model->reset_fields();
            $this->model->set_fields(["name" => $uploaded_file["id"] . "_versions", "type" => "folder", "parent" => $uploaded_file["parent"], "module" => $uploaded_file["module"], "module_record_id" => $uploaded_file["module_record_id"], "system_document" => 1, "visible" => 0, "visible_in_cp" => 0, "visible_in_ap" => 0, "createdOn" => date("Y-m-d H:i:s"), "createdBy" => $this->user_id, "createdByChannel" => $this->channel, "modifiedOn" => date("Y-m-d H:i:s"), "modifiedBy" => $this->user_id, "modifiedByChannel" => $this->channel]);
            if ($this->model->insert()) {
                $versions_container_lineage = empty($uploaded_file["parent_lineage"]) ? DIRECTORY_SEPARATOR . $uploaded_file["parent"] : $uploaded_file["parent_lineage"];
                $this->model->set_field("lineage", $versions_container_lineage . DIRECTORY_SEPARATOR . $this->model->get_field("id"));
                if ($this->model->update() && mkdir($this->get_module_container_path($file_existant_version["module"]) . $this->model->get_field("lineage"))) {
                    $versions_container = $this->model->get_fields();
                }
            }
        } else {
            $this->model->reset_fields();
            $this->model->fetch(["name" => $file_existant_version["id"] . "_versions", "system_document" => 1]);
            $this->model->set_field("name", $uploaded_file["id"] . "_versions");
            if ($this->model->update()) {
                $versions_container = $this->model->get_fields();
            }
        }
        if (!empty($versions_container)) {
            $this->model->reset_fields();
            $this->model->fetch($file_existant_version["id"]);
            $versioned_file_lineage = $versions_container["lineage"] . DIRECTORY_SEPARATOR . $this->model->get_field("id");
            $this->model->set_fields(["parent" => $versions_container["id"], "lineage" => $versioned_file_lineage, "visible" => 0, "visible_in_cp" => 0, "visible_in_ap" => 0]);
            if ($this->model->update() && rename($this->get_module_container_path($file_existant_version["module"]) . $file_existant_version["lineage"], $this->get_module_container_path($file_existant_version["module"]) . $versioned_file_lineage)) {
                $response["status"] = true;
                $response["file"] = $uploaded_file;
            }
        }
    }
    public function create_folder($parameters, $auto_rename = false)
    {
        $response = [];
        if ($this->validate_parameters($parameters)) {
            $lineage = $this->get_optional_parameter_value($parameters, "lineage");
            if (empty($lineage)) {
                $container = $this->get_container($parameters["module"], $this->get_optional_parameter_value($parameters, "module_record_id"), $this->get_optional_parameter_value($parameters, "container_name"), NULL, $this->get_optional_parameter_value($parameters, "child_ap_visible"));
                $lineage = $container["lineage"];
            }
            $folder_existant_version = $this->model->get_document_existant_version($parameters["name"], "folder", $lineage);
            if (!$folder_existant_version || $folder_existant_version && $auto_rename) {
                $parent = $this->get_id_from_lineage($lineage);
                $parent_visible_in_cp = $this->model->is_visible_in_cp($parent);
                $parent_visible_in_ap = $this->model->is_visible_in_ap($parent);
                $this->model->reset_fields();
                $this->model->set_fields(["name" => $folder_existant_version && $auto_rename ? $this->auto_generate_document_name($parameters["name"], $parameters["name"], "folder", $lineage, $parameters["name"]) : $parameters["name"], "type" => "folder", "parent" => $parent, "module" => $parameters["module"], "module_record_id" => $this->get_optional_parameter_value($parameters, "module_record_id"), "system_document" => isset($parameters["system_document"]) ? $parameters["system_document"] : 0, "visible" => 1, "visible_in_cp" => $parent_visible_in_cp || isset($parameters["visible_in_cp"]) && $parameters["visible_in_cp"] ? 1 : 0, "visible_in_ap" => $parent_visible_in_ap, "createdOn" => date("Y-m-d H:i:s"), "createdBy" => $this->user_id, "createdByChannel" => $this->channel, "modifiedOn" => date("Y-m-d H:i:s"), "modifiedBy" => $this->user_id, "modifiedByChannel" => $this->channel]);
                if ($this->model->insert()) {
                    $this->model->set_field("lineage", $lineage . DIRECTORY_SEPARATOR . $this->model->get_field("id"));
                    if ($this->model->update() && mkdir($this->get_module_container_path($parameters["module"]) . $this->model->get_field("lineage"))) {
                        $response["status"] = true;
                        $response["id"] = $this->model->get_field("id");
                        $response["lineage"] = $this->model->get_field("lineage");
                    }
                }
            } else {
                $response["status"] = false;
                $response["message"] = $this->ci->lang->line("folder_name_exists");
            }
        }
        $response["status"] = empty($response["status"]) ? false : $response["status"];
        $response["message"] = empty($response["message"]) ? $response["status"] ? $this->ci->lang->line("folder_created_successfully") : $this->ci->lang->line("folder_creation_failed") : $response["message"];
        return $response;
    }
    private function auto_generate_document_name($document_full_name, $document_name, $type, $parent_lineage, $initial_name, $i = 1)
    {
        $document_existant_version = $this->model->get_document_existant_version($document_full_name, $type, $parent_lineage);
        if (!empty($document_existant_version)) {
            $document_name = $initial_name . " (" . $i . ")";
            $document_full_name = !empty($document_existant_version["extension"]) ? $document_name . "." . $document_existant_version["extension"] : $document_name;
            return $this->auto_generate_document_name($document_full_name, $document_name, $type, $parent_lineage, $initial_name, $i + 1);
        }
        return $document_name;
    }
    public function rename_document($module, $document_id, $document_type, $new_name, $newest_version = false)
    {
        $response = [];
        $response["status"] = true;
        $this->model->reset_fields();
        if ($newest_version && $document_type == "file") {
            $document = $this->get_document_newest_version_details($document_id);
            if (!empty($document["id"])) {
                $document_id = $document["id"];
            }
        }
        if ($this->model->fetch(["module" => $module, "id" => $document_id, "type" => $document_type])) {
            if (!$this->model->get_document_details(["name" => $new_name, "extension" => $this->model->get_field("extension"), "type" => $this->model->get_field("type"), "parent" => $this->model->get_field("parent")])) {
                $this->_rename_document($new_name);
            } else {
                if ($this->model->fetch(["name" => $new_name, "module" => $module, "type" => $document_type, "parent" => $this->model->get_field("parent")])) {
                    if ($document_id != $this->model->get_field("id")) {
                        $response["status"] = false;
                        $response["message"] = $this->ci->lang->line("document_name_already_exists");
                    } else {
                        $this->_rename_document($new_name);
                    }
                }
            }
        } else {
            $response["message"] = $this->ci->lang->line("document_does_not_exist");
        }
        $response["status"] = empty($response["status"]) ? false : $response["status"];
        $response["message"] = empty($response["message"]) ? $response["status"] ? $this->ci->lang->line("updates_saved_successfully") : $this->ci->lang->line("updates_failed") : $response["message"];
        return $response;
    }
    private function _rename_document($new_name)
    {
        $this->model->set_fields(["name" => $new_name, "modifiedOn" => date("Y-m-d H:i:s"), "modifiedBy" => $this->user_id ?? $this->model->get_field("createdBy"), "modifiedByChannel" => $this->channel]);
        if (!$this->model->update()) {
            $response["status"] = false;
            return $response["status"];
        }
    }
    public function edit_documents($documents)
    {
        $response = [];
        $editable_fields = ["document_type_id", "document_status_id", "comment"];
        $documents_count = count($documents);
        $updated_documents_count = 0;
        foreach ($documents as $document) {
            $this->model->reset_fields();
            if ($this->model->fetch(["module" => $document["module"], "id" => $document["id"]])) {
                if ($document["is_accessible"] != 1) {
                    $response["validation_errors"][$document["id"]] = sprintf($this->ci->lang->line("update_not_accessible"), $this->ci->lang->line($document["type"]), $document["name"]);
                } else {
                    foreach ($editable_fields as $field) {
                        $field_value = empty($document[$field]) ? NULL : (is_array($document[$field]) ? $document[$field]["value"] : $document[$field]);
                        $this->model->set_field($field, $field_value);
                    }
                    if ($document["module"] == "contract") {
                        $this->ci->load->model("approval_signature_document", "approval_signature_documentfactory");
                        $this->ci->approval_signature_document = $this->ci->approval_signature_documentfactory->get_instance();
                        $this->ci->approval_signature_document->reset_fields();
                        $edit = $this->ci->approval_signature_document->fetch(["document_id" => $document["id"]]);
                        $this->ci->approval_signature_document->set_field("to_be_approved", $document["to_be_approved"]);
                        $this->ci->approval_signature_document->set_field("to_be_signed", $document["to_be_signed"]);
                        if ($edit) {
                            $this->ci->approval_signature_document->update();
                        } else {
                            $this->ci->approval_signature_document->set_field("document_id", $document["id"]);
                            $this->ci->approval_signature_document->insert();
                        }
                    }
                    $this->model->set_fields(["modifiedOn" => date("Y-m-d H:i:s"), "modifiedBy" => $this->user_id, "modifiedByChannel" => $this->channel]);
                    if ($this->model->update()) {
                        $updated_documents_count++;
                    }
                }
            }
        }
        $response["status"] = $documents_count == $updated_documents_count ? true : false;
        $response["message"] = $response["status"] ? $this->ci->lang->line("updates_saved_successfully") : $this->ci->lang->line("updates_failed");
        return $response;
    }
    public function share_folder($module, $folder_id, $private, $share_users, $api_user = 0)
    {
        $logged_user = $api_user ? $api_user : $this->ci->session->userdata("AUTH_user_id");
        $response = [];
        $this->model->reset_fields();
        if ($this->model->fetch(["module" => $module, "id" => $folder_id])) {
            $system_preferences = $this->ci->session->userdata("systemPreferences");
            if (isset($system_preferences["systemTimezone"]) && !empty($system_preferences["systemTimezone"])) {
                date_default_timezone_set($system_preferences["systemTimezone"]);
            }
            $this->model->set_fields(["private" => $private, "modifiedOn" => date("Y-m-d H:i:s"), "modifiedBy" => $this->user_id, "modifiedByChannel" => $this->channel]);
            if ($this->model->update()) {
                $share_users = $private ? array_merge(empty($share_users) ? [] : array_filter($share_users), [$this->model->get_field("createdBy"), $logged_user]) : [];
                $watchers_users["users"] = ["recordId" => $folder_id, "users" => $share_users];
                $response["status"] = $this->ci->document_managment_user->insert_watchers_users($watchers_users);
            }
        } else {
            $response["message"] = $this->ci->lang->line("document_does_not_exist");
        }
        $response["status"] = empty($response["status"]) ? false : $response["status"];
        $response["message"] = $response["status"] ? $this->ci->lang->line("updates_saved_successfully") : $this->ci->lang->line("updates_failed");
        return $response;
    }
    public function get_file_download_data($module, $file_id, $newest_version = false)
    {
        $response = [];
        if ($newest_version) {
            $file = $this->model->get_document_newest_version_details($file_id);
        } else {
            $file = $this->model->get_document_details(["module" => $module, "id" => $file_id, "type" => "file"]);
        }
        if (!empty($file)) {
            $this->model->fetch($file["parent"]);
            $private = $this->model->get_field("private");
            if ($private == 1) {
                $allowed_folder = $this->check_folder_privacy($file["parent"]);
                if (empty($allowed_folder)) {
                    return ["status" => false, "message" => $this->ci->lang->line("not_accessible_file")];
                }
            }
            if (!empty($file)) {
                ini_set("memory_limit", "256M");
                if ($content = file_get_contents($this->get_module_container_path($file["module"]) . $file["lineage"])) {
                    $response["data"] = ["file_name" => $file["full_name"], "file_content" => $content, "file_type" => $file["extension"], "url" => $this->get_module_container_path($file["module"]) . $file["lineage"]];
                    $response["status"] = true;
                }
            } else {
                $response["message"] = $this->ci->lang->line("document_does_not_exist");
            }
            $response["status"] = empty($response["status"]) ? false : $response["status"];
            $response["message"] = empty($response["message"]) ? $response["status"] ? $this->ci->lang->line("download_succeeded") : $this->ci->lang->line("download_failed") : $response["message"];
        } else {
            $response["message"] = $this->ci->lang->line("document_does_not_exist");
        }
        return $response;
    }
    public function download_file($module, $file_id, $newest_version = false)
    {
        $this->ci->load->helper("download");
        $response = $this->get_file_download_data($module, $file_id, $newest_version);
        if ($response["status"]) {
            force_download(addslashes($response["data"]["file_name"]), $response["data"]["file_content"]);
            exit;
        }
        return $response;
    }
    public function download_files_as_zip($module, $files_ids, $zip_file_name)
    {
        $zip = new ZipArchive();
        $zip_file = $this->temporary_upload_directory . DIRECTORY_SEPARATOR . $zip_file_name . ".zip";
        $zip->open($zip_file, ZipArchive::CREATE);
        foreach ($files_ids as $file_id) {
            $response = $this->get_file_download_data($module, $file_id);
            if ($response["status"]) {
                $zip->addFromString(addslashes($response["data"]["file_name"]), $response["data"]["file_content"]);
            } else {
                return $response;
            }
        }
        $zip->close();
        header("Content-type: application/zip");
        header("Content-Disposition: attachment; filename=\"" . basename($zip_file) . "\"");
        header("Content-length: " . filesize($zip_file));
        ob_clean();
        flush();
        readfile($zip_file);
        unlink($zip_file);
        exit;
    }
    public function download_files_and_folders_as_zip($module, $module_controller, $docs_ids = [], $zip_file_name = "Documents")
    {
        $zip = new ZipArchive();
        $zip_file = $this->temporary_upload_directory . DIRECTORY_SEPARATOR . $zip_file_name . ".zip";
        $zip->open($zip_file, ZipArchive::CREATE);
        if (isset($docs_ids)) {
            foreach ($docs_ids as $doc_id) {
                $document_details = $this->model->get_document_details(["module" => $module, "id" => $doc_id]);
                if ($document_details["type"] == "folder") {
                    $this->_get_folder_files($zip, $module, $module_controller, $document_details["id"], $document_details["lineage"]);
                } else {
                    $response = $this->get_file_download_data($module, $doc_id);
                    if (isset($response["status"]) && $response["status"]) {
                        $zip->addFromString(addslashes($response["data"]["file_name"]), $response["data"]["file_content"]);
                    } else {
                        return $response;
                    }
                }
            }
        }
        $zip->close();
        header("Content-type: application/zip");
        header("Content-Disposition: attachment; filename=\"" . basename($zip_file) . "\"");
        header("Content-length: " . filesize($zip_file));
        ob_clean();
        flush();
        readfile($zip_file);
        unlink($zip_file);
        exit;
    }
    private function _get_folder_files($zip, $module, $module_controller, $directory_id, $lineage)
    {
        $rootPath = $this->ci->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . $module_controller . $lineage;
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath), RecursiveIteratorIterator::LEAVES_ONLY);
        foreach ($files as $file) {
            if (!$file->isDir()) {
                $file_id = $file->getFileName();
                $response = $this->get_file_download_data($module, $file_id);
                $file_document_details = $this->model->get_document_details(["module" => $module, "id" => $file_id]);
                $directories = explode("\\", strstr($file_document_details["lineage"], $directory_id));
                $directories = array_slice($directories, 0, count($directories) - 1);
                foreach ($directories as $index => $directory) {
                    $directories[$index] = $this->model->get_document_details(["module" => $module, "id" => $directory])["name"];
                }
                $parent_directory = $this->model->get_document_details(["module" => $module, "id" => $file_document_details["parent"]]);
                if (!$parent_directory["system_document"]) {
                    if (isset($response["status"]) && $response["status"]) {
                        $zip->addFromString(addslashes(implode("/", $directories) . "/" . $response["data"]["file_name"]), $response["data"]["file_content"]);
                    } else {
                        return $response;
                    }
                }
            }
        }
        return $zip;
    }
    public function list_file_versions($module, $file_id, $newest_version = false)
    {
        $response = [];
        if ($newest_version) {
            $file = $this->model->get_document_newest_version_details($file_id);
        } else {
            $file = $this->model->get_document_details(["module" => $module, "id" => $file_id]);
        }
        if (!empty($file)) {
            $response["data"] = ["file_name" => $file["full_name"], "file_versions" => $this->model->get_file_versions($file["id"])];
            $response["status"] = true;
        } else {
            $response["message"] = $this->ci->lang->line("document_does_not_exist");
        }
        $response["status"] = empty($response["status"]) ? false : $response["status"];
        return $response;
    }
    public function delete_document($module, $document_id, $newest_version = false)
    {
        $response = [];
        if ($newest_version) {
            $document = $this->get_document_newest_version_details($document_id);
        } else {
            $document = $this->model->get_document_details(["module" => $module, "id" => $document_id]);
        }
        if (!empty($document)) {
            $folder_id = $document["type"] == "file" ? $document["parent"] : $document["id"];
            $this->model->fetch($folder_id);
            $private = $this->model->get_field("private");
            if ($private == 1) {
                $allowed_folder = $this->check_folder_privacy($folder_id);
                if (empty($allowed_folder)) {
                    return ["status" => false, "message" => $this->ci->lang->line("not_accessible_" . $document["type"] . "")];
                }
            }
            if ($document["type"] == "file" || $this->model->folder_is_empty($document["lineage"])) {
                if ($this->model->delete($document["id"])) {
                    $document_path = $this->get_module_container_path($document["module"]) . $document["lineage"];
                    if ($document["type"] == "file" ? unlink($document_path) : rmdir($document_path)) {
                        if ($document["type"] == "file") {
                            $file_versions_container = $this->model->get_document_details(["name" => $document["id"] . "_versions", "system_document" => 1]);
                            if (!empty($file_versions_container) && $this->model->delete_folder_and_content($file_versions_container["id"])) {
                                $response["status"] = $this->delete_tree($this->get_module_container_path($file_versions_container["module"]) . $file_versions_container["lineage"]);
                            }
                            $response["status"] = isset($response["status"]) ? $response["status"] : true;
                        } else {
                            $response["status"] = true;
                        }
                    }
                }
            } else {
                $response["message"] = $this->ci->lang->line("folder_not_empty");
            }
        } else {
            $response["message"] = $this->ci->lang->line("document_does_not_exist");
        }
        $response["status"] = empty($response["status"]) ? false : $response["status"];
        $response["message"] = empty($response["message"]) ? $response["status"] ? $this->ci->lang->line("document_deleted_successfully") : $this->ci->lang->line("document_deletion_failed") : $response["message"];
        return $response;
    }
    public function delete_module_record_container($module, $module_record_id)
    {
        $module_record_container = $this->get_container($module, $module_record_id);
        if (!empty($module_record_container)) {
            if ($this->model->delete_folder_and_content($module_record_container["id"], true)) {
                return $this->delete_tree($this->documents_root_direcotry . $this->get_module_properties($module, "container") . $module_record_container["lineage"]);
            }
            return false;
        }
        return false;
    }
    public function hard_delete_by_id($module, $lineage, $document_id)
    {
        if ($this->model->delete_folder_by_id($document_id)) {
            return $this->delete_tree($this->documents_root_direcotry . $this->get_module_properties($module, "container") . $lineage);
        }
        return false;
    }
    public function delete_tree($folder)
    {
        $documents = array_diff(scandir($folder), [".", ".."]);
        foreach ($documents as $document) {
            is_dir($folder . "/" . $document);
            is_dir($folder . "/" . $document) ? $this->delete_tree($folder . "/" . $document) : unlink($folder . "/" . $document);
        }
        return rmdir($folder);
    }
    public function universal_search($module, $term)
    {
        return $this->model->fetch_universal_search_data($module, $term, $this->ci->is_auth->get_user_id(), $this->ci->is_auth->get_override_privacy());
    }
    public function get_container($module, $module_record_id, $container_name = NULL, $container_parent_name = NULL, $child_ap_visible = false)
    {
        $container = NULL;
        $is_module_record_container = empty($container_name);
        $container_name = $is_module_record_container ? empty($module_record_id) ? $module . "_container" : $module . "_" . (int) $module_record_id : $container_name;
        if (!empty($container_name)) {
            $container_fetch_criteria = ["name" => $container_name, "module" => $module, "module_record_id" => $module_record_id, "system_document" => 1];
            $container = $this->model->get_document_details($container_fetch_criteria);
            if (empty($container)) {
                $visible_in_ap = false;
                if ($module == "case" && !$this->release_script) {
                    $this->ci->load->model("legal_case", "legal_casefactory");
                    $this->ci->legal_case = $this->ci->legal_casefactory->get_instance();
                    $shared_documents_with_advisors = $this->ci->legal_case->shared_documents_with_advisors($module_record_id);
                    if ($shared_documents_with_advisors) {
                        $visible_in_ap = true;
                    }
                }
                if ($module == "case" && $this->release_script && $child_ap_visible) {
                    $visible_in_ap = true;
                }
                $container_lineage = "";
                $container_fields = ["name" => $container_name, "type" => "folder", "module" => $module, "module_record_id" => $module_record_id, "system_document" => 1, "visible" => $is_module_record_container ? 0 : 1, "visible_in_cp" => $container_name == "Matter Notes Attachments" || $container_name == "Contract_Notes_Attachments" ? 1 : 0, "visible_in_ap" => $visible_in_ap, "createdOn" => date("Y-m-d H:i:s"), "createdBy" => $this->user_id, "createdByChannel" => $this->channel, "modifiedOn" => date("Y-m-d H:i:s"), "modifiedBy" => $this->user_id, "modifiedByChannel" => $this->channel];
                if (!$is_module_record_container) {
                    $module_record_container = $this->get_container($module, $module_record_id, $container_parent_name);
                    $container_fields["parent"] = $module_record_container["id"];
                    $container_lineage = $module_record_container["lineage"];
                }
                $this->model->reset_fields();
                $this->model->set_fields($container_fields);
                if ($this->model->insert()) { 
                    $this->model->set_field("lineage", $container_lineage . DIRECTORY_SEPARATOR . $this->model->get_field("id"));
                    if ($this->model->update()) {  
                        if (!file_exists($this->get_module_container_path($module) . $this->model->get_field("lineage"))) {
                            mkdir($this->get_module_container_path22aug2025($module) . $this->model->get_field("lineage"));
                        }
                        $container = $this->model->get_document_details(["id" => $this->model->get_field("id")]);
                    }
                }
            }
        }
        return $container;
    }
    public function get_optional_parameter_value($paramters, $paramter_key)
    {
        return isset($paramters[$paramter_key]) && $paramters[$paramter_key] !== false && $paramters[$paramter_key] != "" ? $paramters[$paramter_key] : NULL;
    }
    public function get_module_properties($module, $property = NULL)
    { 
        return empty($property) ? $this->modules_properties[$module] : $this->modules_properties[$module][$property];
    }
    public function get_module_container_path22aug2025($module)
    {
        return $this->documents_root_direcotry . $this->get_module_properties($module, "container");
    }
    public function get_module_container_path($module)
    {
        $path = $this->documents_root_direcotry . $this->get_module_properties($module, "container");

        // Normalize path (convert relative to absolute, normalize separators)
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

        // Handle relative paths
        if (strpos($path, '..' . DIRECTORY_SEPARATOR) !== false) {
            $path = realpath($path);
            if ($path === false) {
                // If realpath fails, try to resolve manually
                $base = rtrim($this->documents_root_direcotry, DIRECTORY_SEPARATOR);
                $path = $base . DIRECTORY_SEPARATOR . ltrim($this->get_module_properties($module, "container"), DIRECTORY_SEPARATOR);
            }
        }

        // Ensure the path ends with directory separator
        return rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }
    public function get_id_from_lineage($lineage)
    {
        $exploded_lineage = explode(DIRECTORY_SEPARATOR, $lineage);
        $id = $exploded_lineage[count($exploded_lineage) - 1];
        return empty($id) ? NULL : $id;
    }
    public function load_module_model($module)
    {
        $model = NULL;
        $model_properites = $this->get_module_properties($module, "model");
        if (empty($this->ci->{$model_properites["name"]})) {
            if ($model_properites["factory"]) {
                $this->ci->load->model($model_properites["name"], $model_properites["name"] . "factory");
                $model = $this->ci->{$model_properites["name"]} = $this->ci->{$model_properites["name"] . "factory"}->get_instance();
            } else {
                $model = $this->ci->load->model($model_properites["name"]);
            }
        } else {
            $model = $this->ci->{$model_properites["name"]};
        }
        return $model;
    }
    private function validate_parameters($parameters)
    {
        if (!isset($parameters["module"])) {
            return false;
        }
        if ($this->get_optional_parameter_value($parameters, "module_record_id") !== NULL && !$this->release_script) {
            $module_model = $this->load_module_model($parameters["module"]);
            $module_model_properties = $this->get_module_properties($parameters["module"], "model");
            $module_record_fetch = array_merge(["id" => $parameters["module_record_id"]], isset($module_model_properties["fetch_extra"]) ? $module_model_properties["fetch_extra"] : []);
            $module_record_verified = $module_model->fetch($module_record_fetch);
            if (!$module_model->fetch($module_record_fetch)) {
                return false;
            }
        }
        if ($this->get_optional_parameter_value($parameters, "lineage") !== NULL) {
            $lineage_document_propertiers = ["type" => "folder", "lineage" => $parameters["lineage"], "module" => $parameters["module"]];
            if (!empty($parameters["module_record_id"])) {
                $lineage_document_propertiers["module_record_id"] = $parameters["module_record_id"];
            }
            if (!$this->model->get_document_details($lineage_document_propertiers)) {
                return false;
            }
        }
        return true;
    }
    public function generate_document($template_record, $model, $model_id, $category, $module, $extra_data = 0)
    {
        $data = [];
        $response = [];
        $template_dir = $this->documents_root_direcotry . "docs" . $template_record["lineage"];
        $templates = $this->model->load_documents("doc", "", $template_record["lineage"], "");
        $all_templates = $templates["data"];
        $templates = [];
        foreach ($all_templates as $key => $template) {
            if ($template["type"] == "file" && $template["extension"] == "docx") {
                $templates[$template["id"]] = $template["name"];
            }
        }
        $data["templates"] = $templates;
        $data["templates"][""] = $this->ci->lang->line("choose_template");
        $this->ci->load->model("doc_generator");
        $data["model_variables"] = $this->ci->doc_generator->get_variables($model, $category);
        $data["extra_data"] = $extra_data ?? false;
        if ($this->ci->input->get("action", true) == "list") {
            $response["html"] = $this->ci->load->view("document_generator/frontend/list_templates", $data, true);
        } else {
            $template_id = $this->ci->input->get("template_id", true) ? $this->ci->input->get("template_id", true) : $this->ci->input->post("template_id", true);
            $this->model->fetch(["id" => $template_id]);
            $tmp_file = $this->ci->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . rand(1000, 9999) . $this->model->get_field("name") . "." . $this->model->get_field("extension");
            $tmp_file_txt = $this->ci->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . rand(1000, 9999) . $this->model->get_field("name") . ".txt";
            $tmp_file_to_save = $this->ci->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . rand(1000, 9999) . $this->model->get_field("name") . "." . $this->model->get_field("extension");
            copy($template_dir . DIRECTORY_SEPARATOR . $template_id, $tmp_file);
            copy($template_dir . DIRECTORY_SEPARATOR . $template_id, $tmp_file_to_save);
            require_once substr(COREPATH, 0, -12) . "/application/libraries/phpdocx-advanced/classes/CreateDocx.php";
            $options = ["paragraph" => false, "list" => false, "table" => false, "footnote" => false, "endnote" => false, "chart" => 0];
            CreateDocx::DOCX2TXT($tmp_file, $tmp_file_txt, $options);
            $text = file_get_contents($tmp_file_txt);
            $docx_to_read = new CreateDocx();
            $docx_to_read->addText(strip_tags($text));
            $docx_to_read->createDocx(preg_replace("/\\.[^.\\s]{3,4}\$/", "", $tmp_file));
            $docx_to_read = new CreateDocxFromTemplate($tmp_file);
            $docx_to_read::$_templateSymbol = "%%";
            $docx = new CreateDocxFromTemplate($tmp_file_to_save);
            unlink($tmp_file);
            unlink($tmp_file_txt);
            unlink($tmp_file_to_save);
            if ($this->ci->input->get("action", true) == "read") {
                $model_data = NULL;
                if ($model_id && $model) {
                    $this->ci->load->model("custom_field", "custom_fieldfactory");
                    $this->ci->custom_field = $this->ci->custom_fieldfactory->get_instance();
                    $custom_data = $this->ci->custom_field->load_custom_fields_as_array($model_id, $this->ci->{$model}->modelName);
                    $model_data = array_merge($this->ci->{$model}->get_document_generator_data($model_id, $category), $custom_data);
                }
                $template_variables = $docx_to_read->getTemplateVariables();
                if (!empty($template_variables)) {
                    foreach ($template_variables as $target_type => $val) {
                        foreach ($val as $field) {
                            if ($field && strlen($field) <= "50") {
                                $data["template_variables"][$target_type][$field] = $extra_data && in_array($field, $extra_data) ? $extra_data[$field] : (array_key_exists($field, $data["model_variables"]) && isset($model_data) ? $model_data[$data["model_variables"][$field]] : "");
                            } else {
                                $response["template_error"] = sprintf($this->ci->lang->line("document_generator_template_error"), $this->ci->lang->line("documentation_center_generate_doc"));
                                return $response;
                            }
                        }
                    }
                }
                $data["template_id"] = $this->ci->input->get("template_id", true);
                $data["model_id"] = $category == "hearing" ? $extra_data["file_name_prefix"] : ($model_id ? $this->ci->{$model}->get("modelCode") . $model_id : "");
                $response["html"] = $this->ci->load->view("document_generator/frontend/template_variables", $data, true);
            } else {
                if ($this->ci->input->post("action", true) == "save") {
                    $file_name = $this->ci->input->post("doc_name", true);
                    $this->ci->load->library("phpdocxconf");
                    $fields = $this->ci->input->post("field", true);
                    $variables = $this->ci->input->post("variable", true);
                    $field_variable = [];
                    if (!empty($fields) && !empty($variables)) {
                        foreach ($fields as $key1 => $field) {
                            foreach ($variables as $key2 => $variable) {
                                if ($key1 == $key2) {
                                    $field_variable[$field] = $variable;
                                }
                            }
                        }
                    }
                    foreach ($field_variable as $field => $variable) {
                        $docx->replaceVariableByText([$field => $variable], ["target" => "header"]);
                        $docx->replaceVariableByText([$field => $variable], ["target" => "document"]);
                        $docx->replaceVariableByText([$field => $variable], ["target" => "footer"]);
                    }
                    if ($extra_data && !$extra_data["versioning"]) {
                        $file_path = $this->ci->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . $file_name;
                        $docx->createDocx($file_path);
                        $response["result"] = true;
                        $response["msg"] = $this->ci->lang->line("doc_generated_successfully");
                    } else {
                        if ($category == "hearing") {
                            $container = $this->get_container($module, $model_id, "Reports", "Hearings");
                        } else {
                            $container = $this->get_container($module, $model_id);
                        }
                        $post_lineage = $this->ci->input->post("doc_path", true);
                        $lineage = $post_lineage ? $post_lineage : $container["lineage"];
                        $upload_dir = $this->get_module_container_path($module) . $lineage;
                        $file_path = $upload_dir . DIRECTORY_SEPARATOR . $file_name;
                        $docx->createDocx($file_path);
                        $parent = $this->get_id_from_lineage($lineage);
                        $this->model->reset_fields();
                        $this->model->set_fields(["type" => "file", "name" => $file_name, "extension" => "docx", "size" => filesize($file_path . ".docx"), "parent" => $parent, "version" => 1, "document_type_id" => NULL, "document_status_id" => NULL, "comment" => NULL, "module" => $module, "module_record_id" => $model_id, "system_document" => 0, "visible" => 1, "visible_in_cp" => 0, "visible_in_ap" => 0, "createdOn" => date("Y-m-d H:i:s"), "createdBy" => $this->user_id, "createdByChannel" => $this->channel, "modifiedOn" => date("Y-m-d H:i:s"), "modifiedBy" => $this->user_id, "modifiedByChannel" => $this->channel]);
                        if ($this->model->insert()) {
                            $this->model->set_field("lineage", $lineage . DIRECTORY_SEPARATOR . $this->model->get_field("id"));
                            if ($this->model->update() && rename($file_path . ".docx", $upload_dir . DIRECTORY_SEPARATOR . $this->model->get_field("id"))) {
                                $this->ci->session->set_userdata("tmp_file_id", $this->model->get_field("id"));
                                $response["result"] = true;
                                $response["msg"] = $this->ci->lang->line("doc_generated_successfully");
                            }
                        }
                    }
                }
            }
        }
        return $response;
    }
    public function generate_contract_document($template_record, $model, $model_id, $category, $module, $extra_data = 0)
    {
        $data = [];
        $response = [];
        $template_dir = $this->documents_root_direcotry . "docs" . $template_record["lineage"];
        $templates = $this->model->load_documents("doc", "", $template_record["lineage"], "");
        $all_templates = $templates["data"];
        $templates = [];
        foreach ($all_templates as $key => $template) {
            if ($template["type"] == "file" && $template["extension"] == "docx") {
                $templates[$template["id"]] = $template["name"];
            }
        }
        require_once substr(COREPATH, 0, -12) . "/application/libraries/phpdocx-premium-12.5-ns/Classes/Phpdocx/Create/CreateDocx.php";
        $data["templates"] = $templates;
        $data["templates"][""] = $this->ci->lang->line("choose_template");
        $this->ci->load->model("doc_generator");
        $data["model_variables"] = $this->ci->doc_generator->get_variables($model, $category);
        $data["extra_data"] = $extra_data ?? false;
        if ($this->ci->input->get("action", true) == "list") {
            $response["html"] = $this->ci->load->view("document_generator/frontend/list_templates", $data, true);
        } else {
            $template_id = $this->ci->input->get("template_id", true) ? $this->ci->input->get("template_id", true) : $this->ci->input->post("template_id", true);
            $this->model->fetch(["id" => $template_id]);
            $tmp_file = $this->ci->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . rand(1000, 9999) . $this->model->get_field("name") . "." . $this->model->get_field("extension");
            $tmp_file_txt = $this->ci->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . rand(1000, 9999) . $this->model->get_field("name") . ".txt";
            $tmp_file_to_save = $this->ci->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . rand(1000, 9999) . $this->model->get_field("name") . "." . $this->model->get_field("extension");
            copy($template_dir . DIRECTORY_SEPARATOR . $template_id, $tmp_file);
            copy($template_dir . DIRECTORY_SEPARATOR . $template_id, $tmp_file_to_save);
            $options = ["paragraph" => false, "list" => false, "table" => false, "footnote" => false, "endnote" => false, "chart" => 0];
            Phpdocx\Create\CreateDocx::DOCX2TXT($tmp_file, $tmp_file_txt, $options);
            $text = file_get_contents($tmp_file_txt);
            $docx_to_read = new Phpdocx\Create\CreateDocx();
            $docx_to_read->addText(strip_tags($text));
            $docx_to_read->createDocx(preg_replace("/\\.[^.\\s]{3,4}\$/", "", $tmp_file));
            $docx_to_read = new Phpdocx\Create\CreateDocxFromTemplate($tmp_file);
            $docx_to_read->setTemplateSymbol("\${", "}");
            $docx = new Phpdocx\Create\CreateDocxFromTemplate($tmp_file_to_save);
            unlink($tmp_file);
            unlink($tmp_file_txt);
            unlink($tmp_file_to_save);
            if ($this->ci->input->get("action", true) == "read") {
                $model_data = NULL;
                if ($model_id && $model) {
                    $this->ci->load->model("custom_field", "custom_fieldfactory");
                    $this->ci->custom_field = $this->ci->custom_fieldfactory->get_instance();
                    $custom_data = $this->ci->custom_field->load_custom_fields_as_array($model_id, $this->ci->{$model}->modelName);
                    $model_data = array_merge($this->ci->{$model}->get_document_generator_data($model_id, $category), $custom_data);
                }
                $template_variables = $docx->getTemplateVariables();
                if (!empty($template_variables)) {
                    foreach ($template_variables as $target_type => $val) {
                        foreach ($val as $field) {
                            if ($field && strlen($field) <= "50") {
                                $data["template_variables"][$target_type][$field] = $extra_data && in_array($field, $extra_data) ? $extra_data[$field] : (array_key_exists($field, $data["model_variables"]) && isset($model_data) ? $model_data[$data["model_variables"][$field]] : "");
                            } else {
                                $response["template_error"] = sprintf($this->ci->lang->line("document_generator_template_error"), $this->ci->lang->line("documentation_center_generate_doc"));
                                return $response;
                            }
                        }
                    }
                }
                $data["template_id"] = $this->ci->input->get("template_id", true);
                $data["model_id"] = $category == "hearing" ? $extra_data["file_name_prefix"] : ($model_id ? $this->ci->{$model}->get("modelCode") . $model_id : "");
                $response["html"] = $this->ci->load->view("document_generator/frontend/template_variables", $data, true);
            } else {
                if ($this->ci->input->post("action", true) == "save") {
                    $file_name = $this->ci->input->post("doc_name", true);
                    $this->ci->load->library("phpdocxconf");
                    $fields = $this->ci->input->post("field", true);
                    $variables = $this->ci->input->post("variable", true);
                    $field_variable = [];
                    if (!empty($fields) && !empty($variables)) {
                        foreach ($fields as $key1 => $field) {
                            foreach ($variables as $key2 => $variable) {
                                if ($key1 == $key2) {
                                    $field_variable[$field] = $variable;
                                }
                            }
                        }
                    }
                    foreach ($field_variable as $field => $variable) {
                        $docx->replaceVariableByText([$field => $variable], ["target" => "header"]);
                        $docx->replaceVariableByText([$field => $variable], ["target" => "document"]);
                        $docx->replaceVariableByText([$field => $variable], ["target" => "footer"]);
                    }
                    if ($extra_data && !$extra_data["versioning"]) {
                        $file_path = $this->ci->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . $file_name;
                        $docx->createDocx($file_path);
                        $response["result"] = true;
                        $response["msg"] = $this->ci->lang->line("doc_generated_successfully");
                    } else {
                        if ($category == "hearing") {
                            $container = $this->get_container($module, $model_id, "Reports", "Hearings");
                        } else {
                            $container = $this->get_container($module, $model_id);
                        }
                        $post_lineage = $this->ci->input->post("doc_path", true);
                        $lineage = $post_lineage ? $post_lineage : $container["lineage"];
                        $upload_dir = $this->get_module_container_path($module) . $lineage;
                        $file_path = $upload_dir . DIRECTORY_SEPARATOR . $file_name;
                        $docx->createDocx($file_path);
                        $parent = $this->get_id_from_lineage($lineage);
                        $this->model->reset_fields();
                        $this->model->set_fields(["type" => "file", "name" => $file_name, "extension" => "docx", "size" => filesize($file_path . ".docx"), "parent" => $parent, "version" => 1, "document_type_id" => NULL, "document_status_id" => NULL, "comment" => NULL, "module" => $module, "module_record_id" => $model_id, "system_document" => 0, "visible" => 1, "visible_in_cp" => $this->channel == "CP" ? 1 : 0, "visible_in_ap" => 0, "createdOn" => date("Y-m-d H:i:s"), "createdBy" => $this->user_id, "createdByChannel" => $this->channel, "modifiedOn" => date("Y-m-d H:i:s"), "modifiedBy" => $this->user_id, "modifiedByChannel" => $this->channel]);
                        if ($this->model->insert()) {
                            $this->model->set_field("lineage", $lineage . DIRECTORY_SEPARATOR . $this->model->get_field("id"));
                            if ($this->model->update() && rename($file_path . ".docx", $upload_dir . DIRECTORY_SEPARATOR . $this->model->get_field("id"))) {
                                $this->ci->session->set_userdata("tmp_file_id", $this->model->get_field("id"));
                                $response["result"] = true;
                                $response["msg"] = $this->ci->lang->line("doc_generated_successfully");
                            }
                        }
                    }
                }
            }
        }
        return $response;
    }
    public function open_document($file_id, $extension, $module)
    {
        $data = $this->get_file_download_data($module, $file_id);
        $tem_user_dir = $this->ci->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "open_document" . DIRECTORY_SEPARATOR . "user_" . $this->ci->is_auth->get_user_id();
        $temp_user_core = $tem_user_dir;
        $temp_user_file = $temp_user_core . DIRECTORY_SEPARATOR . $data["data"]["file_name"];
        if (!is_dir($temp_user_core)) {
            @mkdir($temp_user_core, 493);
        }
        $all_files = glob($temp_user_core . DIRECTORY_SEPARATOR . "*");
        foreach ($all_files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        copy($data["data"]["url"], $temp_user_file);
        $data["url"] = base_url($tem_user_dir . DIRECTORY_SEPARATOR . $data["data"]["file_name"]);
        $data["extension"] = strtolower($extension);
        $data["is_office_file"] = in_array(strtolower($extension), ["doc", "docx", "xls", "xlsx", "ppt", "pps", "pptx", "pdf"]);
        $data["is_openable_file"] = in_array(strtolower($extension), ["gif", "jpg", "jpeg", "png", "html", "txt", "htm", "mpg", "mp3", "mp4", "flv", "mov", "wav", "3gp", "avi"]);
        $data["isImage"] = in_array(strtolower($extension), ["gif", "jpg", "jpeg", "png"]);
        $data["isAudio"] = in_array(strtolower($extension), ["mpg", "mp3", "mp4", "flv", "mov", "wav", "3gp", "avi"]);
        return $data;
    }
    public function load_all_folders($module, $module_record_id = "")
    {
        return $this->model->load_all_folders($module, $module_record_id);
    }
    public function get_module_record_root_folder($module, $module_record_id = "")
    {
        return $this->model->get_module_record_root_folder($module, $module_record_id);
    }
    public function move_document_handler($target_folder_id, $selected_items_ids, $new_created_folders, $module = "")
    {
        $response = ["result" => ["status" => false]];
        if (!empty($new_created_folders)) {
            $count_new_created_folders = count($new_created_folders);
            $response["result"]["error"] = $this->move_document_to_children_checker($new_created_folders, $count_new_created_folders, $selected_items_ids, $target_folder_id) ?? $this->move_document_to_itself_checker($selected_items_ids, $target_folder_id) ?? NULL;
            if (!empty($response["result"]["error"])) {
                return $response;
            }
            $before_move_document_checkers = $this->before_move_document_checkers($module, $target_folder_id, $selected_items_ids, $new_created_folders, true);
            if (is_array($before_move_document_checkers)) {
                $response["result"]["error"] = $before_move_document_checkers;
                return $response;
            }
            $target_folder_real_id = $this->create_new_folders($target_folder_id, $new_created_folders, $count_new_created_folders, $module);
        }
        $target_folder_real_id = !empty($target_folder_real_id) && 0 < $target_folder_real_id ? $target_folder_real_id : $target_folder_id;
        if (!empty($target_folder_real_id) && !empty($selected_items_ids)) {
            $response["result"] = $this->move_document($module, $target_folder_real_id, $selected_items_ids, true, true);
        } else {
            $response["error"] = $this->ci->lang->line("data_missing");
        }
        return $response;
    }
    private function create_new_folders($target_folder_id, $new_created_folders, $count_new_created_folders, $module)
    {
        $target_folder_real_id = 0;
        for ($i = 0; $i < $count_new_created_folders; $i++) {
            $new_folder = $new_created_folders[$i];
            $new_folder_parent = $this->model->get_document_details(["id" => $new_folder["parent"]]);
            if (!empty($new_folder_parent)) {
                $params = ["module" => $new_folder["module"], "lineage" => !empty($new_folder["lineage"]) ? $new_folder["lineage"] : $new_folder_parent["lineage"], "name" => $new_folder["name"]];
                if ($module !== "doc") {
                    $params["module_record_id"] = $new_folder["module_record_id"];
                }
                $result = $this->create_folder($params, true);
                if (!empty($result["id"])) {
                    $fake_folder_id = $new_folder["fake_id"];
                    $new_created_folders[$i]["id"] = $result["id"];
                    if ($fake_folder_id == $target_folder_id) {
                        $target_folder_real_id = $result["id"];
                    }
                    for ($j = $i + 1; $j < $count_new_created_folders; $j++) {
                        $child_folder = $new_created_folders[$j];
                        if ($child_folder["parent"] == $fake_folder_id) {
                            $new_created_folders[$j]["parent"] = $new_created_folders[$i]["id"];
                        }
                    }
                }
            }
        }
        return $target_folder_real_id;
    }
    private function move_document_to_children_checker($new_created_folders, $count_new_created_folders, $selected_items_ids, $target_folder_id)
    {
        for ($i = 0; $i < $count_new_created_folders; $i++) {
            $new_folder = $new_created_folders[$i];
            if ($new_folder["fake_id"] == $target_folder_id) {
                if ($this->is_new_created_folder($new_folder["fake_id"], $new_created_folders)) {
                    $new_folder_parent = $this->get_new_folder_real_ancestor($new_folder["parent"], $new_created_folders);
                    foreach ($selected_items_ids as $item_id) {
                        $file = $this->get_document_newest_version_details($item_id);
                        if (!empty($file["id"])) {
                            $item_id = $file["id"];
                        }
                        if ($this->model->fetch($item_id)) {
                            $item_full_name = $this->model->get_field("name") . ($this->model->get_field("extension") ? "." . $this->model->get_field("extension") : "");
                            if ($item_id == $new_folder_parent) {
                                return ["message" => "moveDocumentToChildren", "value" => $item_full_name];
                            }
                        }
                    }
                } else {
                    $new_folder_parent = $this->model->get_document_details(["id" => $new_folder["parent"]]);
                }
                if (!empty($new_folder_parent)) {
                    foreach ($selected_items_ids as $item_id) {
                        $file = $this->get_document_newest_version_details($item_id);
                        if (!empty($file["id"])) {
                            $item_id = $file["id"];
                        }
                        if ($this->model->fetch($item_id)) {
                            $item_full_name = $this->model->get_field("name") . ($this->model->get_field("extension") ? "." . $this->model->get_field("extension") : "");
                            if ($this->targetFolderIsDescendant($new_folder, $item_id, true)) {
                                return ["message" => "moveDocumentToChildren", "value" => $item_full_name];
                            }
                        }
                    }
                }
            }
        }
    }
    private function move_document_to_itself_checker($selected_items_ids, $target_folder_id)
    {
        foreach ($selected_items_ids as $item_id) {
            $file = $this->get_document_newest_version_details($item_id);
            if (!empty($file["id"])) {
                $item_id = $file["id"];
            }
            if ($this->model->fetch($item_id)) {
                $item_full_name = $this->model->get_field("name") . ($this->model->get_field("extension") ? "." . $this->model->get_field("extension") : "");
                if ($item_id == $target_folder_id) {
                    return ["message" => "moveDocumentToItself", "value" => $item_full_name];
                }
            }
        }
    }
    private function before_move_document_checkers($module, $target_folder_id, $selected_items_ids, $new_created_folders, $newest_version = false)
    {
        $target_folder = $this->is_new_created_folder($target_folder_id, $new_created_folders) ? true : $this->model->get_document_details(["module" => $module, "id" => $target_folder_id, "type" => "folder"]);
        if ($target_folder) {
            foreach ($selected_items_ids as $item_id) {
                if ($newest_version) {
                    $file = $this->get_document_newest_version_details($item_id);
                    if (!empty($file["id"])) {
                        $item_id = $file["id"];
                    }
                }
                if ($this->model->fetch($item_id)) {
                    $item_full_name = $this->model->get_field("name") . ($this->model->get_field("extension") ? "." . $this->model->get_field("extension") : "");
                    if (!$this->targetFolderIsDescendant($target_folder, $item_id)) {
                        if ($item_id != $target_folder_id) {
                            if ($this->model->get_field("parent") != $target_folder_id) {
                                return true;
                            }
                            return ["message" => "moveDocumentAlreadyExists", "value" => $item_full_name];
                        }
                        return ["message" => "moveDocumentToItself", "value" => $item_full_name];
                    }
                    return ["message" => "moveDocumentToChildren", "value" => $item_full_name];
                }
                return ["message" => "moveDocumentNotFound", "vaule" => $item_id];
            }
        }
        return ["message" => "moveDocumentNotFoundDestination"];
    }
    private function get_new_folder_real_ancestor($target_parent, $new_created_folders)
    {
        foreach ($new_created_folders as $new_folder) {
            if ($target_parent == $new_folder["fake_id"]) {
                return $this->get_new_folder_real_ancestor($new_folder["parent"], $new_created_folders);
            }
        }
        return $target_parent;
    }
    private function is_new_created_folder($target_folder_id, $new_created_folders)
    {
        foreach ($new_created_folders as $new_folder) {
            if ($new_folder["fake_id"] == $target_folder_id) {
                return true;
            }
        }
        return false;
    }
    private function move_document($module, $target_folder_id, $selected_items_ids, $auto_rename, $newest_version = false)
    {
        $response = ["status" => false, "error" => NULL];
        $target_folder = $this->model->get_document_details(["module" => $module, "id" => $target_folder_id, "type" => "folder"]);
        if ($target_folder) {
            foreach ($selected_items_ids as $item_id) {
                if ($newest_version) {
                    $file = $this->get_document_newest_version_details($item_id);
                    if (!empty($file["id"])) {
                        $item_id = $file["id"];
                    }
                }
                if ($this->model->fetch($item_id)) {
                    $item_full_name = $this->model->get_field("name") . ($this->model->get_field("extension") ? "." . $this->model->get_field("extension") : "");
                    $item_name = $this->model->get_field("name");
                    if (!$this->targetFolderIsDescendant($target_folder, $item_id)) {
                        if ($item_id != $target_folder_id) {
                            $parent_lineage = $target_folder["lineage"];
                            $old_lineage = $this->model->get_field("lineage");
                            $new_lineage = $parent_lineage . DIRECTORY_SEPARATOR . $this->model->get_field("id");
                            $document_existant_version = $this->model->get_document_existant_version($item_full_name, $this->model->get_field("type"), $parent_lineage);
                            if ($this->model->get_field("parent") == $target_folder_id) {
                                $response["error"] = ["message" => "moveDocumentAlreadyExists", "value" => $item_full_name];
                                return $response;
                            }
                            if ($document_existant_version && $auto_rename) {
                                $item_name = $this->auto_generate_document_name($document_existant_version["full_name"], $document_existant_version["name"], $document_existant_version["type"], $document_existant_version["parent_lineage"], $document_existant_version["name"]);
                                $this->model->set_field("name", $item_name);
                            }
                            $this->do_move_document($module, $this->model, $item_name, $target_folder_id, $this->get_module_container_path($module), $new_lineage, $old_lineage, $parent_lineage, $response);
                        } else {
                            $response["error"] = ["message" => "moveDocumentToItself", "value" => $item_full_name];
                        }
                    } else {
                        $response["error"] = ["message" => "moveDocumentToChildren", "value" => $item_full_name];
                    }
                } else {
                    $response["error"] = ["message" => "moveDocumentNotFound", "vaule" => $item_id];
                }
            }
        } else {
            $response["error"] = ["message" => "moveDocumentNotFoundDestination", "value" => $target_folder];
        }
        return $response;
    }
    private function do_move_document($module, $model, $document_name, $target_folder_id, $module_path, $new_lineage, $old_lineage, $parent_lineage, &$response)
    {
        $model->set_field("parent", $target_folder_id);
        $model->set_field("lineage", $new_lineage);
        if ($model->update() && rename($module_path . $old_lineage, $module_path . $new_lineage)) {
            $criteria = ["type" => "folder", "name" => $model->get_field("id") . "_versions"];
            $document_versions_folder = $this->model->get_document_details($criteria);
            if (!empty($document_versions_folder)) {
                $this->move_document_versions_folder($document_name, $model->get_field("id"), $document_versions_folder, $target_folder_id, $module_path, $parent_lineage, $response);
            } else {
                $response["status"] = true;
            }
            $this->move_folder_children($module, $model->get_field("id"), $new_lineage);
        }
    }
    private function move_document_versions_folder($document_name, $document_id, $document_versions_folder, $target_folder_id, $module_path, $parent_lineage, &$response)
    {
        $this->model->reset_fields();
        if ($this->model->fetch($document_versions_folder["id"])) {
            $versions_folder_old_lineage = $this->model->get_field("lineage");
            $versions_folder_new_lineage = $parent_lineage . DIRECTORY_SEPARATOR . $this->model->get_field("id");
            $this->model->set_field("parent", $target_folder_id);
            $this->model->set_field("lineage", $versions_folder_new_lineage);
            $versions_folder_new_lineage = $this->model->get_field("lineage");
            $response["status"] = $this->model->update();
            if ($response["status"] && rename($module_path . $versions_folder_old_lineage, $module_path . $versions_folder_new_lineage)) {
                $criteria = ["type" => "file", "parent" => $this->model->get_field("id")];
                $document_versions_folder_items = $this->model->get_file_versions($document_id);
                if (!empty($document_versions_folder_items)) {
                    $this->model->reset_fields();
                    foreach ($document_versions_folder_items as $item) {
                        if ($this->model->fetch($item["id"])) {
                            $this->model->set_field("name", $document_name);
                            $criteria = ["type" => "file", "lineage" => $versions_folder_old_lineage . DIRECTORY_SEPARATOR . $this->model->get_field("id")];
                            $child_of_versions_folder = $this->model->get_document_details($criteria);
                            if (!empty($child_of_versions_folder)) {
                                $this->model->set_field("lineage", $versions_folder_new_lineage . DIRECTORY_SEPARATOR . $this->model->get_field("id"));
                            }
                            $response["status"] = $this->model->update();
                        }
                    }
                } else {
                    $response["status"] = true;
                }
            }
        }
    }
    private function move_folder_children($module, $folder_id, $folder_lineage)
    {
        $criteria = ["module" => $module, "parent" => $folder_id];
        $folder_children = $this->model->get_document_details($criteria, "where", "array");
        if (!empty($folder_children)) {
            foreach ($folder_children as $document) {
                if ($this->model->fetch($document["id"])) {
                    $this->model->set_field("lineage", $folder_lineage . DIRECTORY_SEPARATOR . $this->model->get_field("id"));
                    if ($this->model->update() && $this->model->get_field("type") == "folder") {
                        $document_criteria = ["module" => $module, "parent" => $document["id"]];
                        $document_children = $this->model->get_document_details($document_criteria, "where", "array");
                        if (!empty($document_children)) {
                            $this->move_folder_children($module, $this->model->get_field("id"), $this->model->get_field("lineage"));
                        }
                    }
                }
            }
        }
        return true;
    }
    public function get_document_details($document_fetch_criteria, $condition = "where", $return = "")
    {
        return $this->model->get_document_details($document_fetch_criteria, $condition, $return);
    }
    public function get_document_newest_version_details($id)
    {
        return $this->model->get_document_newest_version_details($id);
    }
    private function targetFolderIsDescendant($target_folder, $selected_item_id, $is_parent_lineage = false)
    {
        $target_folder_parents = explode(DIRECTORY_SEPARATOR, $target_folder["lineage"]);
        if (!empty($target_folder_parents)) {
            if (!$is_parent_lineage) {
                array_pop($target_folder_parents);
            }
            foreach ($target_folder_parents as $parent) {
                if ($parent == $selected_item_id) {
                    return true;
                }
            }
        }
        return false;
    }
    public function check_folder_privacy($id = "", $lineage = "")
    {
        $this->model->fetch($id ?: ["lineage" => $lineage]);
        if ($this->model->get_field("private") == 1) {
            $this->ci->load->model("user_profile");
            $this->ci->user_profile->fetch(["user_id" => $this->user_id]);
            if ($this->ci->user_profile->get_field("overridePrivacy") != "yes") {
                $is_private = $this->model->fetch_private_folder($id, $lineage);
                return $is_private;
            }
        }
        return true;
    }
    public function get_document_content($id)
    {
        $document = $this->model->get_document_newest_version_details($id);
        if (!empty($document)) {
            if (in_array(strtolower($document["extension"]), ["html", "html"])) {
                echo "File type not allowed!";
            } else {
                $document["content"] = file_get_contents($this->get_module_container_path($document["module"]) . $document["lineage"]);
                $f = finfo_open();
                $mime_type = finfo_buffer($f, $document["content"], FILEINFO_MIME_TYPE);
                $document["mime_type"] = $mime_type;
                header("Content-Type:", $mime_type);
                echo $document["content"];
            }
        }
    }
    public function get_preview_document_content($id)
    {
        $document = $this->model->get_document_newest_version_details($id);
        if (!empty($document)) {
            if (in_array(strtolower($document["extension"]), ["html", "html"])) {
                echo "File type not allowed!";
            } else {
                $obj = $this->convert_to_pdf($document);
                if (isset($obj) && is_array($obj) && file_exists($obj["converted_file"])) {
                    $document["content"] = file_get_contents($obj["converted_file"]);
                    $f = finfo_open();
                    $mime_type = finfo_buffer($f, $document["content"], FILEINFO_MIME_TYPE);
                    $document["mime_type"] = $mime_type;
                    header("Content-Type:", $mime_type);
                    echo $document["content"];
                    unlink($obj["converted_file"]);
                    unlink($obj["tmp_file"]);
                }
            }
        }
    }
    private function convert_to_pdf($doc_details)
    {
        if (!empty($doc_details)) {
            $path = $this->get_module_container_path($doc_details["module"]);
            $current_file = $path . $doc_details["lineage"];
            if (is_file($current_file)) {
                $rand = rand(1000, 9999);
                $tmp_file = $path . DIRECTORY_SEPARATOR . $rand . $doc_details["id"] . "." . $doc_details["extension"];
                copy($current_file, $tmp_file);
                $core_path = substr(COREPATH, 0, -12);
                require_once $core_path . "/application/libraries/OfficeConverter.php";
                $parsed_file = parse_ini_file($core_path . "application/config/phpdocx.ini");
                $converted_file = $rand . $doc_details["id"] . ".pdf";
                $converter = new OfficeConverter($current_file, $path, $parsed_file["path"]);
                if ($converter->convertTo($converted_file) !== NULL) {
                    return ["converted_file" => $path . DIRECTORY_SEPARATOR . $converted_file, "tmp_file" => $tmp_file];
                }
            }
        }
    }
    public function get_assets_folder($id)
    {
        return $this->get_document_extended_details(["d.parent" => $id, "d.name" => "Assets", "d.system_document" => "1", "d.extension" => NULL]);
    }
    public function get_document_extended_details($document_fetch_criteria, $condition = "where", $return = "")
    {
        return $this->model->get_document_extended_details($document_fetch_criteria, $condition, $return);
    }
    public function show_hide_document_in_cp($id, $module)
    {
        if ($this->model->fetch($id)) {
            $action = $this->model->get_field("visible_in_cp") ? "hide" : "show";
            $type = $this->model->get_field("type");
            if ($action == "hide") {
                $this->model->set_field("visible_in_cp", 0);
                $response["message"] = sprintf($this->ci->lang->line("document_hidden_in_cp"), $this->ci->lang->line($type));
            } else {
                $parents_ids = explode(DIRECTORY_SEPARATOR, $this->model->get_field("lineage"));
                foreach ($parents_ids as $i => $parent_id) {
                    $parent_is_visible = 1 < $i && $parent_id != $id ? $this->model->is_visible_in_cp($parent_id) : 1;
                    if (!$parent_is_visible) {
                        $this->model->fetch($id);
                        if ($parent_is_visible) {
                            $this->model->set_field("visible_in_cp", 1);
                            $response["message"] = sprintf($this->ci->lang->line("document_visible_in_cp"), $this->ci->lang->line($type));
                        } else {
                            $response["info"] = sprintf($this->ci->lang->line("parent_folder_not_shared"), $this->ci->lang->line($type));
                        }
                    }
                }
            }
            if (!$this->model->update()) {
                $response["error"] = sprintf($this->ci->lang->line("error_in_sharing_in_cp"), $this->ci->lang->line($type));
            }
            if ($action == "hide" && $type == "folder") {
                $this->show_hide_children_documents($id, $module, $action);
            }
        } else {
            $response["error"] = $this->lang->line("invalid_record");
        }
        return $response;
    }
    public function show_hide_children_documents($id, $module, $action)
    {
        $this->model->fetch($id);
        $module_record_id = $this->model->get_field("module_record_id");
        $all_documents = $this->model->load_all_documents($module, $module_record_id);
        foreach ($all_documents as $document) {
            if ($document["id"] != $id) {
                $lineage = explode(DIRECTORY_SEPARATOR, $document["lineage"]);
                if (in_array($id, $lineage)) {
                    $this->model->fetch($document["id"]);
                    $this->model->set_field("visible_in_cp", $action == "hide" ? 0 : 1);
                    $this->model->update();
                }
            }
        }
    }
    public function show_hide_document_in_ap($id, $module)
    {
        if ($this->model->fetch($id)) {
            $action = $this->model->get_field("visible_in_ap") ? "hide" : "show";
            $type = $this->model->get_field("type");
            if ($action == "hide") {
                $this->model->set_field("visible_in_ap", 0);
                $response["message"] = sprintf($this->ci->lang->line("document_hidden_in_ap"), $this->ci->lang->line($type));
            } else {
                $parents_ids = explode(DIRECTORY_SEPARATOR, $this->model->get_field("lineage"));
                foreach ($parents_ids as $i => $parent_id) {
                    $parent_is_visible = 1 < $i && $parent_id != $id ? $this->model->is_visible_in_ap($parent_id) : 1;
                    if (!$parent_is_visible) {
                        $this->model->fetch($id);
                        if ($parent_is_visible) {
                            $this->model->set_field("visible_in_ap", 1);
                            $response["message"] = sprintf($this->ci->lang->line("document_visible_in_ap"), $this->ci->lang->line($type));
                        } else {
                            $response["info"] = sprintf($this->ci->lang->line("parent_folder_not_shared_in_ap"), $this->ci->lang->line($type));
                        }
                    }
                }
            }
            if (!$this->model->update()) {
                $response["error"] = sprintf($this->ci->lang->line("error_in_sharing_in_ap"), $this->ci->lang->line($type));
            }
            if ($action == "hide" && $type == "folder") {
                $this->show_hide_ap_children_documents($id, $module, $action);
            }
        } else {
            $response["error"] = $this->lang->line("invalid_record");
        }
        return $response;
    }
    public function show_hide_ap_children_documents($id, $module, $action)
    {
        $this->model->fetch($id);
        $module_record_id = $this->model->get_field("module_record_id");
        $all_documents = $this->model->load_all_documents($module, $module_record_id);
        foreach ($all_documents as $document) {
            if ($document["id"] != $id) {
                $lineage = explode(DIRECTORY_SEPARATOR, $document["lineage"]);
                if (in_array($id, $lineage)) {
                    $this->model->fetch($document["id"]);
                    $this->model->set_field("visible_in_ap", $action == "hide" ? 0 : 1);
                    $this->model->update();
                }
            }
        }
    }
    public function load_signature_documents($parameters)
    {
        $response = [];
        if ($this->validate_parameters($parameters)) {
            $response = $this->model->load_signature_documents($parameters["module"], $this->get_optional_parameter_value($parameters, "module_record_id"), $this->get_optional_parameter_value($parameters, "lineage"), $this->get_optional_parameter_value($parameters, "term"), $this->get_optional_parameter_value($parameters, "type"), $this->get_optional_parameter_value($parameters, "visible_in_cp"));
        }
        $response["status"] = isset($response["data"]) && is_array($response["data"]) ? true : false;
        $response["message"] = $response["status"] ? $this->ci->lang->line("documents_loaded_successfully") : $this->ci->lang->line("documents_loading_failed");
        return $response;
    }
    public function generate_document_from_questionnaire($template_record, $post_variables, $model_details)
    {
        $module = "contract";
        $post_data = $this->ci->input->post("template", true);
        $core_path = substr(COREPATH, 0, -12);
        $template_dir = $this->documents_root_direcotry . "docs" . $template_record["lineage"];
        $tmp_file = $this->ci->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "contracts" . DIRECTORY_SEPARATOR . rand(1000, 9999) . $template_record["name"] . "." . $template_record["extension"];
        $tmp_file_txt = $this->ci->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "contracts" . DIRECTORY_SEPARATOR . rand(1000, 9999) . $template_record["name"] . ".txt";
        $tmp_file_to_save = $this->ci->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "contracts" . DIRECTORY_SEPARATOR . rand(1000, 9999) . $template_record["name"] . "." . $template_record["extension"];
        copy($template_dir, $tmp_file);
        copy($template_dir, $tmp_file_to_save);
        require_once $core_path . "/application/libraries/phpdocx-premium-12.5-ns/Classes/Phpdocx/Create/CreateDocx.php";
        $options = ["paragraph" => false, "list" => false, "table" => false, "footnote" => false, "endnote" => false, "chart" => 0];
        Phpdocx\Create\CreateDocx::DOCX2TXT($tmp_file, $tmp_file_txt, $options);
        $text = file_get_contents($tmp_file_txt);
        $docx_to_read = new Phpdocx\Create\CreateDocx();
        $docx_to_read->addText(strip_tags($text));
        $docx_to_read->createDocx(preg_replace("/\\.[^.\\s]{3,4}\$/", "", $tmp_file));
        $docx_to_read = new Phpdocx\Create\CreateDocxFromTemplate($tmp_file);
        $docx_to_read->setTemplateSymbol("\${", "}");
        $docx = new Phpdocx\Create\CreateDocxFromTemplate($tmp_file_to_save);
        unlink($tmp_file);
        unlink($tmp_file_txt);
        unlink($tmp_file_to_save);
        $template_variables = $docx->getTemplateVariables();
        $values = array_values($post_variables);
        $post_variables_keys = array_map("strtolower", array_keys($post_variables));
        foreach ($template_variables as $target => $variables) {
            foreach (array_unique($variables) as $key2 => $field) {
                if (isset($post_variables[$field]) && !empty($post_variables[$field])) {
                    $key = array_search(strtolower($field), $post_variables_keys);
                    $docx->replaceVariableByText([$field => $values[$key]], ["target" => $target]);
                } else {
                    $docx->replaceVariableByText([$field => ""], ["target" => $target]);
                }
            }
        }
        $docx_to_read->setTemplateSymbol("\$");
        $block_variables = $docx->getTemplateVariables();
        $operators = ["is", "equals", "isnot", "gt", "greaterthan", "gte", "greaterthanequal", "lt", "lessthan", "lte", "lessthanequal", "notempty", "isknown", "isempty", "notknown"];
        if (!empty($block_variables)) {
            foreach ($block_variables as $target_type => $val) {
                $variables = array_filter(array_unique($val), function ($element) {
                    if ($element != "") {
                        return $element;
                    }
                });
                foreach ($variables as $block) {
                    if (substr($block, 0, 6) == "BLOCK_") {
                        $block_name = substr($block, strlen("BLOCK_"));
                        preg_match_all("/_/", $block, $matches, PREG_OFFSET_CAPTURE);
                        if ($matches && isset($matches[0])) {
                            if (count($matches[0]) == 2) {
                                $field = substr($block, $matches[0][0][1] + 1, $matches[0][1][1] - ($matches[0][0][1] + 1));
                                $operator = substr($block, $matches[0][1][1] + 1, strlen($block) - ($matches[0][1][1] + 1));
                            }
                            if (count($matches[0]) == 3) {
                                $field = substr($block, $matches[0][0][1] + 1, $matches[0][1][1] - ($matches[0][0][1] + 1));
                                $operator = substr($block, $matches[0][1][1] + 1, $matches[0][2][1] - ($matches[0][1][1] + 1));
                                $value = substr($block, $matches[0][2][1] + 1, strlen($block) - ($matches[0][2][1] + 1));
                            }
                            if (in_array(strtolower($field), $post_variables_keys)) {
                                $key = array_search(strtolower($field), $post_variables_keys);
                                switch ($operator) {
                                    case "is":
                                    case "equals":
                                        if (strtolower($values[$key]) == strtolower($value)) {
                                            $docx->removeTemplateVariable($block, "inline", "header");
                                            $docx->removeTemplateVariable($block, "inline", "footer");
                                        } else {
                                            $docx->deleteTemplateBlock($block_name);
                                            $docx->removeTemplateVariable($block, "block", "header");
                                            $docx->removeTemplateVariable($block, "block", "footer");
                                        }
                                        break;
                                    case "isnot":
                                        if (strtolower($values[$key]) != strtolower($value)) {
                                            $docx->removeTemplateVariable($block, "inline", "header");
                                            $docx->removeTemplateVariable($block, "inline", "footer");
                                        } else {
                                            $docx->deleteTemplateBlock($block_name);
                                            $docx->removeTemplateVariable($block, "block", "header");
                                            $docx->removeTemplateVariable($block, "block", "footer");
                                        }
                                        break;
                                    case "gt":
                                    case "greaterthan":
                                        if (is_numeric($values[$key]) && is_numeric($value) && $value < $values[$key]) {
                                            $docx->removeTemplateVariable($block, "inline", "header");
                                            $docx->removeTemplateVariable($block, "inline", "footer");
                                        } else {
                                            $docx->deleteTemplateBlock($block_name);
                                            $docx->removeTemplateVariable($block, "block", "header");
                                            $docx->removeTemplateVariable($block, "block", "footer");
                                        }
                                        break;
                                    case "gte":
                                    case "greaterthanequal":
                                        if (is_numeric($values[$key]) && is_numeric($value) && $value <= $values[$key]) {
                                            $docx->removeTemplateVariable($block, "inline", "header");
                                            $docx->removeTemplateVariable($block, "inline", "footer");
                                        } else {
                                            $docx->deleteTemplateBlock($block_name);
                                            $docx->removeTemplateVariable($block, "block", "header");
                                            $docx->removeTemplateVariable($block, "block", "footer");
                                        }
                                        break;
                                    case "lt":
                                    case "lessthan":
                                        if (is_numeric($values[$key]) && is_numeric($value) && $values[$key] < $value) {
                                            $docx->removeTemplateVariable($block, "inline", "header");
                                            $docx->removeTemplateVariable($block, "inline", "footer");
                                        } else {
                                            $docx->deleteTemplateBlock($block_name);
                                            $docx->removeTemplateVariable($block, "block", "header");
                                            $docx->removeTemplateVariable($block, "block", "footer");
                                        }
                                        break;
                                    case "lte":
                                    case "lessthanequal":
                                        if (is_numeric($values[$key]) && is_numeric($value) && $values[$key] <= $value) {
                                            $docx->removeTemplateVariable($block, "inline", "header");
                                            $docx->removeTemplateVariable($block, "inline", "footer");
                                        } else {
                                            $docx->deleteTemplateBlock($block_name);
                                            $docx->removeTemplateVariable($block, "block", "header");
                                            $docx->removeTemplateVariable($block, "block", "footer");
                                        }
                                        break;
                                    case "isempty":
                                    case "notknown":
                                        if (empty($values[$key]) || $values[$key] == "") {
                                            $docx->removeTemplateVariable($block, "inline", "header");
                                            $docx->removeTemplateVariable($block, "inline", "footer");
                                        } else {
                                            $docx->deleteTemplateBlock($block_name);
                                            $docx->removeTemplateVariable($block, "block", "header");
                                            $docx->removeTemplateVariable($block, "block", "footer");
                                        }
                                        break;
                                    case "notempty":
                                    case "isknown":
                                        if (!empty($values[$key]) || $values[$key] !== "") {
                                            $docx->removeTemplateVariable($block, "inline", "header");
                                            $docx->removeTemplateVariable($block, "inline", "footer");
                                        } else {
                                            $docx->deleteTemplateBlock($block_name);
                                            $docx->removeTemplateVariable($block, "block", "header");
                                            $docx->removeTemplateVariable($block, "block", "footer");
                                        }
                                        break;
                                }
                            } else {
                                $docx->deleteTemplateBlock($block_name);
                            }
                        }
                    }
                }
            }
            $docx->clearBlocks();
        }
        $file_name = $post_data["name"];
        $container = $this->get_container($module, $model_details["id"]);
        $upload_dir = $this->get_module_container_path($module) . $container["lineage"];
        $file_path = $upload_dir . DIRECTORY_SEPARATOR . $file_name;
        $properties = ["title" => $file_name, "custom" => ["related_contract" => ["number" => $model_details["id"]]]];
        $docx->addProperties($properties);
        $docx->createDocx($file_path);
        $parent = $this->get_id_from_lineage($container["lineage"]);
        $this->model->reset_fields();
        $this->model->set_fields(["type" => "file", "name" => $file_name, "extension" => "docx", "size" => filesize($file_path . ".docx"), "parent" => $parent, "version" => 1, "document_type_id" => NULL, "document_status_id" => NULL, "comment" => NULL, "module" => $module, "module_record_id" => $model_details["id"], "system_document" => 0, "visible" => 1, "visible_in_cp" => $model_details["channel"] == "CP" ? 1 : 0, "visible_in_ap" => 0, "createdOn" => date("Y-m-d H:i:s"), "createdBy" => $model_details["createdBy"], "createdByChannel" => $model_details["channel"], "modifiedOn" => date("Y-m-d H:i:s"), "modifiedBy" => $model_details["modifiedBy"], "modifiedByChannel" => $model_details["modifiedByChannel"]]);
        if ($this->model->insert()) {
            $this->model->set_field("lineage", $container["lineage"] . DIRECTORY_SEPARATOR . $this->model->get_field("id"));
            if ($this->model->update() && rename($file_path . ".docx", $upload_dir . DIRECTORY_SEPARATOR . $this->model->get_field("id"))) {
                $response["result"] = true;
                $response["msg"] = $this->ci->lang->line("doc_generated_successfully");
            }
        } else {
            $response["result"] = false;
            $response["validation_errors"] = $this->model->get("validationErrors");
        }
        return $response;
    }
    public function load_documents_for_collaboration($module, $module_record_id)
    {
        return $this->model->load_documents_for_collaboration($module, $module_record_id);
    }
    public function add_default_generator_template($reference)
    {
        $template_file_english = $this->model->get_document_details(["name" => $reference . "_arabic" . "_default_template"]);
        $template_file_arabic = $this->model->get_document_details(["name" => $reference . "_english" . "_default_template"]);
        if (!$template_file_english && !$template_file_arabic) {
            $this->ci->load->model("doc_generator");
            if ($reference == "hearing") {
                $existing_template_folder_id = $this->ci->doc_generator->get_value_by_key("hearing_report_template_folder");
            } else {
                if ($reference == "contract") {
                    $existing_template_folder_id = $this->ci->doc_generator->get_value_by_key("contract_template_folder_path");
                } else {
                    $existing_template_folder_id = $this->ci->doc_generator->get_value_by_key("template_folder_path");
                }
            }
            $existing_template_folder = $this->model->get_document_details(["id" => $existing_template_folder_id]);
            if (!$existing_template_folder) {
                $template_folder = $this->model->get_document_details(["name" => "Templates"]);
                if (!$template_folder) {
                    $template_folder = $this->create_folder(["module" => "doc", "lineage" => "", "name" => "Templates"]);
                }
                $reference_template_folder = $this->model->get_document_details(["name" => ucwords($reference) . " Template"]);
                if (!$reference_template_folder) {
                    $reference_template_folder = $this->create_folder(["module" => "doc", "lineage" => $template_folder["lineage"], "name" => ucwords($reference) . " Template"]);
                }
                $this->insert_template_file_inside_template_folder($reference_template_folder, $reference);
                if ($reference == "hearing") {
                    $this->ci->doc_generator->set_value_by_key("hearing_report_template_folder", strval($reference_template_folder["id"]));
                } else {
                    if ($reference == "contract") {
                        $this->ci->doc_generator->set_value_by_key("contract_template_folder_path", strval($reference_template_folder["id"]));
                    } else {
                        $this->ci->doc_generator->set_value_by_key("template_folder_path", strval($reference_template_folder["id"]));
                    }
                }
            } else {
                $this->insert_template_file_inside_template_folder($existing_template_folder, $reference);
            }
        }
    }
    private function insert_template_file_inside_template_folder($folder, $reference)
    {
        $file_lineage = $folder["lineage"];
        $parent = $this->get_id_from_lineage($file_lineage);
        $parent_visible_in_cp = $this->model->is_visible_in_cp($parent);
        $parent_visible_in_ap = $this->model->is_visible_in_ap($parent);
        $this->ci->load->model("user_preference");
        $language = $this->ci->user_preference->get_value("language");
        if ($language == "arabic") {
            $template_filename = $reference . "_arabic_default_template";
        } else {
            $template_filename = $reference . "_english_default_template";
        }
        $file_path = substr(COREPATH, 0, -12) . "files" . DIRECTORY_SEPARATOR . "initial_configuration" . DIRECTORY_SEPARATOR . $template_filename . ".docx";
        $size = filesize($file_path);
        $this->model->reset_fields();
        $this->model->set_fields(["type" => "file", "name" => $template_filename, "extension" => "docx", "size" => $size, "parent" => $parent, "version" => 1, "document_type_id" => NULL, "document_status_id" => NULL, "comment" => NULL, "module" => "doc", "module_record_id" => NULL, "system_document" => 0, "visible" => 1, "visible_in_cp" => $parent_visible_in_cp ? 1 : 0, "visible_in_ap" => $parent_visible_in_ap, "createdOn" => date("Y-m-d H:i:s"), "createdBy" => $this->ci->is_auth->get_user_id(), "createdByChannel" => $this->channel, "modifiedOn" => date("Y-m-d H:i:s"), "modifiedBy" => $this->ci->is_auth->get_user_id(), "modifiedByChannel" => $this->channel]);
        if ($this->model->insert()) {
            $this->model->set_field("lineage", $file_lineage . DIRECTORY_SEPARATOR . $this->model->get_field("id"));
            if ($this->model->update()) {
                copy($file_path, $this->get_module_container_path("doc") . $this->model->get_field("lineage"));
            }
        }
    }
    public function create_note_parent_folder($module_record_id, $createdOn, $module)
    {
        $note_parent_folder = $this->get_document_details(["name" => $createdOn, "module" => $module, "module_record_id" => $module_record_id]);
        if ($note_parent_folder) {
            return $note_parent_folder;
        }
        $container_name = "";
        switch ($module) {
            case "case":
                $container_name = "Matter Notes Attachments";
                break;
            case "contract":
                $container_name = "Contract_Notes_Attachments";
                break;
            case "company":
                $container_name = "Company_Notes_Attachments";
                break;
            default:
                $new_note_parent_folder = $this->create_folder(["name" => $createdOn, "module" => $module, "module_record_id" => $module_record_id, "container_name" => $container_name]);
                return $new_note_parent_folder;
        }
    }
    public function check_if_files_were_uploaded($files)
    {
        $filesUploaded = false;
        foreach ($files as $file) {
            if ($file["error"] != 4) {
                $filesUploaded = true;
                return $filesUploaded;
            }
        }
    }
}

?>