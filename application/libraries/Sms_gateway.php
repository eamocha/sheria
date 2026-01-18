<?php

class Sms_gateway
{
    public $ci = null;
    public $system_preferences = null;

    // Define constants for SMS gateway configuration keys as per the system_preferences table
    const SMS_GATEWAY_USERNAME_KEY = 'smsUsername';
    const SMS_GATEWAY_PASSWORD_KEY = 'smsPassword';
    const SMS_GATEWAY_URL_KEY = 'smsUrl';
    const SMS_FEATURE_ENABLED_KEY = 'smsFeatureEnabled';
    const SMS_AUTH_TYPE_KEY = 'smsAuthType';    // Value '2' for Basic Authentication
    const SMS_AUTH_TOKEN_KEY = 'smsAuthToken';  // Not used for Basic Auth, but noted for context
    protected $_sms_logging_enabled = true;
    protected $_sms_logging_threshold = "4";
    protected $_sms_logging_levels = ["LOG" => "0", "ERROR" => "1", "DEBUG" => "2", "INFO" => "3", "ALL" => "4"];
    protected $_sms_logging_log_path;
    protected $_sms_logging_date_fmt = "Y-m-d H:i:s";

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->model('system_preference');
        $this->system_preferences = $this->ci->system_preference->get_key_groups();

        // Load necessary helpers for SMS (e.g., URL helper if needed, string helper for length)
        $this->ci->load->helper('url');
        $this->ci->load->helper('string'); // For potential string manipulation or checks
    }

    /**
     * Sends an SMS notification.
     *
     * @param array $smsData Contains 'to' (recipient), 'message', and optional 'from' (sender ID).
     * @return bool True if SMS was sent successfully, false otherwise.
     */
    public function notify($smsData)
    {
        // Check if SMS feature is enabled in system preferences
        $sms_feature_enabled = $this->system_preferences['SMSGateway'][self::SMS_FEATURE_ENABLED_KEY] ?? 0;
        if (!$sms_feature_enabled) {
            $this->write_log("SMS_gateway: SMS feature is disabled in system preferences.", "", "INFO");
            return false;
        }

        extract($smsData);

        // Ensure 'to' is an array for consistency, even if only one recipient
        $to = is_array($to) ? $to : array($to);

        $sms_sent = true; // Assume success, set to false on failure for any individual send

        // Get SMS gateway credentials from system preferences (DB)
        $sms_username = $this->system_preferences['SMSGateway'][self::SMS_GATEWAY_USERNAME_KEY] ?? null;
        $sms_password = $this->system_preferences['SMSGateway'][self::SMS_GATEWAY_PASSWORD_KEY] ?? null;
        $sms_url = $this->system_preferences['SMSGateway'][self::SMS_GATEWAY_URL_KEY] ?? null;
        $sms_auth_type = $this->system_preferences['SMSGateway'][self::SMS_AUTH_TYPE_KEY] ?? null;
        // $sms_auth_token = $this->system_preferences['SMSGateway'][self::SMS_AUTH_TOKEN_KEY] ?? null; // Not directly used for Basic Auth

        // Basic validation for gateway details
        if (empty($sms_username) || empty($sms_password) || empty($sms_url) || $sms_auth_type != 2) {
            $this->write_log("SMS_gateway: Missing or invalid SMS gateway configuration in system preferences.", "", "ERROR");
            return false;
        }

        foreach ($to as $recipient_number) {
            // Validate Kenyan phone number before sending
            if (!$this->is_valid_kenyan_phone_number($recipient_number)) {
                $this->write_log("SMS_gateway: Invalid Kenyan phone number provided:", $recipient_number, "ERROR");
                $sms_sent = false; // Mark overall send as failed, but continue to try others if in a loop
                continue;
            }

            // Ensure message content adheres to character limits (e.g., 160 for GSM, 70 for Unicode)
            $processed_message = $this->process_sms_content($message);

            $params = array(
                'txtMobile' => $recipient_number,
                'txtMsg' => $processed_message
            );

            // Using cURL to send the SMS
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $sms_url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_USERPWD, "$sms_username:$sms_password");
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params)); // Use http_build_query for POST fields
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // CAUTION: For production, set to true and ensure proper CA certs.

            $curl_response = curl_exec($curl);
            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($curl);
            curl_close($curl);

            if ($curl_response === false) {
                $this->write_log("SMS_gateway: cURL error for recipient " . $recipient_number . ":", $curl_error, "ERROR");
                $sms_sent = false;
                continue;
            }

            $temp = @json_decode($curl_response, true);

            // Check gateway's specific response for success
            // The sample PHP code checks for @$temp["status"]=="Ok"
            if (isset($temp["status"]) && $temp["status"] === "Ok") {
                $this->write_log("SMS_gateway: SMS sent successfully to " . $recipient_number . ". Gateway response:", $curl_response, "INFO");
            } else {
                $this->write_log("SMS_gateway: Failed to send SMS to " . $recipient_number . ". HTTP Status: " . $http_status . ". Gateway Response:", $curl_response, "ERROR");
                $sms_sent = false;
            }
        }

        return $sms_sent;
    }

    /**
     * Validates a Kenyan phone number.
     * Supports formats: 07XXXXXXXX, 2547XXXXXXXX, +2547XXXXXXXX
     *
     * @param string $phone_number The phone number to validate.
     * @return bool True if valid, false otherwise.
     */
    private function is_valid_kenyan_phone_number($phone_number)
    {
        // Remove any non-digit characters except leading plus sign
        $phone_number = preg_replace('/[^\d+]/', '', $phone_number);

        // Standardize to 2547XXXXXXXX format for validation
        // --- FIX: Replaced str_starts_with() for PHP < 8.0 compatibility ---
        if (substr($phone_number, 0, 2) === '07') { // This was likely Line Number: 128
            $phone_number = '254' . substr($phone_number, 1); // 07XXXXXXXX -> 2547XXXXXXXX
        } elseif (substr($phone_number, 0, 5) === '+2547') {
            $phone_number = substr($phone_number, 1); // +2547XXXXXXXX -> 2547XXXXXXXX
        } elseif (substr($phone_number, 0, 4) !== '2547') {
            return false; // Does not start with a recognized Kenyan mobile prefix
        }
        // --- END FIX ---

        // Validate against the pattern: starts with 2547 and followed by 8 digits
        // Total length should be 12 digits (254 + 9 digits for 7XXXXXXXX)
        return (bool) preg_match('/^2547\d{8}$/', $phone_number);
    }

    /**
     * Processes SMS content for character limitations.
     * This is a basic example; a real implementation might split messages or transliterate.
     *
     * @param string $message The original SMS message.
     * @return string The processed message.
     */
    private function process_sms_content($message)
    {
        // Basic GSM-7 character set has 160 character limit per SMS.
        // If special characters are used, it becomes UCS-2/Unicode, limit 70 characters per SMS.
        // For simplicity, we'll assume a conservative limit or strip non-GSM characters.
        // This gateway expects plain text.
        $max_gsm_chars = 160;
        // $max_unicode_chars = 70; // If message contains non-GSM characters

        // For this example, we'll simply ensure it's a string and trim it if it's too long
        // A more advanced solution would check for Unicode characters and adjust the limit.
        // For now, let's just assume GSM-7 and truncate if over.
        $encoded_message = utf8_decode($message); // Attempt to convert to something closer to GSM-7 compatible
        // This can lead to loss of specific Unicode characters.
        // Better: check actual GSM-7 encoding or count characters.
        if (strlen($encoded_message) > $max_gsm_chars) {
            $this->write_log("SMS_gateway: SMS message truncated due to character limit.", "", "WARNING");
            return substr($encoded_message, 0, $max_gsm_chars);
        }

        return $encoded_message;
    }


    public function write_log($level = "error", $msg, $php_error = false)
    {
        if ($this->_sms_logging_enabled === false) {
            return false;
        }
        $level = strtoupper($level);
        if (!isset($this->_sms_logging_levels[$level]) || $this->_sms_logging_threshold < $this->_sms_logging_levels[$level]) {
            return false;
        }
        $filepath = $this->_sms_logging_log_path . "log-" . date("Y-m-d") . ".log";
        if (MODULE !== "core") {
            $filepath = "../../" . $filepath;
        }
        $message = "";
        if (!($fp = @fopen($filepath, FOPEN_WRITE_CREATE))) {
            return false;
        }
        $systemPreferences = $this->ci->system_preference->get_values();
        $timezone = isset($systemPreferences["systemTimezone"]) && $systemPreferences["systemTimezone"] ? $systemPreferences["systemTimezone"] : $this->ci->config->item("default_timezone");
        date_default_timezone_set($timezone);
        $message .= $level . "\t" . ($level == "INFO" ? "\t-" : "-") . "\t" . date($this->_sms_logging_date_fmt) . "\t\t" . $msg . "\n";
        flock($fp, LOCK_EX);
        fwrite($fp, $message);
        flock($fp, LOCK_UN);
        fclose($fp);
        @chmod($filepath, FILE_WRITE_MODE);
        return true;
    }

}