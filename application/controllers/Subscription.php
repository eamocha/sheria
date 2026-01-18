<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require "Top_controller.php";
class Subscription extends Top_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->currentTopNavItem = "home";
    }
    public function subscribe()
    {
        if (!$this->is_auth->is_logged_in() || !$this->input->is_ajax_request()) {
            $this->is_auth->deny_access();
        }
        $instance_data = $this->session->userdata("instance_data");
        if ($this->cloud_installation_type && $this->instance_client_type == "lead" && $instance_data["instanceID"]) {
            $user_id = $this->session->userdata("AUTH_user_id");
            $this->user->fetch($user_id);
            $this->load->model("user_profile");
            $this->user_profile->fetch(["user_id" => $user_id]);
            $user_country_id = $this->user_profile->get_field("country");
            $user_country_name = "";
            if ($user_country_id) {
                $this->load->model("language");
                $language = $this->language->get_id_by_session_lang();
                $this->load->model("country_language");
                $this->country_language->fetch(["country_id" => $user_country_id, "language_id" => $language]);
                $user_country_name = $this->country_language->get_field("name");
            }
            $response = [];
            $data = [];
            $user_data = [];
            $user_data["firstName"] = $this->user_profile->get_field("firstName");
            $user_data["lastName"] = $this->user_profile->get_field("lastName");
            $user_data["email"] = $this->user->get_field("email");
            $user_data["city"] = $this->user_profile->get_field("city");
            $user_data["mobile"] = $this->user_profile->get_field("mobile");
            $user_data["address"] = $this->user_profile->get_field("address1");
            $user_data["country"] = $user_country_name;
            $user_data["instanceID"] = $instance_data["instanceID"];
            $user_data["userID"] = $user_id;
            $cloud_params = $this->session->userdata("cloud_config_params");
            if (!empty($cloud_params)) {
                $this->load->library("a4l_cc");
                $user_data["token"] = file_get_contents($this->a4l_cc->tokenPath);
                $user_data["secretHash"] = hash("sha256", $instance_data["instanceID"] . $user_data["token"]);
                $data["go_to_url"] = $cloud_params["cc_url"] . $cloud_params["cc_subscribe_path"];
                $data["user_data"] = serialize($user_data);
                $response["html"] = $this->load->view("subscription/temp_form", $data, true);
            } else {
                $response["error"] = $this->lang->line("subscription_access_empty_instance_data");
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $this->is_auth->deny_access();
        }
    }
    public function details()
    {
        if (!$this->is_auth->is_logged_in()) {
            $this->is_auth->deny_access();
        }
        $instance_data = $this->session->userdata("instance_data");
        if ($this->cloud_installation_type && $instance_data["instanceID"] && $this->instance_subscription) {
            $response = [];
            $cloud_params = $this->session->userdata("cloud_config_params");
            if (!empty($cloud_params)) {
                $data = [];
                $this->load->library("a4l_cc");
                $subscription = $this->a4l_cc->load_subscription_data();
                if ($subscription) {
                    $subscription = json_decode($subscription, true);
                    if (isset($subscription["success"]["data"]) && ($subscription = $subscription["success"]["data"])) {
                        if ($subscription["status"] === "trialing" && $subscription["trial_start"] && $subscription["trial_end"]) {
                            $data["trialing"] = true;
                        } else {
                            if ($this->instance_client_type == "customer") {
                                $data["trialing"] = false;
                            } else {
                                redirect("home");
                            }
                        }
                        $subscription_amount = 0;
                        foreach ($subscription["items"]["data"] as $item) {
                            $subscription_amount += $item["plan"]["amount"] * $item["quantity"];
                        }
                        $data["subscription_amount"] = number_format($subscription_amount / 100, 2, ".", ",");
                        $data["subscription"] = $subscription;
                        $data["all_invoices"] = $subscription["allInvoices"];
                        $data["all_payments"] = $subscription["allPayments"];
                        $data["planKey"] = $subscription["planKey"];
                        $data["planName"] = $subscription["planName"];
                        $data["email_support_team"] = $cloud_params["email_support_team"];
                        $licenses = $this->licensor->get_all_licenses();
                        $licenses["app4legal"]["activeUsers"] = $this->user->count_active_users();
                        $this->load->model("customer_portal_users", "customer_portal_usersFactory");
                        $this->customer_portal_users = $this->customer_portal_usersFactory->get_instance();
                        $licenses["customer-portal"]["activeUsers"] = $this->customer_portal_users->count_active_users();
                        $licenses["exempted_users"] = $this->user->get_super_admin_excempted_users();
                        $data["licenses"] = $licenses;
                        $data["instanceID"] = $instance_data["instanceID"];
                        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("subscription"));
                        $this->load->view("partial/header");
                        $this->load->view("subscription/details", $data);
                        $this->load->view("partial/footer");
                    } else {
                        $this->set_flashmessage("error", $this->lang->line("invalid_license"));
                        redirect("home");
                    }
                } else {
                    $this->set_flashmessage("error", $this->lang->line("invalid_license"));
                    redirect("home");
                }
            } else {
                $this->set_flashmessage("error", $this->lang->line("subscription_access_empty_instance_data"));
                redirect("home");
            }
        } else {
            $this->is_auth->deny_access();
        }
    }
    private function upgrade()
    {
        if (!$this->is_auth->is_logged_in() || !$this->input->is_ajax_request()) {
            $this->is_auth->deny_access();
        }
        $instance_data = $this->session->userdata("instance_data");
        if ($this->cloud_installation_type && $this->instance_client_type == "customer" && $instance_data["instanceID"]) {
            $response = [];
            $data = [];
            $cloud_params = $this->session->userdata("cloud_config_params");
            if (!empty($cloud_params)) {
                $this->load->library("a4l_cc");
                $user_data["instanceID"] = $instance_data["instanceID"];
                $user_data["token"] = file_get_contents($this->a4l_cc->tokenPath);
                $user_data["secretHash"] = hash("sha256", $instance_data["instanceID"] . $user_data["token"]);
                $user_data["userID"] = $this->session->userdata("AUTH_user_id");
                $data["go_to_url"] = $cloud_params["cc_url"] . $cloud_params["cc_subscription_upgrade_path"];
                $data["user_data"] = serialize($user_data);
                $response["html"] = $this->load->view("subscription/temp_form", $data, true);
            } else {
                $response["error"] = $this->lang->line("subscription_access_empty_instance_data");
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $this->is_auth->deny_access();
        }
    }
    public function update_card()
    {
        if (!$this->is_auth->is_logged_in() || !$this->input->is_ajax_request()) {
            $this->is_auth->deny_access();
        }
        $instance_data = $this->session->userdata("instance_data");
        if ($this->cloud_installation_type && $this->instance_client_type == "customer" && $instance_data["instanceID"]) {
            $response = [];
            $data = [];
            $cloud_params = $this->session->userdata("cloud_config_params");
            if (!empty($cloud_params)) {
                $this->load->library("a4l_cc");
                $user_data["instanceID"] = $instance_data["instanceID"];
                $user_data["token"] = file_get_contents($this->a4l_cc->tokenPath);
                $user_data["secretHash"] = hash("sha256", $instance_data["instanceID"] . $user_data["token"]);
                $data["go_to_url"] = $cloud_params["cc_url"] . $cloud_params["cc_subscription_update_card_path"];
                $data["user_data"] = serialize($user_data);
                $response["html"] = $this->load->view("subscription/temp_form", $data, true);
            } else {
                $response["error"] = $this->lang->line("subscription_access_empty_instance_data");
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $this->is_auth->deny_access();
        }
    }
    public function add_user()
    {
        if (!$this->is_auth->is_logged_in() || !$this->input->is_ajax_request()) {
            $this->is_auth->deny_access();
        }
        $instance_data = $this->session->userdata("instance_data");
        if ($this->cloud_installation_type && $this->instance_client_type == "customer" && $instance_data["instanceID"]) {
            $response = [];
            $data = [];
            $cloud_params = $this->session->userdata("cloud_config_params");
            if (!empty($cloud_params)) {
                $this->load->library("a4l_cc");
                $user_id = $this->session->userdata("AUTH_user_id");
                $this->user->fetch($user_id);
                $this->load->model("user_profile");
                $this->user_profile->fetch(["user_id" => $user_id]);
                $user_data["instanceID"] = $instance_data["instanceID"];
                $user_data["firstName"] = $this->user_profile->get_field("firstName");
                $user_data["lastName"] = $this->user_profile->get_field("lastName");
                $user_data["email"] = $this->user->get_field("email");
                $user_data["userID"] = $this->session->userdata("AUTH_user_id");
                $user_data["token"] = file_get_contents($this->a4l_cc->tokenPath);
                $user_data["secretHash"] = hash("sha256", $instance_data["instanceID"] . $user_data["token"]);
                $user_data["userID"] = $this->session->userdata("AUTH_user_id");
                $data["go_to_url"] = $cloud_params["cc_url"] . $cloud_params["cc_subscription_purchase_additional_user_path"];
                $data["user_data"] = serialize($user_data);
                $response["html"] = $this->load->view("subscription/temp_form", $data, true);
            } else {
                $response["error"] = $this->lang->line("subscription_access_empty_instance_data");
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $this->is_auth->deny_access();
        }
    }
    public function add_user_window()
    {
        if (!$this->is_auth->is_logged_in() || !$this->input->is_ajax_request()) {
            $this->is_auth->deny_access();
        }
        $instance_data = $this->session->userdata("instance_data");
        if ($this->cloud_installation_type && $this->instance_client_type == "customer" && $instance_data["instanceID"]) {
            $response = [];
            $data = [];
            $cloud_params = $this->session->userdata("cloud_config_params");
            if (!empty($cloud_params)) {
                $this->load->library("a4l_cc");
                if ($this->instance_subscription) {
                    $subscription = $this->a4l_cc->load_subscription_data();
                    if ($subscription) {
                        $subscription = json_decode($subscription, true);
                        if (isset($subscription["success"]["data"]) && ($subscription = $subscription["success"]["data"])) {
                            if (isset($subscription["status"]) && $subscription["status"] == "trialing") {
                                $config = parse_ini_file(INSTANCE_PATH . "../config.ini");
                                require "application/libraries/sendgrid/vendor/autoload.php";
                                $email = new SendGrid\Mail\Mail();
                                $email->setFrom("info@sheria360.com");
                                $email->setSubject("Request for an additional user - Instance ID " . $instance_data["instanceID"] . " -  " . $subscription["customer"]["name"]);
                                $email->addTo($cloud_params["email_support_team"]);
                                $email->addCc($config["email_accounting_team"]);
                                $email->addContent("text/html", sprintf($this->lang->line("additional_user_email_content"), $instance_data["instanceID"], $this->session->userdata("AUTH_userProfileName"), $this->is_auth->get_email_address()));
                                $sendgrid = new SendGrid($config["sg.api-key"]);
                                $sendgrid->send($email);
                                $response["request_being_processed"] = $this->lang->line("request_being_processed");
                            } else {
                                $data["plan_key"] = $subscription["planKey"];
                                $data["subscription_items"] = $subscription["addons"]["clientPlan"][$data["plan_key"]]["year"];
                                $maxActiveUsers = $this->licensor->get("maxActiveUsers");
                                $this->load->model("user", "userfactory");
                                $this->user = $this->userfactory->get_instance();
                                $data["maxActiveUsers"] = $maxActiveUsers - $this->user->get_super_admin_excempted_users();
                                $response["html"] = $this->load->view("subscription/purchase_additional_user_form", $data, true);
                            }
                        } else {
                            $response["error"] = $this->lang->line("invalid_license");
                        }
                    } else {
                        $response["error"] = $this->lang->line("invalid_license");
                    }
                } else {
                    $client_data = $this->a4l_cc->load_client_data();
                    $client_data = json_decode($client_data, true);
                    $config = parse_ini_file(INSTANCE_PATH . "../config.ini");
                    require "application/libraries/sendgrid/vendor/autoload.php";
                    $email = new SendGrid\Mail\Mail();
                    $email->setFrom("info@sheria360.com");
                    $email->setSubject("Request for an additional user - Instance ID " . $instance_data["instanceID"] . " - " . $client_data["success"]["data"]["name"]);
                    $email->addTo($cloud_params["email_support_team"]);
                    $email->addCc($config["email_accounting_team"]);
                    $email->addContent("text/html", sprintf($this->lang->line("additional_user_email_content"), $instance_data["instanceID"], $this->session->userdata("AUTH_userProfileName"), $this->is_auth->get_email_address()));
                    $sendgrid = new SendGrid($config["sg.api-key"]);
                    $sendgrid->send($email);
                    $response["request_being_processed"] = $this->lang->line("request_being_processed");
                }
            } else {
                $response["error"] = $this->lang->line("subscription_access_empty_instance_data");
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $this->is_auth->deny_access();
        }
    }
    public function user_add_response()
    {
        if (!$this->is_auth->is_logged_in()) {
            $this->is_auth->deny_access();
        }
        if ($this->input->get("success")) {
            $this->set_flashmessage("success", $this->lang->line("subscription_add_user_msg"));
            redirect("users/add");
        } else {
            $this->set_flashmessage("error", $this->lang->line("subscription_failed_to_add_user"));
            redirect("users");
        }
    }
}

?>