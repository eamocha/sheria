<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Legal_Cases_Columns extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- Add 'criminal' column to 'case_types' table ---
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
                    'constraint' => 3, // Tinyint is 0-255, constraint 3 is for display width
                    'null' => FALSE,
                    'default' => 0,
                ]
            ]);
        }

        // Add first_litigation_case_court_activity_purpose
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

        // --- Handle existing and new foreign key constraints on 'legal_cases' referencing 'users' ---
        // We will drop and re-add or just add them with ON UPDATE NO ACTION to prevent cascade cycles.

        $fk_definitions = [
            'FK_legal_cases_createdBy' => ['column' => 'createdBy', 'on_delete' => 'NO ACTION'], // Assuming createdBy is NOT NULL in legal_cases
            'FK_legal_cases_modifiedBy' => ['column' => 'modifiedBy', 'on_delete' => 'NO ACTION'], // Assuming modifiedBy is NOT NULL in legal_cases
            'FK_legal_cases_user_id' => ['column' => 'user_id', 'on_delete' => 'NO ACTION'], // Common FK for assignee/owner
            'FK_legal_cases_assignee' => ['column' => 'assigned_to', 'on_delete' => 'NO ACTION'], // Common FK for assigned_to
            'FK_legal_cases_closure_requested_by' => ['column' => 'closure_requested_by', 'on_delete' => 'NO ACTION'],
            'FK_legal_cases_closed_by' => ['column' => 'closed_by', 'on_delete' => 'NO ACTION'],
        ];

        foreach ($fk_definitions as $fk_name => $props) {
            $column_name = $props['column'];
            $on_delete_action = $props['on_delete'];

            $check_fk_sql = "SELECT COUNT(*) AS count FROM sys.foreign_keys WHERE name = '{$fk_name}' AND parent_object_id = OBJECT_ID('[dbo].[legal_cases]');";
            $query_result = $this->db->query($check_fk_sql);
            $fk_exists = false;
            if ($query_result && $query_result->num_rows() > 0) {
                $row = $query_result->row();
                if (isset($row->count) && $row->count > 0) {
                    $fk_exists = true;
                }
            }

            if ($fk_exists) {
                // Drop the existing foreign key
                $this->db->query("ALTER TABLE [dbo].[legal_cases] DROP CONSTRAINT {$fk_name};");
            }

            // Add or Re-add the foreign key with ON UPDATE NO ACTION
            // Check if the column exists before trying to add a FK on it
            if ($this->db->field_exists($column_name, 'legal_cases')) {
                $this->db->query("
                    ALTER TABLE [dbo].[legal_cases]
                    ADD CONSTRAINT {$fk_name}
                    FOREIGN KEY ({$column_name}) REFERENCES [dbo].[users](id)
                    ON DELETE {$on_delete_action} ON UPDATE NO ACTION;
                ");
            }
        }

        // --- Add indexes using raw SQL for performance ---
        $indexes = [
            'IX_legal_cases_closure_requested_by' => 'closure_requested_by',
            'IX_legal_cases_closed_by' => 'closed_by',
        ];

        foreach ($indexes as $idx_name => $column_name) {
            $check_idx_sql = "SELECT COUNT(*) AS count FROM sys.indexes WHERE name = '{$idx_name}' AND object_id = OBJECT_ID('[dbo].[legal_cases]');";
            $query_result = $this->db->query($check_idx_sql);
            $idx_exists = false;
            if ($query_result && $query_result->num_rows() > 0) {
                $row = $query_result->row();
                if (isset($row->count) && $row->count > 0) {
                    $idx_exists = true;
                }
            }

            if (!$idx_exists) {
                // Check if the column exists before creating an index on it
                if ($this->db->field_exists($column_name, 'legal_cases')) {
                    $this->db->query("CREATE INDEX {$idx_name} ON [dbo].[legal_cases]({$column_name});");
                }
            }
        }

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- Drop indexes first ---
        $indexes_to_drop = [
            'IX_legal_cases_closure_requested_by',
            'IX_legal_cases_closed_by',
        ];
        foreach ($indexes_to_drop as $idx_name) {
            if ($this->db->query("SELECT COUNT(*) AS count FROM sys.indexes WHERE name = '{$idx_name}' AND object_id = OBJECT_ID('[dbo].[legal_cases]');")->row()->count > 0) {
                $this->db->query("DROP INDEX {$idx_name} ON [dbo].[legal_cases];");
            }
        }


        // --- Drop foreign key constraints ---
        $fk_names_to_drop = [
            'FK_legal_cases_closure_requested_by',
            'FK_legal_cases_closed_by',
            'FK_legal_cases_createdBy',
            'FK_legal_cases_modifiedBy',
            'FK_legal_cases_user_id',
            'FK_legal_cases_assignee',
        ];
        foreach ($fk_names_to_drop as $fk_name) {
            if ($this->db->query("SELECT COUNT(*) AS count FROM sys.foreign_keys WHERE name = '{$fk_name}' AND parent_object_id = OBJECT_ID('[dbo].[legal_cases]');")->row()->count > 0) {
                $this->db->query("ALTER TABLE [dbo].[legal_cases] DROP CONSTRAINT {$fk_name};");
            }
        }

        // --- Drop columns from 'legal_cases' table ---
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

        // --- Drop 'criminal' column from 'case_types' table ---
        if ($this->db->field_exists('criminal', 'case_types')) {
            $this->dbforge->drop_column('case_types', 'criminal');
        }

        $this->db->trans_complete(); // Complete the transaction
    }
}
