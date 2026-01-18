<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Opinion_Effective_Effort_View extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction

        $sql = "
        -- Drop the view if it already exists to allow recreation
        IF OBJECT_ID('[dbo].[opinion_effective_effort]', 'V') IS NOT NULL
            DROP VIEW [dbo].[opinion_effective_effort];
        GO

        CREATE VIEW [dbo].[opinion_effective_effort] AS
        SELECT TOP(9223372036854775800) user_activity_logs.opinion_id AS opinion_id, SUM(user_activity_logs.effectiveEffort) AS effectiveEffort
        FROM user_activity_logs
        WHERE user_activity_logs.opinion_id IS NOT NULL
        GROUP BY user_activity_logs.opinion_id;
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
            log_message('error', 'Migration for opinion_effective_effort view failed and transaction was rolled back.');
        } else {
            log_message('info', 'Migration for opinion_effective_effort view completed successfully.');
        }
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction

        $sql = "
        IF OBJECT_ID('[dbo].[opinion_effective_effort]', 'V') IS NOT NULL
            DROP VIEW [dbo].[opinion_effective_effort];
        ";
        $this->db->query($sql);

        $this->db->trans_complete(); // Complete the transaction

        if ($this->db->trans_status() === FALSE) {
            log_message('error', 'Rollback for opinion_effective_effort view failed and transaction was rolled back.');
        } else {
            log_message('info', 'Rollback for opinion_effective_effort view completed successfully.');
        }
    }
}
