<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require APPPATH.'controllers/Top_controller.php';

class Hearing_external_ref_merge extends Top_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function create_log_file($level, $msg, $php_error = false)
    {
        $filepath = 'files/logs/hearing_external_reference_merge-'.date('Y-m-d').'.php';
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
        $log_message = $this->session->userdata('AUTH_email_address')."\r\nMigration Logs:    \n";
        $this->create_log_file('Migation of Legal Case Hearing', $log_message);
        $flag_result = 1;
        $records = '';
        $this->load->model('legal_case_litigation_external_reference');
        $this->load->model('legal_case_hearing', 'legal_case_hearingfactory');
        $this->legal_case_hearing = $this->legal_case_hearingfactory->get_instance();
        $sql = 'select id,legal_case_id,startDate,reference from legal_case_hearings where reference is not NULL';
        $data = $this->db->query($sql);
        $result = array();
        $result1 = array();
        $log_message = "Start migrating the legal case hearing external reference Data...\n\n";
        $this->create_log_file('', $log_message);
        $log_message = '';
        foreach ($data->result() as $value) {
            if ($value->reference != '') {
                $this->legal_case_litigation_external_reference->reset_fields();
                $this->legal_case_hearing->reset_fields();
                $flag = $this->legal_case_litigation_external_reference->fetch(array('number' => $value->reference, 'legal_case_id' => $value->legal_case_id));
                if (!$flag) {
                    $this->legal_case_litigation_external_reference->set_field('legal_case_id', $value->legal_case_id);
                    $this->legal_case_litigation_external_reference->set_field('number', $value->reference);
                    $this->legal_case_litigation_external_reference->set_field('refDate', $value->startDate);
                    $result1['status'][] = $this->legal_case_litigation_external_reference->insert() ? 202 : 102;
                    $result1['validationErrors'][] = $this->legal_case_litigation_external_reference->get('validationErrors');
                    $id = $this->legal_case_litigation_external_reference->get_field('id');
                    $result1['Legal_Case_Hearing_ext_ref'][] = $value->id;
                    if ($id != null) {
                        $this->legal_case_hearing->fetch(array('id' => $value->id));
                        $this->legal_case_hearing->set_field('reference', $id * 1);
                        $result['status'][] = $this->legal_case_hearing->update() ? 202 : 102;
                        $result['validationErrors'][] = $this->legal_case_hearing->get('validationErrors');
                        $result['Legal_Case_Hearing'][] = $value->id;
                    }
                } else {
                    $id = $this->legal_case_litigation_external_reference->get_field('id');
                    $this->legal_case_hearing->fetch(array('id' => $value->id));
                    $this->legal_case_hearing->set_field('reference', $id * 1);
                    $result['status'][] = $this->legal_case_hearing->update() ? 202 : 102;
                    $result['validationErrors'][] = $this->legal_case_hearing->get('validationErrors');
                    $result['Legal_Case_Hearing'][] = $value->id;
                }
            } else {
                $this->legal_case_hearing->reset_fields();
                $this->legal_case_hearing->fetch(array('id' => $value->id));
                $this->legal_case_hearing->set_field('reference', null);
                $result['status'][] = $this->legal_case_hearing->update() ? 202 : 102;
                $result['validationErrors'][] = $this->legal_case_hearing->get('validationErrors');
                $result['Legal_Case_Hearing'][] = $value->id;
            }
        }
        if (!empty($result)) {
            $count1 = 0;
            $count2 = 0;
            $message = '';
            foreach ($result['status'] as $key => $value) {
                if ($value == 202) {
                    ++$count1;
                } elseif ($value == 102) {
                    ++$count2;
                    $message = 'Legal Case Hearing of id = '.$result['Legal_Case_Hearing'][$key];
                    foreach ($result['validationErrors'][$key] as $key1 => $value1) {
                        $message .= "\n Details: \n".$key1.': '.$value1."\n";
                    }
                    $log_message .= "\n Failed to update: ".$message."\n";
                }
            }
            if ($count1 > 0) {
                $records .= $count1." record(s) of legal case hearing saved successfully \n";
            }
            if ($count2 > 0) {
                $records .= $count2." record(s) of legal case hearing not saved.\n";
            }
        } else {
            $records .= "There is no of legal case hearing records to migrate the Hearing Cases.\n";
        }
        $this->create_log_file('', $records);
        $this->create_log_file('', $log_message);
        if (!empty($result1)) {
            $count1 = 0;
            $count2 = 0;
            $message = '';
            foreach ($result1['status'] as $key => $value) {
                if ($value == 202) {
                    ++$count1;
                } elseif ($value == 102) {
                    ++$count2;
                    $message = 'legal case external reference related to Legal Case Hearing of id = '.$result1['Legal_Case_Hearing_ext_ref'][$key];
                    foreach ($result1['validationErrors'][$key] as $key1 => $value1) {
                        $message .= "\n Details: \n".$key1.': '.$value1."\n";
                    }
                    $log_message .= "\n Failed to update: ".$message."\n";
                }
            }
        }
        $this->create_log_file('', $log_message);
        $log_message = '';
        switch ($this->db->dbdriver) {
            case 'mysqli':
                $query = "SELECT *
                            FROM information_schema.REFERENTIAL_CONSTRAINTS
                            WHERE CONSTRAINT_NAME =  'legal_case_hearings_ibfk_8'
                            AND CONSTRAINT_SCHEMA = '" .$this->db->database."'";
                $data = $this->db->query($query);
                if (sizeof($data->result()) == 0) {
                    $query = 'ALTER TABLE legal_case_hearings MODIFY reference int(10) unsigned';
                    $result = $this->db->query($query);
                    if ($result) {
                        $log_message .= "\r\n Reference Type Changed succussfully..      Successful Transaction\r\n ";
                    } else {
                        $log_message .= "\r\n failed to Reference Type      Transaction passed Error \r\n";
                        $flag_result = 0;
                    }
                    $query = 'ALTER TABLE `legal_case_hearings`
                            ADD CONSTRAINT `legal_case_hearings_ibfk_8` FOREIGN KEY ( `reference` ) REFERENCES `legal_case_litigation_external_references` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION';
                    $result = $this->db->query($query);
                    if ($result) {
                        $log_message .= "legal_case_hearings CONSTRAINT 'legal_case_hearings_ibfk_8' created succussfully..      Successful Transaction\r\n ";
                    } else {
                        $log_message .= "failed to create legal_case_hearings CONSTRAINT 'legal_case_hearings_ibfk_8'       Transaction passed Error \r\n";
                        $flag_result = 0;
                    }
                    $query = "CREATE OR REPLACE ALGORITHM=TEMPTABLE SQL SECURITY DEFINER VIEW `legal_case_hearings_full_details` AS SELECT `legal_case_hearings`.`id`, `legal_case_hearings`.`legal_case_id`,`legal_cases`.`subject` AS `caseSubject`, `legal_case_hearings`.`task_id`,`legal_case_hearings`.`subject`, `legal_case_hearings`.`court_type_id`, `legal_case_hearings`.`court_degree_id`, `legal_case_hearings`.`court_region_id`, `legal_case_hearings`.`court_id`, `legal_case_hearings`.`startDate`, `legal_case_hearings`.`startTime`, `legal_case_hearings`.`postponedDate`, `legal_case_hearings`.`postponedTime`, `legal_case_hearings`.`summary`, `legal_case_hearings`.`comments`,`lcler`.`number` as `reference`, `legal_case_hearings`.`hearing_client_position_id`, CONCAT('C', legal_case_hearings.legal_case_id) as caseID, GROUP_CONCAT( DISTINCT opponents_view.name SEPARATOR ';' ) AS opponents, GROUP_CONCAT( DISTINCT clients_view.name SEPARATOR ';' ) AS clients,GROUP_CONCAT( DISTINCT CONCAT( contJud.firstName, ' ', contJud.lastName ) SEPARATOR ';' ) AS judges,GROUP_CONCAT( DISTINCT CONCAT( contoppLaw.firstName, ' ', contoppLaw.lastName ) SEPARATOR ';' ) AS opponentLawyers,GROUP_CONCAT( DISTINCT CONCAT( contExtLawyer.firstName, ' ', contExtLawyer.lastName ) SEPARATOR ';' ) AS externalLawyers, GROUP_CONCAT( DISTINCT CONCAT( userLaw.firstName, ' ', userLaw.lastName ,IF( userLaw.status='Inactive', ' (Inactive)', '') ) SEPARATOR ';' ) AS lawyers, court_types.name AS courtType, court_degrees.name AS courtDegree, court_regions.name AS courtRegion, courts.name AS court, `lccplen`.`name` AS `clientPosition_en`, `lccplfr`.`name` AS `clientPosition_fr`, `lccplar`.`name` AS `clientPosition_ar` FROM (`legal_case_hearings`) LEFT JOIN `legal_cases` ON `legal_cases`.`id` = `legal_case_hearings`.`legal_case_id` LEFT JOIN `legal_case_hearings_clients` lchcl ON `lchcl`.`legal_case_hearing_id` = `legal_case_hearings`.`id` LEFT JOIN `clients_view` ON `clients_view`.`id` = `lchcl`.`client_id` AND `clients_view`.`model` = 'clients' LEFT JOIN `legal_case_hearings_opponents` lcho ON `lcho`.`legal_case_hearing_id` = `legal_case_hearings`.`id` LEFT JOIN `opponents_view` ON `opponents_view`.`id` = `lcho`.`opponent_id` LEFT JOIN `legal_case_hearings_contacts` lchcj ON `lchcj`.`legal_case_hearing_id` = `legal_case_hearings`.`id` AND lchcj.contactType = 'judge' LEFT JOIN `contacts` contJud ON `contJud`.`id` = `lchcj`.`contact_id` LEFT JOIN `legal_case_hearings_contacts` lchcol ON `lchcol`.`legal_case_hearing_id` = `legal_case_hearings`.`id` AND lchcol.contactType = 'opponentLawyer' LEFT JOIN `contacts` contoppLaw ON `contoppLaw`.`id` = `lchcol`.`contact_id`LEFT JOIN `legal_case_hearings_contacts` lchcel ON `lchcel`.`legal_case_hearing_id` = `legal_case_hearings`.`id` AND lchcel.contactType = 'externalLawyer' LEFT JOIN `contacts` contExtLawyer ON `contExtLawyer`.`id` = `lchcel`.`contact_id` LEFT JOIN `legal_case_hearings_users` ON `legal_case_hearings_users`.`legal_case_hearing_id` = `legal_case_hearings`.`id` LEFT JOIN `user_profiles` userLaw ON `userLaw`.`user_id` = `legal_case_hearings_users`.`user_id` LEFT JOIN `courts` ON `courts`.`id` = `legal_case_hearings`.`court_id` LEFT JOIN `court_types` ON `court_types`.`id` = `legal_case_hearings`.`court_type_id` LEFT JOIN `court_degrees` ON `court_degrees`.`id` = `legal_case_hearings`.`court_degree_id` LEFT JOIN `court_regions` ON `court_regions`.`id` = `legal_case_hearings`.`court_region_id` LEFT JOIN `legal_case_client_position_languages` `lccplen` ON `lccplen`.`legal_case_client_position_id` = `legal_case_hearings`.`hearing_client_position_id` AND `lccplen`.`language_id` = '1' LEFT JOIN `legal_case_client_position_languages` `lccplar` ON `lccplar`.`legal_case_client_position_id` = `legal_case_hearings`.`hearing_client_position_id` AND `lccplar`.`language_id` = '2' LEFT JOIN `legal_case_client_position_languages` `lccplfr` ON `lccplfr`.`legal_case_client_position_id` = `legal_case_hearings`.`hearing_client_position_id` AND `lccplfr`.`language_id` = '3'  LEFT JOIN legal_case_litigation_external_references lcler on lcler.id=legal_case_hearings.reference GROUP BY `legal_case_hearings`.`id`";
                    $result = $this->db->query($query);
                    if ($result) {
                        $log_message .= "legal_case_hearings_full_details Updated succussfully..      Successful Transaction\r\n ";
                    } else {
                        $log_message .= "failed to Update legal_case_hearings_full_details       Transaction passed Error\r\n";
                        $flag_result = 0;
                    }
                } else {
                    $this->set_flashmessage('warning', 'script already executed ');
                    redirect('home');
                }
                break;
            case 'sqlsrv':
                $query = "SELECT *
                        FROM sys.foreign_keys
                        WHERE object_id = OBJECT_ID(N'[dbo].[legal_case_hearings_ibfk_8]')
                        AND parent_object_id = OBJECT_ID(N'[dbo].[legal_case_hearings]')";
                $data = $this->db->query($query);
                if (sizeof($data->result()) == 0) {
                    $query = "declare @table_name nvarchar(256)
                            declare @col_name nvarchar(256)
                            declare @Command  nvarchar(1000)

                            set @table_name = 'legal_case_hearings'
                            set @col_name = 'reference'

                            select @Command = 'ALTER TABLE ' + @table_name + ' drop constraint ' + d.name
                             from sys.tables t
                              join    sys.default_constraints d
                               on d.parent_object_id = t.object_id
                              join    sys.columns c
                               on c.object_id = t.object_id
                                and c.column_id = d.parent_column_id
                             where t.name = @table_name
                              and c.name = @col_name
                            execute (@Command)";
                    $result = $this->db->query($query);
                    if ($result) {
                        $log_message .= "\r\n Constraints deleted succussfully..      Successful Transaction\r\n ";
                    } else {
                        $log_message .= "\r\n Failed to delete Constraints      Transaction passed Error\r\n";
                        $flag_result = 0;
                    }

                    $query = 'ALTER TABLE legal_case_hearings alter COLUMN reference BIGINT;';
                    $result = $this->db->query($query);
                    if ($result) {
                        $log_message .= "Reference Type Changed succussfully..      Successful Transaction\r\n ";
                    } else {
                        $log_message .= "failed to Reference Type      Transaction passed Error\r\n";
                        $flag_result = 0;
                    }
                    $query = ' ALTER TABLE legal_case_hearings
                  ADD CONSTRAINT legal_case_hearings_ibfk_8 FOREIGN KEY ( reference ) REFERENCES legal_case_litigation_external_references (id) ON DELETE NO ACTION ON UPDATE NO ACTION;';
                    $result = $this->db->query($query);
                    if ($result) {
                        $log_message .= "legal_case_hearings CONSTRAINT 'legal_case_hearings_ibfk_8' created succussfully..      Successful Transaction\r\n ";
                    } else {
                        $log_message .= "failed to create legal_case_hearings CONSTRAINT 'legal_case_hearings_ibfk_8'     Transaction passed Error\r\n";
                        $flag_result = 0;
                    }
                    $query = "IF OBJECT_ID('dbo.legal_case_hearings_full_details', 'V') IS NOT NULL DROP VIEW dbo.legal_case_hearings_full_details;";
                    $result = $this->db->query($query);
                    if ($result) {
                        $log_message .= "legal_case_hearings_full_details deleted succussfully..      Successful Transaction\r\n ";
                    } else {
                        $log_message .= "failed to delete legal_case_hearings_full_details      Transaction passed Error\r\n";
                        $flag_result = 0;
                    }
                    $query = "CREATE VIEW legal_case_hearings_full_details AS SELECT
                        TOP(9223372036854775800)
                        legal_case_hearings.id,legal_case_hearings.subject,legal_case_hearings.legal_case_id, legal_cases.subject as caseSubject, legal_case_hearings.task_id,
                        legal_case_hearings.court_type_id, legal_case_hearings.court_degree_id, legal_case_hearings.court_region_id, legal_case_hearings.court_id,
                        legal_case_hearings.startDate, SUBSTRING(CAST(legal_case_hearings.startTime AS nvarchar), 1, 5) AS startTime, legal_case_hearings.postponedDate, SUBSTRING(CAST(legal_case_hearings.postponedTime AS nvarchar), 1, 5) AS postponedTime,
                        legal_case_hearings.summary, legal_case_hearings.comments, lcler.number as reference, ('C'+CAST( legal_case_hearings.legal_case_id AS nvarchar )) as caseID,
                        opponents = STUFF((SELECT ' ; ' + opponents_view.name FROM opponents_view INNER JOIN legal_case_hearings_opponents lcho ON lcho.legal_case_hearing_id = legal_case_hearings.id AND opponents_view.id = lcho.opponent_id FOR XML PATH('')), 1, 3, ''),
                        clients = STUFF((SELECT ' ; ' + clients_view.name FROM clients_view INNER JOIN legal_case_hearings_clients lchcl ON lchcl.legal_case_hearing_id = legal_case_hearings.id AND clients_view.id = lchcl.client_id AND clients_view.model = 'clients' FOR XML PATH('')), 1, 3, ''),
                        judges = STUFF((SELECT ' ; ' + ( contJud.firstName + ' ' + contJud.lastName ) FROM contacts AS contJud INNER JOIN legal_case_hearings_contacts lchcj ON lchcj.legal_case_hearing_id = legal_case_hearings.id AND lchcj.contactType = 'judge' AND contJud.id = lchcj.contact_id FOR XML PATH('')), 1, 3, ''),
                        opponentLawyers = STUFF((SELECT ' ; ' + ( contOppLaw.firstName + ' ' + contOppLaw.lastName ) FROM contacts AS contOppLaw INNER JOIN legal_case_hearings_contacts lchcol ON lchcol.legal_case_hearing_id = legal_case_hearings.id AND lchcol.contactType = 'opponentLawyer' AND contOppLaw.id = lchcol.contact_id FOR XML PATH('')), 1, 3, ''),        externalLawyers = STUFF((SELECT ' ; ' + ( contExtLawyer.firstName + ' ' + contExtLawyer.lastName ) FROM contacts AS contExtLawyer INNER JOIN legal_case_hearings_contacts lchcel ON lchcel.legal_case_hearing_id = legal_case_hearings.id AND lchcel.contactType = 'externalLawyer' AND contExtLawyer.id = lchcel.contact_id FOR XML PATH('')), 1, 3, ''),
                        lawyers = STUFF((SELECT ' ; ' + ( userLaw.firstName + ' ' + userLaw.lastName+ CASE WHEN userLaw.status = 'Inactive' THEN ' (Inactive)' ELSE '' END ) FROM user_profiles AS userLaw INNER JOIN legal_case_hearings_users ON legal_case_hearings_users.legal_case_hearing_id = legal_case_hearings.id AND userLaw.user_id = legal_case_hearings_users.user_id FOR XML PATH('')), 1, 3, ''),
                        court_types.name AS courtType, court_degrees.name AS courtDegree, court_regions.name AS courtRegion, courts.name AS court,
                        legal_case_hearings.hearing_client_position_id AS hearing_client_position_id, lccplen.name AS clientPosition_en, lccplfr.name AS clientPosition_fr, lccplar.name AS clientPosition_ar
                FROM legal_case_hearings
                LEFT JOIN legal_cases ON legal_cases.id = legal_case_hearings.legal_case_id
                LEFT JOIN courts ON courts.id = legal_case_hearings.court_id
                LEFT JOIN court_types ON court_types.id = legal_case_hearings.court_type_id
                LEFT JOIN court_degrees ON court_degrees.id = legal_case_hearings.court_degree_id
                LEFT JOIN court_regions ON court_regions.id = legal_case_hearings.court_region_id
                LEFT JOIN legal_case_client_position_languages lccplen ON lccplen.legal_case_client_position_id = legal_case_hearings.hearing_client_position_id AND lccplen.language_id = '1'
                LEFT JOIN legal_case_client_position_languages lccplar ON lccplar.legal_case_client_position_id = legal_case_hearings.hearing_client_position_id AND lccplar.language_id = '2'
                LEFT JOIN legal_case_client_position_languages lccplfr ON lccplfr.legal_case_client_position_id = legal_case_hearings.hearing_client_position_id AND lccplfr.language_id = '3'
                LEFT JOIN legal_case_litigation_external_references lcler on lcler.id=legal_case_hearings.reference
                ";
                    $result = $this->db->query($query);
                    if ($result) {
                        $log_message .= "legal_case_hearings_full_details Updated succussfully..      Successful Transaction\r\n ";
                    } else {
                        $log_message .= "failed to Update legal_case_hearings_full_details      Transaction passed Error\r\n";
                        $flag_result = 0;
                    }
                } else {
                    $this->set_flashmessage('warning', 'script already executed ');
                    redirect('home');
                }
                break;
        }

        $this->create_log_file('', $log_message);
        if ($flag_result == 1) {
            $this->set_flashmessage('success', $records);
            redirect('home');
        } else {
            $this->set_flashmessage('error', 'Error accured when running the script');
            redirect('home');
        }
    }
}
