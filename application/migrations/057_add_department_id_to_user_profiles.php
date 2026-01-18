<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Department_Id_To_User_Profiles extends CI_Migration {

    private $table_name = 'user_profiles';
    private $column_name = 'department_id';
    private $constraint_name = 'FK_user_profiles_department_id';

    public function up()
    {
        $this->db->trans_start(); // Start a transaction

        // SQL to check and add the column and the foreign key constraint
        $sql = "
            -- Check if department_id column exists
            IF NOT EXISTS (SELECT 1 
                           FROM sys.columns 
                           WHERE Name = N'{$this->column_name}' 
                           AND Object_ID = Object_ID(N'dbo.{$this->table_name}'))
            BEGIN
                -- Add department_id column to user_profiles
                ALTER TABLE dbo.{$this->table_name}
                ADD {$this->column_name} BIGINT NULL;

                -- Add foreign key constraint to reference departments(id)
                ALTER TABLE dbo.{$this->table_name}
                ADD CONSTRAINT {$this->constraint_name}
                FOREIGN KEY ({$this->column_name})
                REFERENCES dbo.departments(id);
            END;
        ";

        $this->db->query($sql);
        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction

        // Drop the foreign key constraint first
        $this->db->query("
            IF EXISTS (SELECT 1
                       FROM sys.foreign_keys
                       WHERE name = N'{$this->constraint_name}'
                       AND parent_object_id = OBJECT_ID(N'dbo.{$this->table_name}'))
            BEGIN
                ALTER TABLE dbo.{$this->table_name}
                DROP CONSTRAINT {$this->constraint_name};
            END
        ");

        // Then drop the column
        if ($this->db->field_exists($this->column_name, $this->table_name)) {
            $this->dbforge->drop_column($this->table_name, $this->column_name);
        }

        $this->db->trans_complete(); // Complete the transaction
    }
}
