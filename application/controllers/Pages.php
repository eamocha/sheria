<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require "Top_controller.php";
class Pages extends Top_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->currentTopNavItem = "pages";
    }
    public function index()
    {
        $this->_remap("home");
    }
    public function _remap($method_name, $args = [])
    {
        if (!method_exists($this, $method_name)) {
            array_unshift($args, $method_name);
            call_user_func_array([$this, "render_page"], $args);
        } else {
            empty($args);
            empty($args) ? call_user_func([$this, $method_name]) : call_user_func_array([$this, $method_name], $args);
        }
    }
    private function render_page($pageName)
    {
        if (!$this->is_auth->is_logged_in()) {
            $sso_app4legal = $this->system_preference->get_specified_system_preferences("ssoApp4legal");
            if ($sso_app4legal && $this->input->server("AUTH_USER")) {
                redirect("users/login");
            }
            $this->pageTitle = sprintf($this->lang->line("app4legal_browser_title"), $this->lang->line("home_page_browser_title"));
            $data = ["warningMessageOnLoginPage" => ""];
            if ($pageName == "home") {
                $this->force_cloud_login_screen();
                $this->load->model("system_preference");
                $warningMessageOnLoginPage = $this->system_preference->get_value_by_key("warningMessageOnLoginPage");
                if (isset($warningMessageOnLoginPage["keyValue"]) && !empty($warningMessageOnLoginPage["keyValue"])) {
                    $data["warningMessageOnLoginPage"] = $warningMessageOnLoginPage["keyValue"];
                }
            }
            $data["is_cloud"] = $this->cloud_installation_type;
            $data["idp_enabled"] = $this->instance_data_array["idp_enabled"];
            $this->load->view("partial/header");
            $this->load->view("users/login", $data);
            $this->load->view("partial/footer");
        } else {
            redirect("dashboard");
        }
    }
    private function reload_captcha()
    {
        ob_start();
        $response = ["status" => 0, "image" => "", "bugs" => ""];
        $this->load->library("captcha");
        if (true) {
            $captcha = $this->captcha->create();
            $response["status"] = is_array($captcha) ? 500 : 0;
            $response["image"] = isset($captcha["image"]) ? $captcha["image"] : "";
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function change_password($user_id = 0, $username_hash = "")
    {
        $this->load->model("user", "userfactory");
        $this->user = $this->userfactory->get_instance();
        $this->load->model("system_preference");
        $this->includes("scripts/password_validation", "js");
        if ($this->input->post(NULL)) {
            if (!$this->is_auth->is_logged_in()) {
                $userId = $this->input->post("user_id");
                $newPassword = $this->input->post("newPassword");
                $confirmNewPassword = $this->input->post("confirmNewPassword");
                $data = $this->get_user_change_pass_data($user_id);
                if (strlen($newPassword) < $data["minPassword"]) {
                    $data["response_message"] = sprintf($this->lang->line("min_pass_characters"), $data["minPassword"]);
                    $data["view"] = "activation_change_pass";
                    $this->load_view($data);
                } else {
                    if ($data["complexPassword"] != "no" && !$this->user->validate_complexity_pass($newPassword)) {
                        $data["response_message"] = $this->lang->line("complex_pass_msg");
                        $data["view"] = "activation_change_pass";
                        $this->load_view($data);
                    } else {
                        if ($newPassword == $confirmNewPassword) {
                            if (!$this->_password_history_check($newPassword)) {
                                $data["response_message"] = $this->lang->language["_password_history_check"];
                                $data["view"] = "activation_change_pass";
                                $this->load_view($data);
                            } else {
                                $this->user->fetch($userId);
                                $this->user->set_field("id", $userId);
                                $this->user->set_field("password", password_hash($newPassword, PASSWORD_DEFAULT));
                                $response["status"] = $this->user->update();
                                $this->load->library("form_validation");
                                $this->form_validation->set_rules("newPassword", $this->lang->line("new_password"), "trim|required|xss_clean|min_length[" . $this->user->minPassword . "]|max_length[" . $this->user->maxPassword . "]");
                                $this->form_validation->set_rules("confirmNewPassword", $this->lang->line("confirm_new_password"), "trim|required|xss_clean|matches[newPassword]");
                                $response["result"] = $this->form_validation->run();
                                if ($response["status"] && $response["result"]) {
                                    $oldUserValues = $this->user->get_old_values($user_id);
                                    $this->user->touch_logs("update", $oldUserValues);
                                    $this->user->log_password_change();
                                    $this->load->model("user_profile");
                                    $this->user_profile->fetch(["user_id" => $user_id]);
                                    $this->user_profile->set_field("flagChangePassword", "0");
                                    $this->user_profile->update();
                                    $this->session->set_userdata("AUTH_userflagChangePassword", 0);
                                    if (!$this->is_auth->is_logged_in()) {
                                        $data["response_message"]["success"] = $this->lang->line("password_changed_successfully");
                                        $data["idp_enabled"] = $this->instance_data_array["idp_enabled"];
                                        $data["view"] = "login";
                                        $this->load_view($data);
                                    } else {
                                        $this->set_flashmessage("success", $this->lang->line("password_changed_successfully"));
                                        redirect("dashboard");
                                    }
                                } else {
                                    $data["response_message"] = validation_errors();
                                    $data["view"] = "activation_change_pass";
                                    $this->load_view($data);
                                }
                            }
                        } else {
                            $data["response_message"] = $this->lang->line("passwords_not_match");
                            $data["view"] = "activation_change_pass";
                            $this->load_view($data);
                        }
                    }
                }
            } else {
                $this->load->model("user_profile");
                $this->user_profile->fetch(["user_id" => $user_id]);
                $this->user_profile->set_field("forgetPasswordFlag", "1");
                $this->user_profile->update();
                $this->set_flashmessage("error", $this->lang->line("expired_link"));
                redirect("dashboard");
            }
        } else {
            if (!$user_id && !$username_hash) {
                redirect("pages");
            } else {
                $this->load->model("user_profile");
                $profile = $this->user_profile->get_profile_by_id($user_id);
                $this->user_profile->fetch(["user_id" => $user_id]);
                if (!$this->is_auth->is_logged_in()) {
                    $data["idp_enabled"] = $this->instance_data_array["idp_enabled"];
                    if ($profile["forgetPasswordHashKey"] == $username_hash) {
                        if (!isset($profile["forgetPasswordUrlCreatedOn"])) {
                            $profile["forgetPasswordUrlCreatedOn"] = date_create("now")->format("Y-m-d H:i:s");
                            $this->user_profile->set_field("forgetPasswordUrlCreatedOn", $profile["forgetPasswordUrlCreatedOn"]);
                            $this->user_profile->update();
                        }
                        $date_diff_between_current_createdOn = date_diff(date_create("now"), date_create($profile["forgetPasswordUrlCreatedOn"]));
                        $forget_password_expires_in = $this->config->item("forgot_password_url_expires_in");
                        if ($forget_password_expires_in <= $date_diff_between_current_createdOn->h) {
                            $profile["forgetPasswordFlag"] = 1;
                            $this->user_profile->set_field("forgetPasswordFlag", "1");
                            $this->user_profile->update();
                        }
                        if ($profile["flagChangePassword"] == 1) {
                            $data["view"] = "activation_change_pass";
                            $data += $this->get_user_change_pass_data($user_id);
                            $this->session->set_userdata("flag_change_password", 1);
                            $this->load_view($data);
                        } else {
                            if ($profile["forgetPasswordFlag"] == 0) {
                                $this->user_profile->set_field("forgetPasswordFlag", "1");
                                $this->user_profile->update();
                                $data["view"] = "activation_change_pass";
                                $data += $this->get_user_change_pass_data($user_id);
                                $this->load_view($data);
                            } else {
                                if ($profile["forgetPasswordFlag"] == 1) {
                                    $data["response_message"]["error"] = $this->lang->line("link_expired");
                                    $data["view"] = "login";
                                    $this->load_view($data);
                                }
                            }
                        }
                    } else {
                        $data["response_message"]["error"] = $this->lang->line("invalid_url");
                        $data["view"] = "login";
                        $this->load_view($data);
                    }
                } else {
                    if ($this->session->userdata("flag_change_password") == 1) {
                        $this->session->set_userdata("flag_change_password", 0);
                        redirect("dashboard");
                    } else {
                        $this->session->set_userdata("flag_change_password", 0);
                        $this->user_profile->set_field("forgetPasswordFlag", "1");
                        $this->user_profile->update();
                        $this->session->userdata("a4l_sso_login");
                        $this->set_flashmessage("error", $this->lang->line("expired_link"));
                        redirect("dashboard");
                    }
                }
            }
        }
    }
    public function reset_password()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post(NULL)) {
                $user_login = $this->input->post("username");
                $this->load->model("user", "userfactory");
                $this->user = $this->userfactory->get_instance();
                $userData = $this->user->get_user_by_username($user_login);
                $userData = empty($userData) ? $this->user->get_user_by_email($user_login) : $userData;
                if (!empty($userData)) {
                    if ($userData["isAd"] == 1) {
                        $response["isAdUser"] = true;
                    } else {
                        if ($this->session->userdata("a4l_sso_login") && $this->session->userdata("is_cloud")) {//--atinga changed from (!$this->session->userdata("a4l_sso_login") && !$this->session->userdata("is_cloud"))
                            $response["isIdpUser"] = true;
                        } else {
                            if ($userData["banned"] == 1) {
                                $response["bannedUser"] = true;
                            } else {
                                $data["userId"] = $userData["id"];
                                $this->load->model("user_profile");
                                $this->user_profile->fetch(["user_id" => $data["userId"]]);
                                $this->load->library("email_notifications");
                                $this->load->model("system_preference");
                                $system_preference = $this->system_preference->get_key_groups();
                                $encoded_username = md5(mt_rand() . $userData["username"]);
                                $changePasswordURL = site_url("pages/change_password/" . $data["userId"] . "/" . $encoded_username);
                                $config = $system_preference["OutgoingMail"];
                                try {
                                    $result = $this->email_notifications->send_email($userData["email"], $this->lang->line("reset_password"), sprintf($this->lang->line("reset_password_message"), $this->user_profile->get_field("firstName") . " " . $this->user_profile->get_field("lastName"), $changePasswordURL, $changePasswordURL, $config["outgoingMailSubjectPrefix"]), [], []);
                                    if (!$result) {
                                        $response["emailSent"] = false;
                                    } else {
                                        if ($result) {
                                            $userProfileData = [];
                                            $userProfileData["forgetPasswordHashKey"] = $encoded_username;
                                            $userProfileData["forgetPasswordFlag"] = 0;
                                            $userProfileData["forgetPasswordUrlCreatedOn"] = date_create("now")->format("Y-m-d H:i:s");
                                            $this->user_profile->set_fields($userProfileData);
                                            $this->user_profile->update();
                                            $response["emailSent"] = true;
                                        }
                                    }
                                } catch (Exception $ex) {
                                    $this->set_flashmessage("error", $ex->getMessage());
                                }
                            }
                        }
                    }
                } else {
                    $response["unAvailableUserLogin"] = true;
                }
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    private function load_view($data)
    {
        $this->load->view("partial/header");
        $this->load->view("users/" . $data["view"], $data);
        $this->load->view("partial/footer");
    }
    private function get_user_change_pass_data($user_id)
    {
        $system_preference = $this->system_preference->get_key_groups();
        $this->user->fetch($user_id);
        $userEmail = $this->user->get_field("email");
        $data["email"] = $userEmail;
        $data["user_id"] = $user_id;
        $data["minPassword"] = $system_preference["PasswordPolicy"]["passwordMinimumLength"];
        $data["complexPassword"] = $system_preference["PasswordPolicy"]["passwordStrongComplexity"];
        return $data;
    }
    public function _password_history_check($clear_pass)
    {
        $this->load->model("system_preference");
        $system_preferences = $this->system_preference->get_key_groups();
        $password_disallowed_previous = $system_preferences["PasswordPolicy"]["passwordDisallowedPrevious"];
        if ($password_disallowed_previous != "" && 0 < $password_disallowed_previous) {
            $this->load->model("user_password");
            $old_passwords = $this->user_password->load_old_password($this->input->post("user_id"));
            if (!empty($old_passwords)) {
                foreach ($old_passwords as $old_password) {
                    if (substr($old_password, 0, 7) !== "\$2y\$10\$") {
                        if (crypt($this->is_auth->_encode($clear_pass), $old_password) === $old_password) {
                            $this->lang->language["_password_history_check"] = sprintf($this->lang->language["_password_history_check"], $password_disallowed_previous);
                            return false;
                        }
                    } else {
                        if (password_verify($clear_pass, $old_password)) {
                            $this->lang->language["_password_history_check"] = sprintf($this->lang->language["_password_history_check"], $password_disallowed_previous);
                            return false;
                        }
                    }
                }
            }
        }
        return true;
    }
}

?>