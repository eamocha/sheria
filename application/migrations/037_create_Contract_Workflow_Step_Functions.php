<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Contract_Workflow_Step_Functions extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- 1. Drop the table first if it exists to ensure a clean state ---
        $this->dbforge->drop_table('contract_workflow_step_functions', TRUE);

        // --- 2. Create the contract_workflow_step_functions table ---
        $this->dbforge->add_field([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'step_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'null' => FALSE,
            ],
            'function_name' => [
                'type' => 'NVARCHAR',
                'constraint' => '100',
                'null' => FALSE,
            ],
            'label' => [
                'type' => 'NVARCHAR',
                'constraint' => '255',
                'null' => FALSE,
            ],
            'icon_class' => [
                'type' => 'NVARCHAR',
                'constraint' => '100',
                'null' => FALSE,
            ],
            'sort_order' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'null' => FALSE,
                'default' => 0,
            ],
            'data_action' => [
                'type' => 'NVARCHAR',
                'constraint' => '100',
                'null' => TRUE,
            ],
            'created_at' => [
                'type' => 'DATETIME2',
                'null' => TRUE,
                'default' => 'GETDATE()'
            ],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('contract_workflow_step_functions', TRUE);

        // --- 3. Add the foreign key and unique constraints using raw SQL ---
        $this->db->query("
            IF NOT EXISTS (
                SELECT *
                FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS
                WHERE CONSTRAINT_NAME = 'fk_step_functions_step'
            )
            BEGIN
                ALTER TABLE [dbo].[contract_workflow_step_functions]
                ADD CONSTRAINT [fk_step_functions_step]
                FOREIGN KEY ([step_id]) REFERENCES [dbo].[contract_status_language]([id]) ON DELETE CASCADE
            END
        ");

        $this->db->query("
            IF NOT EXISTS (
                SELECT *
                FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
                WHERE CONSTRAINT_TYPE = 'UNIQUE'
                AND TABLE_NAME = 'contract_workflow_step_functions'
                AND CONSTRAINT_NAME = 'UQ_contract_workflow_step_functions_step_function'
            )
            BEGIN
                ALTER TABLE [dbo].[contract_workflow_step_functions]
                ADD CONSTRAINT [UQ_contract_workflow_step_functions_step_function]
                UNIQUE ([step_id], [function_name])
            END
        ");

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- Drop the table and its dependents ---
        $this->dbforge->drop_table('contract_workflow_step_functions', TRUE);

        $this->db->trans_complete(); // Complete the transaction
    }
}
