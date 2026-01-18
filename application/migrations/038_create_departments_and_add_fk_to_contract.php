<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Departments_And_Add_FK_To_Contract extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- 1. Create departments table ---
        $this->dbforge->drop_table('departments', TRUE);

        $this->dbforge->add_field([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'name' => [
                'type' => 'NVARCHAR',
                'constraint' => '100',
                'null' => FALSE,
            ],
            'CONSTRAINT pk_departments PRIMARY KEY (id)'
        ]);
        $this->dbforge->create_table('departments', TRUE);

        // --- 2. Add the 'department_id' column to the 'contract' table ---
        // Use SQL Server specific check for column existence
        $column_check = $this->db->query("
            SELECT COUNT(*) as column_exists
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_NAME = 'contract' 
            AND COLUMN_NAME = 'department_id'
        ")->row()->column_exists;

        if ($column_check == 0) {
            $this->dbforge->add_column('contract', [
                'department_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ]
            ]);
            echo "Added department_id column to contract table\n";
        } else {
            echo "department_id column already exists in contract table\n";
        }

        // --- 3. Add the Foreign Key constraint ---
        $fk_constraint_name = 'FK_contract_department';

        // Check if FK exists before attempting to create it
        $fk_check = $this->db->query("
            SELECT COUNT(*) as fk_exists
            FROM sys.foreign_keys 
            WHERE name = '{$fk_constraint_name}'
        ")->row()->fk_exists;

        if ($fk_check == 0) {
            $this->db->query("
                ALTER TABLE [dbo].[contract]
                ADD CONSTRAINT [{$fk_constraint_name}]
                FOREIGN KEY ([department_id])
                REFERENCES [dbo].[departments]([id])
            ");
            echo "Added foreign key constraint\n";
        } else {
            echo "Foreign key constraint already exists\n";
        }

        $this->db->trans_complete(); // Complete the transaction

        if ($this->db->trans_status() === FALSE) {
            echo "Migration failed\n";
        } else {
            echo "Migration completed successfully\n";
        }
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // --- 1. Drop the Foreign Key constraint ---
        $fk_constraint_name = 'FK_contract_department';

        $fk_check = $this->db->query("
            SELECT COUNT(*) as fk_exists
            FROM sys.foreign_keys 
            WHERE name = '{$fk_constraint_name}'
        ")->row()->fk_exists;

        if ($fk_check > 0) {
            $this->db->query("
                ALTER TABLE [dbo].[contract]
                DROP CONSTRAINT [{$fk_constraint_name}]
            ");
            echo "Dropped foreign key constraint\n";
        }

        // --- 2. Drop the 'department_id' column from 'contract' table ---
        $column_check = $this->db->query("
            SELECT COUNT(*) as column_exists
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_NAME = 'contract' 
            AND COLUMN_NAME = 'department_id'
        ")->row()->column_exists;

        if ($column_check > 0) {
            $this->dbforge->drop_column('contract', 'department_id');
            echo "Dropped department_id column from contract table\n";
        }

        // --- 3. Drop the 'departments' table ---
        $this->dbforge->drop_table('departments', TRUE);
        echo "Dropped departments table\n";

        $this->db->trans_complete(); // Complete the transaction

        if ($this->db->trans_status() === FALSE) {
            echo "Rollback failed\n";
        } else {
            echo "Rollback completed successfully\n";
        }
    }
}