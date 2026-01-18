<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Contract_Workflow_Step_Tables extends CI_Migration {

    private $checklist_table = 'contract_workflow_step_checklist';
    private $functions_table = 'contract_workflow_step_functions';
    private $step_table = 'contract_status_language'; // Parent table

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- EXPLICITLY DROP TABLES IF THEY EXIST (Ensuring clean recreation) ---
        // Dropping the child tables first is safer, although CI should handle it.
        $this->dbforge->drop_table($this->checklist_table, TRUE);
        $this->dbforge->drop_table($this->functions_table, TRUE);

        // --- 1. Create contract_workflow_step_checklist table ---
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
            'item_text' => [
                'type' => 'NVARCHAR',
                'constraint' => '250',
                'null' => FALSE,
            ],
            'input_type' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => FALSE,
                'default' => 'yesno',
            ],
            'is_required' => [
                'type' => 'BIT',
                'null' => FALSE,
                'default' => 1,
            ],
            'sort_order' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'default' => 0,
            ],
            'CONSTRAINT pk_checklist PRIMARY KEY (id)'
        ]);
        $this->dbforge->create_table($this->checklist_table, TRUE);

        // --- 1a. Add FK and Unique Constraint for Checklist table (using raw SQL) ---
        $fk_checklist = 'FK_Checklist_Step';
        $uq_checklist = 'UQ_contract_workflow_step_checklist_step_item';

        // Add Foreign Key (FK)
        if ($this->db->query("SELECT name FROM sys.foreign_keys WHERE name = '{$fk_checklist}'")->num_rows() == 0) {
            $this->db->query("
                ALTER TABLE [dbo].[{$this->checklist_table}]
                ADD CONSTRAINT [{$fk_checklist}]
                FOREIGN KEY ([step_id])
                REFERENCES [dbo].[{$this->step_table}]([id])
                ON DELETE CASCADE
            ");
        }

        // Add Unique Constraint (UQ)
        $this->db->query("
            IF NOT EXISTS (
                SELECT *
                FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
                WHERE CONSTRAINT_TYPE = 'UNIQUE'
                AND TABLE_NAME = '{$this->checklist_table}'
                AND CONSTRAINT_NAME = '{$uq_checklist}'
            )
            BEGIN
                ALTER TABLE [dbo].[{$this->checklist_table}]
                ADD CONSTRAINT [{$uq_checklist}]
                UNIQUE ([step_id], [item_text]);
            END
        ");


        // --- 2. Create contract_workflow_step_functions table ---
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
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => FALSE,
            ],
            'label' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => FALSE,
            ],
            'icon_class' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => FALSE,
            ],
            'sort_order' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'default' => 0,
            ],
            'data_action' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'default' => 'GETDATE()',
            ],
            'CONSTRAINT pk_functions PRIMARY KEY (id)'
        ]);
        $this->dbforge->create_table($this->functions_table, TRUE);

        // --- 2a. Add FK and Unique Constraint for Functions table (using raw SQL) ---
        $fk_functions = 'FK_Functions_Step';
        $uq_functions = 'UQ_contract_workflow_step_functions_step_function';

        // Add Foreign Key (FK)
        if ($this->db->query("SELECT name FROM sys.foreign_keys WHERE name = '{$fk_functions}'")->num_rows() == 0) {
            $this->db->query("
                ALTER TABLE [dbo].[{$this->functions_table}]
                ADD CONSTRAINT [{$fk_functions}]
                FOREIGN KEY ([step_id])
                REFERENCES [dbo].[{$this->step_table}]([id])
                ON DELETE CASCADE
            ");
        }

        // Add Unique Constraint (UQ)
        $this->db->query("
            IF NOT EXISTS (
                SELECT *
                FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
                WHERE CONSTRAINT_TYPE = 'UNIQUE'
                AND TABLE_NAME = '{$this->functions_table}'
                AND CONSTRAINT_NAME = '{$uq_functions}'
            )
            BEGIN
                ALTER TABLE [dbo].[{$this->functions_table}]
                ADD CONSTRAINT [{$uq_functions}]
                UNIQUE ([step_id], [function_name]);
            END
        ");


        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // Drop tables (Dropping the table implicitly drops the indexes/constraints on it)
        $this->dbforge->drop_table($this->functions_table, TRUE);
        $this->dbforge->drop_table($this->checklist_table, TRUE);

        $this->db->trans_complete(); // Complete the transaction
    }
}
