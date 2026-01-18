<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_Correspondences_Document_Type_Id extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // Check if the column exists before attempting to alter it
        if ($this->db->field_exists('document_type_id', 'correspondences')) {

            // --- Drop any foreign key constraints on document_type_id first ---
            $fk_name = 'FK_correspondences_document_type'; // This was the name used in 024_create_correspondence_module_tables
            $this->drop_foreign_key_if_exists('correspondences', $fk_name);

            // --- Drop any default constraints on document_type_id ---
            $query_default_constraint = $this->db->query("
                SELECT dc.name
                FROM sys.default_constraints dc
                INNER JOIN sys.columns c ON dc.parent_object_id = c.object_id AND dc.parent_column_id = c.column_id
                WHERE dc.parent_object_id = OBJECT_ID('correspondences') AND c.name = 'document_type_id';
            ");

            $default_constraint_name = null;
            if ($query_default_constraint->num_rows() > 0) {
                $row = $query_default_constraint->row();
                $default_constraint_name = $row->name;
                $this->db->query("ALTER TABLE [dbo].[correspondences] DROP CONSTRAINT " . $default_constraint_name);
            }

            // --- Alter the column type to BIGINT NULL to match correspondence_document_types.id ---
            $this->dbforge->modify_column('correspondences', [
                'document_type_id' => [
                    'type' => 'BIGINT', // Changed to BIGINT
                    'constraint' => 20, // Constraint for BIGINT
                    'null' => TRUE,
                ]
            ]);

            // --- Re-add the foreign key constraint if the referenced table exists ---
            if ($this->db->table_exists('correspondence_document_types')) {
                $this->add_foreign_key_if_not_exists(
                    'correspondences',
                    $fk_name,
                    'document_type_id',
                    'correspondence_document_types',
                    'id',
                    'NO ACTION', // Assuming NO ACTION to prevent cascade issues
                    'NO ACTION'
                );
            }
        }

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // Revert the column to its original definition (BIGINT NULL)
        if ($this->db->field_exists('document_type_id', 'correspondences')) {

            // --- Drop any foreign key constraints on document_type_id first ---
            $fk_name = 'FK_correspondences_document_type';
            $this->drop_foreign_key_if_exists('correspondences', $fk_name);

            // --- Drop any default constraints on document_type_id ---
            $query_default_constraint = $this->db->query("
                SELECT dc.name
                FROM sys.default_constraints dc
                INNER JOIN sys.columns c ON dc.parent_object_id = c.object_id AND dc.parent_column_id = c.column_id
                WHERE dc.parent_object_id = OBJECT_ID('correspondences') AND c.name = 'document_type_id';
            ");

            $default_constraint_name = null;
            if ($query_default_constraint->num_rows() > 0) {
                $row = $query_default_constraint->row();
                $default_constraint_name = $row->name;
                $this->db->query("ALTER TABLE [dbo].[correspondences] DROP CONSTRAINT " . $default_constraint_name);
            }

            // --- Alter the column type back to BIGINT NULL ---
            $this->dbforge->modify_column('correspondences', [
                'document_type_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ]
            ]);

            // --- Re-add the foreign key constraint if the referenced table exists ---
            if ($this->db->table_exists('correspondence_document_types')) {
                $this->add_foreign_key_if_not_exists(
                    'correspondences',
                    $fk_name,
                    'document_type_id',
                    'correspondence_document_types',
                    'id',
                    'NO ACTION', // Keep NO ACTION for consistency and to avoid cascade issues
                    'NO ACTION'
                );
            }
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
