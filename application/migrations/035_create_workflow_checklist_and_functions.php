<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Workflow_Checklist_And_Functions extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- Create the contract_workflow_step_checklist table ---
        // The BIT(1) was causing an error, as the BIT datatype in SQL Server
        // does not accept a length. It has been corrected to just BIT.
        $this->db->query("
            IF NOT EXISTS (SELECT * FROM sysobjects WHERE ID = object_id(N'contract_workflow_step_checklist') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)
            CREATE TABLE contract_workflow_step_checklist (
                id BIGINT NOT NULL IDENTITY(1,1),
                step_id BIGINT NOT NULL,
                item_text NVARCHAR(250) NOT NULL,
                input_type VARCHAR(50) DEFAULT 'yesno' NOT NULL,
                is_required BIT DEFAULT 1 NOT NULL,
                sort_order BIGINT DEFAULT 0,
                CONSTRAINT pk_checklist PRIMARY KEY (id),
                CONSTRAINT fk_checklist_step FOREIGN KEY (step_id) REFERENCES contract_status_language(id) ON DELETE CASCADE
            )
        ");

        // --- Create the stored procedure sp_add_workflow_to_new_contracts ---
        $this->db->query("
            CREATE OR ALTER PROCEDURE sp_add_workflow_to_new_contracts
            AS
            BEGIN
                SET NOCOUNT ON;
                
                -- Check if there is an active workflow to assign to new contracts
                IF NOT EXISTS(SELECT 1 FROM contract_workflows WHERE is_active = 1)
                BEGIN
                    RAISERROR('No active contract workflow found.', 16, 1);
                    RETURN;
                END

                -- Find the active workflow
                DECLARE @active_workflow_id BIGINT;
                SELECT TOP 1 @active_workflow_id = id FROM contract_workflows WHERE is_active = 1;

                -- Find the entry point of the active workflow
                DECLARE @entry_point_status_id BIGINT;
                SELECT TOP 1 @entry_point_status_id = id FROM contract_workflow_status WHERE workflow_id = @active_workflow_id AND is_entry_point = 1;
                
                IF @entry_point_status_id IS NULL
                BEGIN
                    RAISERROR('No entry point status found for the active workflow.', 16, 1);
                    RETURN;
                END
                
                -- Update any contracts with status_id = 1 (pending) to the new workflow's entry point status_id
                UPDATE contracts
                SET status_id = @entry_point_status_id
                WHERE status_id = 1;
            END;
        ");

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- Drop the stored procedure ---
        $this->db->query("
            IF OBJECT_ID('sp_add_workflow_to_new_contracts', 'P') IS NOT NULL
            BEGIN
                DROP PROCEDURE sp_add_workflow_to_new_contracts;
            END
        ");

        // --- Drop the table ---
        if ($this->db->table_exists('contract_workflow_step_checklist')) {
            $this->dbforge->drop_table('contract_workflow_step_checklist', TRUE);
        }

        $this->db->trans_complete(); // Complete the transaction
    }
}
