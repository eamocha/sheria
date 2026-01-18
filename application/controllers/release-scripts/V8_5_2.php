<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V8_5_2 extends CI_Controller
{
    use MigrationLogTrait;

    public $log_path = null;

    public function __construct()
    {
        parent::__construct();
        
        $this->hooks->enabled = false;
        $this->log_path = 'release-scripts' . DIRECTORY_SEPARATOR . get_class($this);
        $this->load->database();
        
        $this->write_log($this->log_path, 'start migration script');
    }

    public function index()
    {
        $this->rename_advisor_permissions_table();
        $this->grant_users_to_access_new_permissions();
        $this->update_legal_case_litigation_stages_full_details_view();
        $this->add_actions_to_email_notifications_scheme();

        $this->write_log($this->log_path, 'End migration script');
    }

    public function update_legal_case_litigation_stages_full_details_view()
    {
        $this->write_log($this->log_path, 'start update legal case litigation stages full details view.');

        if ($this->db->dbdriver === 'sqlsrv') {
            $this->db->query("IF OBJECT_ID('dbo.legal_case_litigation_stages_full_details', 'V') IS NOT NULL DROP VIEW dbo.legal_case_litigation_stages_full_details");
            $this->db->query("CREATE VIEW legal_case_litigation_stages_full_details AS SELECT
                                TOP (9223372036854775800)
                                stages.id as id,stages.legal_case_id,stages.sentenceDate,stages.comments,stages.legal_case_stage,stages.client_position,stages.status,stages.modifiedBy, stages.modifiedOn,
                                court_types.name as court_type,court_degrees.name as court_degree,court_regions.name as court_region, courts.name as court,clients_view.name as client_name, stages.createdOn, stages.createdBy,
                                ( UP.firstName + ' ' + UP.lastName ) as modifiedByName, ( creator.firstName + ' ' + creator.lastName ) as createdByName,
                                ext_references =  STUFF((SELECT DISTINCT ' , ' + (ref.number) from legal_case_litigation_external_references as ref WHERE ref.stage = stages.id FOR XML PATH('')), 1, 3, ''),
                                case_opponents = STUFF((SELECT ' , ' +(CASE WHEN legal_case_opponents.opponent_member_type IS NULL THEN NULL ELSE (CASE WHEN legal_case_opponents.opponent_member_type = 'company' THEN opponentCompany.name ELSE
                                (CASE WHEN opponentContact.father!='' THEN (opponentContact.firstName + ' ' + opponentContact.father + ' ' + opponentContact.lastName) ELSE (opponentContact.firstName + ' ' + opponentContact.lastName) END) END)END ) from legal_case_opponents INNER JOIN opponents ON opponents.id = legal_case_opponents.opponent_id
                                LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id AND legal_case_opponents.opponent_member_type = 'company'
                                LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id AND legal_case_opponents.opponent_member_type = 'contact'
                                WHERE legal_case_opponents.case_id = stages.legal_case_id FOR XML PATH('')), 1, 3, ''),
                                opponents = STUFF((SELECT ' , ' + (CASE WHEN opponents.company_id IS NOT NULL THEN opponentCompany.name ELSE (CASE WHEN opponentContact.father!=''
                                THEN (opponentContact.firstName + ' ' + opponentContact.father + ' ' + opponentContact.lastName) ELSE (opponentContact.firstName + ' ' + opponentContact.lastName) END) END  )
                                from  legal_case_litigation_stages_opponents as stages_opponents
                                INNER JOIN opponents ON opponents.id = stages_opponents.opponent_id
                                LEFT JOIN companies AS opponentCompany ON opponentCompany.id = opponents.company_id
                                LEFT JOIN contacts AS opponentContact ON opponentContact.id = opponents.contact_id
                                WHERE stages_opponents.stage = stages.id FOR XML PATH('')), 1, 3, '')
                                FROM legal_case_litigation_details as stages
                                LEFT JOIN court_types ON court_types.id = stages.court_type_id
                                LEFT JOIN court_degrees ON court_degrees.id = stages.court_degree_id
                                LEFT JOIN court_regions ON court_regions.id = stages.court_region_id
                                LEFT JOIN courts ON courts.id = stages.court_id
                                LEFT JOIN legal_cases ON legal_cases.id = stages.legal_case_id
                                LEFT JOIN clients_view ON clients_view.id=legal_cases.client_id AND clients_view.model = 'clients'
                                LEFT JOIN user_profiles as UP ON UP.user_id = stages.modifiedBy
                                LEFT JOIN user_profiles as creator ON creator.user_id = stages.createdBy
                                ORDER BY stages.modifiedBy DESC;");
        } else {
            $this->db->query("CREATE OR REPLACE ALGORITHM = TEMPTABLE SQL SECURITY DEFINER VIEW `legal_case_litigation_stages_full_details` AS
                                SELECT stages.id                                        as id,
                                       stages.legal_case_id,
                                       stages.sentenceDate,
                                       stages.comments,
                                       stages.legal_case_stage,
                                       stages.client_position,
                                       stages.status,
                                       stages.modifiedBy,
                                       stages.modifiedOn,
                                       stages.createdOn,
                                       stages.createdBy,
                                       court_types.name                                 as court_type,
                                       court_degrees.name                               as court_degree,
                                       court_regions.name                               as court_region,
                                       courts.name                                      as court,
                                       clients_view.name                                as client_name,
                                       CONCAT(UP.firstName, ' ', UP.lastName)           as modifiedByName,
                                       CONCAT(creator.firstName, ' ', creator.lastName) as createdByName,
                                       (SELECT GROUP_CONCAT(DISTINCT ref.number SEPARATOR ',')
                                        from legal_case_litigation_external_references as ref
                                        WHERE ref.stage = stages.id)                    as ext_references
                                FROM legal_case_litigation_details as stages
                                         LEFT JOIN `court_types` ON `court_types`.`id` = `stages`.`court_type_id`
                                         LEFT JOIN `court_degrees` ON `court_degrees`.`id` = `stages`.`court_degree_id`
                                         LEFT JOIN `court_regions` ON `court_regions`.`id` = `stages`.`court_region_id`
                                         LEFT JOIN `courts` ON `courts`.`id` = `stages`.`court_id`
                                         LEFT JOIN `legal_cases` ON `legal_cases`.`id` = `stages`.`legal_case_id`
                                         LEFT JOIN `clients_view` ON `clients_view`.`id` = `legal_cases`.`client_id` AND clients_view.model = 'clients'
                                         LEFT JOIN user_profiles as UP ON UP.user_id = stages.modifiedBy
                                         LEFT JOIN user_profiles as creator ON creator.user_id = stages.createdBy
                                ORDER BY `stages`.`modifiedBy` DESC;");
        }
        $this->write_log($this->log_path, 'done update legal case litigation stages full details view.');
    }

    public function rename_advisor_permissions_table()
    {
        $this->write_log($this->log_path, 'start rename_advisor_permissions_table');

        if ($this->db->dbdriver === 'sqlsrv') {
            $this->db->query("IF OBJECT_ID('dbo.advisor_workflow_permissions', 'U') IS NOT NULL DROP TABLE dbo.advisor_workflow_permissions;");
            
            $this->db->query("
            CREATE TABLE advisor_workflow_permissions (
              id BIGINT NOT NULL PRIMARY KEY IDENTITY,
              workflow_id BIGINT NULL,
              workflow_status_transition_id BIGINT NULL
            );");

            $this->db->query("ALTER TABLE advisor_workflow_permissions
            ADD CONSTRAINT fk_advisor_workflow_permissions_1 FOREIGN KEY (workflow_status_transition_id) REFERENCES workflow_status_transition (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
            CONSTRAINT fk_advisor_workflow_permissions_2 FOREIGN KEY (workflow_id) REFERENCES workflows (id) ON DELETE NO ACTION ON UPDATE NO ACTION;");
        } else {
            $this->db->query("DROP TABLE IF EXISTS `advisor_workflow_permissions`;");
            $this->db->query("
            CREATE TABLE IF NOT EXISTS `advisor_workflow_permissions` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `workflow_id` int(11) NOT NULL,
              `workflow_status_transition_id` int(11) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `workflow_id` (`workflow_id`),
              KEY `workflow_status_transition_id` (`workflow_status_transition_id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;");

            $this->db->query("ALTER TABLE `advisor_workflow_permissions`
            ADD CONSTRAINT `fk_advisor_workflow_permissions_1` FOREIGN KEY (`workflow_status_transition_id`) REFERENCES `workflow_status_transition` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
            ADD CONSTRAINT `fk_advisor_workflow_permissions_2` FOREIGN KEY (`workflow_id`) REFERENCES `workflows` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;");
        }

        $this->write_log($this->log_path, 'done from rename_advisor_permissions_table');
    }

    public function grant_users_to_access_new_permissions()
    {
        $this->write_log($this->log_path, 'add new actions to user permissions');
        
        $this->load->model('user_group', 'user_groupfactory');
        $this->user_group = $this->user_groupfactory->get_instance();
        
        $this->load->model('user_group_permission');
        $user_groups = $this->user_group->load_all();
        
        foreach ($user_groups as $user_group) {
            $group_permissions = $this->user_group_permission->get_permissions($user_group['id'], false);
            
            foreach ($group_permissions as $module => $group_permission) {
                if ($module === 'core') {
                    $new_permissions = $group_permissions;
                    
                    if(in_array('/customer_portal/portal_permissions/', $group_permission)){
                        array_push($new_permissions['core'], 'advisors/portal_permissions');
                        
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
            }
        }

        $this->write_log($this->log_path, 'done from user permissions');
    }

    public function add_actions_to_email_notifications_scheme()
    {
        $this->write_log($this->log_path, 'start add_actions_to_email_notifications_scheme');

        $currentSysDate = date('Y-m-d H:i:s');

        $data = [
            [
                'trigger_action' => 'core_user_assigned_case',
                'notify_to' => 'advisors',
                'notify_cc' => 'case_creator',
                'createdBy' => 1,
                'createdOn' => $currentSysDate,
                'modifiedBy' => 1,
                'modifiedOn' => $currentSysDate,
                'hide_show_send_email_notification' => '1'
            ],
            [
                'trigger_action' => 'advisor_add_comment',
                'notify_to' => 'assignees;advisors',
                'notify_cc' => 'case_creator',
                'createdBy' => 1,
                'createdOn' => $currentSysDate,
                'modifiedBy' => 1,
                'modifiedOn' => $currentSysDate,
                'hide_show_send_email_notification' => '1'
            ],
            [
                'trigger_action' => 'advisor_edit_case_status',
                'notify_to' => 'assignees;advisors',
                'notify_cc' => 'case_creator',
                'createdBy' => 1,
                'createdOn' => $currentSysDate,
                'modifiedBy' => 1,
                'modifiedOn' => $currentSysDate,
                'hide_show_send_email_notification' => '1'
            ],
            [
                'trigger_action' => 'advisor_edit_case_stage',
                'notify_to' => 'assignees;advisors',
                'notify_cc' => 'case_creator',
                'createdBy' => 1,
                'createdOn' => $currentSysDate,
                'modifiedBy' => 1,
                'modifiedOn' => $currentSysDate,
                'hide_show_send_email_notification' => '1'
            ],
            [
                'trigger_action' => 'advisor_add_hearing',
                'notify_to' => 'advisors',
                'notify_cc' => 'case_creator;assignees',
                'createdBy' => 1,
                'createdOn' => $currentSysDate,
                'modifiedBy' => 1,
                'modifiedOn' => $currentSysDate,
                'hide_show_send_email_notification' => '1'
            ]
        ];

        foreach ($data as $row) {
            $this->db->insert('email_notifications_scheme', $row);
        }

        $this->write_log($this->log_path, 'done from add_actions_to_email_notifications_scheme');
    }
}
