<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Conveyancing_And_Insert_Data extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- 1. Insert data into opinion_workflows ---
        // Check if the 'System Workflow (default)' record already exists
        $system_workflow_exists = $this->db->get_where('opinion_workflows', ['name' => 'System Workflow (default)'])->num_rows();
        if ($system_workflow_exists == 0) {
            $this->db->insert('opinion_workflows', [
                'name' => 'System Workflow (default)',
                'type' => 'system',
                'createdBy' => null,
                'createdOn' => null,
                'modifiedBy' => null,
                'modifiedOn' => null,
            ]);
        }

        // Check if the 'Legal Opinions' record already exists
        $legal_opinions_exists = $this->db->get_where('opinion_workflows', ['name' => 'Legal Opinions'])->num_rows();
        if ($legal_opinions_exists == 0) {
            $this->db->insert('opinion_workflows', [
                'name' => 'Legal Opinions',
                'type' => 'default',
                'createdBy' => 1,
                'createdOn' => '2024-08-22 09:50:33.000',
                'modifiedBy' => 1,
                'modifiedOn' => '2024-08-22 09:50:33.000',
            ]);
        }

        // --- 2. Add columns to conveyancing_instruments ---
        // Add parties_id column
        if (!$this->db->field_exists('parties_id', 'conveyancing_instruments')) {
            $this->dbforge->add_column('conveyancing_instruments', [
                'parties_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ]
            ]);
        }

        // Add contact_type column
        if (!$this->db->field_exists('contact_type', 'conveyancing_instruments')) {
            $this->dbforge->add_column('conveyancing_instruments', [
                'contact_type' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '20',
                    'null' => TRUE,
                ]
            ]);
        }

        // --- 3. Drop constraint CHK_amounts ---
        $check_constraint_sql = "SELECT COUNT(*) AS count FROM sys.check_constraints WHERE name = 'CHK_amounts';";
        $query = $this->db->query($check_constraint_sql);
        if ($query->num_rows() > 0 && $query->row()->count > 0) {
            $this->db->query("ALTER TABLE [dbo].[conveyancing_instruments] DROP CONSTRAINT [CHK_amounts];");
        }

        // --- 4. Create index on ci_sessions table ---
        $index_exists_sql = "SELECT 1 FROM sys.indexes WHERE name = 'IX_ci_sessions_timestamp' AND object_id = OBJECT_ID('ci_sessions');";
        $query_index = $this->db->query($index_exists_sql);
        if ($query_index->num_rows() == 0) {
            $this->db->query("CREATE INDEX IX_ci_sessions_timestamp ON ci_sessions (timestamp);");
        }

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- 1. Reverse data insertion ---
        $this->db->delete('opinion_workflows', ['name' => 'System Workflow (default)']);
        $this->db->delete('opinion_workflows', ['name' => 'Legal Opinions']);

        // --- 2. Reverse column additions ---
        if ($this->db->field_exists('parties_id', 'conveyancing_instruments')) {
            $this->dbforge->drop_column('conveyancing_instruments', 'parties_id');
        }
        if ($this->db->field_exists('contact_type', 'conveyancing_instruments')) {
            $this->dbforge->drop_column('conveyancing_instruments', 'contact_type');
        }

        // --- 3. Recreate the dropped constraint (manual intervention might be needed) ---
        // NOTE: The `down()` method cannot automatically recreate the exact `CHK_amounts` constraint
        // because its definition is not provided. If this is a required constraint,
        // you will need to add the `ALTER TABLE ... ADD CONSTRAINT` statement here manually
        // with the original check condition.

        // --- 4. Reverse index creation ---
        $index_exists_sql = "SELECT 1 FROM sys.indexes WHERE name = 'IX_ci_sessions_timestamp' AND object_id = OBJECT_ID('ci_sessions');";
        $query_index = $this->db->query($index_exists_sql);
        if ($query_index->num_rows() > 0) {
            $this->db->query("DROP INDEX IX_ci_sessions_timestamp ON ci_sessions;");
        }

        $this->db->trans_complete(); // Complete the transaction
    }
}
