<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Surety_Bonds_Table extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- 1. Drop the table first if it exists to ensure a clean state ---
        $this->dbforge->drop_table('surety_bonds', TRUE);

        // --- 2. Create the 'surety_bonds' table ---
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
                'null' => FALSE,
            ],
            'bond_type' => [
                'type' => 'NVARCHAR',
                'constraint' => '50',
                'null' => FALSE,
            ],
            'bond_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '18, 2',
                'null' => FALSE,
            ],
            'currency_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
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
                'unique' => TRUE, // Add unique constraint here
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
                'null' => TRUE,
            ],
            'remarks' => [
                'type' => 'NVARCHAR',
                'constraint' => 'MAX',
                'null' => TRUE,
            ],
            'createdOn' => [
                'type' => 'DATETIME2',
                'null' => TRUE,
                'default' => 'GETDATE()'
            ],
            'createdBy' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'null' => FALSE,
            ],
            'modifiedOn' => [
                'type' => 'DATETIME2',
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
            'CONSTRAINT pk_surety_bonds PRIMARY KEY (id)'
        ]);
        $this->dbforge->create_table('surety_bonds', TRUE);

        // --- 3. Add Foreign Key constraints using raw SQL (checking existence first) ---

        $fk_bonds_contract = 'FK_SuretyBond_Contract';
        $fk_bonds_currency = 'FK_SuretyBond_Currency';
        $fk_bonds_document = 'FK_SuretyBond_Document';

        // Add FK for contract_id
        if ($this->db->query("SELECT name FROM sys.foreign_keys WHERE name = '{$fk_bonds_contract}'")->num_rows() == 0) {
            $this->db->query("
                ALTER TABLE [dbo].[surety_bonds]
                ADD CONSTRAINT [{$fk_bonds_contract}]
                FOREIGN KEY ([contract_id])
                REFERENCES [dbo].[contract]([id])
            ");
        }

        // Add FK for currency_id
        if ($this->db->query("SELECT name FROM sys.foreign_keys WHERE name = '{$fk_bonds_currency}'")->num_rows() == 0) {
            $this->db->query("
                ALTER TABLE [dbo].[surety_bonds]
                ADD CONSTRAINT [{$fk_bonds_currency}]
                FOREIGN KEY ([currency_id])
                REFERENCES [dbo].[iso_currencies]([id])
            ");
        }
        
        // Add FK for document_id
        if ($this->db->query("SELECT name FROM sys.foreign_keys WHERE name = '{$fk_bonds_document}'")->num_rows() == 0) {
            $this->db->query("
                ALTER TABLE [dbo].[surety_bonds]
                ADD CONSTRAINT [{$fk_bonds_document}]
                FOREIGN KEY ([document_id])
                REFERENCES [dbo].[documents_management_system]([id])
            ");
        }

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- Drop Foreign Key constraints (in reverse order of creation) ---
        $fk_bonds_contract = 'FK_SuretyBond_Contract';
        $fk_bonds_currency = 'FK_SuretyBond_Currency';
        $fk_bonds_document = 'FK_SuretyBond_Document';

        // Drop FK for document_id
        if ($this->db->query("SELECT name FROM sys.foreign_keys WHERE name = '{$fk_bonds_document}'")->num_rows() > 0) {
            $this->db->query("ALTER TABLE [dbo].[surety_bonds] DROP CONSTRAINT [{$fk_bonds_document}]");
        }
        
        // Drop FK for currency_id
        if ($this->db->query("SELECT name FROM sys.foreign_keys WHERE name = '{$fk_bonds_currency}'")->num_rows() > 0) {
            $this->db->query("ALTER TABLE [dbo].[surety_bonds] DROP CONSTRAINT [{$fk_bonds_currency}]");
        }

        // Drop FK for contract_id
        if ($this->db->query("SELECT name FROM sys.foreign_keys WHERE name = '{$fk_bonds_contract}'")->num_rows() > 0) {
            $this->db->query("ALTER TABLE [dbo].[surety_bonds] DROP CONSTRAINT [{$fk_bonds_contract}]");
        }
        
        // --- Drop the 'surety_bonds' table ---
        $this->dbforge->drop_table('surety_bonds', TRUE);

        $this->db->trans_complete(); // Complete the transaction
    }
}