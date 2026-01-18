<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Legal_Cases_Columns_Duplicate extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- Add 'criminal' column to 'case_types' table ---
        // Check if the 'criminal' column already exists before adding it
        if (!$this->db->field_exists('criminal', 'case_types')) {
            $this->dbforge->add_column('case_types', [
                'criminal' => [
                    'type' => 'CHAR',
                    'constraint' => '3',
                    'null' => TRUE,
                ]
            ]);
        }

        // --- Add columns to 'legal_cases' table ---
        // Add closure_requested_by
        if (!$this->db->field_exists('closure_requested_by', 'legal_cases')) {
            $this->dbforge->add_column('legal_cases', [
                'closure_requested_by' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ]
            ]);
        }

        // Add closed_by
        if (!$this->db->field_exists('closed_by', 'legal_cases')) {
            $this->dbforge->add_column('legal_cases', [
                'closed_by' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ]
            ]);
        }

        // Add closure_comments
        if (!$this->db->field_exists('closure_comments', 'legal_cases')) {
            $this->dbforge->add_column('legal_cases', [
                'closure_comments' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '250',
                    'null' => TRUE,
                ]
            ]);
        }

        // Add approval_step
        if (!$this->db->field_exists('approval_step', 'legal_cases')) {
            $this->dbforge->add_column('legal_cases', [
                'approval_step' => [
                    'type' => 'TINYINT',
                    'constraint' => 3,
                    'null' => FALSE,
                    'default' => "0",
                ]
            ]);
        }

      
	        if (!$this->db->field_exists('first_litigation_case_court_activity_purpose', 'legal_cases')) {
		        $this->dbforge->add_column('legal_cases', [
			        'first_litigation_case_court_activity_purpose' => [
				        'type' => 'NVARCHAR',
				        'constraint' => '250',
				        'null' => TRUE,
			        ]
		        ]);

        }

        // Add closedBy_comments
        if (!$this->db->field_exists('closedBy_comments', 'legal_cases')) {
            $this->dbforge->add_column('legal_cases', [
                'closedBy_comments' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '250',
                    'null' => TRUE,
                ]
            ]);
        }

        // --- Add foreign key constraints using helper function ---
        // FK_legal_cases_closure_requested_by
        $this->add_foreign_key_if_not_exists(
            'legal_cases',
            'FK_legal_cases_closure_requested_by',
            'closure_requested_by',
            'users',
            'id',
            'NO ACTION',
            'NO ACTION'
        );

        // FK_legal_cases_closed_by
        $this->add_foreign_key_if_not_exists(
            'legal_cases',
            'FK_legal_cases_closed_by',
            'closed_by',
            'users',
            'id',
            'NO ACTION',
            'NO ACTION'
        );

        // --- Add indexes using raw SQL for performance ---
        // Check if IX_legal_cases_closure_requested_by index exists before adding
        $index_exists = $this->db->query("SELECT 1 FROM sys.indexes WHERE name = 'IX_legal_cases_closure_requested_by' AND object_id = OBJECT_ID('[dbo].[legal_cases]')")->num_rows() > 0;
        if (!$index_exists) {
            $this->db->query("CREATE INDEX IX_legal_cases_closure_requested_by ON [dbo].[legal_cases](closure_requested_by);");
        }

        // Check if IX_legal_cases_closed_by index exists before adding
        $index_exists = $this->db->query("SELECT 1 FROM sys.indexes WHERE name = 'IX_legal_cases_closed_by' AND object_id = OBJECT_ID('[dbo].[legal_cases]')")->num_rows() > 0;
        if (!$index_exists) {
            $this->db->query("CREATE INDEX IX_legal_cases_closed_by ON [dbo].[legal_cases](closed_by);");
        }

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- Drop indexes first (only if they exist) ---
        if ($this->db->query("SELECT 1 FROM sys.indexes WHERE name = 'IX_legal_cases_closure_requested_by' AND object_id = OBJECT_ID('[dbo].[legal_cases]')")->num_rows() > 0) {
            $this->db->query("DROP INDEX IX_legal_cases_closure_requested_by ON [dbo].[legal_cases];");
        }
        if ($this->db->query("SELECT 1 FROM sys.indexes WHERE name = 'IX_legal_cases_closed_by' AND object_id = OBJECT_ID('[dbo].[legal_cases]')")->num_rows() > 0) {
            $this->db->query("DROP INDEX IX_legal_cases_closed_by ON [dbo].[legal_cases];");
        }

        // --- Drop foreign key constraints (only if they exist) ---
        $this->drop_foreign_key_if_exists('legal_cases', 'FK_legal_cases_closure_requested_by');
        $this->drop_foreign_key_if_exists('legal_cases', 'FK_legal_cases_closed_by');

        // --- Drop columns from 'legal_cases' table (only if they exist) ---
        if ($this->db->field_exists('closedBy_comments', 'legal_cases')) {
            $this->dbforge->drop_column('legal_cases', 'closedBy_comments');
        }
        if ($this->db->field_exists('first_litigation_case_court_activity_purpose', 'legal_cases')) {
            $this->dbforge->drop_column('legal_cases', 'first_litigation_case_court_activity_purpose');
        }
        if ($this->db->field_exists('approval_step', 'legal_cases')) {
            $this->dbforge->drop_column('legal_cases', 'approval_step');
        }
        if ($this->db->field_exists('closure_comments', 'legal_cases')) {
            $this->dbforge->drop_column('legal_cases', 'closure_comments');
        }
        if ($this->db->field_exists('closed_by', 'legal_cases')) {
            $this->dbforge->drop_column('legal_cases', 'closed_by');
        }
        if ($this->db->field_exists('closure_requested_by', 'legal_cases')) {
            $this->dbforge->drop_column('legal_cases', 'closure_requested_by');
        }

        // --- Drop 'criminal' column from 'case_types' table (only if it exists) ---
        if ($this->db->field_exists('criminal', 'case_types')) {
            $this->dbforge->drop_column('case_types', 'criminal');
        }

        $this->db->trans_complete(); // Complete the transaction
    }

    /**
     * Helper function to add foreign key if it doesn't exist.
     */
    private function add_foreign_key_if_not_exists($table, $fk_name, $column, $ref_table, $ref_column, $on_delete, $on_update = 'NO ACTION')
    {
        $check_fk_sql = "SELECT COUNT(*) AS count FROM sys.foreign_keys WHERE name = '{$fk_name}' AND parent_object_id = OBJECT_ID('[dbo].[{$table}]');";
        $query_result = $this->db->query($check_fk_sql);
        $fk_exists = false;
        if ($query_result instanceof CI_DB_result && $query_result->num_rows() > 0 && isset($query_result->row()->count) && $query_result->row()->count > 0) {
            $fk_exists = true;
        }

        if (!$fk_exists && $this->db->table_exists($table) && $this->db->field_exists($column, $table) && $this->db->table_exists($ref_table)) {
            $this->db->query("
                ALTER TABLE [dbo].[{$table}] WITH CHECK ADD CONSTRAINT [{$fk_name}] FOREIGN KEY([{$column}])
                REFERENCES [dbo].[{$ref_table}] ([{$ref_column}])
                ON DELETE {$on_delete} ON UPDATE {$on_update};
            ");
            $this->db->query("ALTER TABLE [dbo].[{$table}] CHECK CONSTRAINT [{$fk_name}];");
        }
    }

    /**
     * Helper function to drop foreign key if it exists.
     */
    private function drop_foreign_key_if_exists($table, $fk_name)
    {
        $check_fk_sql = "SELECT COUNT(*) AS count FROM sys.foreign_keys WHERE name = '{$fk_name}' AND parent_object_id = OBJECT_ID('[dbo].[{$table}]');";
        $query_result = $this->db->query($check_fk_sql);
        if ($query_result instanceof CI_DB_result && $query_result->num_rows() > 0 && isset($query_result->row()->count) && $query_result->row()->count > 0) {
            $this->db->query("ALTER TABLE [dbo].[{$table}] DROP CONSTRAINT [{$fk_name}];");
        }
    }
}
