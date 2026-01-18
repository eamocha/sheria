<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_Contract_Tables extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- 1. Alter 'contract' table ---

        // Alter 'status' column type (assuming it exists and needs resizing)
        // CodeIgniter's modify_column requires the full column definition
        if ($this->db->field_exists('status', 'contract')) {
            $this->dbforge->modify_column('contract', [
                'status' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '10', // Change to NVARCHAR(10)
                    'null' => TRUE, // Maintain existing nullability or specify
                ],
            ]);
        }

        // Add 'category' column to 'contract' table
        if (!$this->db->field_exists('category', 'contract')) {
            $this->dbforge->add_column('contract', [
                'category' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '20',
                    'null' => TRUE,
                ],
            ]);
        }

        // Add 'stage' column to 'contract' table
        if (!$this->db->field_exists('stage', 'contract')) {
            $this->dbforge->add_column('contract', [
                'stage' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '20',
                    'null' => TRUE,
                ],
            ]);
        }

        // --- 2. Add DEFAULT constraint to 'applies_to' in 'contract_type_language' table ---
        // Note: CodeIgniter's dbforge does not have a direct method for adding DEFAULT to an existing column.
        // We use raw SQL for this.
        // First, check if the constraint exists to avoid errors on re-run.
        $constraint_name = 'DF_contract_type_language_applies_to';
        $check_constraint_sql = "
            SELECT COUNT(*) AS constraint_count
            FROM sys.default_constraints
            WHERE parent_object_id = OBJECT_ID('dbo.contract_type_language')
            AND name = '{$constraint_name}';
        ";
        // Safely get the row and check the property
        $query_result = $this->db->query($check_constraint_sql)->row();
        $constraint_exists = ($query_result && isset($query_result->constraint_count) && $query_result->constraint_count > 0);


        if (!$constraint_exists) {
            $this->db->query("
                ALTER TABLE [dbo].[contract_type_language]
                ADD CONSTRAINT {$constraint_name} DEFAULT 'contract' FOR [applies_to];
            ");
        }


        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- 1. Drop DEFAULT constraint from 'applies_to' in 'contract_type_language' table ---
        $constraint_name = 'DF_contract_type_language_applies_to';
        $check_constraint_sql = "
            SELECT name
            FROM sys.default_constraints
            WHERE parent_object_id = OBJECT_ID('dbo.contract_type_language')
            AND parent_column_id = (SELECT column_id FROM sys.columns WHERE object_id = OBJECT_ID('dbo.contract_type_language') AND name = 'applies_to');
        ";
        $result = $this->db->query($check_constraint_sql)->row();

        if ($result) {
            $existing_constraint_name = $result->name;
            $this->db->query("
                ALTER TABLE [dbo].[contract_type_language]
                DROP CONSTRAINT {$existing_constraint_name};
            ");
        }

        // --- 2. Drop columns from 'contract' table ---

        // Drop 'stage' column
        if ($this->db->field_exists('stage', 'contract')) {
            $this->dbforge->drop_column('contract', 'stage');
        }

        // Drop 'category' column
        if ($this->db->field_exists('category', 'contract')) {
            $this->dbforge->drop_column('contract', 'category');
        }

        // --- 3. Revert 'status' column type (if applicable, to its previous state) ---
        // You might need to know the original type/constraint to revert properly.
        // Assuming it was NVARCHAR(50) or similar, as a common fallback.
        if ($this->db->field_exists('status', 'contract')) {
            $this->dbforge->modify_column('contract', [
                'status' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '50', // Revert to a common previous size, or original if known
                    'null' => TRUE, // Revert to original nullability
                ],
            ]);
        }

        $this->db->trans_complete(); // Complete the transaction
    }
}
