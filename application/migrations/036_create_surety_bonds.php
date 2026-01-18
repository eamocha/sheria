<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Surety_Bonds extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- 1. Create the surety_bonds table ---
        if (!$this->db->table_exists('surety_bonds')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'contract_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'unsigned' => TRUE,
                    'null' => FALSE,
                ],
                'bond_type' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '50',
                    'null' => FALSE,
                ],
                'bond_amount' => [
                    'type' => 'DECIMAL',
                    'constraint' => '18,2',
                    'null' => FALSE,
                ],
                'currency_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'unsigned' => TRUE,
                    'null' => FALSE,
                ],
                'surety_provider' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '255',
                    'null' => FALSE,
                ],
                'bond_number' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '100',
                    'null' => FALSE,
                ],
                'effective_date' => [
                    'type' => 'DATE',
                    'null' => FALSE,
                ],
                'expiry_date' => [
                    'type' => 'DATE',
                    'null' => TRUE,
                ],
                'released_date' => [
                    'type' => 'DATE',
                    'null' => TRUE,
                ],
                'bond_status' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '50',
                    'null' => FALSE,
                ],
                'document_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'unsigned' => TRUE,
                    'null' => TRUE,
                ],
                'remarks' => [
                    'type' => 'NVARCHAR',
                    'constraint' => 'MAX',
                    'null' => TRUE,
                ],
                'createdOn' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                    'default' => 'GETDATE()'
                ],
                'createdBy' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => FALSE,
                ],
                'modifiedOn' => [
                    'type' => 'DATETIME',
                    'null' => TRUE,
                    'default' => 'GETDATE()'
                ],
                'modifiedBy' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ],
                'archived' => [
                    'type' => 'NVARCHAR',
                    'constraint' => '3',
                    'null' => FALSE,
                    'default' => 'no',
                ],
                'CONSTRAINT pk_surety_bonds PRIMARY KEY (id)',
                'CONSTRAINT uq_surety_bonds_bond_number UNIQUE (bond_number)'
            ]);
            $this->dbforge->create_table('surety_bonds', TRUE);
        }

        // --- 2. Add foreign key constraints ---
        // FK to contract table
        $this->add_foreign_key_if_not_exists(
            'surety_bonds', 'FK_SuretyBond_Contract', 'contract_id', 'contract', 'id'
        );

        // FK to iso_currencies table
        $this->add_foreign_key_if_not_exists(
            'surety_bonds', 'FK_SuretyBond_Currency', 'currency_id', 'iso_currencies', 'id'
        );

        // FK to documents_management_system table
        $this->add_foreign_key_if_not_exists(
            'surety_bonds', 'FK_SuretyBond_Document', 'document_id', 'documents_management_system', 'id'
        );

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- 1. Drop foreign key constraints first ---
        $this->drop_foreign_key_if_exists('surety_bonds', 'FK_SuretyBond_Contract');
        $this->drop_foreign_key_if_exists('surety_bonds', 'FK_SuretyBond_Currency');
        $this->drop_foreign_key_if_exists('surety_bonds', 'FK_SuretyBond_Document');

        // --- 2. Drop the table ---
        if ($this->db->table_exists('surety_bonds')) {
            $this->dbforge->drop_table('surety_bonds', TRUE);
        }

        $this->db->trans_complete(); // Complete the transaction
    }

    /**
     * Helper function to add foreign key if it doesn't exist.
     */
    private function add_foreign_key_if_not_exists($table, $fk_name, $column, $ref_table, $ref_column, $on_delete = 'NO ACTION', $on_update = 'NO ACTION')
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
