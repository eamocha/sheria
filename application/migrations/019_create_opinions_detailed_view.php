<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Opinions_Detailed_View extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction

        $sql = "
        -- Drop the view if it already exists to allow recreation
        IF OBJECT_ID('[dbo].[opinions_detailed_view]', 'V') IS NOT NULL
            DROP VIEW [dbo].[opinions_detailed_view];
        GO

        CREATE VIEW [dbo].[opinions_detailed_view] AS
        SELECT TOP (9223372036854775800)
            opinions.id,
            ('LO' + CAST(opinions.id AS NVARCHAR(MAX))) AS opinionId, -- Cast to NVARCHAR(MAX) for string concatenation
            opinions.title,
            CASE
                WHEN opinions.legal_case_id IS NULL THEN ''
                ELSE ('M' + CAST(opinions.legal_case_id AS NVARCHAR(MAX)))
            END AS caseId,
            opinions.legal_case_id,
            opinions.user_id,
            opinions.due_date,
            opinions.assigned_to AS assignedToId,
            opinions.reporter AS reportedById,
            opinions.private,
            opinions.priority,
            opinions.opinion_location_id AS opinion_location_id,
            opinion_locations.name AS location,
            opinions.detailed_info AS opinionFulldetailed_info,
            opinions.background_info,
            opinions.legal_question,
            opinions.opinion_status_id,
            opinions.opinion_type_id,
            opinions.estimated_effort,
            opinions.channel,
            opinions.requester,
            opinions.is_visible_to_cp,
            CAST(opinions.createdOn AS DATE) AS createdOn,
            CAST(opinions.modifiedOn AS DATE) AS modifiedOn,
            opinions.modifiedBy,
            opinions.archived,
            tee.effectiveEffort,
            (assigned.firstName + ' ' + assigned.lastName) AS assigned_to, -- SQL Server string concat
            (reporter.firstName + ' ' + reporter.lastName) AS reporter, -- SQL Server string concat
            (created.firstName + ' ' + created.lastName) AS createdBy, -- SQL Server string concat
            (modified.firstName + ' ' + modified.lastName) AS modifiedByName, -- SQL Server string concat
            opinions.createdBy AS createdById,
            ts.name AS opinionStatus,
            opinions.archived AS archivedOpinions,
            SUBSTRING(opinions.detailed_info, 1, 50) AS detailed_info,
            SUBSTRING(lg.subject, 1, 50) AS caseSubject,
            lg.subject AS caseFullSubject,
            lg.category AS caseCategory,
            opinions.contract_id,
            contract.name AS contract_name,
            assigned.status AS assignee_status,
            reporter.status AS reporter_status,
            created.status AS creator_status,
            modified.status AS modifier_status,
            opinions.stage,
            opinions.category AS opinionCategory,
            contributors = STUFF(
                (
                    SELECT ', ' + (contr.firstName + ' ' + contr.lastName)
                    FROM user_profiles AS contr
                    INNER JOIN opinion_contributors ON opinions.id = opinion_contributors.opinion_id AND contr.user_id = opinion_contributors.user_id
                    FOR XML PATH(''), TYPE
                ).value('.', 'NVARCHAR(MAX)'), -- Proper way to extract text from XML in SQL Server
                1,
                2, -- Changed from 1 to 2 to remove the leading ', '
                ''
            )
        FROM opinions
        LEFT JOIN user_profiles assigned ON assigned.user_id = opinions.assigned_to
        LEFT JOIN user_profiles reporter ON reporter.user_id = opinions.reporter
        LEFT JOIN user_profiles created ON created.user_id = opinions.createdBy
        LEFT JOIN user_profiles modified ON modified.user_id = opinions.modifiedBy
        LEFT JOIN opinion_statuses ts ON ts.id = opinions.opinion_status_id
        LEFT JOIN legal_cases AS lg ON lg.id = opinions.legal_case_id
        LEFT JOIN contract ON contract.id = opinions.contract_id
        LEFT JOIN opinion_effective_effort AS tee ON tee.opinion_id = opinions.id
        LEFT JOIN opinion_locations ON opinion_locations.id = opinions.opinion_location_id
        WHERE
            opinions.legal_case_id IS NULL OR lg.isDeleted = 0;
        ";

        // Split the SQL by 'GO' and execute each batch
        $statements = array_filter(array_map('trim', explode('GO', $sql)));
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $this->db->query($statement);
            }
        }

        $this->db->trans_complete(); // Complete the transaction

        if ($this->db->trans_status() === FALSE) {
            log_message('error', 'Migration for opinions_detailed_view failed and transaction was rolled back.');
        } else {
            log_message('info', 'Migration for opinions_detailed_view completed successfully.');
        }
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction

        $sql = "
        IF OBJECT_ID('[dbo].[opinions_detailed_view]', 'V') IS NOT NULL
            DROP VIEW [dbo].[opinions_detailed_view];
        ";
        $this->db->query($sql);

        $this->db->trans_complete(); // Complete the transaction

        if ($this->db->trans_status() === FALSE) {
            log_message('error', 'Rollback for opinions_detailed_view failed and transaction was rolled back.');
        } else {
            log_message('info', 'Rollback for opinions_detailed_view completed successfully.');
        }
    }
}
