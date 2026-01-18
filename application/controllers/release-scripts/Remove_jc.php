<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require APPPATH.'controllers/Top_controller.php';

class Remove_jc extends TOP_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function create_log_file($level, $msg, $php_error = false)
    {
        $filepath = 'files/logs/migration-jurisicContacts-'.date('Y-m-d').'.php';
        if (MODULE !== 'core') {
            $filepath = '../../../'.$filepath;
        }
        $message = '';

        if (!file_exists($filepath)) {
            $message .= '<'."?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?".">\n\n";
        }

        if (!$fp = @fopen($filepath, FOPEN_WRITE_CREATE)) {
            return false;
        }
        $this->load->model('system_preference');
        $systemPreferences = $this->system_preference->get_values();
        $timezone = (isset($systemPreferences['systemTimezone']) && $systemPreferences['systemTimezone']) ? $systemPreferences['systemTimezone'] : 'Europe/London';
        date_default_timezone_set($timezone);
        if ($level != '') {
            $message .= $level.'	'.(($level == 'INFO') ? '	-' : '-').'	'.date('Y-m-d H:i:s').'		';
        }
        $message .= $msg."\n";

        flock($fp, LOCK_EX);
        fwrite($fp, $message);
        flock($fp, LOCK_UN);
        fclose($fp);

        @chmod($filepath, FILE_WRITE_MODE);

        return true;
    }

    public function index()
    {
        $login_log_message = "\r\nMigration Logs:    ";
        $this->load->model('company', 'Companyfactory');
        $this->company = $this->companyfactory->get_instance();
        $sql = 'select id, companyID, legalName, name, shortName, foreignName, status, category, private, company_id, nationality_id, company_legal_type_id, object, address1, address2, city, state, zip, country_id, website, phone, fax, mobile, capital, capitalCurrency, nominalShares, bearerShares, shareParValue, shareParValueCurrency, qualifyingShares, registrationNb, registrationDate, registrationCity, registrationTaxNb, registrationYearsNb, registrationByLawNotaryPublic, registrationByLawRef, registrationByLawDate, registrationByLawCity, comments, sharesLocation, ownedByGroup, sheerLebanese, contributionRatio, notes, otherNotes, createdOn, createdBy, modifiedOn, modifiedBy, legalType, nationality, majorParent, lawyer, registrationAuthorityName, internalReference, email, crNumber, crReleasedOn, crExpiresOn from companies_full_details where category=\'External\'';
        $data = $this->db->query($sql);
        $result = array();
        $records = '';
        $flag_result = 1;
        print_r('Start migrating the juristic contacts to companies...<br><br>');
        $this->create_log_file('Migation of Juristic Contacts', $login_log_message);
        foreach ($data->result() as $value) {
            $flag = $this->company->fetch(array('shortName' => mb_substr($value->name, 0, 15)));
            $this->company->fetch($value->id);
            if ($flag) {
                $value->shortName = mb_substr($value->name, 0, 11).mt_rand(1000, 9000);
                $this->company->set_field('shortName', $value->shortName);
            } else {
                $value->shortName = mb_substr($value->name, 0, 15);
                $this->company->set_field('shortName', $value->shortName);
            }
            $this->company->set_field('category', 'Internal');
            $status_update = $this->company->update() ? 202 : 102;
            $validations = $this->company->get('validationErrors');
            $result['status'][] = $status_update;
            $message = '';
            if ($status_update == 202) {
                $login_log_message = "\n".$value->name.'('.$value->id.')'." Updated successfully. \n";
            } elseif ($status_update == 102) {
                $message = $value->name.'('.$value->id.')'." : \n";
                foreach ($validations as $key1 => $value1) {
                    $message .= $key1.': '.$value1."\n";
                }
                $login_log_message = "\n Failed to update: ".$message."\n";
            }
            $this->create_log_file('', $login_log_message);
            $result['validationErrors'][] = $validations;
            $result['companies'][] = $value->name.'('.$value->id.')';
        }
        if (!empty($result)) {
            $count1 = 0;
            $count2 = 0;
            foreach ($result['status'] as $key => $value) {
                if ($value == 202) {
                    ++$count1;
                } elseif ($value == 102) {
                    ++$count2;
                }
            }
            if ($count1 > 0) {
                $records = $count1.' record(s) updates successfully <br>';
            }
            if ($count2 > 0) {
                $records = $count2.' record(s) not saved.';
            }
        } else {
            $records = 'There is no records to migrate the Juristic contacts.<br>';
        }
        $login_log_message = '';
        switch ($this->db->dbdriver) {
            case 'mysqli':
                $query = "ALTER TABLE  `companies` CHANGE category `category` ENUM(  'Internal',  'Audit',  'Group' ) NOT NULL;";
                $result = $this->db->query($query);
                if ($result) {
                    $login_log_message .= "\r\n New Companies CONSTRAINT  'CK_companies_category' added succussfully..\r\n  ";
                } else {
                    $login_log_message .= "\r\n failed add New Companies CONSTRAINT  'CK_companies_category' \r\n ";
                    $flag_result = 0;
                }
                break;
            case 'sqlsrv':
                $query = "
                        DECLARE @defaultname VARCHAR(255)
                        DECLARE @executesql VARCHAR(1000)

                        SELECT @defaultname = dc.name
                                FROM sys.check_constraints dc
                                    INNER JOIN sys.columns sc
                                    ON dc.parent_object_id = sc.object_id
                                    AND dc.parent_column_id = sc.column_id
                                    WHERE OBJECT_NAME (parent_object_id) = 'companies'
                                    AND sc.name ='category'
                        SET @executesql =  'ALTER TABLE companies  DROP CONSTRAINT ' + @defaultname
                        EXEC(@executesql)";
                $result = $this->db->query($query);
                if ($result) {
                    $login_log_message .= "\r\nConstraints deleted succussfully..\r\n ";
                } else {
                    $login_log_message .= "\r\nFailed to delete Constraints\r\n";
                    $flag_result = 0;
                }
                $query = "ALTER TABLE companies ADD CONSTRAINT  CK_companies_category  CHECK(category IN ('Internal','Audit','Group')) ;";
                $result = $this->db->query($query);
                if ($result) {
                    $login_log_message .= "New Companies CONSTRAINT 'CK_companies_category' added succussfully..\r\n ";
                } else {
                    $login_log_message .= "failed add New Companies CONSTRAINT  'CK_companies_category' \r\n";
                    $flag_result = 0;
                }
                break;
        }
        $this->create_log_file('', $login_log_message);
        if ($flag_result == 1) {
            $this->set_flashmessage('success', $records);
            redirect('home');
        } else {
            $this->set_flashmessage('error', 'Error accured when running the script');
            redirect('home');
        }
    }
}
