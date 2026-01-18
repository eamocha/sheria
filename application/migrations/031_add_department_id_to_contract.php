<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Department_Id_To_Contract extends CI_Migration {

    public function up()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // Create the 'departments' table
        if (!$this->db->table_exists('departments')) {
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
        }

        // Add the department_id column (nullable) to the 'contract' table
        if (!$this->db->field_exists('department_id', 'contract')) {
            $this->dbforge->add_column('contract', [
                'department_id' => [
                    'type' => 'BIGINT',
                    'constraint' => 20,
                    'null' => TRUE,
                ]
            ]);
        }

        // Add foreign key constraint to the departments table
        $fk_name = 'FK_contract_department';
        $this->add_foreign_key_if_not_exists(
            'contract',
            $fk_name,
            'department_id',
            'departments',
            'id',
            'NO ACTION', // ON DELETE behavior
            'NO ACTION'  // ON UPDATE behavior
        );

        $this->db->trans_complete(); // Complete the transaction
    }

    public function down()
    {
        $this->db->trans_start(); // Start a transaction for atomicity

        // Drop the foreign key constraint first
        $this->drop_foreign_key_if_exists('contract', 'FK_contract_department');

        // Drop the department_id column from the 'contract' table
        if ($this->db->field_exists('department_id', 'contract')) {
            $this->dbforge->drop_column('contract', 'department_id');
        }

        // Drop the 'departments' table
        if ($this->db->table_exists('departments')) {
            $this->dbforge->drop_table('departments', TRUE);
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
