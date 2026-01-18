<?php

echo "\r\n";
require "Top_controller.php";
class Documents extends Top_controller
{
    public $responseData;
    public function __construct()
    {
        parent::__construct();
        $this->load->library("dms", ["channel" => $this->user_logged_in_data["channel"], "user_id" => $this->user_logged_in_data["user_id"]]);
        $this->responseData = default_response_data();
    }
    public function company_load_documents()
    {
        $response = $this->load_documents("company");
        $this->render($response);
    }
    public function company_upload_file()
    {
        $response = $this->upload_files("company");
        $this->render($response);
    }
    public function company_create_folder()
    {
        $response = $this->create_folder("company");
        $this->render($response);
    }
    public function company_rename_file()
    {
        $response = $this->rename_document("company", "file");
        $this->render($response);
    }
    public function company_rename_folder()
    {
        $response = $this->rename_document("company", "folder");
        $this->render($response);
    }
    public function company_edit_documents()
    {
        $response = $this->edit_documents("company");
        $this->render($response);
    }
    public function company_share_folder($folder_id = NULL)
    {
        $response = $this->share_folder("company", $folder_id);
        $this->render($response);
    }
    public function company_download_file($file_id, $newest_version = false, $base64 = true)
    {
        $base64 = is_string($base64) ? strtolower($base64) == "true" : $base64;
        $newest_version = strtolower($newest_version) == "true" ? true : false;
        $response = $this->download_file("company", $file_id, $newest_version, $base64);
        $this->render($response);
    }
    public function company_file_info($file_id, $newest_version = false)
    {
        $response = $this->file_info("company", $file_id, $newest_version);
        $this->render($response);
    }
    public function company_delete_document()
    {
        $response = $this->delete_document("company");
        $this->render($response);
    }
    public function contact_load_documents()
    {
        $response = $this->load_documents("contact");
        $this->render($response);
    }
    public function contact_upload_file()
    {
        $response = $this->upload_files("contact");
        $this->render($response);
    }
    public function contact_create_folder()
    {
        $response = $this->create_folder("contact");
        $this->render($response);
    }
    public function contact_rename_file()
    {
        $response = $this->rename_document("contact", "file");
        $this->render($response);
    }
    public function contact_rename_folder()
    {
        $response = $this->rename_document("contact", "folder");
        $this->render($response);
    }
    public function contact_edit_documents()
    {
        $response = $this->edit_documents("contact");
        $this->render($response);
    }
    public function contact_share_folder($folder_id = NULL)
    {
        $response = $this->share_folder("contact", $folder_id);
        $this->render($response);
    }
    public function contact_download_file($file_id, $newest_version = false, $base64 = true)
    {
        $base64 = is_string($base64) ? strtolower($base64) == "true" : $base64;
        $newest_version = strtolower($newest_version) == "true" ? true : false;
        $response = $this->download_file("contact", $file_id, $newest_version, $base64);
        $this->render($response);
    }
    public function contact_file_info($file_id, $newest_version = false)
    {
        $response = $this->file_info("contact", $file_id, $newest_version);
        $this->render($response);
    }
    public function contact_delete_document()
    {
        $response = $this->delete_document("contact");
        $this->render($response);
    }
    public function case_load_documents()
    {
        $response = $this->load_documents("case");
        $this->render($response);
    }
    public function case_upload_file()
    {
        $response = $this->upload_files("case");
        $this->render($response);
    }
    public function case_create_folder()
    {
        $response = $this->create_folder("case");
        $this->render($response);
    }
    public function case_rename_file()
    {
        $response = $this->rename_document("case", "file");
        $this->render($response);
    }
    public function case_rename_folder()
    {
        $response = $this->rename_document("case", "folder");
        $this->render($response);
    }
    public function case_edit_documents()
    {
        $response = $this->edit_documents("case");
        $this->render($response);
    }
    public function case_share_folder($folder_id = NULL)
    {
        $response = $this->share_folder("case", $folder_id);
        $this->render($response);
    }
    public function case_download_file($file_id, $newest_version = false, $base64 = true)
    {
        $base64 = is_string($base64) ? strtolower($base64) == "true" : $base64;
        $newest_version = strtolower($newest_version) == "true" ? true : false;
        $response = $this->download_file("case", $file_id, $newest_version, $base64);
        $this->render($response);
    }
    public function case_file_info($file_id, $newest_version = false)
    {
        $response = $this->file_info("case", $file_id, $newest_version);
        $this->render($response);
    }
    public function case_email_download_file($file_id)
    {
        $response = $this->download_file("case", $file_id);
        $this->render($response);
    }
    public function case_delete_document()
    {
        $response = $this->delete_document("case");
        $this->render($response);
    }
    public function case_email_delete_document()
    {
        $response = $this->delete_document("case");
        $this->render($response);
    }
    public function intellectual_property_load_documents()
    {
        $response = $this->load_documents("case");
        $this->render($response);
    }
    public function intellectual_property_upload_file()
    {
        $response = $this->upload_files("case");
        $this->render($response);
    }
    public function intellectual_property_create_folder()
    {
        $response = $this->create_folder("case");
        $this->render($response);
    }
    public function intellectual_property_rename_file()
    {
        $response = $this->rename_document("case", "file");
        $this->render($response);
    }
    public function intellectual_property_rename_folder()
    {
        $response = $this->rename_document("case", "folder");
        $this->render($response);
    }
    public function intellectual_property_edit_documents()
    {
        $response = $this->edit_documents("case");
        $this->render($response);
    }
    public function intellectual_property_share_folder($folder_id = NULL)
    {
        $response = $this->share_folder("case", $folder_id);
        $this->render($response);
    }
    public function intellectual_property_download_file($file_id, $newest_version = false, $base64 = true)
    {
        $base64 = is_string($base64) ? strtolower($base64) == "true" : $base64;
        $newest_version = strtolower($newest_version) == "true" ? true : false;
        $response = $this->download_file("case", $file_id, $newest_version, $base64);
        $this->render($response);
    }
    public function intellectual_property_file_info($file_id, $newest_version = false)
    {
        $response = $this->file_info("case", $file_id, $newest_version);
        $this->render($response);
    }
    public function intellectual_property_delete_document()
    {
        $response = $this->delete_document("case");
        $this->render($response);
    }
    public function docs_load_documents()
    {
        $response = $this->load_documents("doc");
        $this->render($response);
    }
    public function docs_upload_file()
    {
        $response = $this->upload_files("doc");
        $this->render($response);
    }
    public function docs_create_folder()
    {
        $response = $this->create_folder("doc");
        $this->render($response);
    }
    public function docs_rename_file()
    {
        $response = $this->rename_document("doc", "file");
        $this->render($response);
    }
    public function docs_rename_folder()
    {
        $response = $this->rename_document("doc", "folder");
        $this->render($response);
    }
    public function docs_edit_documents()
    {
        $response = $this->edit_documents("doc");
        $this->render($response);
    }
    public function docs_share_folder($folder_id = NULL)
    {
        $response = $this->share_folder("doc", $folder_id);
        $this->render($response);
    }
    public function docs_download_file($file_id, $newest_version = false, $base64 = true)
    {
        $base64 = is_string($base64) ? strtolower($base64) == "true" : $base64;
        $newest_version = strtolower($newest_version) == "true" ? true : false;
        $response = $this->download_file("doc", $file_id, $newest_version, $base64);
        $this->render($response);
    }
    public function docs_file_info($file_id, $newest_version = false)
    {
        $response = $this->file_info("doc", $file_id, $newest_version);
        $this->render($response);
    }
    public function docs_delete_document()
    {
        $response = $this->delete_document("doc");
        $this->render($response);
    }
    public function bill_load_documents()
    {
        $response = $this->load_documents("BI");
        $this->render($response);
    }
    public function bill_upload_file()
    {
        $response = $this->upload_files("BI");
        $this->render($response);
    }
    public function bill_rename_file()
    {
        $response = $this->rename_document("BI", "file");
        $this->render($response);
    }
    public function bill_edit_documents()
    {
        $response = $this->edit_documents("BI");
        $this->render($response);
    }
    public function bill_download_file($file_id)
    {
        $response = $this->download_file("BI", $file_id);
        $this->render($response);
    }
    public function bill_file_info($file_id, $newest_version = false)
    {
        $response = $this->file_info("BI", $file_id, $newest_version);
        $this->render($response);
    }
    public function bill_delete_document()
    {
        $response = $this->delete_document("BI");
        $this->render($response);
    }
    public function expense_load_documents()
    {
        $response = $this->load_documents("EXP");
        $this->render($response);
    }
    public function expense_upload_file()
    {
        $response = $this->upload_files("EXP");
        $this->render($response);
    }
    public function expense_rename_file()
    {
        $response = $this->rename_document("EXP", "file");
        $this->render($response);
    }
    public function expense_edit_documents()
    {
        $response = $this->edit_documents("EXP");
        $this->render($response);
    }
    public function expense_download_file($file_id)
    {
        $response = $this->download_file("EXP", $file_id);
        $this->render($response);
    }
    public function expense_file_info($file_id, $newest_version = false)
    {
        $response = $this->file_info("EXP", $file_id, $newest_version);
        $this->render($response);
    }
    public function expense_delete_document()
    {
        $response = $this->delete_document("EXP");
        $this->render($response);
    }
    public function invoice_load_documents()
    {
        $response = $this->load_documents("INV");
        $this->render($response);
    }
    public function invoice_upload_file()
    {
        $response = $this->upload_files("INV");
        $this->render($response);
    }
    public function invoice_rename_file()
    {
        $response = $this->rename_document("INV", "file");
        $this->render($response);
    }
    public function invoice_edit_documents()
    {
        $response = $this->edit_documents("INV");
        $this->render($response);
    }
    public function invoice_download_file($file_id)
    {
        $response = $this->download_file("INV", $file_id);
        $this->render($response);
    }
    public function invoice_file_info($file_id, $newest_version = false)
    {
        $response = $this->file_info("INV", $file_id, $newest_version);
        $this->render($response);
    }
    public function invoice_delete_document()
    {
        $response = $this->delete_document("INV");
        $this->render($response);
    }
    private function load_documents($module)
    {
        $response = $this->responseData;
        if ($this->dms->get_module_properties($module, "model") && !$this->input->post("module_record_id")) {
            $response["error"] = $this->lang->line("missing_id");
        } else {
            $lineage = $this->input->post("lineage");
            $allowed = true;
            if ($lineage) {
                $private_folders = $this->dms->check_folder_privacy(false, $lineage);
                $allowed = empty($private_folders) ? false : true;
            }
            if ($allowed) {
                $result = $this->dms->load_documents(["module" => $module, "module_record_id" => $this->input->post("module_record_id"), "lineage" => $this->input->post("lineage"), "term" => $this->input->post("term")]);
                if ($result["status"]) {
                    $response["success"] = $result;
                } else {
                    $response["error"] = $result["message"];
                }
            } else {
                $response["error"] = $this->lang->line("not_accessible_folder");
            }
        }
        return $response;
    }
    private function upload_file($module)
    {
        $response = $this->responseData;
        if ($this->dms->get_module_properties($module, "model") && !$this->input->post("module_record_id")) {
            $response["error"] = $this->lang->line("missing_id");
        } else {
            if (!empty($_FILES) && !empty($_FILES["file"]["name"])) {
                $this->config->load("allowed_file_uploads", true);
                $allowed_types = $this->config->item($module, "allowed_file_uploads");
                $allowed_types_arr = explode("|", $allowed_types);
                $file_info = pathinfo($_FILES["file"]["name"]);
                $file_info_extension = strtolower($file_info["extension"]);
                if (in_array($file_info_extension, $allowed_types_arr)) {
                    $lineage = $this->input->post("lineage");
                    $allowed = true;
                    if ($lineage) {
                        $private_folders = $this->dms->check_folder_privacy(false, $lineage);
                        $allowed = empty($private_folders) ? false : true;
                    }
                    if ($allowed) {
                        $result = $this->dms->upload_file(["module" => $module, "module_record_id" => $this->input->post("module_record_id"), "lineage" => $this->input->post("lineage"), "upload_key" => "file", "document_type_id" => $this->input->post("document_type_id"), "document_status_id" => $this->input->post("document_status_id"), "comment" => $this->input->post("comment")]);
                        if ($result["status"]) {
                            $response["success"]["data"]["file"] = $result["file"];
                            $response["success"]["msg"] = $result["message"];
                        } else {
                            $response["error"] = $result["message"];
                        }
                    } else {
                        $response["error"] = $this->lang->line("not_accessible_folder");
                    }
                } else {
                    $response["error"] = [];
                    $response["error"]["message"] = $this->lang->line("unallowed_upload_extensions");
                    $response["error"]["notAllowedExtensions"][] = $file_info_extension;
                }
            }
        }
        return $response;
    }
    private function upload_files($module)
    {
        $response = $this->responseData;
        if ($this->dms->get_module_properties($module, "model") && !$this->input->post("module_record_id")) {
            $response["error"] = $this->lang->line("missing_id");
        } else {
            if (!empty($_FILES)) {
                $this->config->load("allowed_file_uploads", true);
                $allowed_types = $this->config->item($module, "allowed_file_uploads");
                $allowed_types_arr = explode("|", $allowed_types);
                foreach ($_FILES as $uploaded_file_key => $uploaded_file) {
                    $file_info = pathinfo($uploaded_file["name"]);
                    $file_info_extension = strtolower($file_info["extension"]);
                    if (in_array($file_info_extension, $allowed_types_arr)) {
                        $lineage = $this->input->post("lineage");
                        $allowed = true;
                        if ($lineage) {
                            $private_folders = $this->dms->check_folder_privacy(false, $lineage);
                            $allowed = empty($private_folders) ? false : true;
                        }
                        if ($allowed) {
                            $new_version = $this->input->post("same_version", NULL, true) ? $this->input->post("same_version", NULL, true) == "true" ? false : true : true;
                            $container = $this->dms->get_container($module, $this->input->post("module_record_id"));
                            $file_existant_version = $this->document_management_system->get_document_existant_version($uploaded_file["name"], "file", $container["lineage"]);
                            if ($new_version || !$file_existant_version) {
                                $result = $this->dms->upload_file(["module" => $module, "module_record_id" => $this->input->post("module_record_id"), "lineage" => $this->input->post("lineage"), "upload_key" => $uploaded_file_key, "document_type_id" => $this->input->post("document_type_id"), "document_status_id" => $this->input->post("document_status_id"), "comment" => $this->input->post("comment")]);
                                if ($result["status"]) {
                                    $response["success"]["data"][$uploaded_file_key] = $result["file"];
                                    $response["success"]["msg"] = $this->lang->line("files_uploaded_successfully");
                                } else {
                                    $response["error"] = $result["message"];
                                }
                            } else {
                                $result = $this->dms->replace_file(["id" => $file_existant_version["id"], "module" => $module, "upload_key" => $uploaded_file_key]);
                                if ($result["status"]) {
                                    $response["success"]["data"][$uploaded_file_key] = $result["file"];
                                    $response["success"]["msg"] = $this->lang->line("files_uploaded_successfully");
                                } else {
                                    $response["error"] = $result["message"];
                                }
                            }
                        } else {
                            $response["error"] = $this->lang->line("not_accessible_folder");
                        }
                    } else {
                        $response["error"] = [];
                        $response["error"]["message"] = $this->lang->line("unallowed_upload_extensions");
                        $response["error"]["notAllowedExtensions"][] = $file_info_extension;
                    }
                }
            } else {
                $response["error"] = $this->lang->line("missing_uploaded_file_data");
            }
        }
        return $response;
    }
    private function create_folder($module)
    {
        $response = $this->responseData;
        if ($this->dms->get_module_properties($module, "model") && !$this->input->post("module_record_id")) {
            $response["error"] = $this->lang->line("missing_id");
        } else {
            if (!$this->input->post("name")) {
                $response["error"] = $this->lang->line("invalid_directory_name");
            } else {
                $result = $this->dms->create_folder(["module" => $module, "module_record_id" => $this->input->post("module_record_id"), "lineage" => $this->input->post("lineage"), "name" => $this->input->post("name")]);
                if ($result["status"]) {
                    $result["folder"] = $this->dms->model->get_document_full_details(["d.id" => $this->dms->model->get_field("id")]);
                    $folder = empty($result["folder"]) ? NULL : $result["folder"];
                    $response["success"]["data"]["folder"] = $folder;
                    $response["success"]["msg"] = $result["message"];
                } else {
                    $response["error"] = $result["message"];
                }
            }
        }
        return $response;
    }
    private function rename_document($module, $document_type)
    {
        $response = $this->responseData;
        if (!$this->input->post(NULL)) {
            $response["error"] = $this->lang->line("data_missing");
        } else {
            if (!$this->input->post("document_id")) {
                $response["error"] = $this->lang->line("missing_id");
            } else {
                if (!$this->input->post("new_name")) {
                    $response["error"] = "Invalid Name";
                } else {
                    $result = $this->dms->rename_document($module, $this->input->post("document_id"), $document_type, $this->input->post("new_name"), $document_type == "file");
                    if ($result["status"]) {
                        $response["success"]["msg"] = $result["message"];
                    } else {
                        $response["error"] = $result["message"];
                    }
                }
            }
        }
        return $response;
    }
    private function edit_documents($module)
    {
        $response = $this->responseData;
        if (!$this->input->post(NULL)) {
            $response["error"] = $this->lang->line("data_missing");
        } else {
            if (!$this->input->post("id")) {
                $response["error"] = $this->lang->line("missing_id");
            } else {
                $_POST["module"] = $module;
                $result = $this->dms->edit_documents([$this->input->post(NULL)]);
                if ($result["status"]) {
                    $response["success"]["msg"] = $result["message"];
                } else {
                    $response["error"] = $result["message"];
                }
            }
        }
        return $response;
    }
    private function share_folder($module, $folder_id)
    {
        $response = $this->responseData;
        $this->load->model("document_managment_user", "document_managment_userfactory");
        $this->document_managment_user = $this->document_managment_userfactory->get_instance();
        if (0 < $folder_id) {
            if ($this->dms->model->get_document_details(["module" => $module, "id" => $folder_id]) < 1) {
                $response["error"] = $this->lang->line("invalid_record");
            } else {
                $data = [];
                $shared_with_users = $this->document_managment_user->load_watchers_users($folder_id);
                $data["sharedWithUsers"] = isset($shared_with_users[0]) ? $shared_with_users[0] : [];
                $data["sharedWithUsersStatus"] = isset($shared_with_users[1]) ? $shared_with_users[1] : [];
                $response["success"]["data"] = $data;
            }
        } else {
            if (!$this->input->post(NULL)) {
                $response["error"] = $this->lang->line("data_missing");
            } else {
                if (!$this->input->post("id") || $this->input->post("id") <= 0) {
                    $response["error"] = $this->lang->line("missing_id");
                } else {
                    $folder_id = $this->input->post("id");
                    if ($this->dms->model->get_document_details(["module" => $module, "id" => $folder_id]) < 1) {
                        $response["error"] = $this->lang->line("invalid_record");
                    } else {
                        $private = !$this->input->post("private") ? 0 : 1;
                        $watchers_users = !$this->input->post("watchers_users") ? [] : $this->input->post("watchers_users");
                        $result = $this->dms->share_folder($module, $folder_id, $private, $watchers_users, $this->user_logged_in_data["user_id"]);
                        if ($result["status"]) {
                            $response["success"]["msg"] = $result["message"];
                        } else {
                            $response["error"] = $result["message"];
                        }
                    }
                }
            }
        }
        return $response;
    }
    private function download_file($module, $file_id, $newest_version = false, $base64 = true)
    {
        if (empty($file_id)) {
            show_404();
        }
        if ($base64) {
            $response = $this->responseData;
            $result = $this->dms->get_file_download_data($module, $file_id, $newest_version);
            if ($result["status"]) {
                $result["data"]["file_content"] = base64_encode($result["data"]["file_content"]);
                $response["success"]["data"] = $result["data"];
            } else {
                $response["error"] = $result["message"];
            }
            return $response;
        }
        $this->load->helper("download");
        $response = $this->dms->get_file_download_data($module, $file_id, true);
        if ($response["status"]) {
            force_download(addslashes($response["data"]["file_name"]), $response["data"]["file_content"]);
            exit;
        }
    }
    private function file_info($module, $file_id, $newest_version = false)
    {
        $response = $this->responseData;
        $file_info = $newest_version ? $this->dms->model->get_document_newest_version_details($file_id) : $this->dms->model->get_document_details(["id" => $file_id, "module" => $module]);
        if (!empty($file_info)) {
            if ($file_info["last_locked_by_channel"] == "EXT") {
                $this->load->model("external_share_document", "external_share_documentfactory");
                $this->external_share_document = $this->external_share_documentfactory->get_instance();
                $this->external_share_document->fetch(["token_id" => $file_info["last_locked_by"]]);
                $file_info["last_locked_by_name"] = $this->external_share_document->get_field("external_user_email");
                $file_info["last_locked_by_email"] = $file_info["last_locked_by_name"];
            }
            $response["success"]["data"] = $file_info;
        }
        $this->render($response);
    }
    private function delete_document($module)
    {
        $response = $this->responseData;
        if (!$this->input->post("document_id")) {
            $response["error"] = $this->lang->line("data_missing");
        } else {
            $result = $this->dms->delete_document($module, $this->input->post("document_id"), true);
            if ($result["status"]) {
                $response["success"]["msg"] = $result["message"];
            } else {
                $response["error"] = $result["message"];
            }
        }
        return $response;
    }
    public function docs_move_document()
    {
        $this->render($this->move_document("doc"));
    }
    public function case_move_document()
    {
        $this->render($this->move_document("case"));
    }
    public function contact_move_document()
    {
        $this->render($this->move_document("contact"));
    }
    public function company_move_document()
    {
        $this->render($this->move_document("company"));
    }
    public function intellectual_property_move_document()
    {
        $this->render($this->move_document("case"));
    }
    private function move_document($module)
    {
        $response = $this->responseData;
        $selected_documents = $this->input->post("selected_documents");
        $target_folder_id = $this->input->post("target_folder");
        if (empty($selected_documents) || empty($target_folder_id)) {
            $response["error"] = $this->lang->line("data_missing");
        } else {
            $response_result = $this->dms->move_document_handler($target_folder_id, $selected_documents, [], $module);
            $response["error"] = sprintf($this->lang->line($response_result["result"]["error"]["message"]), $response_result["result"]["error"]["value"]);
            $response["success"] = $response_result["result"]["status"];
        }
        return $response;
    }
    public function case_container_load_documents()
    {
        $response = $this->load_documents("caseContainer");
        $this->render($response);
    }
    public function case_container_upload_file()
    {
        $response = $this->upload_file("caseContainer");
        $this->render($response);
    }
    public function case_container_upload_files()
    {
        $response = $this->upload_files("caseContainer");
        $this->render($response);
    }
    public function case_container_create_folder()
    {
        $response = $this->create_folder("caseContainer");
        $this->render($response);
    }
    public function case_container_rename_file()
    {
        $response = $this->rename_document("caseContainer", "file");
        $this->render($response);
    }
    public function case_container_rename_folder()
    {
        $response = $this->rename_document("caseContainer", "folder");
        $this->render($response);
    }
    public function case_container_edit_documents()
    {
        $response = $this->edit_documents("caseContainer");
        $this->render($response);
    }
    public function case_container_share_folder($folder_id = NULL)
    {
        $response = $this->share_folder("caseContainer", $folder_id);
        $this->render($response);
    }
    public function case_container_download_file($file_id, $newest_version = false, $base64 = true)
    {
        $base64 = is_string($base64) ? strtolower($base64) == "true" : $base64;
        $newest_version = strtolower($newest_version) == "true" ? true : false;
        $response = $this->download_file("caseContainer", $file_id, $newest_version, $base64);
        $this->render($response);
    }
    public function case_container_file_info($file_id, $newest_version = false)
    {
        $response = $this->file_info("caseContainer", $file_id, $newest_version);
        $this->render($response);
    }
    public function case_container_delete_document()
    {
        $response = $this->delete_document("caseContainer");
        $this->render($response);
    }
    public function case_container_move_document()
    {
        $response = $this->move_document("caseContainer");
        $this->render($response);
    }
    public function contract_upload_file()
    {
        $response = $this->upload_files("contract");
        $this->render($response);
    }
    public function contract_download_file($file_id, $newest_version = false, $base64 = true)
    {
        $base64 = is_string($base64) ? strtolower($base64) == "true" : $base64;
        $newest_version = strtolower($newest_version) == "true" ? true : false;
        $response = $this->download_file("contract", $file_id, $newest_version, $base64);
        $this->render($response);
    }
    public function contract_file_info($file_id, $newest_version = false)
    {
        $response = $this->file_info("contract", $file_id, $newest_version);
        $this->render($response);
    }
    public function contract_load_documents()
    {
        $response = $this->load_documents("contract");
        $this->render($response);
    }
    public function contract_create_folder()
    {
        $response = $this->create_folder("contract");
        $this->render($response);
    }
    public function contract_rename_file()
    {
        $response = $this->rename_document("contract", "file");
        $this->render($response);
    }
    public function contract_rename_folder()
    {
        $response = $this->rename_document("contract", "folder");
        $this->render($response);
    }
    public function contract_edit_documents()
    {
        $response = $this->edit_documents("contract");
        $this->render($response);
    }
    public function contract_share_folder($folder_id = NULL)
    {
        $response = $this->share_folder("contract", $folder_id);
        $this->render($response);
    }
    public function contract_delete_document()
    {
        $response = $this->delete_document("contract");
        $this->render($response);
    }
    public function contract_move_document()
    {
        $this->render($this->move_document("contract"));
    }
    public function docs_upload_template()
    {
        $this->load->model("doc_generator");
        $this->load->model("document_management_system", "document_management_systemfactory");
        $this->document_management_system = $this->document_management_systemfactory->get_instance();
        $template_folder_id = $this->doc_generator->get_value_by_key("template_folder_path");
        if ($template_folder_id) {
            $this->document_management_system->fetch($template_folder_id);
            $_POST["lineage"] = $this->document_management_system->get_field("lineage");
            $response = $this->upload_files("doc");
        } else {
            $response = $this->responseData;
            $response["error"] = $this->lang->line("template_folder_path_is_not_specified");
        }
        $this->render($response);
    }
    public function docs_load_all_folders()
    {
        $response = $this->responseData;
        $this->load->model("document_management_system", "document_management_systemfactory");
        $this->document_management_system = $this->document_management_systemfactory->get_instance();
        $response["success"]["data"] = $this->document_management_system->load_all_folders("doc", "", true);
        $this->render($response);
    }
    public function update_lock_status()
    {
        $this->load->model("document_management_system", "document_management_systemfactory");
        $this->document_management_system = $this->document_management_systemfactory->get_instance();
        $response = $this->responseData;
        if ($this->input->post(NULL) && $this->document_management_system->fetch($this->input->post("id"))) {
            if ($this->input->post("is_locked")) {
                $this->document_management_system->set_field("is_locked", 1);
                $this->document_management_system->set_field("last_locked_by", $this->user_logged_in_data["user_id"]);
                $this->document_management_system->set_field("last_locked_by_channel", "A4L");
                $this->document_management_system->set_field("last_locked_on", date("Y-m-d H:i:s"));
            } else {
                $this->document_management_system->set_field("is_locked", 0);
            }
            $this->document_management_system->update();
        } else {
            $response["error"] = $this->lang->line("missing_id");
        }
        $this->render($response);
    }
    // Added By: Atinga Eric
    //add 
     public function load_documents_form_data()
    {
        $response = $this->responseData;
         $this->load->model("contract_document_status_language", "contract_document_status_languagefactory");
        $this->contract_document_status_language = $this->contract_document_status_languagefactory->get_instance();
        $this->load->model("contract_document_type_language", "contract_document_type_languagefactory");
        $this->contract_document_type_language = $this->contract_document_type_languagefactory->get_instance();
        $data["document_statuses"] = $this->contract_document_status_language->load_list_per_language("en");
        $data["document_types"] = $this->contract_document_type_language->load_list_per_language("en");

        $response["success"]["data"] = $data;
        $this->render($response);
       
    }
}

?>