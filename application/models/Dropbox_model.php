<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require_once substr(COREPATH, 0, -12) . "/application/libraries/Kunnu/vendor/autoload.php";
class Dropbox_model extends CI_Model implements Document_third_party
{
    public $dropbox;
    public $auth_helper;
    public $auth_callback_URL;
    public $token;
    public $ci;
    public function __construct($params)
    {
        parent::__construct();
        $this->ci =& get_instance();
        if ($this->cloud_installation_type) {
            $params = ["client_id" => "gijf1d9trda53qt", "client_secret" => "78nmfewhs02i7fr"];
        }
        $this->token = $this->get_token();
        $app = new Kunnu\Dropbox\DropboxApp($params["client_id"]??=0, $params["client_secret"]??=null, $this->token);
        $this->dropbox = new Kunnu\Dropbox\Dropbox($app);
        $this->auth_helper = $this->dropbox->getAuthHelper();
        $get_data = $this->ci->input->get("state");
        if (!empty($get_data["state"])) {
        }
    }
    public function get_auth_callback_url()
    {
        if ($this->cloud_installation_type) {
            $instance_data = $this->ci->session->userdata("instance_data");
            $current_url = current_url();
            return ["url" => substr($current_url, 0, strpos($current_url, $instance_data["instanceID"])) . "callback_document_oauth.php", "state" => urlencode(base_url())];
        }
        return ["url" => BASEURL . "integrations/dropbox_auth_callback", "state" => ""];
    }
    public function get_request_headers()
    {
        $return = [];
        if ($this->get_team_id()) {
            $admin_info = $this->dropbox->getAuthenticatedAdmin();
            $return = ["Dropbox-API-Select-User" => $admin_info["admin_profile"]["team_member_id"]];
        }
        return $return;
    }
    public function get_type()
    {
        return strtolower(substr("Dropbox_model", 0, -6));
    }
    public function get_auth_url()
    {
        $callback = $this->get_auth_callback_url();
        $authUrl = $this->auth_helper->getAuthUrl($callback["url"], [], $callback["state"]);
        return $authUrl;
    }
    public function login_callback($in_temp_session = false)
    {
        $code = $this->ci->input->get("code");
        $state = $this->ci->input->get("state");
        if (!empty($code) && !empty($state)) {
            $callback = $this->get_auth_callback_url();
            $accessToken = $this->auth_helper->getAccessToken($code, $state, $callback["url"]);
            if (!$in_temp_session) {
                $this->set_token($accessToken->getToken());
                $this->set_team_id($accessToken->getTeamId());
                $this->set_default_folder("Apps/default_application_folder");
            } else {
                $this->session->set_userdata("dropbox_access_token", $accessToken->getToken());
                $this->session->set_userdata("dropbox_team_id", $accessToken->getTeamId());
            }
        }
    }
    public function get_token()
    {
        return $this->session->userdata("dropbox_access_token") ? $this->session->userdata("dropbox_access_token") : false;
    }
    public function get_team_id()
    {
        return $this->session->userdata("dropbox_team_id") ? $this->session->userdata("dropbox_team_id") : false;
    }
    public function set_token($token)
    {
        $this->load->model("system_configuration");
        $this->system_configuration->set_value_by_key("dropbox_access_token", $token);
        $this->session->set_userdata("dropbox_access_token", $token);
    }
    public function set_default_folder($dropbox_default_folder)
    {
        $this->load->model("system_configuration");
        $this->system_configuration->set_value_by_key("dropbox_default_folder", $dropbox_default_folder);
        $this->session->set_userdata("dropbox_default_folder", $dropbox_default_folder);
    }
    public function get_default_folder()
    {
        return $this->session->userdata("dropbox_default_folder") ? $this->session->userdata("dropbox_default_folder") : false;
    }
    public function get_token_from_db()
    {
        $this->load->model("system_configuration");
        return $this->system_configuration->get_value_by_key("dropbox_access_token");
    }
    public function set_team_id($team_id)
    {
        $this->load->model("system_configuration");
        $this->system_configuration->set_value_by_key("dropbox_team_id", $team_id);
        $this->session->set_userdata("dropbox_team_id", $team_id);
    }
    public function get_metadata($path, $headers = [])
    {
        return $this->dropbox->getMetadata($path, [], $headers);
    }
    public function list_folder_contents($path, $only_folder = false)
    {
        $headers = $this->get_request_headers();
        $list_folder_contents = $this->dropbox->listFolder($path, [], $headers);
        $items = $list_folder_contents->getItems();
        $content = $items->all();
        $response = [];
        $provider = $this->get_type();
        foreach ($content as $data) {
            if (!$only_folder) {
                $response["data"][] = ["id" => $data->id, "name" => $data->name, "lineage" => $data->path_display, "type" => $data->_obfuscated_2E746167_, "providerName" => $provider, "extension" => $data->_obfuscated_2E746167_ == "file" ? pathinfo($data->name, PATHINFO_EXTENSION) : ""];
            } else {
                if ($data->_obfuscated_2E746167_ == "folder") {
                    $response["data"][] = ["id" => $data->id, "name" => $data->name, "lineage" => $data->path_display, "type" => $data->_obfuscated_2E746167_, "providerName" => $provider, "extension" => $data->_obfuscated_2E746167_ == "file" ? pathinfo($data->name, PATHINFO_EXTENSION) : ""];
                }
            }
        }
        $response["totalRows"] = isset($response["data"]) ? count($response["data"]) : 0;
        return $response;
    }
    public function create_root_folder()
    {
        $searchQuery = "App4Legal";
        $searchResults = $this->dropbox->search("/", $searchQuery);
        $items = $searchResults->getItems();
        $items->all();
        $item = $items->first();
        $item->getMatchType();
        $item->getMetadata();
        $root_id = "";
        foreach ($items as $item) {
            if ($item->metadata["path_lower"] === "/app4legal") {
                $root_id = $item->metadata["id"];
            }
        }
        if (!$root_id) {
            $root_id = $this->create_folder("/App4Legal");
        }
        return $root_id;
    }
    public function rename_document($old_name, $new_name)
    {
        $headers = $this->get_request_headers();
        $file = $this->dropbox->move($old_name, $new_name, $headers);
        return $file->getName();
    }
    public function revoke_access_token()
    {
        $headers = $this->get_request_headers();
        $this->auth_helper->revokeAccessToken($headers);
        $this->set_token(NULL);
        $this->set_team_id(NULL);
        $this->set_default_folder(NULL);
    }
    public function upload_file()
    {
        $headers = $this->get_request_headers();
        $this->ci->config->load("allowed_file_uploads", true);
        $config["max_size"] = $this->ci->config->item("allowed_upload_size_kilobite");
        $config["upload_path"] = $this->ci->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "docs";
        $this->create_tmp_directory($config["upload_path"]);
        $config["upload_path"] .= DIRECTORY_SEPARATOR . "usr_" . $this->ci->session->userdata("AUTH_user_id");
        $this->create_tmp_directory($config["upload_path"]);
        $this->delete_directory_content($config["upload_path"]);
        $config["allowed_types"] = $this->ci->config->item("doc", "allowed_file_uploads");
        $config["remove_spaces"] = false;
        $config["overwrite"] = true;
        $this->ci->load->library("upload", $config);
        $response = ["status" => false, "message" => "", "file" => ""];
        if ($this->ci->upload->do_upload("uploadDoc")) {
            $upload_data = $this->ci->upload->data();
            $dropbox_file = new Kunnu\Dropbox\DropboxFile($upload_data["full_path"]);
            $file = $this->dropbox->upload($dropbox_file, $this->ci->input->post("lineage") . "/" . $upload_data["file_name"], ["mode" => "overwrite"], $headers);
            $response["status"] = true;
            $response["message"] = $this->ci->lang->line("file_uploaded_successfully");
            $response["file"] = $file->getName();
        } else {
            $response["message"] = strip_tags($this->ci->upload->display_errors());
        }
        return $response;
    }
    public function delete_directory_content($dir_path)
    {
        if (!is_dir($dir_path)) {
            return NULL;
        }
        if (substr($dir_path, strlen($dir_path) - 1, 1) != "/") {
            $dir_path .= "/";
        }
        $files = glob($dir_path . "*", GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
    }
    private function create_tmp_directory($path)
    {
        if (!file_exists($path)) {
            mkdir($path);
        }
    }
    public function create_folder($path, $headers = [])
    {
        $headers = empty($headers) ? $this->get_request_headers() : $headers;
        $folder = $this->dropbox->createFolder($path, false, $headers);
        return $folder->getId();
    }
    public function delete_document($path)
    {
        $headers = $this->get_request_headers();
        $deleted_folder = $this->dropbox->delete($path, $headers);
        return $deleted_folder->getName();
    }
    public function download_file($source_path, $file_name)
    {
        $response = ["file_content" => "", "result" => false, "message" => ""];
        $this->ci->config->load("allowed_file_uploads", true);
        if (strpos($this->ci->config->item("doc", "allowed_file_uploads"), pathinfo($file_name, PATHINFO_EXTENSION)) !== false) {
            $headers = $this->get_request_headers();
            $file_metadata = $this->get_metadata($source_path, $headers);
            if ($file_metadata->size && $file_metadata->size < $this->ci->config->item("allowed_upload_size_bite")) {
                $upload_path = $this->ci->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "docs";
                $this->create_tmp_directory($upload_path);
                $path_to_save_file = $upload_path . DIRECTORY_SEPARATOR . $file_name;
                $file = $this->dropbox->download($source_path, $path_to_save_file, $headers);
                $response["file_content"] = file_get_contents($path_to_save_file);
                unlink($path_to_save_file);
                $response["result"] = true;
            } else {
                $response["message"] = sprintf($this->ci->lang->line("integration_document_large_file"), $this->get_type());
            }
        } else {
            $response["message"] = $this->ci->lang->line("upload_invalid_file_type");
        }
        return $response;
    }
    public function search_folder($folder_path, $search_query)
    {
        $headers = $this->get_request_headers();
        $searchResults = $this->dropbox->search($folder_path, $search_query, ["start" => 0, "max_results" => 5], $headers);
        $items = $searchResults->getItems();
        $content = $items->all();
        $response = [];
        $provider = $this->get_type();
        foreach ($content as $data) {
            $response["data"][] = ["id" => $data->metadata["id"], "name" => $data->metadata["name"], "lineage" => $data->metadata["path_display"], "type" => $data->metadata[".tag"], "providerName" => $provider, "extension" => $data->metadata[".tag"] == "file" ? pathinfo($data->metadata["name"], PATHINFO_EXTENSION) : ""];
        }
        $response["totalRows"] = isset($response["data"]) ? count($response["data"]) : 0;
        return $response;
    }
    public function create_model_folder($root_dir, $model_id, $model_name, $default_folder = "")
    {
        $headers = $this->get_request_headers();
        $dir = mb_substr($model_name, 0, 30);
        $dir = str_replace("\\", "", $dir);
        $dir = preg_replace("/(\\/(.|[\\r\\n])*)|(ns:[0-9]+(\\/.*)?)/", "", $dir);
        $dir = trim($dir);
        $dir = $dir ? $model_id . "-" . $dir : $model_id;
        if ($default_folder == "Apps/default_application_folder" || $default_folder == "/") {
            $default_folder = "";
        }
        $list_folder_contents = $this->dropbox->listFolder($default_folder, [], $headers);
        $items = $list_folder_contents->getItems();
        $content = $items->all();
        $root_id = "";
        foreach ($content as $data) {
            if ($data->name === $root_dir) {
                $root_id = $data->id;
            }
        }
        if (!$root_id) {
            $this->create_folder(empty($default_folder) ? "/" . $root_dir : $default_folder . "/" . $root_dir, $headers);
        }
        try {
            $list_folder_contents = $this->dropbox->getMetadata(empty($default_folder) ? "/" . $root_dir . "/" . $dir : $default_folder . "/" . $root_dir . "/" . $dir, [], $headers);
        } catch (Kunnu\Dropbox\Exceptions\DropboxClientException $exception) {
            if (strpos($exception->getMessage(), "not_found")) {
                $this->create_folder(empty($default_folder) ? "/" . $root_dir . "/" . $dir : $default_folder . "/" . $root_dir . "/" . $dir, $headers);
            }
        }
        return empty($default_folder) ? "/" . $root_dir . "/" . $dir : $default_folder . "/" . $root_dir . "/" . $dir;
    }
}

?>