<?php

require "Ci_top_controller.php";
class Users extends CI_Top_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library("api");
        $this->responseData = default_response_data();
    }
    public function login()
    {
        $this->load->model("action");
        $post_data = $this->input->post(NULL);
        $post_data["password"] = $this->input->post("password", false);
        $response = $this->action->login($post_data);
        $response["success"]["data"]["timezone"] = $this->timezone;
        $response["success"]["data"]["timezoneOffset"] = $this->timezoneOffset;
        $this->render_data($response);
    }
    public function check_api()
    {
        $this->load->model("action");
        $apiKey = $this->input->get_request_header("X-api-key", true);
        $response = $this->action->check_key($apiKey, $this->getInstanceConfig("api_key_validity_time"));
        $this->render_data($response);
    }
    private function render_data($response)
    {
        $this->output->set_content_type("application/json")->set_header("Access-Control-Allow-Origin: *")->set_output(json_encode($response));
    }
    public function productVersion()
    {
        $this->load->library("is_auth");
        $response = $this->responseData;
        $appVersions = json_decode(@file_get_contents(@substr(COREPATH, 0, -12) . "appVersions.json"), true);
        $response["success"]["data"] = ["productVersion" => isset($appVersions["a4l"]) ? $appVersions["a4l"] : "", "apiVersion" => isset($appVersions["api"]) ? $appVersions["api"] : ""];
        $this->output->set_content_type("application/json")->set_header("Access-Control-Allow-Origin: *")->set_output(json_encode($response));
    }
    public function reset_password()
    {
        $user_login = trim($this->input->post("userLogin"));
        $response = $this->responseData;
        if (strcmp($user_login, "")) {
            $this->load->model("user", "userfactory");
            $this->user = $this->userfactory->get_instance();
            $userData = $this->user->get_user_by_username($user_login);
            $userData = empty($userData) ? $this->user->get_user_by_email($user_login) : $userData;
            if (!empty($userData)) {
                if ($userData["isAd"] == 1) {
                    $this->set_flashmessage("error", $this->set_flashmessage("error", sprintf($this->lang->line("no_password_change"), $this->lang->line("ActiveDirectory"))));
                } else {
                    if ($this->session->userdata("a4l_sso_login") && $this->session->userdata("is_cloud")) {
                        $this->set_flashmessage("error", $this->set_flashmessage("error", sprintf($this->lang->line("no_password_change"), $this->lang->line("AzureDirectory"))));
                    } else {
                        if ($userData["banned"] == 1) {
                            $response["error"] = $this->lang->line("user_banned");
                        } else {
                            $data["userId"] = $userData["id"];
                            $this->load->model("user_profile");
                            $this->user_profile->fetch(["user_id" => $data["userId"]]);
                            $this->load->library("email_notifications");
                            $encoded_username = md5(mt_rand() . $userData["username"]);
                            $changePasswordURL = str_replace("/modules/api/", "/pages/change_password/", site_url()) . $data["userId"] . "/" . $encoded_username;
                            $this->load->model("system_preference");
                            $system_preference = $this->system_preference->get_key_groups();
                            $config = $system_preference["OutgoingMail"];
                            try {
                                $result = $this->email_notifications->send_email($userData["email"], $this->lang->line("reset_password"), sprintf($this->lang->line("reset_password_message"), $this->user_profile->get_field("firstName") . " " . $this->user_profile->get_field("lastName"), $changePasswordURL, $changePasswordURL, $config["outgoingMailSubjectPrefix"]), [], []);
                                if (!$result) {
                                    $response["error"] = $this->lang->line("email_not_Sent");
                                } else {
                                    if ($result) {
                                        $userProfileData = [];
                                        $userProfileData["forgetPasswordHashKey"] = $encoded_username;
                                        $userProfileData["forgetPasswordFlag"] = 0;
                                        $this->user_profile->set_fields($userProfileData);
                                        $this->user_profile->update();
                                        $response["success"]["msg"] = $this->lang->line("email_reset_password_sent");
                                    }
                                }
                            } catch (Exception $ex) {
                                $response["error"] = $ex->getMessage();
                            }
                        }
                    }
                }
            } else {
                $response["error"] = $this->lang->line("user_login_not_found");
            }
        } else {
            $response["error"] = $this->lang->line("data_missing");
        }
        $output = $this->output->set_output(json_encode($response));
        header("Content-Type: application/json");
        header("Access-Control-Allow-Origin: *");
        echo $output->get_output();
        exit;
    }
    public function refresh_api_key()
    {
        $response = $this->responseData;
        $api_key = $this->input->get_request_header("X-api-key", true);
        if ($api_key) {
            $this->load->model("user_api_key", "user_api_keyfactory");
            $this->user_api_key = $this->user_api_keyfactory->get_instance();
            $refreshed_api_key = $this->user_api_key->refresh($api_key, $this->getInstanceConfig("api_key_validity_time"));
            if ($refreshed_api_key !== false) {
                $response["success"]["data"] = ["key" => $refreshed_api_key];
                $response["success"]["msg"] = "Api Key has been refreshed succesfully";
            } else {
                $response["error"] = "unable to refresh the api key";
            }
        } else {
            $response["error"] = $this->lang->line("data_missing");
        }
        $this->render_data($response);
    }
    public function user_info()
    {
        $api_key = $this->input->get_request_header("X-api-key", true);
        $language = $this->input->post("lang") ?? "english";
        if ($api_key) {
            $this->load->model("action");
            $response = $this->action->get_user_info($api_key, $language);
            $response["success"]["data"]["timezone"] = $this->timezone;
            $response["success"]["data"]["timezoneOffset"] = $this->timezoneOffset;
        } else {
            $response["error"] = $this->lang->line("data_missing");
        }
        $this->render_data($response);
    }
}

?>