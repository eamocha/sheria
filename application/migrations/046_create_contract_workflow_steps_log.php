<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Contract_Workflow_Steps_Log extends CI_Migration {

    private $table_name = 'contract_workflow_steps_log';

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- 1. Drop table first if it exists to ensure a clean state ---
        $this->dbforge->drop_table($this->table_name, TRUE);

        // --- 2. Create the 'contract_workflow_steps_log' table ---
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
            'contract_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'null' => FALSE,
            ],
            'user_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'null' => TRUE,
            ],
            'action_type' => [
                'type' => 'NVARCHAR',
                'constraint' => '100',
                'null' => FALSE,
            ],
            'action_type_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'null' => FALSE,
            ],
            'details' => [
                'type' => 'NVARCHAR',
                'constraint' => 'MAX',
                'null' => TRUE,
            ],
            'createdBy' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'null' => FALSE,
            ],
            'createdOn' => [
                'type' => 'DATETIME2',
                'null' => TRUE,
                'default' => 'SYSDATETIME()'
            ],
            'CONSTRAINT pk_workflow_steps_log PRIMARY KEY (id)'
        ]);
        $this->dbforge->create_table($this->table_name, TRUE);

        // --- 3. Add Foreign Key constraints using raw SQL ---
        $fks = [
            'fk_log_step_id' => ['column' => 'step_id', 'ref_table' => 'contract_status_language', 'ref_column' => 'id'],
            'fk_log_actor' => ['column' => 'createdBy', 'ref_table' => 'users', 'ref_column' => 'id'],
            'fk_workflow_contract_id' => ['column' => 'contract_id', 'ref_table' => 'contract', 'ref_column' => 'id'],
        ];

        foreach ($fks as $name => $details) {
            $fk_check = $this->db->query("SELECT name FROM sys.foreign_keys WHERE name = '{$name}'")->num_rows();

            if ($fk_check == 0) {
                $this->db->query("
                    ALTER TABLE [dbo].[{$this->table_name}]
                    ADD CONSTRAINT [{$name}]
                    FOREIGN KEY ([{$details['column']}] )
                    REFERENCES [dbo].[{$details['ref_table']}]([{$details['ref_column']}])
                    ON DELETE CASCADE
                ");
            }
        }

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- Drop Foreign Key constraints (reverse order is safer) ---
        $fks_to_drop = [
            'fk_workflow_contract_id',
            'fk_log_actor',
            'fk_log_step_id',
        ];
        
        foreach ($fks_to_drop as $name) {
            $fk_check = $this->db->query("SELECT name FROM sys.foreign_keys WHERE name = '{$name}'")->num_rows();
            if ($fk_check > 0) {
                $this->db->query("ALTER TABLE [dbo].[{$this->table_name}] DROP CONSTRAINT [{$name}]");
            }
        }

        // --- Drop the 'contract_workflow_steps_log' table ---
        $this->dbforge->drop_table($this->table_name, TRUE);

        $this->db->trans_complete(); // Complete the transaction
    }
}
