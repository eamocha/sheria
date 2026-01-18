<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Case_Investigation_Tables extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- 1. Create case_investigation_log table ---
        if (!$this->db->table_exists('case_investigation_log')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'case_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => FALSE,
                ],
                'log_date' => [
                    'type' => 'DATE',
                    'null' => FALSE,
                ],
                'details' => [
                    'type' => 'NVARCHAR',
                    'constraint' => 'MAX',
                    'null' => FALSE,
                ],
                'action_taken' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '100',
                    'null' => TRUE,
                ],
                'createdBy' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ],
                'createdOn datetime NULL DEFAULT GETDATE()', // Default handled here
                'modifiedBy' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ],
                'modifiedOn datetime NULL',
                'CONSTRAINT pk_case_investigation_log PRIMARY KEY (id)'
            ]);
            $this->dbforge->create_table('case_investigation_log', TRUE);

            // Add foreign key for case_id to legal_cases
            $fk_name_case_id = 'FK_case_investigation_log_legal_cases';
            $this->add_foreign_key_if_not_exists(
                'case_investigation_log',
                $fk_name_case_id,
                'case_id',
                'legal_cases',
                'id',
                'CASCADE', // Typically CASCADE for log entries related to a case
                'NO ACTION'
            );

            // Add foreign key for createdBy to users
            $fk_name_createdBy = 'FK_case_investigation_log_createdBy_users';
            $this->add_foreign_key_if_not_exists(
                'case_investigation_log',
                $fk_name_createdBy,
                'createdBy',
                'users',
                'id',
                'NO ACTION',
                'NO ACTION'
            );

            // Add foreign key for modifiedBy to users
            $fk_name_modifiedBy = 'FK_case_investigation_log_modifiedBy_users';
            $this->add_foreign_key_if_not_exists(
                'case_investigation_log',
                $fk_name_modifiedBy,
                'modifiedBy',
                'users',
                'id',
                'NO ACTION',
                'NO ACTION'
            );
        }

        // --- 2. Create case_investigation_log_document table ---
        if (!$this->db->table_exists('case_investigation_log_document')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'investigation_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => FALSE,
                ],
                'document' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => FALSE,
                ],
                'CONSTRAINT pk_case_investigation_log_document PRIMARY KEY (id)'
            ]);
            $this->dbforge->create_table('case_investigation_log_document', TRUE);

            // Add foreign key for investigation_id to case_investigation_log
            $fk_name_doc_log = 'fk_case_investigation_log_doc_1';
            $this->add_foreign_key_if_not_exists(
                'case_investigation_log_document',
                $fk_name_doc_log,
                'investigation_id',
                'case_investigation_log',
                'id',
                'CASCADE',
                'NO ACTION'
            );

            // Add foreign key for document to documents_management_system
            $fk_name_doc_system = 'fk_case_investigation_log_doc_2';
            $this->add_foreign_key_if_not_exists(
                'case_investigation_log_document',
                $fk_name_doc_system,
                'document',
                'documents_management_system',
                'id',
                'CASCADE',
                'CASCADE' // As specified in your SQL
            );
        }

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- Drop Foreign Key Constraints First (reverse order of dependency) ---
        $fk_names_to_drop_doc = [
            'fk_case_investigation_log_doc_2',
            'fk_case_investigation_log_doc_1',
        ];
        foreach ($fk_names_to_drop_doc as $fk_name) {
            $this->drop_foreign_key_if_exists('case_investigation_log_document', $fk_name);
        }

        $fk_names_to_drop_log = [
            'FK_case_investigation_log_modifiedBy_users',
            'FK_case_investigation_log_createdBy_users',
            'FK_case_investigation_log_legal_cases',
        ];
        foreach ($fk_names_to_drop_log as $fk_name) {
            $this->drop_foreign_key_if_exists('case_investigation_log', $fk_name);
        }

        // --- Drop Tables (reverse order of creation) ---
        if ($this->db->table_exists('case_investigation_log_document')) {
            $this->dbforge->drop_table('case_investigation_log_document', TRUE);
        }
        if ($this->db->table_exists('case_investigation_log')) {
            $this->dbforge->drop_table('case_investigation_log', TRUE);
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
